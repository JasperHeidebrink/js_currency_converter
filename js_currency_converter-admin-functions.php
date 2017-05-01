<?php
/**
 * @package JS Currency Converter
 * @version 1.0
 *
 * Required by js_currency_converter-init.php
 * This document contains all the admin functions for the JS Currency Converter.
 */


if ( preg_match( '#' . basename( __FILE__ ) . '#', $_SERVER['PHP_SELF'] ) ) {
	die( 'You are not allowed to call this page directly.' );
}


class JsCurrencyConverterAdmin {

	/**
	 * @var string
	 */
	protected $_slug = 'js_currency_converter';

	/**
	 * @var array
	 */
	protected $_from_currency = [ 'USD', 'EUR' ];

	/**
	 * AdminSettings constructor.
	 */
	function __construct() {

		/*
		 * initialize admin settings when needed
		 */
		if ( ! is_admin() ) {
			return false;
		}

		/*
		 * Initialize the settings
		 */
		add_action( 'admin_init', [ $this, 'action__jcc_RegisterSettings' ] );
		add_action( 'admin_menu', [ $this, 'action__jcc_CreateMenu' ] );

		/*
		 * Admin part
		 */
		add_action( 'admin_enqueue_scripts', [ $this, 'action__jcc_admin_scripts' ] );

		return true;
	}

	/**
	 * Load the admin javascript
	 *
	 * @param $hook
	 */
	public function action__jcc_admin_scripts( $hook ) {
		wp_register_script( 'JsCurrencyConverterAdmin',
		                    plugin_dir_url( __FILE__ ) . 'assets/js/js_currency_converter_admin.js',
		                    [ 'jquery' ],
		                    '1.0' );
		$i18n = array(
			'ajaxUrl'            => admin_url( 'admin-ajax.php' ),
			'ajaxNonce'          => wp_create_nonce( $this->_slug ),
			'target'             => esc_html( get_option( 'jcc_target_class' ) ),
			'exchange_rates_api' => esc_html( get_option( 'jcc_exchange_rates_api' ) ),
		);
		wp_localize_script( 'JsCurrencyConverterAdmin', $this->_slug, $i18n );
		wp_enqueue_script( 'JsCurrencyConverterAdmin' );
	}

	/**
	 * Create the menu item in the settigns part
	 */
	public function action__jcc_CreateMenu() {

		/*
		 * create new top-level menu
		 */
		add_submenu_page( 'options-general.php',
		                  ucfirst( $this->_slug ),
		                  'JS Currency Converter',
		                  'administrator',
		                  __FILE__,
		                  [ $this, 'jcc_options_page' ] );
	}

	/**
	 * The settings for this site than can be updated by the customer
	 */
	public function action__jcc_RegisterSettings() {
		/*
		 * The table options settings
		 */
		register_setting( $this->_slug . '_settings', 'jcc_target_class' );
		register_setting( $this->_slug . '_settings', 'jcc_currency' );
		register_setting( $this->_slug . '_settings', 'jcc_exchange_rates' );
		register_setting( $this->_slug . '_settings', 'jcc_exchange_rates_from' );
		register_setting( $this->_slug . '_settings', 'jcc_exchange_rates_api_key' );
	}

	/**
	 * The settings page in the admin menu
	 */
	public function jcc_options_page() {

		$url = 'http://apilayer.net/api/live?access_key=' . esc_attr( get_option( 'jcc_exchange_rates_api_key' ) );

		echo '<div class="wrap">';
		echo '	<h2>' . __( 'JS Currency Converter settings', $this->_slug ) . '</h2>';
		echo '	<form method="post" action="options.php">';

		settings_fields( $this->_slug . '_settings' );
		do_settings_sections( $this->_slug . '_settings' );

		echo '<div class="card" style="width:90%; max-width:90%;">';
		echo '	<table class="form-table">';

		echo '	<tr valign="top">';
		echo '		<th scope="row">' . __( 'The original currency', $this->_slug ) . '</th>';
		echo '		<td><select name="jcc_exchange_rates_from">';
		foreach ( $this->_from_currency as $currency ) {
			echo '  <option name="' . $currency . '"';
			if ( get_option( 'jcc_exchange_rates_from' ) == $currency ) {
				echo ' selected="selected"';
			};
			echo '>' . $currency . '</option>';
		}
		echo '	</select><em> ';
		echo sprintf( __( 'Special subscription needed at <a href="%s" target="currency_list">%s</a>', $this->_slug ), 'https://currencylayer.com/', 'https://currencylayer.com/');
		echo '		</em></td>';
		echo '	</tr>';

		echo '	<tr valign="top">';
		echo '		<th scope="row">' . __( 'API-KEY for <a href="http://apilayer.net" target="api_layer">http://apilayer.net</a>', $this->_slug ) . '</th>';
		echo '		<td><input type="text" name="jcc_exchange_rates_api_key" value="' . esc_attr( get_option( 'jcc_exchange_rates_api_key' ) ) . '" style="width:65%;" /></td>';
		echo '	</tr>';

		echo '	<tr valign="top">';
		echo '		<th scope="row">' . __( 'CLASS of target element', $this->_slug ) . '</th>';
		echo '		<td><input type="text" name="jcc_target_class" value="' . esc_attr( get_option( 'jcc_target_class' ) ) . '" style="width:65%;" /></td>';
		echo '	</tr>';

		echo '	<tr valign="top">';
		echo '		<th scope="row">' . __( 'Currencies', $this->_slug ) . '</th>';
		echo '		<td>';
		echo '		<textarea name="jcc_currency" style="float:left;width:25%;min-height:150px;">' . esc_html( get_option( 'jcc_currency' ) ) . '</textarea>';
		echo '<br/><em>';
		echo __( 'Every currency should be on a new line', $this->_slug ) . '<br>';
		echo sprintf( __( 'Currency list can be found here <a href="%s" target="currency_list">%s</a>', $this->_slug ), $url, $url );
		echo '		</em></td>';
		echo '	</tr>';

		echo '	<tr valign="top">';
		echo '		<th scope="row">' . __( 'Exchange rates', $this->_slug ) . '</th>';
		echo '		<td>';
		echo '		<textarea name="jcc_exchange_rates" style="float:left;width:25%;min-height:250px;">' . esc_html( get_option( 'jcc_exchange_rates' ) ) . '</textarea>';
		echo '      <div class="currency_holder" style="float:left;width:25%;min-height:250px;border:solid 1px;margin:0 15px;">Example:<br>' . $this->retrieve_exchange_rates() . '</div></td>';
		echo '	</tr>';

		echo '	</table>';
		echo '</div>';

		echo get_submit_button();

		echo '</form></div>';
	}

	/**
	 * Retrieve the live exchange rates
	 *
	 * @return string
	 */
	private function retrieve_exchange_rates() {

		$access_key = esc_attr( get_option( 'jcc_exchange_rates_api_key' ) );
		$from       = esc_attr( get_option( 'jcc_exchange_rates_from' ) );
		$currencies = preg_replace( "/[\r\n]/", ',', get_option( 'jcc_currency' ) );
		$currencies = str_replace( ',,', ',', $currencies );
		$url        = 'http://apilayer.net/api/live?access_key=' . $access_key . '&from=' . $from . '&currencies=' . $currencies;

		/*
		 * Initialize CURL:
		 */
		$ch = curl_init( $url );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

		/*
		 * Store the data:
		*/
		$json = curl_exec( $ch );
		curl_close( $ch );

		/*
		 * Decode JSON response:
		 */
		$exchangeRates = json_decode( $json, true );

		if ( ! isset( $exchangeRates['quotes'] ) ) {
			return __( 'API(key) is not valid', $this->_slug );
		}

		$output = '';
		foreach ( $exchangeRates['quotes'] as $type => $rate ) {
			$output .= $type . ':' . $rate . "\n";
		}

		return nl2br( $output );
	}
}