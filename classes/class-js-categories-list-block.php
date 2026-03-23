<?php
/**
 * Block rendering functionality.
 *
 * @package JQueryCategoriesList
 */

/**
 * Handles block rendering and frontend bootstrap data.
 */
class JS_Categories_List_Block {
	/**
	 * Singleton instance.
	 *
	 * @var JS_Categories_List_Block|null
	 */
	public static $instance;

	/**
	 * Normalized block attributes.
	 *
	 * @var array<string, mixed>
	 */
	private $attributes = [];

	/**
	 * Returns the singleton instance.
	 *
	 * @return JS_Categories_List_Block
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Builds the block HTML markup.
	 *
	 * @param array<string, mixed> $attributes Block settings.
	 * @return string
	 */
	public function build_html( $attributes ) {
		$this->set_attributes( $attributes );

		return sprintf(
			'<div %1$s%2$s></div>',
			get_block_wrapper_attributes(),
			$this->print_attributes()
		);
	}

	/**
	 * Returns the data attributes consumed by the frontend app.
	 *
	 * @return string
	 */
	private function print_attributes() {
		$buffer = '';

		foreach ( $this->attributes as $key => $value ) {
			$buffer .= sprintf( ' data-%1$s="%2$s"', esc_attr( $key ), esc_attr( (string) $value ) );
		}

		return $buffer;
	}

	/**
	 * Normalizes block attributes before rendering.
	 *
	 * @param array<string, mixed> $block_attributes Raw block attributes.
	 * @return void
	 */
	private function set_attributes( $block_attributes = [] ) {
		$this->attributes = [
			'title'              => $block_attributes['title'] ?? '',
			'symbol'             => $block_attributes['symbol'] ?? '0',
			'effect'             => $block_attributes['effect'] ?? 'none',
			'layout'             => $block_attributes['layout'] ?? 'left',
			'orderby'            => $block_attributes['orderby'] ?? 'name',
			'orderdir'           => $block_attributes['orderdir'] ?? 'ASC',
			'expand'             => $block_attributes['expand'] ?? '',
			'showcount'          => (int) ( $block_attributes['showcount'] ?? 0 ),
			'show_empty'         => (int) ( $block_attributes['show_empty'] ?? 0 ),
			'parent_expand'      => (int) ( $block_attributes['parent_expand'] ?? 0 ),
			'include_or_exclude' => $block_attributes['include_or_exclude'] ?? 'include',
			'categories'         => isset( $block_attributes['categories'] ) ? implode( ',', $block_attributes['categories'] ) : '',
		];
	}

	/**
	 * Injects the current category tree into the footer for the legacy frontend.
	 *
	 * @return void
	 */
	public function inject_post_data() {
		if ( ! is_category() ) {
			return;
		}

		$category_id          = get_queried_object_id();
		$category_and_parents = [ $category_id ];
		$parent_category_ids  = get_ancestors( $category_id, 'category' );

		while ( ! empty( $parent_category_ids ) ) {
			$parent_category_id = array_shift( $parent_category_ids );

			$category_and_parents[]   = $parent_category_id;
			$grandparent_category_ids = get_ancestors( $parent_category_id, 'category' );
			$parent_category_ids      = array_merge( $parent_category_ids, $grandparent_category_ids );
		}

		$category_and_parents = array_map( 'absint', array_unique( $category_and_parents ) );

		printf(
			'<script type="text/javascript">var jclCurrentCat=%s;</script>',
			wp_json_encode( implode( ',', $category_and_parents ) )
		);
	}
}

add_action( 'wp_footer', [ JS_Categories_List_Block::instance(), 'inject_post_data' ] );
