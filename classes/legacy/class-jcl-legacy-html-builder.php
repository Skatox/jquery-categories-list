<?php

/**
 * Class to build the HTML code of the widget.
 */
class JCL_Legacy_HTML_Builder {
	const ACTIVE_CLASS = 'jcl_active';

	private $config = null;
	private $all_categories = [];
	private $current_post = null;
	private $ids_to_expand;
	private $cats_to_exclude;

	public function __construct( $config, $categories ) {
		$this->config         = $config;
		$this->all_categories = $categories;
		$this->current_post   = is_single() ? get_post( get_the_ID() ) : null;
		$this->find_cats_to_expand();
		$this->set_cats_to_exclude();
	}

	/**
	 * Builds a list of ids category to expand.
	 */
	public function find_cats_to_expand() {
		$this->ids_to_expand = [ 0 ];

		foreach ( $this->all_categories as $category ) {
			if ( $this->should_expand( $category->cat_ID ) ) {
				$this->ids_to_expand[] = $category->cat_ID;
			}
		}
	}

	private function set_cats_to_exclude() {
		$cats_to_exclude = [];

		if ( ! empty( $this->config['exclude'] ) ) {
			$cats_to_exclude = unserialize( $this->config['exclude'] );

			if ( is_string( $cats_to_exclude ) ) {
				$cats_to_exclude = unserialize( $cats_to_exclude );
			}
		}

		$this->cats_to_exclude = $cats_to_exclude;
	}

	/**
	 * Function to check if category should be shown in the HTML.
	 *
	 * @param int $category_id Category ID to check if should be shown.
	 *
	 * @return bool If child category should be shown.
	 */
	private function should_expand( $category_id ) {
		if ( $this->config['expand'] === 'all' ) {
			return true;
		}

		if ( $this->config['expand'] === 'sel_cat' ) {
			// If current category matches show childs.
			if ( is_category( $category_id ) ) {
				return true;
			}

			$post_cats = empty( $this->current_post ) ? null : $this->current_post->post_category;

			// If current post's cats matches show.
			$is_current_cat = is_array( $post_cats ) ? in_array( $category_id, $post_cats ) : $category_id == $post_cats;

			if ( $is_current_cat ) {
				return true;
			}

			// Show if child matches.
			return $this->has_active_child( $category_id );
		}

		return false;
	}

	/**
	 * Search if current category has a child which is active (clicked )
	 *
	 * @param int $parent_id The category's ID.
	 *
	 * @return boolean      if category has a active child
	 */
	protected function has_active_child( $parent_id ) {
		$cats = get_categories( [
			'type'         => 'post',
			'child_of'     => $parent_id,
			'hide_empty'   => 0,
			'hierarchical' => 1,
			'taxonomy'     => 'category',
		] );

		if ( empty( $cats ) ) {
			return false;
		}

		foreach ( $cats as $cat ) {
			if ( is_category( $cat->cat_ID ) ) {
				return true;
			}

			if (
				$this->current_post && is_array( $cat->cat_ID ) &&
				in_array( $cat->cat_ID, $this->current_post->post_category )
			) {
				return true;
			}

			if ( $this->has_active_child( (int) $cat->cat_ID ) ) {
				return true;
			}
		}

		return false;
	}


	/**
	 * Build the HTML code of the widget
	 */
	public function get_code() {
		return $this->print_category( 0 );
	}

	protected function filter_cats_by_parent_id( $parent_id ) {
		return array_filter( $this->all_categories, function ( $cat ) use ( $parent_id ) {
			return $cat->parent == $parent_id;
		} );
	}

	/**
	 * Creates HTML code recursive
	 */
	protected function print_category( $parent_id, $override_show_children = false ) {
		$html       = '';
		$categories = $this->filter_cats_by_parent_id( $parent_id );

		foreach ( $categories as $category ) {
			if ( empty( $category->cat_name ) || in_array( $category->term_id, $this->cats_to_exclude ) ) {
				continue;
			}

			//Gets the code for children.
			$child_html   = $this->print_category( $category->cat_ID, (bool) $this->config['parent_expand'] );
			$has_child    = ! empty( $child_html );
			$expand_child = $override_show_children || in_array( $category->cat_ID, $this->ids_to_expand );
			$symbol_link  = $has_child ? $this->create_toggle_link( $category, $expand_child ) : '';

			$html .= sprintf( '<li class="jcl_category %s">', $expand_child ? 'expanded' : '' );

			$cat_link = $this->create_category_link( $category, $expand_child );
			$html     .= $this->config['layout'] === 'right' ? $cat_link . $symbol_link : $symbol_link . $cat_link;

			if ( $has_child ) {
				$html .= sprintf(
					'<ul %s>%s</ul>',
					$expand_child ? '' : 'style="display: none;"',
					$child_html
				);
			}

			$html .= '</li>';
		}

		return $html;
	}

	/**
	 * Creates the anchor link for the category
	 *
	 * @param object $category WP Category object.
	 * @param bool $is_active Tells if category is active.
	 *
	 * @return string                   HTML code of the link
	 */
	protected function create_category_link( $category, $is_active ) {

		$classCode = $is_active ? 'class="' . self::ACTIVE_CLASS . '"' : '';
		$link      = get_category_link( $category->term_id );

		$html = '<a href="' . $link . '" ' . $classCode . '>' . $category->cat_name;

		if ( $this->config['showcount'] ) {
			$html .= '<span class="jcl_count">(' . $category->count . ')</span>';
		}

		$html .= '</a>';

		return $html;
	}

	/**
	 * Function to build the toggle link
	 *
	 * @param stdClass $category Category object.
	 * @param bool $expand_child_cat If child category is expanded or not.
	 *
	 * @return string HTML code for the category toggle link.
	 */
	public function create_toggle_link( $category, $expand_child_cat ) {
		$symbol_key = $expand_child_cat ? 'con_sym' : 'ex_sym';

		return sprintf(
			'<a href="%s" class="jcl_symbol" title="%s">%s</a>',
			esc_attr( get_category_link( $category->term_id ) ),
			esc_attr( __( 'View Sub-Categories', 'jcl_i18n' ) ),
			htmlspecialchars( $this->config[ $symbol_key ] )
		);
	}
}
