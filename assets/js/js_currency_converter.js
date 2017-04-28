var fx;

jQuery( document ).ready( function ( $ ) {

	fx.base  = 'EUR';
	fx.rates = {
		'EUR' : 0.745101, // eg. 1 USD === 0.745101 EUR
		'GBP' : 0.647710, // etc...
		'USD' : 1
	};


	/*
	 * Default storing the of a like
	 */
	$( '.js_currency_converter_select' ).change( function ( event ) {

		event.preventDefault();

		update_currency();
	} );

	/**
	 * Loading the gallery like counters
	 *
	 * @param item_url
	 * @param target
	 */
	init_like_counter = function ( item_url, target ) {

		$.ajax( {
			type     : 'POST',
			url      : JsCurrencyConverter.ajaxUrl,
			dataType : 'json',
			data     : {
				action   : 'dg_get_like_counter',
				nonce    : JsCurrencyConverter.ajaxNonce,
				item_url : item_url,
				target   : encodeURIComponent( target )
			},
			success  : function ( result ) {
				if ( undefined === result.data.html ) {
					return;
				}

				/*
				 * Update the counter
				 */
				$( result.data.html ).insertAfter( decodeURIComponent( result.data.target ) );
			}
		} );
	};

	/**
	 * Update the currency based on the selection
	 *
	 */
	update_currency = function () {

		var currency = $( '.js_currency_converter_select' ).val();

		$( '.' + JsCurrencyConverter.target ).each( function () {
			/*
			 * Get the value and transform it to the new values
			 */
			// var value     = parseInt( $( this ).text().split( '-' ).join( '00' ) ),
			//     new_value = fx.convert( value, { from : 'EUR', to : currency } );
			// $( this ).html( new_value );


			var value          = accounting.unformat( $( this ).text() );
			var convertedValue = fx( value ).from( 'EUR' ).to( currency );


			$( this ).html(
				accounting.formatMoney( convertedValue, {
					symbol : currency,
					format : "%v %s"
				} )
			);


			console.log( 'value: ' + value + ' convertedValue: ' + convertedValue + ' to currency: ' + currency );
		} );
	};




    // Load exchange rates data via AJAX:
    $.getJSON(
    	// NB: using Open Exchange Rates here, but you can use any source!
        'https://openexchangerates.org/api/latest.json?app_id=[YOUR APP ID]',
        function(data) {
            // Check money.js has finished loading:
            if ( typeof fx !== "undefined" && fx.rates ) {
                fx.rates = data.rates;
                fx.base = data.base;
            } else {
                // If not, apply to fxSetup global:
                var fxSetup = {
                    rates : data.rates,
                    base : data.base
                }
            }
        }
    );

    

	/*
	 * Set the stored currency, from the php coockie
	 */
	// $( '.' + JsCurrencyConverter.target )

	update_currency();
} );
