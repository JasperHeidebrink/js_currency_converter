<?php
/**
 * @package JS Currency Converter
 * @version 1.0
 *
 * Required by js_currency_converter-init.php
 * This document contains all the front-end functions for the dragonet page like functionality
 */


if ( preg_match( '#' . basename( __FILE__ ) . '#', $_SERVER['PHP_SELF'] ) ) {
	die( 'You are not allowed to call this page directly.' );
}


class JsCurrencyConverter {

	/**
	 * @var string
	 */
	protected $_slug = 'js_currency_converter';

	/**
	 * @var string
	 */
	protected $_default_currency = 'USD';

	/**
	 * Initial actions
	 */
	public function setActions() {

		add_action( 'plugins_loaded', [ $this, 'init_textdomain' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'action__enqueue_scripts' ] );

		add_filter( 'add_currency_converter_dropdown', [ $this, 'filter__add_currency_converter_dropdown' ] );
		add_filter( 'retrieve_exchange_rates', [ $this, 'filter__retrieve_exchange_rates' ] );

		if ( get_option( 'jcc_exchange_rates_from' ) ) {
			$this->_default_currency = esc_attr( get_option( 'jcc_exchange_rates_from' ) );
		}
	}

	/**
	 * Init the translations
	 */
	public function init_textdomain() {
		$plugin_dir = basename( dirname( __FILE__ ) );
		load_plugin_textdomain( $this->_slug );
	}

	/**
	 * Enqueue the scripts for the ajax calls
	 */
	public function action__enqueue_scripts() {

		wp_register_script( 'accounting_js',
		                    plugin_dir_url( __FILE__ ) . 'assets/js/accounting_js/accounting.min.js',
		                    [ 'jquery' ],
		                    '0.4.2',
		                    true );

		wp_register_script( 'money_js',
		                    plugin_dir_url( __FILE__ ) . 'assets/js/money_js/money.min.js',
		                    [ 'jquery' ],
		                    '0.2',
		                    true );

		wp_register_script( 'JsCurrencyConverter',
		                    plugin_dir_url( __FILE__ ) . 'assets/js/js_currency_converter.js',
		                    [ 'jquery', 'money_js', 'accounting_js' ],
		                    '1.0',
		                    true );

		$i18n = array(
			'ajaxUrl'        => admin_url( 'admin-ajax.php' ),
			'ajaxNonce'      => wp_create_nonce( $this->_slug ),
			'from'           => esc_attr( get_option( 'jcc_exchange_rates_from' ) ),
			'target'         => esc_html( get_option( 'jcc_target_class' ) ),
			'exchange_rates' => $this->get_exchange_rates_json(),
		);
		wp_localize_script( 'JsCurrencyConverter', $this->_slug, $i18n );
		wp_enqueue_script( 'JsCurrencyConverter' );

		wp_register_style( 'JsCurrencyConverter-styles', plugin_dir_url( __FILE__ ) . 'assets/css/js_currency_converter.css' );
		wp_enqueue_style( 'JsCurrencyConverter-styles' );
	}

	/**
	 * Generate the curency converter
	 *
	 * @return string
	 */
	public function filter__add_currency_converter_dropdown() {
		$currencies_list = preg_split( "/[\r\n]/", get_option( 'jcc_currency' ), - 1, PREG_SPLIT_NO_EMPTY );

		$html = '<div class="js_currency_converter">' .
		        '<select class="js_currency_converter_select" name="js_currency_converter">';
		foreach ( $currencies_list as $currency ) {
			$html .= '  <option name="' . $currency . '"';
			if ( $this->_default_currency == $currency ) {
				$html .= ' selected="selected"';
			};
			$html .= '>' . $currency . '</option>';
		}
		$html .= '</select></div>';

		echo $html;
	}

	/**
	 * Retrieve the live exchange rates
	 *
	 * @return string
	 */
	public function get_exchange_rates_json() {

		$from           = esc_attr( get_option( 'jcc_exchange_rates_from' ) );
		$exchange_rates = [];
		$currency_list  = preg_split( "/[\r\n]/", get_option( 'jcc_exchange_rates' ) );
		foreach ( $currency_list as $currency ) {
			if ( false === strpos( $currency, ':' ) ) {
				continue;
			}
			$rate                     = explode( ':', $currency );
			$title                    = preg_replace( '/^' . $from . '/', '', $rate[0] );
			$exchange_rates[ $title ] = $rate[1];
		}

		return $exchange_rates;
	}
}