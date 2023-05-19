<?php

class JS_Categories_List_Block {
	/**
	 * Class instance, used to control plugin's action from a third party plugin
	 *
	 * @var $instance JS_Categories_List_Block
	 */
	public static $instance;

	/**
	 * @var $attributes array This widget's config.
	 */
	private $attributes;

	/**
	 * Access to a class instance
	 *
	 * @return JS_Categories_List_Block
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Builds widget's HTML markup so react can be mounted there.
	 *
	 * @param array $attributes Block's settings.
	 *
	 * @return string Generated HTML markup.
	 */
	public function build_html( $attributes ) {
		$this->set_attributes( $attributes );

		return sprintf(
			'<div %s %s></div>',
			get_block_wrapper_attributes(),
			$this->print_attributes()
		);
	}

	/**
	 * Prints widget's attributes in HTML attributes so
	 * the React component can take use it.
	 *
	 * @return string The HTML attributes.
	 */
	private function print_attributes() {
		$buffer = '';

		foreach ( $this->attributes as $key => $value ) {
			$buffer .= ' data-' . esc_attr( $key ) . '="' . esc_attr( $value ) . '"';
		}

		return $buffer;
	}

	private function set_attributes( $block_attributes = array() ) {
		$this->attributes = array(
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
			'categories'         => isset( $block_attributes['categories'] )
				? implode( ',', $block_attributes['categories'] )
				: '',
		);
	}

	/**
	 * Registers current post's month and year as JS variable so frontend can access it
	 *
	 * @return void
	 */
	public function inject_post_data() {
		if ( is_category() ) {
			$category_id          = get_queried_object_id();
			$category_and_parents = [];

			$category_and_parents[] = $category_id;
			$parent_category_ids    = get_ancestors( $category_id, 'category' );

			// var_dump($parent_category_ids);die;

			while ( ! empty( $parent_category_ids ) ) {
				$parent_category_id = array_shift( $parent_category_ids );

				$category_and_parents[]   = $parent_category_id;
				$grandparent_category_ids = get_ancestors( $parent_category_id, 'category' );
				$parent_category_ids      = array_merge( $parent_category_ids, $grandparent_category_ids );
			}

			printf(
				'<script type="text/javascript">var jclCurrentCat="%s";</script>',
				implode( ',', array_unique( $category_and_parents ) )
			);
		}
	}
}

// Adds current category's tree to the JS variable so widget can check it.
$jalw_frontend = JS_Categories_List_Block::instance();
add_action( 'wp_footer', [ $jalw_frontend, 'inject_post_data' ] );
