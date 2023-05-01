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
			'<div id="app" %s %s></div>',
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
			$buffer .= ' data-' . esc_attr($key) . '="' . esc_attr( $value ) . '"';
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
			'showcount'          => (int)( $block_attributes['showcount'] ?? 0 ),
			'show_empty'         => (int)( $block_attributes['show_empty'] ?? 0 ),
			'parent_expand'      => (int)( $block_attributes['parent_expand'] ?? 0 ),
			'include_or_exclude' => $block_attributes['include_or_exclude'] ?? 'include',
			'categories'         => isset( $block_attributes['categories'] )
				? implode( ',', $block_attributes['categories'] )
				: '',
    );
	}
}
