<?php

/**
 * Class to register REST API endpoints for the block.
 */
class JS_Categories_List_Rest_Endpoints {

	public $config = [];

	public function register_routes() {
		$version   = '1';
		$namespace = 'jcl/v' . $version;

		register_rest_route( $namespace, '/categories', [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [ $this, 'get_categories' ],
			'permission_callback' => '__return_true',
		] );
	}

	/**
	 * Creates internal config from received parameters.
	 *
	 * @param WP_REST_Request $request
	 */
	private function build_config( $request ) {
		$include_or_exclude = $request->get_param( 'exclusionType' ) ?? 'include';
		$categories         = $request->get_param( 'cats' ) ?? '';

		if ( $include_or_exclude === 'include' ) {
			$included = $categories;
			$excluded = [];
		} else {
			$included = [];
			$excluded = $categories;
		}

		$show_empty = $request->get_param( 'showEmpty' ) ?? true;

		if ( is_string( $show_empty ) ) {
			$show_empty = $show_empty === 'true';
		}

		return [
			'exclude'    => $excluded,
			'include'    => $included,
			'orderby'    => $request->get_param( 'orderby' ) ?? 'name',
			'orderdir'   => $request->get_param( 'orderdir' ) ?? 'ASC',
			'parent'     => $request->get_param( 'parent' ) ?? 0,
			'show_empty' => $show_empty,
			'taxonomy'   => $request->get_param( 'taxonomy' ) ?? 'category',
			'type'       => $request->get_param( 'type' ) ?? 'post',
		];
	}

	/**
	 * Get categories of posts.
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_Error|WP_REST_Response Request with the data.
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
				'parent'       => $config['parent']
			]
		);

		$categories = [];

		foreach ( $full_categories as $key => $category ) {
			$categories[] = [
				'id'        => $category->term_id,
				'name'      => $category->cat_name,
				'count'     => $category->count,
				'url'       => esc_url( get_category_link( $category->term_id ) ),
				'child_num' => count( get_term_children( $category->term_id, 'category' ) )
			];
		}

		return new WP_REST_Response( [
			'categories' => $categories,
		], 200 );
	}
}

$jcl_endpoints = new JS_Categories_List_Rest_Endpoints();
add_action( 'rest_api_init', [ $jcl_endpoints, 'register_routes' ] );
