<?php
/**
 * Mawthouq Pay
 *
 * @package       MAWTHOUQPA
 * @author        Mubtakar Agency
 * @license       gplv2
 * @version       1.0.0
 *
 * @wordpress-plugin
 * Plugin Name:   Mawthouq Pay
 * Plugin URI:    https://mawthouq.org/
 * Description:   Mawthouq Payment is an open source project by Mawthouq · #1 The first platform to search and evaluate online stores in Algiers
 * Version:       1.0.0
 * Author:        Mubtakar Agency
 * Author URI:    https://mubtakar.com/
 * Text Domain:   mawthouq-pay
 * Domain Path:   /languages
 * License:       GPLv2
 * License URI:   https://www.gnu.org/licenses/gpl-2.0.html
 *
 * You should have received a copy of the GNU General Public License
 * along with Mawthouq Pay. If not, see <https://www.gnu.org/licenses/gpl-2.0.html/>.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;
// Plugin name
define( 'MAWTHOUQPA_NAME',			'Mawthouq Pay' );

// Plugin version
define( 'MAWTHOUQPA_VERSION',		'1.0.0' );

// Plugin Root File
define( 'MAWTHOUQPA_PLUGIN_FILE',	__FILE__ );

// Plugin base
define( 'MAWTHOUQPA_PLUGIN_BASE',	plugin_basename( MAWTHOUQPA_PLUGIN_FILE ) );

// Plugin Folder Path
define( 'MAWTHOUQPA_PLUGIN_DIR',	plugin_dir_path( MAWTHOUQPA_PLUGIN_FILE ) );

// Plugin Folder URL
define( 'MAWTHOUQPA_PLUGIN_URL',	plugin_dir_url( MAWTHOUQPA_PLUGIN_FILE ) );

/**
 * Load the main class for the core functionality
 */
require_once MAWTHOUQPA_PLUGIN_DIR . 'core/class-mawthouq-pay.php';

/**
 * The main function to load the only instance
 * of our master class.
 *
 * @author  Mubtakar Agency
 * @since   1.0.0
 * @return  object|Mawthouq_Pay
 */
function MAWTHOUQPA() {
	return Mawthouq_Pay::instance();
}

MAWTHOUQPA();

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

add_action( 'plugins_loaded', 'init_your_gateway_class' );

function init_your_gateway_class() {
  class WC_satim_cib_gateway extends WC_Payment_Gateway {

    function __construct() {
      $this->id = 'satim_cib_gateway';
      $this->method_title = "Mawthouq Pay";
      $this->method_description = "Mawthouq Pay Wordpress plugin";
      $this->icon = "https://mubtakar.com/wp-content/uploads/ciblogo-01.webp";
      $this->has_field = false;
      $this->enabled = $this->get_option('enabled');
      $this->title = $this->get_option('title');
      $this->username = $this->get_option('ccb-username');
      $this->password = $this->get_option('ccb-password');

      $this->init_form_fields();
      $this->init_settings();

      add_action('woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
    }

    function init_form_fields() {
      $this->form_fields = array(
        'enabled' => array(
          'title'   => __( 'Enable/Disable' ),
          'type'    => 'checkbox',
          'label'   => __( 'Enable SATIM ePayment' ),
          'default' => 'yes'
        ),
        'title' => array(
          'title' => __('Title to display to user'),
          'type' => 'text',
          'default' => $this->title
        ),
        'ccb-username' => array(
          'title' => __('Gateway username'),
          'type' => 'text',
          //'default' => $this->username
          'default' => ''
        ),
        'ccb-password' => array(
          'title' => __('Gateway password'),
          'type' => 'text',
          //'default' => $this->password
          'default' => ''
        ),
        'order-register-request' => array(
          'title' => __('Order Register Request URL'),
          'type' => 'text',
          'default' => 'https://cib.satim.dz/payment/rest/register.do'
        ),
        'order-status-request' => array(
          'title' => __('Order Status Request URL'),
          'type' => 'text',
          'default' => 'https://cib.satim.dz/payment/rest/getOrderStatus.do'
        ),
        'order-confirm-request' => array(
          'title' => __('Order Confirm Request URL'),
          'type' => 'text',
          'default' => 'https://cib.satim.dz/payment/rest/confirmOrder.do'
        ),
        'sslverify' => array(
          'title' => __('SSL Verify'),
          'type' => 'checkbox',
          'label'   => __( 'Enable SSL Certificat check' ),
          'default' => 'yes'
        )
      );
    }

    function process_payment($order_id) {

      $order = new WC_Order($order_id);
       
      $gatewayApiUrl = $this->get_option('order-register-request');


      $returnUrl = sprintf('%s&orderNumber=%d',$this->get_return_url($order),$order_id);


      $trial = 1;
      $params = array(
        'timeout' => 50,
        'sslverify' => ($this->get_option('sslverify') == 'yes'),
        'body' => array(
          'userName' => $this->username,
          'password' => $this->password,
          'amount' => $order->get_total() * 100,
          'currency' => '012',
          'orderNumber' => $order_id,
          'returnUrl' => $returnUrl,
          'jsonParams' => '{"orderNumber":'.$order_id.',"udf1":"'.$order_id.'","udf5":"00","force_terminal_id":""}'
        )
      );

      // Order Register Request
      $response = wp_remote_post($gatewayApiUrl, $params);


      while(is_wp_error($response) || $trial > 9) {
        $trial++;
        $params = array(
        'timeout' => 50,
        'sslverify' => ($this->get_option('sslverify') == 'yes'),
        'body' => array(
          'userName' => $this->username,
          'password' => $this->password,
          'amount' => $order->get_total() * 100,
          'currency' => '012',
          'orderNumber' => $order_id,
          'returnUrl' => $returnUrl,
          'jsonParams' => '{"orderNumber":'.$order_id.',"udf1":"'.$order_id.'","udf5":"00","force_terminal_id":"E002000007"}'
        )
      );

      // Order Register Request
      $response = wp_remote_post($gatewayApiUrl, $params);
      }

      if(is_wp_error($response)) {
        throw new Exception($response->get_error_message());
      }

      $json = json_decode($response['body']);

      if($json->errorCode != 0 )
      {
        throw new Exception($json->errorMessage);
      }

      // Save remote ID transaction
      update_post_meta($order_id ,"gateway_order_id", $json->orderId);

      // To Gateway page
      return array(
        'result' => 'success',
        'redirect' => $json->formUrl,
      );
    }
  }
}

add_action('plugins_loaded', 'init_your_gateway_class');

add_filter('woocommerce_payment_gateways', function ($methods) {

  $methods[] = 'WC_satim_cib_gateway';
  return $methods;
});
