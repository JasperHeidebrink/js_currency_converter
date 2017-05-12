# JS Currency Converter
- Plugin Name:	JS Currency converter
- Description: 	This plugin converts a currency using front-end JavaScript
- Author: 		Dragonet
- Version: 		1.1
- Author URI: 	http://www.dragonet.nl/

This plugin converts a currency using front-end JavaScript.

The front-end part of this plugin is Javascript based to convert existing values in the website. A dropdown menu is generated during the parsing of your template using a filter action.

All elements defined in the admin part will be updates. For example:
When defined that all elements with the 'price' class must be updated, you should add a span of div with the class 'price' around the class.

In the admin part an exchange rate API can be linked to pre-fill your current currencies. 
A free exchange rate API is for example: http://apilayer.net

If you wish to define the origin of the currency you need to subscribe to: https://currencylayer.com. Whiteout this subscription the plugin uses USD as a origin currency, so this is optional. 


# Installation
1. Download the plugin.
2. Activate the plugin.
3. Setup the plugin in the admin part under the settings menu.
4. Add this code somewhere in your template files e.a. header.php to display the currency-select drowdown menu:
    ```
    <?php apply_filters( 'add_currency_converter_dropdown' ); ?>
    ```