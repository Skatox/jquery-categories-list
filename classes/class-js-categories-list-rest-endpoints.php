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
		];
	}

	/**
	 * Gets the requested categories.
	 *
	 * @param WP_REST_Request $request Full request data.
	 * @return WP_REST_Response
	 */
	public function get_categories( $request ) {
		$config          = $this->build_config( $request );
		$full_categories = get_categories(
			[
				'type'         => 'post',
				'orderby'      => $config['orderby'],
				'order'        => $config['orderdir'],
				'hide_empty'   => ! $config['show_empty'],
				'hierarchical' => 1,
				'taxonomy'     => 'category',
				'pad_counts'   => true,
				'include'      => $config['include'],
				'exclude'      => $config['exclude'],
				'parent'       => $config['parent'],
			]
		);

		$categories = [];

		foreach ( $full_categories as $category ) {
			$categories[] = [
				'id'        => (int) $category->term_id,
				'name'      => $category->cat_name,
				'count'     => (int) $category->count,
				'url'       => esc_url( get_category_link( $category->term_id ) ),
				'child_num' => count( get_term_children( $category->term_id, 'category' ) ),
			];
		}

		return new WP_REST_Response(
			[
				'categories' => $categories,
			],
			200
		);
	}
}

add_action( 'rest_api_init', [ new JS_Categories_List_Rest_Endpoints(), 'register_routes' ] );
