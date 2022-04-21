<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class Mawthouq_Pay_Run
 *
 * Thats where we bring the plugin to life
 *
 * @package		MAWTHOUQPA
 * @subpackage	Classes/Mawthouq_Pay_Run
 * @author		Mubtakar Agency
 * @since		1.0.0
 */
class Mawthouq_Pay_Run{

	/**
	 * Our Mawthouq_Pay_Run constructor 
	 * to run the plugin logic.
	 *
	 * @since 1.0.0
	 */
	function __construct(){
		$this->add_hooks();
	}

	/**
	 * ######################
	 * ###
	 * #### WORDPRESS HOOKS
	 * ###
	 * ######################
	 */

	/**
	 * Registers all WordPress and plugin related hooks
	 *
	 * @access	private
	 * @since	1.0.0
	 * @return	void
	 */
	private function add_hooks(){
	
		add_action( 'plugin_action_links_' . MAWTHOUQPA_PLUGIN_BASE, array( $this, 'add_plugin_action_link' ), 20 );
	
	}

	/**
	 * ######################
	 * ###
	 * #### WORDPRESS HOOK CALLBACKS
	 * ###
	 * ######################
	 */

	/**
	* Adds action links to the plugin list table
	*
	* @access	public
	* @since	1.0.0
	*
	* @param	array	$links An array of plugin action links.
	*
	* @return	array	An array of plugin action links.
	*/
	public function add_plugin_action_link( $links ) {

		$links['our_shop'] = sprintf( '<a href="%s" title="Get the open source code" style="font-weight:700;">%s</a>', 'https://github.com/mubtakaragency/mawthouqpay', __( 'Get the open source code', 'mawthouq-pay' ) );
		return $links;
	}

}
