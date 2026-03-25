( function () {
	function formatPrice( value ) {
		return '$' + Math.round( Number( value ) || 0 );
	}

	function updateSlider( slider ) {
		const minRange = slider.querySelector( '.muukal-price-range-min' );
		const maxRange = slider.querySelector( '.muukal-price-range-max' );
		const minInput = slider.querySelector( '.muukal-price-input-min' );
		const maxInput = slider.querySelector( '.muukal-price-input-max' );
		const minValue = slider.querySelector( '.muukal-price-value-min' );
		const maxValue = slider.querySelector( '.muukal-price-value-max' );
		const progress = slider.querySelector( '.muukal-price-progress' );
		const minBound = Number( slider.dataset.min || 0 );
		const maxBound = Number( slider.dataset.max || 0 );
		let min = Number( minRange.value || minBound );
		let max = Number( maxRange.value || maxBound );

		if ( min > max ) {
			if ( document.activeElement === minRange || document.activeElement === minInput ) {
				max = min;
				maxRange.value = String( max );
			} else {
				min = max;
				minRange.value = String( min );
			}
		}

		minInput.value = String( min );
		maxInput.value = String( max );
		minValue.textContent = formatPrice( min );
		maxValue.textContent = formatPrice( max );

		if ( maxBound > minBound ) {
			const left = ( ( min - minBound ) / ( maxBound - minBound ) ) * 100;
			const right = ( ( max - minBound ) / ( maxBound - minBound ) ) * 100;
			progress.style.left = left + '%';
			progress.style.width = Math.max( right - left, 0 ) + '%';
		}
	}

	function bindSlider( root ) {
		root.querySelectorAll( '.muukal-price-slider' ).forEach( function ( slider ) {
			const minRange = slider.querySelector( '.muukal-price-range-min' );
			const maxRange = slider.querySelector( '.muukal-price-range-max' );
			const minInput = slider.querySelector( '.muukal-price-input-min' );
			const maxInput = slider.querySelector( '.muukal-price-input-max' );
			const minBound = Number( slider.dataset.min || 0 );
			const maxBound = Number( slider.dataset.max || 0 );

			[ minRange, maxRange ].forEach( function ( input ) {
				input.addEventListener( 'input', function () {
					updateSlider( slider );
				} );
			} );

			[ minInput, maxInput ].forEach( function ( input ) {
				input.addEventListener( 'input', function () {
					let next = Number( input.value || 0 );

					if ( Number.isNaN( next ) ) {
						next = input === minInput ? minBound : maxBound;
					}

					next = Math.max( minBound, Math.min( maxBound, next ) );

					if ( input === minInput ) {
						minRange.value = String( next );
					} else {
						maxRange.value = String( next );
					}

					updateSlider( slider );
				} );
			} );

			updateSlider( slider );
		} );
	}

	function bindDropdowns( root ) {
		const items = root.querySelectorAll( '.muukal-filter-item' );

		items.forEach( function ( item ) {
			const toggle = item.querySelector( '.muukal-filter-toggle' );

			if ( ! toggle ) {
				return;
			}

			toggle.addEventListener( 'click', function () {
				const nextState = ! item.classList.contains( 'is-open' );

				items.forEach( function ( other ) {
					other.classList.remove( 'is-open' );
					const otherToggle = other.querySelector( '.muukal-filter-toggle' );

					if ( otherToggle ) {
						otherToggle.setAttribute( 'aria-expanded', 'false' );
					}
				} );

				item.classList.toggle( 'is-open', nextState );
				toggle.setAttribute( 'aria-expanded', nextState ? 'true' : 'false' );
			} );
		} );

		document.addEventListener( 'click', function ( event ) {
			if ( event.target.closest( '.muukal-filter-toolbar' ) ) {
				return;
			}

			items.forEach( function ( item ) {
				item.classList.remove( 'is-open' );
				const toggle = item.querySelector( '.muukal-filter-toggle' );

				if ( toggle ) {
					toggle.setAttribute( 'aria-expanded', 'false' );
				}
			} );
		} );
	}

	document.addEventListener( 'DOMContentLoaded', function () {
		document.querySelectorAll( '.muukal-filter-archive' ).forEach( function ( root ) {
			bindSlider( root );
			bindDropdowns( root );
		} );
	} );
} )();
