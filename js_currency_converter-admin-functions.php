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

		return true;
	}

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
		register_setting( $this->_slug . '_settings', 'jcc_settings' );
	}

	/**
	 * The settings page in the admin menu
	 */
	public function jcc_options_page() {
		echo '<div class="wrap">';
		echo '	<h2>' . __( 'JS Currency Converter settings', $this->_slug ) . '</h2>';
		echo '	<form method="post" action="options.php">';

		settings_fields( $this->_slug . '_settings' );
		do_settings_sections( $this->_slug . '_settings' );

		echo '<div class="card" style="width:90%; max-width:90%;">';
		echo '	<table class="form-table">';

		echo '	<tr valign="top">';
		echo '		<th scope="row">' . __( 'CLASS of target element', $this->_slug ) . '</th>';
		echo '		<td><input type="text" name="jcc_target_class" value="' . esc_attr( get_option( 'jcc_target_class' ) ) . '" style="width:65%;" /></td>';
		echo '	</tr>';

		echo '	<tr valign="top">';
		echo '		<th scope="row">' . __( 'Currency', $this->_slug ) . '</th>';
		echo '		<td><textarea name="jcc_settings" style="width:25%;min-height:250px;">' . esc_html( get_option( 'jcc_settings' ) ) . '</textarea><br/><em>';
		echo __( 'Every currency should be on a new line', 'handhaving' );
		echo '		</em></td>';
		echo '	</tr>';

		echo '	</table>';
		echo '</div>';

		echo get_submit_button();

		echo '</form></div>';
	}
}