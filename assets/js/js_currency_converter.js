var fx, current_currency;

jQuery( document ).ready( function ( $ ) {

	/*
	 * Default storing the of a like
	 */
	$( '.js_currency_converter_select' ).change( function ( event ) {

		current_currency = $( '.js_currency_converter_select' ).val();
		setCookie( 'current_currency', current_currency );

		event.preventDefault();

		update_currency( current_currency );
	} );

	/**
	 * Update the currency based on the selection
	 *
	 */
	update_currency = function () {

		var currency = $( '.js_currency_converter_select' ).val();
		if ( typeof null === typeof currency ){
			return;
		}

		if ( '' === fx.base ) {
			fx.base  = js_currency_converter.from;
			fx.rates = js_currency_converter.exchange_rates;
		}

		$( '.' + js_currency_converter.target ).each( function () {

			/*
			 * Make sure the original value is stored
			 */
			var org_value = $( this ).attr( 'data-org_value' );

			if ( typeof org_value === typeof undefined || org_value === false ) {
				$( this ).attr( 'data-org_value', accounting.unformat( $( this ).text() ) );
			}

			/*
			 * Get the value and transform it to the new values
			 */
			var value          = accounting.unformat( $( this ).attr( 'data-org_value' ) ),
			    convertedValue = fx( value ).from( js_currency_converter.from ).to( currency );

			$( this ).html(
				accounting.formatMoney( convertedValue, {
					symbol : currency,
					format : "%v %s"
				} )
			);
		} );
	};

	/**
	 * Store a cookie
	 *
	 * @param cname
	 * @param cvalue
	 */
	setCookie = function ( cname, cvalue ) {
		var d      = new Date(),
		    exdays = 31;
		d.setTime( d.getTime() + (exdays * 24 * 60 * 60 * 1000) );
		var expires     = 'expires=' + d.toUTCString();
		document.cookie = cname + '=' + cvalue + ';' + expires + 'path=/';
	};

	/**
	 * Get the cookie value
	 * @param cname
	 * @returns {*}
	 */
	getCookie = function ( cname ) {
		var name = cname + '=';
		var ca   = document.cookie.split( ';' );
		for ( var i = 0; i < ca.length; i ++ ) {
			var c = ca[i];
			while ( c.charAt( 0 ) == ' ' ) {
				c = c.substring( 1 );
			}
			if ( c.indexOf( name ) == 0 ) {
				return c.substring( name.length, c.length );
			}
		}
		return '';
	};

	/*
	 * Set the stored currency, from the php coockie
	 */
	current_currency = getCookie( 'current_currency' );

	if ( undefined !== current_currency ) {
		$( '.js_currency_converter_select' ).val( current_currency );
		update_currency();
	}

	/**
	 * Adding a flag to the option
	 *
	 * @param state
	 * @returns {*|jQuery|HTMLElement}
	 */
	formatState = function ( state ) {
		if ( ! state.id ) {
			return state.text;
		}

		return $( '<span><img src="' + $( state.element ).attr( 'data-image' ) + '" class="js_currency_converter_img-flag" /> ' + state.text + '</span>' );
	};
	/**
	 * Show a flag in the selected item
	 *
	 * @param event
	 */
	formatSelect = function ( event ) {
		var current_value = event.currentTarget.value,
		    current_item  = $( 'option[name="' + event.currentTarget.value + '"]' );

		if ( undefined === current_item.html() ) {
			current_value = js_currency_converter.from
			current_item  = $( 'option[name="' + current_value + '"]' );
		}

		$( '.select2-selection__rendered' ).html( '<span><img src="' + current_item.attr( 'data-image' ) + '" class="js_currency_converter_img-flag" /> ' + current_value + '</span>' );
	};


	$( '.js_currency_converter_select' )
		.select2( {
			templateResult          : formatState,
			minimumResultsForSearch : Infinity
		} )
		.on( 'select2:select', formatSelect )
		.trigger( 'select2:select' );

} );
