<?php
/**
 * REST API functionality.
 *
 * @package JQueryCategoriesList
 */

/**
 * Registers REST API endpoints used by the block frontend.
 */
class JS_Categories_List_Rest_Endpoints {
	/**
	 * Maximum number of category IDs accepted in a request.
	 */
	const MAX_IDS = 200;

	/**
	 * Registers the plugin REST routes.
	 *
	 * @return void
	 */
	public function register_routes() {
		register_rest_route(
			'jcl/v1',
			'/categories',
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_categories' ],
				'permission_callback' => '__return_true',
			]
		);

		register_rest_route(
			'jcl/v1',
			'/category-options',
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_category_options' ],
				'permission_callback' => '__return_true',
			]
		);
	}

	/**
	 * Normalizes the post type request parameter.
	 *
	 * @param string $post_type Requested post type.
	 * @return string
	 */
	private function sanitize_post_type( $post_type ) {
		$post_type = sanitize_key( $post_type ?: 'post' );
		$object    = get_post_type_object( $post_type );

		if ( ! $object ) {
			return 'post';
		}

		return $post_type;
	}

	/**
	 * Returns the default hierarchical taxonomy for a post type.
	 *
	 * @param string $post_type Sanitized post type.
	 * @return string
	 */
	private function get_default_taxonomy_for_post_type( $post_type ) {
		$taxonomies = get_object_taxonomies( $post_type, 'objects' );

		if ( empty( $taxonomies ) ) {
			return 'category';
		}

		$hierarchical_taxonomies = array_filter(
			$taxonomies,
			static function ( $taxonomy ) {
				return ! empty( $taxonomy->hierarchical ) && false !== $taxonomy->show_ui;
			}
		);

		if ( empty( $hierarchical_taxonomies ) ) {
			return 'category';
		}

		if ( isset( $hierarchical_taxonomies['category'] ) ) {
			return 'category';
		}

		$first_taxonomy = reset( $hierarchical_taxonomies );

		return $first_taxonomy->name ?? 'category';
	}

	/**
	 * Sanitizes the taxonomy request parameter.
	 *
	 * @param string $post_type Sanitized post type.
	 * @param string $taxonomy Requested taxonomy.
	 * @return string
	 */
	private function sanitize_taxonomy( $post_type, $taxonomy ) {
		$taxonomy        = sanitize_key( $taxonomy ?: '' );
		$taxonomy_object = get_taxonomy( $taxonomy );

		if (
			empty( $taxonomy ) ||
			! $taxonomy_object ||
			empty( $taxonomy_object->hierarchical ) ||
			! is_object_in_taxonomy( $post_type, $taxonomy )
		) {
			return $this->get_default_taxonomy_for_post_type( $post_type );
		}

		return $taxonomy;
	}

	/**
	 * Fetches taxonomy terms for the current request configuration.
	 *
	 * @param array<string, mixed> $config Normalized request config.
	 * @return WP_Term[]
	 */
	private function get_terms_for_config( $config ) {
		$term_args = [
			'taxonomy'   => $config['taxonomy'],
			'orderby'    => $config['orderby'],
			'order'      => $config['orderdir'],
			'hide_empty' => ! $config['show_empty'],
			'parent'     => $config['parent'],
		];

		if ( ! empty( $config['include'] ) ) {
			$term_args['include'] = $config['include'];
		}

		if ( ! empty( $config['exclude'] ) ) {
			$term_args['exclude'] = $config['exclude'];
		}

		$terms = get_terms( $term_args );

		return is_wp_error( $terms ) ? [] : $terms;
	}

	/**
	 * Creates the internal configuration from request parameters.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return array<string, mixed>
	 */
	private function build_config( $request ) {
		$include_or_exclude = $request->get_param( 'exclusionType' ) ?: 'include';
		$include_or_exclude = in_array( $include_or_exclude, [ 'include', 'exclude' ], true ) ? $include_or_exclude : 'include';

		$categories = wp_parse_id_list( $request->get_param( 'cats' ) ?: '' );
		if ( count( $categories ) > self::MAX_IDS ) {
			$categories = array_slice( $categories, 0, self::MAX_IDS );
		}

		$show_empty = rest_sanitize_boolean( $request->get_param( 'showEmpty' ) );
		$orderby    = sanitize_key( $request->get_param( 'orderby' ) ?: 'name' );
		$orderdir   = strtoupper( (string) ( $request->get_param( 'orderdir' ) ?: 'ASC' ) );

		if ( ! in_array( $orderby, [ 'name', 'id', 'count', 'slug' ], true ) ) {
			$orderby = 'name';
		}

		if ( ! in_array( $orderdir, [ 'ASC', 'DESC' ], true ) ) {
			$orderdir = 'ASC';
		}

		return [
			'exclude'    => 'exclude' === $include_or_exclude ? $categories : [],
			'include'    => 'include' === $include_or_exclude ? $categories : [],
			'orderby'    => $orderby,
			'orderdir'   => $orderdir,
			'parent'     => absint( $request->get_param( 'parent' ) ?: 0 ),
			'show_empty' => (bool) $show_empty,
			'post_type'  => $this->sanitize_post_type( (string) $request->get_param( 'postType' ) ),
			'taxonomy'   => '',
		];
	}

	/**
	 * Gets the requested categories.
	 *
	 * @param WP_REST_Request $request Full request data.
	 * @return WP_REST_Response
	 */
	public function get_categories( $request ) {
		$config             = $this->build_config( $request );
		$config['taxonomy'] = $this->sanitize_taxonomy(
			$config['post_type'],
			(string) $request->get_param( 'taxonomy' )
		);
		$terms              = $this->get_terms_for_config( $config );

		$categories = [];

		foreach ( $terms as $term ) {
			$link = get_term_link( $term, $config['taxonomy'] );

			$categories[] = [
				'id'        => (int) $term->term_id,
				'name'      => $term->name,
				'count'     => (int) $term->count,
				'url'       => is_wp_error( $link ) ? '' : esc_url( $link ),
				'child_num' => count( get_term_children( $term->term_id, $config['taxonomy'] ) ),
			];
		}

		return new WP_REST_Response(
			[
				'categories' => $categories,
			],
			200
		);
	}

	/**
	 * Gets category options for editor controls.
	 *
	 * @param WP_REST_Request $request Full request data.
	 * @return WP_REST_Response
	 */
	public function get_category_options( $request ) {
		$post_type = $this->sanitize_post_type( (string) $request->get_param( 'postType' ) );
		$taxonomy  = $this->sanitize_taxonomy( $post_type, (string) $request->get_param( 'taxonomy' ) );
		$terms     = get_terms(
			[
				'taxonomy'   => $taxonomy,
				'orderby'    => 'name',
				'order'      => 'ASC',
				'hide_empty' => false,
			]
		);

		if ( is_wp_error( $terms ) ) {
			$terms = [];
		}

		$options = array_map(
			static function ( $term ) {
				return [
					'id'   => (int) $term->term_id,
					'name' => $term->name,
				];
			},
			$terms
		);

		return new WP_REST_Response(
			[
				'categories' => $options,
			],
			200
		);
	}
}

add_action( 'rest_api_init', [ new JS_Categories_List_Rest_Endpoints(), 'register_routes' ] );
