<?php
/**
 * Legacy widget functionality.
 *
 * @package JQueryCategoriesList
 */

/**
 * Legacy PHP widget implementation.
 */
class JCL_Legacy_Widget extends WP_Widget {
	/**
	 * Registered asset handle.
	 */
	const ASSETS_ID = 'js-categories-list';

	/**
	 * Legacy JavaScript filename.
	 */
	const JS_FILENAME = 'jcl.js';

	/**
	 * Legacy stylesheet filename.
	 */
	const CSS_FILENAME = 'jcl.css';

	/**
	 * Singleton instance.
	 *
	 * @var JCL_Legacy_Widget|null
	 */
	public static $instance;

	/**
	 * Current widget configuration.
	 *
	 * @var array<string, mixed>
	 */
	public $config = [];

	/**
	 * Default widget settings.
	 *
	 * @var array<string, mixed>
	 */
	public $defaults = [
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
		'exclude'       => [],
	];

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct(
			'jcl_widget',
			__( 'JS Categories List Widget (Legacy)', JCL_TEXT_DOMAIN ),
			[
				'classname'   => 'widget_category widget_jcl_widget',
				'description' => __( 'A widget for displaying a categories list with some effects.', JCL_TEXT_DOMAIN ),
			]
		);
	}

	/**
	 * Adds the legacy widget hooks.
	 *
	 * @return void
	 */
	public static function init() {
		$self = self::instance();

		add_shortcode( 'jQueryCategoriesList', [ $self, 'filter' ] );
		add_shortcode( 'JsCategoriesList', [ $self, 'filter' ] );
		add_filter( 'widget_text', 'do_shortcode' );
		add_action( 'wp_enqueue_scripts', [ $self, 'enqueue_scripts' ] );
	}

	/**
	 * Returns the singleton instance.
	 *
	 * @return JCL_Legacy_Widget
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Outputs the widget markup.
	 *
	 * @param array<string, string> $args Widget wrapper arguments.
	 * @param array<string, mixed>  $instance Saved widget settings.
	 * @return void
	 */
	public function widget( $args, $instance ) {
		$this->config = wp_parse_args( $instance, $this->defaults );
		$settings     = wp_parse_args(
			$args,
			[
				'before_widget' => '',
				'before_title'  => '',
				'after_title'   => '',
				'after_widget'  => '',
			]
		);

		echo wp_kses_post( $settings['before_widget'] );
		echo wp_kses_post( $settings['before_title'] );
		echo esc_html( apply_filters( 'widget_title', apply_filters( 'jcl_widget_title', $this->config['title'] ) ) );
		echo wp_kses_post( $settings['after_title'] );
		echo $this->build_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML is escaped during generation.
		echo wp_kses_post( $settings['after_widget'] );
	}

	/**
	 * Builds the legacy widget HTML.
	 *
	 * @return string
	 */
	protected function build_html() {
		wp_enqueue_script( self::ASSETS_ID );
		wp_enqueue_style( self::ASSETS_ID );

		$all_categories = $this->get_categories();
		$layout_class   = 'right' === $this->config['layout'] ? 'layout-right' : 'layout-left';
		$html           = sprintf( '<div class="js-categories-list %s">', esc_attr( $layout_class ) );
		$html          .= sprintf( '<ul class="jcl_widget legacy preload" %s>', $this->print_data_attrs() );

		if ( empty( $all_categories ) ) {
			$html .= '<li>' . esc_html__( 'There are no categories to display', JCL_TEXT_DOMAIN ) . '</li>';
		} else {
			$html_builder = new JCL_Legacy_HTML_Builder( $this->config, $all_categories );
			$html        .= $html_builder->get_code();
		}

		$html .= '</ul></div>';

		return $html;
	}

	/**
	 * Returns the legacy data attributes.
	 *
	 * @return string
	 */
	protected function print_data_attrs() {
		$required_values = [ 'effect', 'ex_sym', 'con_sym' ];
		$data_attrs      = [];

		foreach ( $required_values as $value_name ) {
			$data_attrs[] = sprintf( 'data-%1$s="%2$s"', esc_attr( $value_name ), esc_attr( $this->config[ $value_name ] ) );
		}

		if ( ! empty( $this->config['parent_expand'] ) ) {
			$data_attrs[] = sprintf( 'data-parent_expand="%s"', esc_attr( $this->config['parent_expand'] ) );
		}

		return implode( ' ', $data_attrs );
	}

	/**
	 * Returns all categories for the legacy widget.
	 *
	 * @return array<int, WP_Term>
	 */
	protected function get_categories() {
		return get_categories(
			[
				'type'         => 'post',
				'child_of'     => 0,
				'orderby'      => $this->config['orderby'],
				'order'        => $this->config['orderdir'],
				'hide_empty'   => ! $this->config['show_empty'],
				'hierarchical' => 1,
				'taxonomy'     => 'category',
				'pad_counts'   => true,
			]
		);
	}

	/**
	 * Sanitizes and saves widget settings.
	 *
	 * @param array<string, mixed> $new_instance Submitted settings.
	 * @param array<string, mixed> $old_instance Previous settings.
	 * @return array<string, mixed>
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title']         = empty( $new_instance['title'] ) ? __( 'Categories', JCL_TEXT_DOMAIN ) : sanitize_text_field( wp_unslash( $new_instance['title'] ) );
		$instance['symbol']        = isset( $new_instance['symbol'] ) ? (string) $new_instance['symbol'] : '1';
		$instance['effect']        = isset( $new_instance['effect'] ) ? sanitize_key( $new_instance['effect'] ) : 'slide';
		$instance['layout']        = isset( $new_instance['layout'] ) ? sanitize_key( $new_instance['layout'] ) : 'right';
		$instance['orderby']       = isset( $new_instance['orderby'] ) ? sanitize_key( $new_instance['orderby'] ) : 'name';
		$instance['orderdir']      = isset( $new_instance['orderdir'] ) ? strtoupper( sanitize_key( $new_instance['orderdir'] ) ) : 'ASC';
		$instance['show_empty']    = empty( $new_instance['show_empty'] ) ? 0 : 1;
		$instance['showcount']     = empty( $new_instance['showcount'] ) ? 0 : 1;
		$instance['parent_expand'] = empty( $new_instance['parent_expand'] ) ? 0 : 1;
		$instance['expand']        = isset( $new_instance['expand'] ) ? sanitize_key( $new_instance['expand'] ) : 'none';
		$instance['exclude']       = $this->sanitize_excluded_categories( $new_instance );

		if ( ! in_array( $instance['effect'], [ 'none', 'slide', 'fade' ], true ) ) {
			$instance['effect'] = 'slide';
		}

		if ( ! in_array( $instance['layout'], [ 'left', 'right' ], true ) ) {
			$instance['layout'] = 'right';
		}

		if ( ! in_array( $instance['orderby'], [ 'name', 'id', 'count', 'slug' ], true ) ) {
			$instance['orderby'] = 'name';
		}

		if ( ! in_array( $instance['orderdir'], [ 'ASC', 'DESC' ], true ) ) {
			$instance['orderdir'] = 'ASC';
		}

		if ( ! in_array( $instance['expand'], [ 'none', 'sel_cat', 'all' ], true ) ) {
			$instance['expand'] = 'none';
		}

		$instance = array_merge( $instance, $this->get_symbol_config( $instance['symbol'] ), $this->get_effect_config( $instance['effect'] ) );

		return $instance;
	}

	/**
	 * Renders the widget admin form.
	 *
	 * @param array<string, mixed> $instance Current instance values.
	 * @return void
	 */
	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, $this->defaults );
		$instance['exclude'] = $this->normalize_excluded_categories( $instance['exclude'] );
		?>
		<dl>
			<dt><strong><?php esc_html_e( 'Title', JCL_TEXT_DOMAIN ); ?></strong></dt>
			<dd>
				<input id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
			</dd>
			<dt><strong><?php esc_html_e( 'Trigger Symbol', JCL_TEXT_DOMAIN ); ?></strong></dt>
			<dd>
				<select id="<?php echo esc_attr( $this->get_field_id( 'symbol' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'symbol' ) ); ?>">
					<option value="0" <?php selected( $instance['symbol'], '0' ); ?>><?php esc_html_e( 'Empty Space', JCL_TEXT_DOMAIN ); ?></option>
					<option value="1" <?php selected( $instance['symbol'], '1' ); ?>>► ▼</option>
					<option value="2" <?php selected( $instance['symbol'], '2' ); ?>>(+) (-)</option>
					<option value="3" <?php selected( $instance['symbol'], '3' ); ?>>[+] [-]</option>
				</select>
			</dd>
			<dt><strong><?php esc_html_e( 'Symbol position', JCL_TEXT_DOMAIN ); ?></strong></dt>
			<dd>
				<select id="<?php echo esc_attr( $this->get_field_id( 'layout' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'layout' ) ); ?>">
					<option value="right" <?php selected( $instance['layout'], 'right' ); ?>><?php esc_html_e( 'Right', JCL_TEXT_DOMAIN ); ?></option>
					<option value="left" <?php selected( $instance['layout'], 'left' ); ?>><?php esc_html_e( 'Left', JCL_TEXT_DOMAIN ); ?></option>
				</select>
			</dd>
			<dt><strong><?php esc_html_e( 'Effect', JCL_TEXT_DOMAIN ); ?></strong></dt>
			<dd>
				<select id="<?php echo esc_attr( $this->get_field_id( 'effect' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'effect' ) ); ?>">
					<option value="none" <?php selected( $instance['effect'], 'none' ); ?>><?php esc_html_e( 'None', JCL_TEXT_DOMAIN ); ?></option>
					<option value="slide" <?php selected( $instance['effect'], 'slide' ); ?>><?php esc_html_e( 'Slide (Accordion)', JCL_TEXT_DOMAIN ); ?></option>
					<option value="fade" <?php selected( $instance['effect'], 'fade' ); ?>><?php esc_html_e( 'Fade', JCL_TEXT_DOMAIN ); ?></option>
				</select>
			</dd>
			<dt><strong><?php esc_html_e( 'Order By', JCL_TEXT_DOMAIN ); ?></strong></dt>
			<dd>
				<select id="<?php echo esc_attr( $this->get_field_id( 'orderby' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'orderby' ) ); ?>">
					<option value="name" <?php selected( $instance['orderby'], 'name' ); ?>><?php esc_html_e( 'Name', JCL_TEXT_DOMAIN ); ?></option>
					<option value="id" <?php selected( $instance['orderby'], 'id' ); ?>><?php esc_html_e( 'Category ID', JCL_TEXT_DOMAIN ); ?></option>
					<option value="count" <?php selected( $instance['orderby'], 'count' ); ?>><?php esc_html_e( 'Entries count', JCL_TEXT_DOMAIN ); ?></option>
					<option value="slug" <?php selected( $instance['orderby'], 'slug' ); ?>><?php esc_html_e( 'Slug', JCL_TEXT_DOMAIN ); ?></option>
				</select>
				<select id="<?php echo esc_attr( $this->get_field_id( 'orderdir' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'orderdir' ) ); ?>">
					<option value="ASC" <?php selected( $instance['orderdir'], 'ASC' ); ?>><?php esc_html_e( 'ASC', JCL_TEXT_DOMAIN ); ?></option>
					<option value="DESC" <?php selected( $instance['orderdir'], 'DESC' ); ?>><?php esc_html_e( 'DESC', JCL_TEXT_DOMAIN ); ?></option>
				</select>
			</dd>
			<dt><strong><?php esc_html_e( 'Expand', JCL_TEXT_DOMAIN ); ?></strong></dt>
			<dd>
				<select id="<?php echo esc_attr( $this->get_field_id( 'expand' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'expand' ) ); ?>">
					<option value="none" <?php selected( $instance['expand'], 'none' ); ?>><?php esc_html_e( 'None', JCL_TEXT_DOMAIN ); ?></option>
					<option value="sel_cat" <?php selected( $instance['expand'], 'sel_cat' ); ?>><?php esc_html_e( 'Selected category', JCL_TEXT_DOMAIN ); ?></option>
					<option value="all" <?php selected( $instance['expand'], 'all' ); ?>><?php esc_html_e( 'All', JCL_TEXT_DOMAIN ); ?></option>
				</select>
			</dd>
			<dt><strong><?php esc_html_e( 'Extra options', JCL_TEXT_DOMAIN ); ?></strong></dt>
			<dd>
				<label>
					<input id="<?php echo esc_attr( $this->get_field_id( 'showcount' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'showcount' ) ); ?>" type="checkbox" value="1" <?php checked( $instance['showcount'], 1 ); ?> />
					<?php esc_html_e( 'Show number of posts', JCL_TEXT_DOMAIN ); ?>
				</label>
			</dd>
			<dd>
				<label>
					<input id="<?php echo esc_attr( $this->get_field_id( 'show_empty' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_empty' ) ); ?>" type="checkbox" value="1" <?php checked( ! empty( $instance['show_empty'] ) ); ?> />
					<?php esc_html_e( 'Show empty categories', JCL_TEXT_DOMAIN ); ?>
				</label>
			</dd>
			<dd>
				<label>
					<input id="<?php echo esc_attr( $this->get_field_id( 'parent_expand' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'parent_expand' ) ); ?>" type="checkbox" value="1" <?php checked( ! empty( $instance['parent_expand'] ) ); ?> />
					<?php esc_html_e( 'Parent expand sub-categories', JCL_TEXT_DOMAIN ); ?>
				</label>
			</dd>
			<dt><strong><?php esc_html_e( 'Categories to exclude:', JCL_TEXT_DOMAIN ); ?></strong></dt>
			<dd>
				<select id="<?php echo esc_attr( $this->get_field_id( 'exclude_select' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'exclude_select' ) ); ?>[]" style="height:75px;" multiple="multiple">
					<?php foreach ( $this->get_category_options() as $category ) : ?>
						<option value="<?php echo esc_attr( $category->term_id ); ?>" <?php selected( in_array( (int) $category->term_id, $instance['exclude'], true ) ); ?>><?php echo esc_html( $category->cat_name ); ?></option>
					<?php endforeach; ?>
				</select>
			</dd>
		</dl>
		<?php
	}

	/**
	 * Renders the legacy shortcode.
	 *
	 * @param array<string, mixed> $attr Shortcode attributes.
	 * @return string
	 */
	public function filter( $attr ) {
		$this->enqueue_scripts();
		$this->config = shortcode_atts( $this->defaults, $attr );
		$this->config['exclude'] = $this->normalize_shortcode_category_ids( $this->config['exclude'] );

		return $this->build_html();
	}

	/**
	 * Registers legacy frontend assets.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		wp_register_script( self::ASSETS_ID, JCL_BASE_URL . 'assets/js/' . self::JS_FILENAME, [], JCL_VERSION, true );
		wp_register_style( self::ASSETS_ID, JCL_BASE_URL . 'assets/css/' . self::CSS_FILENAME, [], JCL_VERSION );
	}

	/**
	 * Returns the symbol configuration for the selected option.
	 *
	 * @param string $symbol Symbol option.
	 * @return array<string, string>
	 */
	private function get_symbol_config( $symbol ) {
		switch ( $symbol ) {
			case '0':
				return [ 'ex_sym' => ' ', 'con_sym' => ' ' ];
			case '1':
				return [ 'ex_sym' => '►', 'con_sym' => '▼' ];
			case '2':
				return [ 'ex_sym' => '(+)', 'con_sym' => '(-)' ];
			case '3':
				return [ 'ex_sym' => '[+]', 'con_sym' => '[-]' ];
			default:
				return [ 'ex_sym' => '>', 'con_sym' => 'v' ];
		}
	}

	/**
	 * Returns the animation configuration for the selected effect.
	 *
	 * @param string $effect Effect option.
	 * @return array<string, string>
	 */
	private function get_effect_config( $effect ) {
		switch ( $effect ) {
			case 'slide':
				return [ 'fx_in' => 'slideDown', 'fx_out' => 'slideUp' ];
			case 'fade':
				return [ 'fx_in' => 'fadeIn', 'fx_out' => 'fadeOut' ];
			default:
				return [ 'fx_in' => 'none', 'fx_out' => 'none' ];
		}
	}

	/**
	 * Sanitizes the excluded category IDs.
	 *
	 * @param array<string, mixed> $instance Submitted instance.
	 * @return array<int, int>
	 */
	private function sanitize_excluded_categories( $instance ) {
		if ( ! empty( $instance['exclude_select'] ) ) {
			return wp_parse_id_list( $instance['exclude_select'] );
		}

		if ( ! empty( $instance['exclude'] ) ) {
			return wp_parse_id_list( $instance['exclude'] );
		}

		return [];
	}


	/**
	 * Normalizes shortcode category IDs without attempting deserialization.
	 *
	 * Shortcode attributes are user-controlled input, so they should only accept
	 * plain comma-separated IDs and never serialized payloads.
	 *
	 * @param mixed $category_ids Shortcode category IDs.
	 * @return array<int, int>
	 */
	private function normalize_shortcode_category_ids( $category_ids ) {
		return wp_parse_id_list( $category_ids );
	}

	/**
	 * Normalizes the excluded category IDs.
	 *
	 * @param mixed $exclude Excluded categories value.
	 * @return array<int, int>
	 */
	private function normalize_excluded_categories( $exclude ) {
		if ( is_string( $exclude ) && is_serialized( $exclude ) ) {
			$exclude = unserialize( $exclude, [ 'allowed_classes' => false ] );
		}

		return wp_parse_id_list( $exclude );
	}

	/**
	 * Returns categories used in the widget form selector.
	 *
	 * @return array<int, WP_Term>
	 */
	private function get_category_options() {
		return get_categories(
			[
				'type'         => 'post',
				'child_of'     => 0,
				'orderby'      => 'name',
				'order'        => 'ASC',
				'hide_empty'   => 0,
				'hierarchical' => 1,
				'taxonomy'     => 'category',
				'pad_counts'   => false,
			]
		);
	}
}

/**
 * Registers the legacy widget.
 *
 * @return void
 */
function jcl_register_widget() {
	register_widget( 'JCL_Legacy_Widget' );
}

add_action( 'widgets_init', 'jcl_register_widget' );
add_action( 'init', [ 'JCL_Legacy_Widget', 'init' ] );
