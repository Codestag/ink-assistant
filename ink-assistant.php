<?php
/**
 * Plugin Name: Ink Assistant
 * Plugin URI: https://github.com/Codestag/ink-assistant
 * Description: A plugin to assit INK theme in adding widgets.
 * Author: Codestag
 * Author URI: https://codestag.com
 * Version: 1.0.2
 * Text Domain: ink-assistant
 * License: GPL2+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.txt
 *
 * @package INK
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Ink_Assistant' ) ) :
	/**
	 * Ink_Assistant Base Plugin Class.
	 *
	 * @since 1.0
	 */
	class Ink_Assistant {

		/**
		 * Base instance property.
		 *
		 * @since 1.0
		 */
		private static $instance;

		/**
		 * Registers a plugin instance & loads required methods.
		 *
		 * @since 1.0
		 */
		public static function register() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Ink_Assistant ) ) {
				self::$instance = new Ink_Assistant();
				self::$instance->define_constants();
				self::$instance->init();
				self::$instance->includes();
			}
		}

		/**
		 * Initialize plugin hooks.
		 *
		 * @since 1.0
		 */
		public function init() {
			add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'plugin_admin_assets' ) );
		}

		/**
		 * Registers constants.
		 *
		 * @since 1.0
		 */
		public function define_constants() {
			$this->define( 'IA_VERSION', '1.0' );
			$this->define( 'IA_DEBUG', true );
			$this->define( 'IA_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
			$this->define( 'IA_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		}

		/**
		 * Checks & defines undefined constants.
		 *
		 * @param string $name Contstant name.
		 * @param string $value Constant value.
		 * @since 1.0
		 */
		private function define( $name, $value ) {
			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
		}

		/**
		 * Loads required files.
		 *
		 * @since 1.0
		 */
		public function includes() {
			// Base Stag Widget class.
			require_once IA_PLUGIN_PATH . 'includes/class-stag-widget.php';

			// Widgets.
			require_once IA_PLUGIN_PATH . 'includes/widgets/contributors.php';
			require_once IA_PLUGIN_PATH . 'includes/widgets/feature-callout.php';
			require_once IA_PLUGIN_PATH . 'includes/widgets/featured-slide.php';
			require_once IA_PLUGIN_PATH . 'includes/widgets/recent-posts-grid.php';
			require_once IA_PLUGIN_PATH . 'includes/widgets/recent-posts.php';
			require_once IA_PLUGIN_PATH . 'includes/widgets/section-featured-slides.php';
			require_once IA_PLUGIN_PATH . 'includes/widgets/static-content.php';

			// Shortcodes.
			require_once IA_PLUGIN_PATH . 'includes/shortcodes/contact-form.php';
			require_once IA_PLUGIN_PATH . 'includes/shortcodes/locked-options.php';

			if ( is_admin() ) : // Admin includes.
				require_once IA_PLUGIN_PATH . 'includes/meta/stag-admin-metabox.php';

				// Post Metaboxes.
				require_once IA_PLUGIN_PATH . 'includes/meta/post.php';
			endif;

		}

		/**
		 * Load plugin language files.
		 *
		 * @access public
		 * @return void
		 */
		public function load_textdomain() {
			load_plugin_textdomain( 'ink-assistant', false, dirname( plugin_basename( IA_PLUGIN_PATH ) ) . '/languages/' );
		}

		/**
		 * Enqueue required scripts and styles.
		 *
		 * @param string $hook Current page slug.
		 *
		 * @since 1.0
		 * @return void
		 */
		public function plugin_admin_assets( $hook ) {
			if ( 'post.php' === $hook || 'post-new.php' === $hook ) {
				wp_enqueue_media();
				wp_enqueue_script( 'wp-color-picker' );
				wp_enqueue_style( 'stag-admin-metabox', IA_PLUGIN_URL . 'assets/css/stag-admin-metabox.css', array( 'wp-color-picker' ), IA_VERSION, 'screen' );
			}
		}
	}
endif;


/**
 * Registers plugin base class instance.
 *
 * @since 1.0
 */
function ink_assistant() {
	return Ink_Assistant::register();
}

/**
 * Plugin activation check notice.
 *
 * @since 1.0
 */
function ink_assistant_activation_notice() {
	echo '<div class="error"><p>';
	echo esc_html__( 'Ink Assistant requires Ink WordPress Theme to be installed and activated.', 'ink-assistant' );
	echo '</p></div>';
}

/**
 * Plugin Activation Check.
 *
 * @since 1.0
 */
function ink_assistant_activation_check() {
	$theme = wp_get_theme(); // gets the current theme.
	if ( 'Ink' === $theme->name || 'Ink' === $theme->parent_theme ) {
		add_action( 'after_setup_theme', 'ink_assistant' );
	} else {
		if ( ! function_exists( 'deactivate_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		deactivate_plugins( plugin_basename( __FILE__ ) );
		add_action( 'admin_notices', 'ink_assistant_activation_notice' );
	}
}

// Theme loads.
ink_assistant_activation_check();
