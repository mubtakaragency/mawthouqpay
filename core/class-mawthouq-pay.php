<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! class_exists( 'Mawthouq_Pay' ) ) :

	/**
	 * Main Mawthouq_Pay Class.
	 *
	 * @package		MAWTHOUQPA
	 * @subpackage	Classes/Mawthouq_Pay
	 * @since		1.0.0
	 * @author		Mubtakar Agency
	 */
	final class Mawthouq_Pay {

		/**
		 * The real instance
		 *
		 * @access	private
		 * @since	1.0.0
		 * @var		object|Mawthouq_Pay
		 */
		private static $instance;

		/**
		 * MAWTHOUQPA helpers object.
		 *
		 * @access	public
		 * @since	1.0.0
		 * @var		object|Mawthouq_Pay_Helpers
		 */
		public $helpers;

		/**
		 * MAWTHOUQPA settings object.
		 *
		 * @access	public
		 * @since	1.0.0
		 * @var		object|Mawthouq_Pay_Settings
		 */
		public $settings;

		/**
		 * Throw error on object clone.
		 *
		 * Cloning instances of the class is forbidden.
		 *
		 * @access	public
		 * @since	1.0.0
		 * @return	void
		 */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, __( 'You are not allowed to clone this class.', 'mawthouq-pay' ), '1.0.0' );
		}

		/**
		 * Disable unserializing of the class.
		 *
		 * @access	public
		 * @since	1.0.0
		 * @return	void
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, __( 'You are not allowed to unserialize this class.', 'mawthouq-pay' ), '1.0.0' );
		}

		/**
		 * Main Mawthouq_Pay Instance.
		 *
		 * Insures that only one instance of Mawthouq_Pay exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @access		public
		 * @since		1.0.0
		 * @static
		 * @return		object|Mawthouq_Pay	The one true Mawthouq_Pay
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Mawthouq_Pay ) ) {
				self::$instance					= new Mawthouq_Pay;
				self::$instance->base_hooks();
				self::$instance->includes();
				self::$instance->helpers		= new Mawthouq_Pay_Helpers();
				self::$instance->settings		= new Mawthouq_Pay_Settings();

				//Fire the plugin logic
				new Mawthouq_Pay_Run();

				/**
				 * Fire a custom action to allow dependencies
				 * after the successful plugin setup
				 */
				do_action( 'MAWTHOUQPA/plugin_loaded' );
			}

			return self::$instance;
		}

		/**
		 * Include required files.
		 *
		 * @access  private
		 * @since   1.0.0
		 * @return  void
		 */
		private function includes() {
			require_once MAWTHOUQPA_PLUGIN_DIR . 'core/includes/classes/class-mawthouq-pay-helpers.php';
			require_once MAWTHOUQPA_PLUGIN_DIR . 'core/includes/classes/class-mawthouq-pay-settings.php';

			require_once MAWTHOUQPA_PLUGIN_DIR . 'core/includes/classes/class-mawthouq-pay-run.php';
		}

		/**
		 * Add base hooks for the core functionality
		 *
		 * @access  private
		 * @since   1.0.0
		 * @return  void
		 */
		private function base_hooks() {
			add_action( 'plugins_loaded', array( self::$instance, 'load_textdomain' ) );
		}

		/**
		 * Loads the plugin language files.
		 *
		 * @access  public
		 * @since   1.0.0
		 * @return  void
		 */
		public function load_textdomain() {
			load_plugin_textdomain( 'mawthouq-pay', FALSE, dirname( plugin_basename( MAWTHOUQPA_PLUGIN_FILE ) ) . '/languages/' );
		}

	}

endif; // End if class_exists check.