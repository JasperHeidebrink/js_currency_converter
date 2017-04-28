##List category posts
- Plugin Name:	DG Currency converter
- Plugin URI:		http://wordpress.org/#
- Description: 	This plugin converts a currency using front-end JavaScript
- Author: 		Dragonet
- Version: 		1.0
- Author URI: 	http://www.dragonet.nl/

##Description
##Installation
##Other notes

# JS Currency Converter
This plugin converts a currency using front-end JavaScript.

This currency converter uses javascript in combination with the Google API
In the admin part of your website you can define the name of the element with the currency that should be updated


# Installation
1. Download the plugin
2. Activate the plugin
3. Setup the plugin in the admin part
4. Add the select menu to location in the website:
    ```
    <?php apply_filters( 'add_currency_converter_dropdown' ); ?>
    ```
