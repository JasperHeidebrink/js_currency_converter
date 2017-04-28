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

	public function setActions() {
		add_action( 'wp_enqueue_scripts', [ $this, 'action__enqueue_scripts' ] );

		add_filter( 'add_currency_converter_dropdown', [ $this, 'filter__add_currency_converter_dropdown' ] );
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
			'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
			'ajaxNonce' => wp_create_nonce( 'JsCurrencyConverter' ),
			'target'    => esc_html( get_option( 'jcc_target_class' ) ),
		);
		wp_localize_script( 'JsCurrencyConverter', 'JsCurrencyConverter', $i18n );
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

		$html = '<div class="js_currency_converter">' .
		        '<select class="js_currency_converter_select" name="js_currency_converter">' .
		        '	<option value="EUR">Euro</option>' .
		        '	<option value="USD">USD</option>' .
		        '	<option value="GBP">GBP</option>' .
		        '</select> ' .
		        '</div>';

		echo $html;
	}
}