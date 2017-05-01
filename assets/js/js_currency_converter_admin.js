jQuery( document ).ready( function ( $ ) {

	/**
	 * Load exchange rates data via AJAX
	 */
	load_exchange_rates = function () {

		console.log( 'Load exchange rates data via AJAX' );

		$.ajax( {
			url      : js_currency_converter.jcc_exchange_rates_api,
			success  : function ( results ) {
				console.log( results );
			},
			error    : function ( results ) {
				console.log( results );
			}
		} );
	};

	load_exchange_rates();
} );
