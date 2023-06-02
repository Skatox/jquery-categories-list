<?php

class JCL_Legacy_Widget extends WP_Widget {

	const ASSETS_ID = 'js-categories-list';
	const JS_FILENAME = 'jcl.js';
	const CSS_FILENAME = 'jcl.css';
	/**
	 * Class instance, used to control plugin's action from a third party plugin
	 *
	 * @var $instance
	 */
	public static $instance;
	public $config;
	public $defaults
		= [
			'title'         => '',
			'symbol'        => 1,
			'ex_sym'        => '►',
			'con_sym'       => '▼',
			'layout'        => 'right',
			'effect'        => 'slide',
			'fx_in'         => 'slideDown',
			'fx_out'        => 'slideUp',
			'orderby'       => 'name',
			'orderdir'      => 'ASC',
			'show_empty'    => 0,
			'showcount'     => 0,
			'expand'        => 'none',
			'parent_expand' => 0,
			'exclude'       => null
		];

	public function __construct() {
		parent::__construct( 'jcl_widget', 'JS Categories List Widget (Legacy)', [
			'classname'   => 'widget_category widget_jcl_widget',
			'description' => __( 'A widget for displaying a categories list with some effects.', 'jcl_i18n' )
		] );
	}

	/**
	 * Adds all the plugin's hooks
	 */
	public static function init() {
		$self = self::instance();

		add_shortcode( 'jQueryCategoriesList', [ $self, 'filter' ] );
		add_shortcode( 'JsCategoriesList', [ $self, 'filter' ] );
		add_filter( 'widget_text', 'do_shortcode' );

		if ( function_exists( 'load_plugin_textdomain' ) ) {
			load_plugin_textdomain( 'jalw_i18n', null, basename( dirname( __FILE__ ) ) . '/languages' );
			load_default_textdomain();
		}

		add_action( 'wp_enqueue_scripts', [ $self, 'enqueue_scripts' ], 10, 1 );
	}

	/**
	 * Access to a class instance
	 *
	 * @return JCL_Legacy_Widget
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Prints widgets code.
	 *
	 * @param array $args Widget arguments.
	 * @param array $instance Current widget instance, there may be multiple widgets at the same time.
	 */
	public function widget( $args, $instance ) {
		$this->config = $instance;

		$defaults = [
			'before_widget' => '',
			'before_title'  => '',
			'after_title'   => '',
			'after_widget'  => '',
		];

		$settings = array_merge( $defaults, $args );

		echo $settings['before_widget'];
		echo $settings['before_title'];
		echo apply_filters( 'widget_title', $this->config['title'] );
		echo $settings['after_title'];
		echo $this->build_html();
		echo $settings['after_widget'];
	}

	/**
	 * Builds categories list's HTML code
	 */
	protected function build_html() {
		wp_enqueue_script( self::ASSETS_ID );
		wp_enqueue_style( self::ASSETS_ID );

		$all_categories = $this->get_categories();
		$layoutClass    = $this->config['layout'] === 'right' ? 'right' : 'left';

		$html = sprintf( '<div class="js-categories-list %s">', $layoutClass );
		$html .= sprintf( '<ul class="jcl_widget legacy preload" %s>', $this->print_data_attrs() );

		if ( empty( $all_categories ) ) {
			$html .= '<li>' . __( 'There are no categories to display', 'jcl_i18n' ) . '</li>';
		} else {
			$html_builder = new JCL_Legacy_HTML_Builder( $this->config, $all_categories );
			$html         .= $html_builder->get_code();
		}

		$html .= '</ul></div>';

		return $html;
	}

	protected function print_data_attrs() {
		$required_vals = [ 'effect', 'ex_sym', 'con_sym' ];
		$data_attrs    = '';

		foreach ( $required_vals as $val ) {
			$data_attrs .= sprintf( 'data-%s="%s"', esc_attr( $val ), esc_attr( $this->config[ $val ] ) );
		}

		if ( $this->config['parent_expand'] ) {
			$data_attrs .= sprintf( 'data-parent_expand="%s"', esc_attr( $this->config['parent_expand'] ) );
		}

		return $data_attrs;
	}


	/**
	 * Returns all categories
	 * @return array of categories.
	 */
	protected function get_categories() {
		return get_categories(
			array(
				'type'         => 'post',
				'child_of'     => 0,
				'orderby'      => $this->config['orderby'],
				'order'        => $this->config['orderdir'],
				'hide_empty'   => ! $this->config['show_empty'],
				'hierarchical' => 1,
				'taxonomy'     => 'category',
				'pad_counts'   => true,
			)
		);
	}

	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		if ( empty( $new_instance['title'] ) ) {
			$instance['title'] = __( 'Categories', 'jcl_i18n' );
		} else {
			$instance['title'] = stripslashes( strip_tags( $new_instance['title'] ) );
		}

		$instance['symbol']        = $new_instance['symbol'];
		$instance['effect']        = stripslashes( $new_instance['effect'] );
		$instance['layout']        = stripslashes( $new_instance['layout'] );
		$instance['orderby']       = stripslashes( $new_instance['orderby'] );
		$instance['orderdir']      = stripslashes( $new_instance['orderdir'] );
		$instance['show_empty']    = empty( $new_instance['show_empty'] ) ? 0 : 1;
		$instance['showcount']     = empty( $new_instance['showcount'] ) ? 0 : 1;
		$instance['parent_expand'] = empty( $new_instance['parent_expand'] ) ? 0 : 1;
		$instance['expand']        = $new_instance['expand'];
		$instance['exclude']       = empty( $new_instance['exclude'] ) ? null : serialize( $new_instance['exclude'] );

		switch ( $new_instance['symbol'] ) {
			case '0':
				$instance['ex_sym']  = ' ';
				$instance['con_sym'] = ' ';
				break;
			case '1':
				$instance['ex_sym']  = '►';
				$instance['con_sym'] = '▼';
				break;
			case '2':
				$instance['ex_sym']  = '(+)';
				$instance['con_sym'] = '(-)';
				break;
			case '3':
				$instance['ex_sym']  = '[+]';
				$instance['con_sym'] = '[-]';
				break;
			default:
				$instance['ex_sym']  = '>';
				$instance['con_sym'] = 'v';
				break;
		}

		switch ( $new_instance['effect'] ) {
			case 'slide':
				$instance['fx_in']  = 'slideDown';
				$instance['fx_out'] = 'slideUp';
				break;
			case 'fade':
				$instance['fx_in']  = 'fadeIn';
				$instance['fx_out'] = 'fadeOut';
				break;
			default:
				$instance['fx_in']  = 'none';
				$instance['fx_out'] = 'none';
		}

		return $instance;
	}

	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, $this->defaults );
		?>
        <dl>
            <dt><strong><?php _e( 'Title', 'jcl_i18n' ) ?></strong></dt>
            <dd>
                <input name="<?php echo $this->get_field_name( 'title' ) ?>" type="text"
                       value="<?php echo $instance['title']; ?>"/>
            </dd>
            <dt><strong><?php _e( 'Trigger Symbol', 'jcl_i18n' ) ?></strong></dt>
            <dd>
                <select id="<?php echo $this->get_field_id( 'symbol' ) ?>"
                        name="<?php echo $this->get_field_name( 'symbol' ) ?>">
                    <option value="0" <?php if ( $instance['symbol'] == '0' )
						echo 'selected="selected"' ?> >
						<?php _e( 'Empty Space', 'jcl_i18n' ) ?>
                    </option>
                    <option value="1" <?php if ( $instance['symbol'] == '1' )
						echo 'selected="selected"' ?> >
                        ► ▼
                    </option>
                    <option value="2" <?php if ( $instance['symbol'] == '2' )
						echo 'selected="selected"' ?> >
                        (+) (-)
                    </option>
                    <option value="3" <?php if ( $instance['symbol'] == '3' )
						echo 'selected="selected"' ?> >
                        [+] [-]
                    </option>
                </select>
            </dd>
            <dt><strong><?php _e( 'Symbol position', 'jcl_i18n' ) ?></strong></dt>
            <dd>
                <select id="<?php echo $this->get_field_id( 'layout' ) ?>"
                        name="<?php echo $this->get_field_name( 'layout' ) ?>">
                    <option value="right" <?php if ( $instance['layout'] == 'right' )
						echo 'selected="selected"' ?> >
						<?php _e( 'Right', 'jcl_i18n' ) ?>
                    </option>
                    <option value="left" <?php if ( $instance['layout'] != 'right' )
						echo 'selected="selected"' ?> >
						<?php _e( 'Left', 'jcl_i18n' ) ?>
                    </option>
                </select>
            </dd>
            <dt><strong><?php _e( 'Effect', 'jcl_i18n' ) ?></strong></dt>
            <dd>
                <select id="<?php echo $this->get_field_id( 'effect' ) ?>"
                        name="<?php echo $this->get_field_name( 'effect' ) ?>">
                    <option value="none" <?php if ( $instance['effect'] == '' )
						echo 'selected="selected"' ?>>
						<?php _e( 'None', 'jcl_i18n' ) ?>
                    </option>
                    <option value="slide" <?php if ( $instance['effect'] == 'slide' )
						echo 'selected="selected"' ?> >
						<?php _e( 'Slide (Accordion)', 'jcl_i18n' ) ?>
                    </option>
                    <option value="fade" <?php if ( $instance['effect'] == 'fade' )
						echo 'selected="selected"' ?> >
						<?php _e( 'Fade', 'jcl_i18n' ) ?>
                    </option>
                </select>
            </dd>
            <dt><strong><?php _e( 'Order By', 'jcl_i18n' ) ?></strong></dt>
            <dd>
                <select id="<?php echo $this->get_field_id( 'orderby' ) ?>"
                        name="<?php echo $this->get_field_name( 'orderby' ) ?>">
                    <option value="name" <?php if ( $instance['orderby'] == 'name' )
						echo 'selected="selected"' ?> ><?php _e( 'Name', 'jcl_i18n' ) ?></option>
                    <option value="id" <?php if ( $instance['orderby'] == 'id' )
						echo 'selected="selected"' ?> ><?php _e( 'Category ID', 'jcl_i18n' ) ?></option>
                    <option value="count" <?php if ( $instance['orderby'] == 'count' )
						echo 'selected="selected"' ?> ><?php _e( 'Entries count', 'jcl_i18n' ) ?></option>
                    <option value="slug" <?php if ( $instance['orderby'] == 'slug' )
						echo 'selected="selected"' ?> ><?php _e( 'Slug', 'jcl_i18n' ) ?></option>
                </select>
                <select id="<?php echo $this->get_field_id( 'orderdir' ) ?>"
                        name="<?php echo $this->get_field_name( 'orderdir' ) ?>">
                    <option value="ASC" <?php if ( $instance['orderdir'] == 'ASC' )
						echo 'selected="selected"' ?> ><?php _e( 'ASC', 'jcl_i18n' ) ?></option>
                    <option value="DESC" <?php if ( $instance['orderdir'] == 'DESC' )
						echo 'selected="selected"' ?> ><?php _e( 'DESC', 'jcl_i18n' ) ?></option>
                </select>
            </dd>
            <dt><strong><?php _e( 'Expand', 'jcl_i18n' ) ?></strong></dt>
            <dd>
                <select id="<?php echo $this->get_field_id( 'expand' ) ?>"
                        name="<?php echo $this->get_field_name( 'expand' ) ?>">
                    <option value="none" <?php if ( $instance['expand'] == '' )
						echo 'selected="selected"' ?>>
						<?php _e( 'None', 'jcl_i18n' ) ?>
                    </option>
                    <option value="sel_cat" <?php if ( $instance['expand'] == 'sel_cat' )
						echo 'selected="selected"' ?>>
						<?php _e( 'Selected category', 'jcl_i18n' ) ?>
                    </option>
                    <option value="all" <?php if ( $instance['expand'] == 'all' )
						echo 'selected="selected"' ?> >
						<?php _e( 'All', 'jcl_i18n' ) ?>
                    </option>
                </select>
            </dd>
            <dt><strong><?php _e( 'Extra options', 'jcl_i18n' ) ?></strong></dt>
            <dd>
                <input id="<?php echo $this->get_field_id( 'showcount' ) ?>" value="1"
                       name="<?php echo $this->get_field_name( 'showcount' ) ?>"
                       type="checkbox" <?php if ( $instance['showcount'] )
					echo 'checked="checked"' ?> />
				<?php _e( 'Show number of posts', 'jcl_i18n' ) ?>
            </dd>
            <dd>
                <input id="<?php echo $this->get_field_id( 'show_empty' ) ?>" value="1"
                       name="<?php echo $this->get_field_name( 'show_empty' ) ?>"
                       type="checkbox" <?php if ( ! empty( $instance['show_empty'] ) )
					echo 'checked="checked"' ?> />
				<?php _e( 'Show empty categories', 'jcl_i18n' ) ?>
            </dd>
            <dd>
                <input id="<?php echo $this->get_field_id( 'parent_expand' ) ?>" value="1"
                       name="<?php echo $this->get_field_name( 'parent_expand' ) ?>"
                       type="checkbox" <?php if ( ! empty( $instance['parent_expand'] ) )
					echo 'checked="checked"' ?> />
				<?php _e( 'Parent expand sub-categories', 'jcl_i18n' ) ?>
            </dd>
            <dt><strong><?php _e( 'Categories to exclude:', 'jcl_i18n' ) ?></strong></dt>
            <dd>
                <select id="<?php echo $this->get_field_id( 'exclude' ) ?>"
                        name="<?php echo $this->get_field_name( 'exclude' ) ?>[]" style="height:75px;"
                        multiple="multiple">
					<?php
					$cats                = get_categories(
						array(
							'type'         => 'post',
							'child_of'     => 0,
							'orderby'      => 'name',
							'order'        => 'asc',
							'hide_empty'   => 0,
							'hierarchical' => 1,
							'taxonomy'     => 'category',
							'pad_counts'   => false
						)
					);
					$instance['exclude'] = empty( $instance['exclude'] ) ? array() : unserialize( $instance['exclude'] );
					if ( is_string( $instance['exclude'] ) ) {
						$instance['exclude'] = unserialize( $instance['exclude'] );
					}

					foreach ( $cats as $cat ) {
						$checked = ( in_array( $cat->term_id, $instance['exclude'] ) ) ? 'selected="selected"' : '';
						echo "<option value=\"{$cat->term_id}\" {$checked}>{$cat->cat_name}</option>";
					}
					?>
                </select>
            </dd>
        </dl>
		<?php
	}

	/**
	 * Function to filter any [JS Categories List] text inside post to display archive list
	 */
	public function filter( $attr ) {
		$this->enqueue_scripts();
		$instance = shortcode_atts( $this->defaults, $attr );
    
    if (!empty($instance['exclude']))
      $instance['exclude'] = serialize( explode( ',', $instance['exclude'] ) );
    
		$this->config = $instance;

		return $this->build_html();
	}

	/**
	 * Function to enqueue custom JS file to create animations
	 */
	public function enqueue_scripts() {
		wp_register_script(
			self::ASSETS_ID,
			JCL_BASE_URL . 'assets/js/' . self::JS_FILENAME,
			null,
			JCL_VERSION,
			true
		);

		wp_register_style(
			self::ASSETS_ID,
			JCL_BASE_URL . 'assets/css/' . self::CSS_FILENAME,
			null,
			JCL_VERSION
		);
	}
}

function jcl_register_widget() {
	register_widget( 'JCL_Legacy_Widget' );
}

add_action( 'widgets_init', 'jcl_register_widget' );
add_action( 'plugins_loaded', [ 'JCL_Legacy_Widget', 'init' ] );
