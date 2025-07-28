<?php
/**
 * Init Configuration
 *
 * @author Jegstudio
 * @package vorfolio
 * @since 1.0.0
 */

namespace Vorfolio;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Vorfolio\Block_Patterns;
use Vorfolio\Block_Styles;

/**
 * Init Class
 *
 * @package vorfolio
 */
class Init {

	/**
	 * Instance variable
	 *
	 * @var $instance
	 */
	private static $instance;

	/**
	 * Class instance.
	 *
	 * @return Init
	 */
	public static function instance() {
		if ( null === static::$instance ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	/**
	 * Class constructor.
	 */
	private function __construct() {
		$this->load_hooks();
	}

	/**
	 * Load initial hooks.
	 */
	private function load_hooks() {
		// actions.
		add_action( 'init', array( $this, 'add_theme_templates' ) );
		add_action( 'after_setup_theme', array( $this, 'theme_setup' ) );
		add_action( 'after_theme_setup', array( $this, 'content_width' ), 0 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_notices', array( $this, 'notice_install_plugin' ) );
		add_action( 'wp_ajax_vorfolio_set_admin_notice_viewed', array( $this, 'notice_closed' ) );
		add_action( 'admin_init', array( $this, 'load_editor_styles' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'init', array( $this, 'register_block_patterns' ), 9 );
		add_action( 'init', array( $this, 'register_block_styles' ), 9 );

		add_action( 'admin_enqueue_scripts', array( $this, 'backend_scripts' ) );

		// filters.
		add_filter( 'excerpt_length', array( $this, 'excerpt_length' ) );
		add_filter( 'excerpt_more', array( $this, 'excerpt_elipsis' ) );
		add_filter( 'gutenverse_template_path', array( $this, 'template_path' ), null, 3 );
		add_filter( 'gutenverse_themes_template', array( $this, 'add_template' ), 10, 2 );

		// Default Font.
		add_filter( 'gutenverse_block_config', array( $this, 'default_font' ), 10 );
		add_filter( 'gutenverse_font_header', array( $this, 'default_header_font' ) );
		add_filter( 'gutenverse_global_css', array( $this, 'global_header_style' ) );

		add_filter( 'gutenverse_stylesheet_directory', array( $this, 'change_stylesheet_directory' ) );
		add_filter( 'gutenverse_themes_override_mechanism', '__return_true' );
	}

	/**
	 * Change Stylesheet Directory.
	 *
	 * @return string
	 */
	public function change_stylesheet_directory() {
		return VORFOLIO_DIR . 'gutenverse-files';
	}

	/**
	 * Generate Global Font
	 *
	 * @param string $value  Value of the option.
	 *
	 * @return string
	 */
	public function global_header_style( $value ) {
		$theme_name      = get_stylesheet();
		$global_variable = get_option( 'gutenverse-global-variable-font-' . $theme_name );

		if ( empty( $global_variable ) && function_exists( 'gutenverse_global_font_style_generator' ) ) {
			$font_variable = $this->default_font_variable();
			$value        .= \gutenverse_global_font_style_generator( $font_variable );
		}

		return $value;
	}

	/**
	 * Header Font.
	 *
	 * @param mixed $value  Value of the option.
	 *
	 * @return mixed Value of the option.
	 */
	public function default_header_font( $value ) {
		if ( ! $value ) {
			$value = array(
				array(
					'value'  => 'Alfa Slab One',
					'type'   => 'google',
					'weight' => 'bold',
				),
			);
		}

		return $value;
	}

	/**
	 * Alter Default Font.
	 *
	 * @param array $config Array of Config.
	 *
	 * @return array
	 */
	public function default_font( $config ) {
		if ( empty( $config['globalVariable']['fonts'] ) ) {
			$config['globalVariable']['fonts'] = $this->default_font_variable();

			return $config;
		}

		if ( ! empty( $config['globalVariable']['fonts'] ) ) {
			// Handle existing fonts.
			$theme_name   = get_stylesheet();
			$initial_font = get_option( 'gutenverse-font-init-' . $theme_name );

			if ( ! $initial_font ) {
				$config['globalVariable']['fonts'] = array_merge( $config['globalVariable']['fonts'], $this->default_font_variable() );
				update_option( 'gutenverse-font-init-' . $theme_name, true );
			}
		}

		return $config;
	}

	/**
	 * Default Font Variable.
	 *
	 * @return array
	 */
	public function default_font_variable() {
		return array(
			array(
				'id'   => 'h1-font',
				'name' => 'H1 Font',
				'font' => array(
					'font'       => array(
						'label' => 'Roboto',
						'value' => 'Roboto',
						'type'  => 'google',
					),
					'size'       => array(
						'Desktop' => array(
							'unit'  => 'px',
							'point' => '60',
						),
						'Mobile'  => array(
							'unit'  => 'px',
							'point' => '32',
						),
					),
					'lineHeight' => array(
						'Desktop' => array(
							'unit'  => 'em',
							'point' => '1.2',
						),
					),
					'weight'     => '700',
				),
			),
			array(
				'id'   => 'h2-font',
				'name' => 'H2 Font',
				'font' => array(
					'font'       => array(
						'label' => 'Roboto',
						'value' => 'Roboto',
						'type'  => 'google',
					),
					'size'       => array(
						'Desktop' => array(
							'unit'  => 'px',
							'point' => '48',
						),
						'Mobile'  => array(
							'unit'  => 'px',
							'point' => '30',
						),
					),
					'lineHeight' => array(
						'Desktop' => array(
							'unit'  => 'em',
							'point' => '1.2',
						),
					),
					'weight'     => '700',
				),
			),
			array(
				'id'   => 'h3-font',
				'name' => 'H3 Font',
				'font' => array(
					'font'       => array(
						'label' => 'Roboto',
						'value' => 'Roboto',
						'type'  => 'google',
					),
					'size'       => array(
						'Desktop' => array(
							'unit'  => 'px',
							'point' => '24',
						),
						'Mobile'  => array(
							'unit'  => 'px',
							'point' => '18',
						),
					),
					'lineHeight' => array(
						'Desktop' => array(
							'unit'  => 'em',
							'point' => '1.2',
						),
					),
					'weight'     => '500',
				),
			),
			array(
				'id'   => 'h3-bold-font',
				'name' => 'H3 Blod Font',
				'font' => array(
					'font'       => array(
						'label' => 'Roboto',
						'value' => 'Roboto',
						'type'  => 'google',
					),
					'size'       => array(
						'Desktop' => array(
							'unit'  => 'px',
							'point' => '24',
						),
						'Mobile'  => array(
							'unit'  => 'px',
							'point' => '22',
						),
					),
					'lineHeight' => array(
						'Desktop' => array(
							'unit'  => 'em',
							'point' => '1.2',
						),
					),
					'weight'     => '700',
				),
			),
			array(
				'id'   => 'h4-font',
				'name' => 'H4 Font',
				'font' => array(
					'font'       => array(
						'label' => 'Roboto',
						'value' => 'Roboto',
						'type'  => 'google',
					),
					'size'       => array(
						'Desktop' => array(
							'unit'  => 'px',
							'point' => '22',
						),
						'Mobile'  => array(
							'unit'  => 'px',
							'point' => '20',
						),
					),
					'lineHeight' => array(
						'Desktop' => array(
							'unit'  => 'em',
							'point' => '1.2',
						),
					),
					'weight'     => '500',
				),
			),
			array(
				'id'   => 'h5-font',
				'name' => 'H5 Font',
				'font' => array(
					'font'       => array(
						'label' => 'Roboto',
						'value' => 'Roboto',
						'type'  => 'google',
					),
					'size'       => array(
						'Desktop' => array(
							'unit'  => 'px',
							'point' => '20',
						),
						'Mobile'  => array(
							'unit'  => 'px',
							'point' => '18',
						),
					),
					'lineHeight' => array(
						'Desktop' => array(
							'unit'  => 'em',
							'point' => '1.2',
						),
					),
					'weight'     => '500',
				),
			),
			array(
				'id'   => 'h5-bold-font',
				'name' => 'H5 Bold Font',
				'font' => array(
					'font'       => array(
						'label' => 'Roboto',
						'value' => 'Roboto',
						'type'  => 'google',
					),
					'size'       => array(
						'Desktop' => array(
							'unit'  => 'px',
							'point' => '20',
						),
						'Mobile'  => array(
							'unit'  => 'px',
							'point' => '18',
						),
					),
					'lineHeight' => array(
						'Desktop' => array(
							'unit'  => 'em',
							'point' => '1.2',
						),
					),
					'weight'     => '700',
				),
			),
			array(
				'id'   => 'h6-font',
				'name' => 'H6 Font',
				'font' => array(
					'font'       => array(
						'label' => 'Roboto',
						'value' => 'Roboto',
						'type'  => 'google',
					),
					'size'       => array(
						'Desktop' => array(
							'unit'  => 'px',
							'point' => '18',
						),
						'Mobile'  => array(
							'unit'  => 'px',
							'point' => '16',
						),
					),
					'lineHeight' => array(
						'Desktop' => array(
							'unit'  => 'em',
							'point' => '1.2',
						),
					),
					'weight'     => '700',
				),
			),
			array(
				'id'   => 'h7-font',
				'name' => 'H7 Font',
				'font' => array(
					'font'       => array(
						'label' => 'Roboto',
						'value' => 'Roboto',
						'type'  => 'google',
					),
					'size'       => array(
						'Desktop' => array(
							'unit'  => 'px',
							'point' => '16',
						),
						'Mobile'  => array(
							'unit'  => 'px',
							'point' => '14',
						),
					),
					'lineHeight' => array(
						'Desktop' => array(
							'unit'  => 'em',
							'point' => '1.7',
						),
					),
					'weight'     => '500',
				),
			),
			array(
				'id'   => 'button-hero-font',
				'name' => 'Button Hero Font',
				'font' => array(
					'font'       => array(
						'label' => 'Roboto',
						'value' => 'Roboto',
						'type'  => 'google',
					),
					'size'       => array(
						'Desktop' => array(
							'unit'  => 'px',
							'point' => '16',
						),
					),
					'lineHeight' => array(
						'Desktop' => array(
							'unit'  => 'em',
							'point' => '1',
						),
					),
					'weight'     => '500',
				),
			),
			array(
				'id'   => 'button-font',
				'name' => 'Button Font',
				'font' => array(
					'font'       => array(
						'label' => 'Roboto',
						'value' => 'Roboto',
						'type'  => 'google',
					),
					'size'       => array(
						'Desktop' => array(
							'unit'  => 'px',
							'point' => '18',
						),
						'Mobile'  => array(
							'unit'  => 'px',
							'point' => '16',
						),
					),
					'lineHeight' => array(
						'Desktop' => array(
							'unit'  => 'em',
							'point' => '1',
						),
					),
					'weight'     => '500',
				),
			),
			array(
				'id'   => 'button-2-font',
				'name' => 'Button 2 Font',
				'font' => array(
					'font'       => array(
						'label' => 'Roboto',
						'value' => 'Roboto',
						'type'  => 'google',
					),
					'size'       => array(
						'Desktop' => array(
							'unit'  => 'px',
							'point' => '16',
						),
						'Mobile'  => array(
							'unit'  => 'px',
							'point' => '14',
						),
					),
					'lineHeight' => array(
						'Desktop' => array(
							'unit'  => 'em',
							'point' => '1',
						),
					),
					'weight'     => '500',
				),
			),
			array(
				'id'   => 'description-font',
				'name' => 'Testimonial Desc Font',
				'font' => array(
					'font'       => array(
						'label' => 'Noto Sans',
						'value' => 'Noto Sans',
						'type'  => 'google',
					),
					'size'       => array(
						'Desktop' => array(
							'unit'  => 'px',
							'point' => '24',
						),
						'Mobile'  => array(
							'unit'  => 'px',
							'point' => '16',
						),
					),
					'lineHeight' => array(
						'Desktop' => array(
							'unit'  => 'em',
							'point' => '1.5',
						),
					),
					'style'      => 'italic',
					'weight'     => '400',
				),
			),
			array(
				'id'   => 'text-font',
				'name' => 'Text Font',
				'font' => array(
					'font'       => array(
						'label' => 'Noto Sans',
						'value' => 'Noto Sans',
						'type'  => 'google',
					),
					'size'       => array(
						'Desktop' => array(
							'unit'  => 'px',
							'point' => '16',
						),
						'Mobile'  => array(
							'unit'  => 'px',
							'point' => '14',
						),
					),
					'lineHeight' => array(
						'Desktop' => array(
							'unit'  => 'em',
							'point' => '1.7',
						),
					),
					'weight'     => '400',
				),
			),
			array(
				'id'   => 'text-14-font',
				'name' => 'Text 14 Font',
				'font' => array(
					'font'       => array(
						'label' => 'Noto Sans',
						'value' => 'Noto Sans',
						'type'  => 'google',
					),
					'size'       => array(
						'Desktop' => array(
							'unit'  => 'px',
							'point' => '14',
						),
						'Mobile'  => array(
							'unit'  => 'px',
							'point' => '12',
						),
					),
					'lineHeight' => array(
						'Desktop' => array(
							'unit'  => 'em',
							'point' => '1.7',
						),
					),
					'weight'     => '400',
				),
			),
			array(
				'id'   => 'text-14-bold-font',
				'name' => 'Text 14 Bold Font',
				'font' => array(
					'font'       => array(
						'label' => 'Noto Sans',
						'value' => 'Noto Sans',
						'type'  => 'google',
					),
					'size'       => array(
						'Desktop' => array(
							'unit'  => 'px',
							'point' => '14',
						),
						'Mobile'  => array(
							'unit'  => 'px',
							'point' => '12',
						),
					),
					'lineHeight' => array(
						'Desktop' => array(
							'unit'  => 'em',
							'point' => '1.7',
						),
					),
					'weight'     => '700',
				),
			),
			array(
				'id'   => '404-font',
				'name' => '404 Font',
				'font' => array(
					'font'       => array(
						'label' => 'Roboto',
						'value' => 'Roboto',
						'type'  => 'google',
					),
					'size'       => array(
						'Desktop' => array(
							'unit'  => 'px',
							'point' => '150',
						),
						'Tablet'  => array(
							'unit'  => 'px',
							'point' => '140',
						),
						'Mobile'  => array(
							'unit'  => 'px',
							'point' => '120',
						),
					),
					'lineHeight' => array(
						'Desktop' => array(
							'unit'  => 'em',
							'point' => '1.4',
						),
					),
					'weight'     => '700',
				),
			),
			array(
				'id'   => 'price-font',
				'name' => 'Price Font',
				'font' => array(
					'font'       => array(
						'label' => 'Roboto',
						'value' => 'Roboto',
						'type'  => 'google',
					),
					'size'       => array(
						'Desktop' => array(
							'unit'  => 'px',
							'point' => '60',
						),
						'Mobile'  => array(
							'unit'  => 'px',
							'point' => '56',
						),
					),
					'lineHeight' => array(
						'Desktop' => array(
							'unit'  => 'em',
							'point' => '1.2',
						),
					),
					'weight'     => '700',
				),
			),
			array(
				'id'   => 'comment-font',
				'name' => 'Comment Font',
				'font' => array(
					'font'       => array(
						'label' => 'Roboto',
						'value' => 'Roboto',
						'type'  => 'google',
					),
					'size'       => array(
						'Desktop' => array(
							'unit'  => 'px',
							'point' => '32',
						),
						'Tablet'  => array(
							'unit'  => 'px',
							'point' => '36',
						),
						'Mobile'  => array(
							'unit'  => 'px',
							'point' => '34',
						),
					),
					'lineHeight' => array(
						'Desktop' => array(
							'unit'  => 'em',
							'point' => '1.2',
						),
					),
					'weight'     => '500',
				),
			),
			array(
				'id'   => 'funfact-number-font',
				'name' => 'Fun Fact Number Font',
				'font' => array(
					'font'       => array(
						'label' => 'Roboto',
						'value' => 'Roboto',
						'type'  => 'google',
					),
					'size'       => array(
						'Desktop' => array(
							'unit'  => 'px',
							'point' => '30',
						),
						'Mobile'  => array(
							'unit'  => 'px',
							'point' => '26',
						),
					),
					'lineHeight' => array(
						'Desktop' => array(
							'unit'  => 'em',
							'point' => '1.7',
						),
					),
					'weight'     => '700',
				),
			),
			array(
				'id'   => 'funfact-super-font',
				'name' => 'Fun Fact Super Font',
				'font' => array(
					'font'       => array(
						'label' => 'Roboto',
						'value' => 'Roboto',
						'type'  => 'google',
					),
					'size'       => array(
						'Desktop' => array(
							'unit'  => 'px',
							'point' => '30',
						),
						'Mobile'  => array(
							'unit'  => 'px',
							'point' => '26',
						),
					),
					'lineHeight' => array(
						'Desktop' => array(
							'unit'  => 'em',
							'point' => '1',
						),
					),
					'weight'     => '700',
				),
			),
			array(
				'id'   => 'title-font',
				'name' => 'Title Font',
				'font' => array(
					'font'       => array(
						'label' => 'Roboto',
						'value' => 'Roboto',
						'type'  => 'google',
					),
					'size'       => array(
						'Desktop' => array(
							'unit'  => 'px',
							'point' => '16',
						),
						'Mobile'  => array(
							'unit'  => 'px',
							'point' => '14',
						),
					),
					'lineHeight' => array(
						'Desktop' => array(
							'unit'  => 'em',
							'point' => '1.7',
						),
					),
					'weight'     => '400',
				),
			),
		);
	}

	/**
	 * Add Template to Editor.
	 *
	 * @param array $template_files Path to Template File.
	 * @param array $template_type Template Type.
	 *
	 * @return array
	 */
	public function add_template( $template_files, $template_type ) {
		if ( 'wp_template' === $template_type ) {
			$new_templates = array(
				'about',
				'blog',
				'contact',
				'faq',
				'pricing',
				'project-details',
				'projects',
				'service',
				'team',
			);

			foreach ( $new_templates as $template ) {
				$template_files[] = array(
					'slug'  => $template,
					'path'  => $this->change_stylesheet_directory() . "/templates/{$template}.html",
					'theme' => get_template(),
					'type'  => 'wp_template',
				);
			}
		}

		return $template_files;
	}

	/**
	 * Use gutenverse template file instead.
	 *
	 * @param string $template_file Path to Template File.
	 * @param string $theme_slug Theme Slug.
	 * @param string $template_slug Template Slug.
	 *
	 * @return string
	 */
	public function template_path( $template_file, $theme_slug, $template_slug ) {
		switch ( $template_slug ) {
			case '404':
				return $this->change_stylesheet_directory() . '/templates/404.html';
			case 'about':
				return $this->change_stylesheet_directory() . '/templates/about.html';
			case 'archive':
				return $this->change_stylesheet_directory() . '/templates/archive.html';
			case 'blog':
				return $this->change_stylesheet_directory() . '/templates/blog.html';
			case 'contact':
				return $this->change_stylesheet_directory() . '/templates/contact.html';
			case 'faq':
				return $this->change_stylesheet_directory() . '/templates/faq.html';
			case 'front-page':
				return $this->change_stylesheet_directory() . '/templates/front-page.html';
			case 'index':
				return $this->change_stylesheet_directory() . '/templates/index.html';
			case 'page':
				return $this->change_stylesheet_directory() . '/templates/page.html';
			case 'pricing':
				return $this->change_stylesheet_directory() . '/templates/pricing.html';
			case 'project-details':
				return $this->change_stylesheet_directory() . '/templates/project-details.html';
			case 'projects':
				return $this->change_stylesheet_directory() . '/templates/projects.html';
			case 'search':
				return $this->change_stylesheet_directory() . '/templates/search.html';
			case 'service':
				return $this->change_stylesheet_directory() . '/templates/service.html';
			case 'single':
				return $this->change_stylesheet_directory() . '/templates/single.html';
			case 'team':
				return $this->change_stylesheet_directory() . '/templates/team.html';
			case 'header':
				return $this->change_stylesheet_directory() . '/parts/header.html';
			case 'footer':
				return $this->change_stylesheet_directory() . '/parts/footer.html';
		}

		return $template_file;
	}

	/**
	 * Register Block Pattern.
	 */
	public function register_block_patterns() {
		new Block_Patterns();
	}

	/**
	 * Register Block Style.
	 */
	public function register_block_styles() {
		new Block_Styles();
	}

	/**
	 * Excerpt Elipsis.
	 *
	 * @param string $more .
	 *
	 * @return string
	 */
	public function excerpt_elipsis( $more ) {
		if ( is_admin() ) {
			return $more;
		}

		return '';
	}

	/**
	 * Excerpt Length.
	 *
	 * @param int $length .
	 *
	 * @return int
	 */
	public function excerpt_length( $length ) {
		if ( is_admin() ) {
			return $length;
		}

		return 100;
	}

	/**
	 * Notice Closed
	 */
	public function notice_closed() {
		if ( isset( $_POST['nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'vorfolio_admin_notice' ) ) {
			update_user_meta( get_current_user_id(), 'gutenverse_install_notice', 'true' );
		}
		die;
	}

	/**
	 * Show notification to install Gutenverse Plugin.
	 */
	public function notice_install_plugin() {
		// Skip if gutenverse block activated.
		if ( defined( 'GUTENVERSE' ) ) {
			return;
		}

		// Skip if gutenverse pro activated.
		if ( defined( 'GUTENVERSE_PRO' ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( isset( $screen->parent_file ) && 'themes.php' === $screen->parent_file && 'appearance_page_vorfolio-dashboard' === $screen->id ) {
			return;
		}

		if ( isset( $screen->parent_file ) && 'plugins.php' === $screen->parent_file && 'update' === $screen->id ) {
			return;
		}

		if ( 'true' === get_user_meta( get_current_user_id(), 'gutenverse_install_notice', true ) ) {
			return;
		}

		$all_plugin = get_plugins();
		$plugins    = $this->theme_config()['plugins'];
		$actions    = array();

		foreach ( $plugins as $plugin ) {
			$slug   = $plugin['slug'];
			$path   = "$slug/$slug.php";
			$active = is_plugin_active( $path );

			if ( isset( $all_plugin[ $path ] ) ) {
				if ( $active ) {
					$actions[ $slug ] = 'active';
				} else {
					$actions[ $slug ] = 'inactive';
				}
			} else {
				$actions[ $slug ] = '';
			}
		}

		$button_text = __( 'Install Required Plugins', 'vorfolio' );
		$button_link = wp_nonce_url( self_admin_url( 'themes.php?page=vorfolio-dashboard' ), 'install-plugin_gutenverse' );
		?>
		<style>
			.install-gutenverse-plugin-notice {
				border: 1px solid #E6E6EF;
				position: relative;
				overflow: hidden;
				padding: 0 !important;
				margin-bottom: 30px !important;
				background: url( <?php echo esc_url( VORFOLIO_URI . '/assets/img/background-banner.png' ); ?> );
				background-size: cover;
				background-position: center;
			}

			.install-gutenverse-plugin-notice .gutenverse-notice-content {
				display: flex;
				align-items: center;
			}

			.gutenverse-notice-text, .gutenverse-notice-image {
				width: 50%;
			}

			.gutenverse-notice-text {
				padding: 40px 0 40px 40px;
			}

			.install-gutenverse-plugin-notice img {
				max-width: 100%;
				display: flex;
			}

			.install-gutenverse-plugin-notice:after {
				content: "";
				position: absolute;
				left: 0;
				top: 0;
				height: 100%;
				width: 5px;
				display: block;
				background: linear-gradient(to bottom, #68E4F4, #4569FF, #F045FF);
			}

			.install-gutenverse-plugin-notice .notice-dismiss {
				top: 20px;
				right: 20px;
				padding: 0;
				background: white;
				border-radius: 6px;
			}

			.install-gutenverse-plugin-notice .notice-dismiss:before {
				content: "\f335";
				font-size: 17px;
				width: 25px;
				height: 25px;
				line-height: 25px;
				border: 1px solid #E6E6EF;
				border-radius: 3px;
			}

			.install-gutenverse-plugin-notice h3 {
				margin-top: 5px;
				margin-bottom: 15px;
				font-weight: 600;
				font-size: 25px;
				line-height: 1.4em;
			}

			.install-gutenverse-plugin-notice h3 span {
				font-weight: 700;
				background-clip: text !important;
				-webkit-text-fill-color: transparent;
				background: linear-gradient(80deg, rgba(208, 77, 255, 1) 0%,rgba(69, 105, 255, 1) 48.8%,rgba(104, 228, 244, 1) 100%);
			}

			.install-gutenverse-plugin-notice p {
				font-size: 13px;
				font-weight: 300;
				margin: 5px 100px 20px 0 !important;
			}

			.install-gutenverse-plugin-notice .gutenverse-bottom {
				display: flex;
				align-items: center;
				margin-top: 30px;
			}

			.install-gutenverse-plugin-notice a {
				text-decoration: none;
				margin-right: 20px;
			}

			.install-gutenverse-plugin-notice a.gutenverse-button {
				font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif;
				text-decoration: none;
				cursor: pointer;
				font-size: 12px;
				line-height: 18px;
				border-radius: 5px;
				background: #3B57F7;
				color: #fff;
				padding: 8px 30px;
				font-weight: 500;
				background: linear-gradient(to left, #68E4F4, #4569FF, #F045FF);
			}

			#gutenverse-install-plugin.loader:after {
				display: block;
				content: '';
				border: 5px solid white;
				border-radius: 50%;
				border-top: 5px solid rgba(255, 255, 255, 0);
				width: 10px;
				height: 10px;
				-webkit-animation: spin 2s linear infinite;
				animation: spin 2s linear infinite;
			}

			@-webkit-keyframes spin {
				0% {
					-webkit-transform: rotate(0deg);
				}
				100% {
					-webkit-transform: rotate(360deg);
				}
			}

			@keyframes spin {
				0% {
					transform: rotate(0deg);
				}
				100% {
					transform: rotate(360deg);
				}
			}

			@media screen and (max-width: 1024px) {
				.gutenverse-notice-text {
					width: 100%;
				}

				.gutenverse-notice-image {
					display: none;
				}
			}
		</style>
		<script>
		var promises = [];
		var actions = <?php echo wp_json_encode( $actions ); ?>;

		function sequenceInstall (plugins, index = 0) {
			if (plugins[index]) {
				var plugin = plugins[index];

				switch (actions[plugin?.slug]) {
					case 'active':
						break;
					case 'inactive':
						var path = plugin?.slug + '/' + plugin?.slug;
						promises.push(
							new Promise((resolve) => {
								fetch(wpApiSettings.root + 'wp/v2/plugins/' + path, {									
									method: 'POST',
									headers: {
										"X-WP-Nonce": wpApiSettings.nonce,
										'Content-Type': 'application/json',
									},
									body: JSON.stringify(
										{
											status: 'active'
										}
									)
								}).then(() => {
									sequenceInstall(plugins, index + 1);
									resolve(plugin);
								}).catch((error) => {
									alert('Plugin Install Failed')	
								});
							})
						);
						break;
					default:
						promises.push(
							new Promise((resolve) => {
								fetch(wpApiSettings.root + 'wp/v2/plugins', {									
									method: 'POST',
									headers: {
										"X-WP-Nonce": wpApiSettings.nonce,
										'Content-Type': 'application/json',
									},
									body: JSON.stringify(
										{
											slug: plugin?.slug,
											status: 'active'
										}
									)
								}).then(() => {
									sequenceInstall(plugins, index + 1);
									resolve(plugin);
								}).catch((error) => {
									alert('Plugin Install Failed')	
								});
							})
						);
						break;
				}
			}

			return;
		};

		jQuery( function( $ ) {
			$( 'div.notice.install-gutenverse-plugin-notice' ).on( 'click', 'button.notice-dismiss', function( event ) {
				event.preventDefault();
				$.post( ajaxurl, {
					action: 'vorfolio_set_admin_notice_viewed',
					nonce: '<?php echo esc_html( wp_create_nonce( 'vorfolio_admin_notice' ) ); ?>',
				} );
			} );

			$("#gutenverse-install-plugin").on('click', function(e) {
				var hasFinishClass = $(this).hasClass('finished');
				var hasLoaderClass = $(this).hasClass('loader');

				if(!hasFinishClass) {
					e.preventDefault();
				}

				if(!hasLoaderClass && !hasFinishClass) {
					promises = [];
					var plugins = <?php echo wp_json_encode( $plugins ); ?>;
					$(this).addClass('loader').text('');

					sequenceInstall(plugins);
					Promise.all(promises).then(() => {						
						$(this).removeClass('loader').addClass('finished').text('Visit Theme Dashboard');
					});
				}
			});
		} );
		</script>
		<div class="notice is-dismissible install-gutenverse-plugin-notice">
			<div class="gutenverse-notice-inner">
				<div class="gutenverse-notice-content">
					<div class="gutenverse-notice-text">
						<h3><?php esc_html_e( 'Take Your Website To New Height with', 'vorfolio' ); ?> <span>Gutenverse!</span></h3> 
						<p><?php esc_html_e( 'Vorfolio theme work best with Gutenverse plugin. By installing Gutenverse plugin you may access Vorfolio templates built with Gutenverse and get access to more than 40 free blocks, hundred free Layout and Section.', 'vorfolio' ); ?></p>
						<div class="gutenverse-bottom">
							<a class="gutenverse-button" id="gutenverse-install-plugin" href="<?php echo esc_url( $button_link ); ?>">
								<?php echo esc_html( $button_text ); ?>
							</a>
						</div>
					</div>
					<div class="gutenverse-notice-image">
						<img src="<?php echo esc_url( VORFOLIO_URI . '/assets/img/banner-install-gutenverse-2.png' ); ?>"/>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Add Menu
	 */
	public function admin_menu() {
		add_theme_page(
			'Vorfolio Dashboard',
			'Vorfolio Dashboard',
			'edit_theme_options',
			'vorfolio-dashboard',
			array( $this, 'load_vorfolio_dashboard' ),
			1
		);
	}

	/**
	 * Vorfolio Template page
	 */
	public function load_vorfolio_dashboard() {
		?>
		<?php if ( defined( 'GUTENVERSE_VERSION' ) && version_compare( GUTENVERSE_VERSION, '1.1.1', '<=' ) ) { ?>
			<div class="notice is-dismissible">
				<span>
				<?php echo esc_html_e( 'Please install newer version of Gutenverse plugin! (v1.1.2 and above)', 'vorfolio' ); ?>
				</span>
			</div>
			<?php } ?>
		<?php do_action( 'gutenverse_after_install_notice' ); ?>
			<div id="gutenverse-theme-dashboard"></div>
		<?php
	}

	/**
	 * Add theme template
	 */
	public function add_theme_templates() {
		add_editor_style( 'block-style.css' );
	}

	/**
	 * Custom Logo Parameter
	 */
	public function custom_logo() {
		return array(
			'height'               => 40,
			'width'                => 200,
			'flex-height'          => true,
			'flex-width'           => true,
			'header-text'          => array( 'site-title', 'site-description' ),
			'unlink-homepage-logo' => true,
		);
	}

	/**
	 * Theme setup.
	 */
	public function theme_setup() {
		load_theme_textdomain( 'vorfolio', VORFOLIO_DIR . '/languages' );

		add_theme_support( 'wp-block-styles' );
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'custom-logo', $this->custom_logo() );
		add_theme_support( 'editor-styles' );

		register_nav_menus(
			array(
				'primary' => esc_html__( 'Primary', 'vorfolio' ),
			)
		);

		add_editor_style(
			array(
				'./assets/css/core-add.css',
			)
		);

		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
				'style',
				'script',
			)
		);

		add_theme_support( 'customize-selective-refresh-widgets' );
	}

	/**
	 * Set the content width.
	 */
	public function content_width() {
		$GLOBALS['content_width'] = apply_filters( 'gutenverse_content_width', 960 );
	}

	/**
	 * Enqueue scripts and styles.
	 */
	public function enqueue_scripts() {
		wp_enqueue_style( 'vorfolio-style', get_stylesheet_uri(), array(), VORFOLIO_VERSION );
		wp_add_inline_style( 'vorfolio-style', $this->load_font_styles() );

		// enqueue additional core css.
		wp_enqueue_style( 'vorfolio-core-add', VORFOLIO_URI . '/assets/css/core-add.css', array(), VORFOLIO_VERSION );

		// enqueue core animation.
		wp_enqueue_script( 'vorfolio-animate', VORFOLIO_URI . '/assets/js/index.js', array(), VORFOLIO_VERSION, true );
		wp_enqueue_style( 'vorfolio-animate', VORFOLIO_URI . '/assets/css/animation.css', array(), VORFOLIO_VERSION );

		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}
	}

	/**
	 * Enqueue scripts and styles.
	 */
	public function backend_scripts() {
		$screen = get_current_screen();

		wp_enqueue_script( 'wp-api' );

		if ( 'appearance_page_vorfolio-dashboard' === $screen->id ) {
			// enqueue css.
			wp_enqueue_style(
				'vorfolio-dashboard',
				VORFOLIO_URI . '/assets/css/dashboard.css',
				array(),
				VORFOLIO_VERSION
			);

			// enqueue js.
			wp_enqueue_script(
				'vorfolio-dashboard',
				VORFOLIO_URI . '/assets/js/dashboard.js',
				array( 'wp-api-fetch' ),
				VORFOLIO_VERSION,
				true
			);

			wp_localize_script( 'vorfolio-dashboard', 'GutenverseThemeConfig', $this->theme_config() );
		}
	}

	/**
	 * Register static data to be used in theme's js file
	 */
	public function theme_config() {
		return array(
			'demo'    => esc_url( 'https:/gutenverse.com/demo?name=vorfolio' ),
			'pages'   => array(
				'home'     => VORFOLIO_URI . '/assets/img/page-home.webp',
				'about'    => VORFOLIO_URI . '/assets/img/page-about.webp',
				'services' => VORFOLIO_URI . '/assets/img/page-services.webp',
				'contact'  => VORFOLIO_URI . '/assets/img/page-contact.webp',
				'pricing'  => VORFOLIO_URI . '/assets/img/page-pricing.webp',
			),
			'domain'  => 'vorfolio',
			'plugins' => array(
				array(
					'slug' => 'gutenverse',
					'name' => 'Gutenverse',
				),
			),
		);
	}

	/**
	 * Load Font Styles
	 */
	public function load_font_styles() {
		$font_families = array(
			'Roboto:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;1,100;1,200;1,300;1,400;1,500;1,600',
			'Noto+Sans:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;1,100;1,200;1,300;1,400;1,500;1,600',
		);

		$fonts_url = add_query_arg(
			array(
				'family'  => implode( '&family=', $font_families ),
				'display' => 'swap',
			),
			'https://fonts.googleapis.com/css2'
		);

		$contents = wptt_get_webfont_url( esc_url_raw( $fonts_url ), 'woff' );

		return "@import url({$contents});";
	}

	/**
	 * Load Editor Styles
	 */
	public function load_editor_styles() {
		wp_add_inline_style( 'wp-block-library', $this->load_font_styles() );
	}
}
