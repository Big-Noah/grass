( function () {
	function formatPriceRange( min, max ) {
		return '$' + Math.round( min ) + ' - $' + Math.round( max );
	}

	function getValues( params, key ) {
		const value = params.get( key );

		if ( ! value ) {
			return [];
		}

		return value.split( ',' ).filter( Boolean );
	}

	function setValues( params, key, values ) {
		params.delete( key );

		if ( values.length ) {
			params.set( key, values.join( ',' ) );
		}
	}

	function navigateWithParams( root, mutate ) {
		const url = new URL( window.location.href );
		const params = url.searchParams;

		mutate( params );
		params.delete( 'mu_page' );

		url.search = params.toString();
		window.location.href = url.toString();
	}

	function updatePriceSlider( slider ) {
		const minRange = slider.querySelector( '.muukal-price-range-min' );
		const maxRange = slider.querySelector( '.muukal-price-range-max' );
		const minHandle = slider.querySelector( '.muukal-price-handle-min' );
		const maxHandle = slider.querySelector( '.muukal-price-handle-max' );
		const minInput = slider.parentElement.querySelector( '.muukal-price-input-min' );
		const maxInput = slider.parentElement.querySelector( '.muukal-price-input-max' );
		const amount = slider.parentElement.querySelector( '.muukal-price-amount' );
		const progress = slider.querySelector( '.muukal-price-progress' );
		const minBound = Number( slider.dataset.min || 0 );
		const maxBound = Number( slider.dataset.max || 0 );
		let min = Number( minRange.value || minBound );
		let max = Number( maxRange.value || maxBound );

		if ( min > max ) {
			if ( document.activeElement === minRange ) {
				max = min;
				maxRange.value = String( max );
			} else {
				min = max;
				minRange.value = String( min );
			}
		}

		minInput.value = String( min );
		maxInput.value = String( max );
		amount.value = formatPriceRange( min, max );

		if ( maxBound > minBound ) {
			const left = ( ( min - minBound ) / ( maxBound - minBound ) ) * 100;
			const right = ( ( max - minBound ) / ( maxBound - minBound ) ) * 100;
			progress.style.left = left + '%';
			progress.style.width = Math.max( right - left, 0 ) + '%';

			if ( minHandle ) {
				minHandle.style.left = left + '%';
			}

			if ( maxHandle ) {
				maxHandle.style.left = right + '%';
			}
		}
	}

	function getSliderContext( slider ) {
		return {
			minRange: slider.querySelector( '.muukal-price-range-min' ),
			maxRange: slider.querySelector( '.muukal-price-range-max' ),
			minHandle: slider.querySelector( '.muukal-price-handle-min' ),
			maxHandle: slider.querySelector( '.muukal-price-handle-max' ),
			minBound: Number( slider.dataset.min || 0 ),
			maxBound: Number( slider.dataset.max || 0 )
		};
	}

	function valueFromPointer( slider, clientX, minBound, maxBound ) {
		const rect = slider.getBoundingClientRect();
		const ratio = rect.width ? Math.min( Math.max( ( clientX - rect.left ) / rect.width, 0 ), 1 ) : 0;

		return Math.round( minBound + ratio * ( maxBound - minBound ) );
	}

	function startHandleDrag( slider, handleType, startEvent ) {
		const context = getSliderContext( slider );
		const handle = handleType === 'min' ? context.minHandle : context.maxHandle;

		if ( ! handle ) {
			return;
		}

		handle.classList.add( 'is-dragging' );

		function updateFromClientX( clientX ) {
			const nextValue = valueFromPointer( slider, clientX, context.minBound, context.maxBound );
			let minValue = Number( context.minRange.value || context.minBound );
			let maxValue = Number( context.maxRange.value || context.maxBound );

			if ( handleType === 'min' ) {
				minValue = Math.min( nextValue, maxValue );
				context.minRange.value = String( minValue );
			} else {
				maxValue = Math.max( nextValue, minValue );
				context.maxRange.value = String( maxValue );
			}

			updatePriceSlider( slider );
		}

		function onPointerMove( moveEvent ) {
			updateFromClientX( moveEvent.clientX );
		}

		function stopDrag() {
			handle.classList.remove( 'is-dragging' );
			window.removeEventListener( 'pointermove', onPointerMove );
			window.removeEventListener( 'pointerup', stopDrag );
			window.removeEventListener( 'pointercancel', stopDrag );
		}

		updateFromClientX( startEvent.clientX );
		window.addEventListener( 'pointermove', onPointerMove );
		window.addEventListener( 'pointerup', stopDrag );
		window.addEventListener( 'pointercancel', stopDrag );
	}

	function bindPriceSliders( root ) {
		root.querySelectorAll( '.muukal-price-slider' ).forEach( function ( slider ) {
			const context = getSliderContext( slider );

			if ( context.minHandle ) {
				context.minHandle.addEventListener( 'pointerdown', function ( event ) {
					event.preventDefault();
					startHandleDrag( slider, 'min', event );
				} );
			}

			if ( context.maxHandle ) {
				context.maxHandle.addEventListener( 'pointerdown', function ( event ) {
					event.preventDefault();
					startHandleDrag( slider, 'max', event );
				} );
			}

			slider.addEventListener( 'pointerdown', function ( event ) {
				if ( event.target.closest( '.muukal-price-handle' ) ) {
					return;
				}

				const minValue = Number( context.minRange.value || context.minBound );
				const maxValue = Number( context.maxRange.value || context.maxBound );
				const nextValue = valueFromPointer( slider, event.clientX, context.minBound, context.maxBound );
				const targetHandle = Math.abs( nextValue - minValue ) <= Math.abs( nextValue - maxValue ) ? 'min' : 'max';

				event.preventDefault();
				startHandleDrag( slider, targetHandle, event );
			} );

			updatePriceSlider( slider );
		} );
	}

	function closeAllMenus( root ) {
		root.querySelectorAll( '.muukal-filter-item.is-open' ).forEach( function ( item ) {
			item.classList.remove( 'is-open' );
			const toggle = item.querySelector( '.dropdown-toggle' );

			if ( toggle ) {
				toggle.setAttribute( 'aria-expanded', 'false' );
			}
		} );
	}

	function bindDropdowns( root ) {
		root.querySelectorAll( '.muukal-filter-item > .dropdown-toggle' ).forEach( function ( toggle ) {
			toggle.addEventListener( 'click', function ( event ) {
				event.preventDefault();

				const item = toggle.closest( '.muukal-filter-item' );
				const nextState = ! item.classList.contains( 'is-open' );

				closeAllMenus( root );
				item.classList.toggle( 'is-open', nextState );
				toggle.setAttribute( 'aria-expanded', nextState ? 'true' : 'false' );
			} );
		} );

		document.addEventListener( 'click', function ( event ) {
			if ( event.target.closest( '.muukal-filter-archive' ) ) {
				return;
			}

			closeAllMenus( root );
		} );
	}

	function bindFilterOptions( root ) {
		root.querySelectorAll( '.filter-icon[fv][val]' ).forEach( function ( option ) {
			option.addEventListener( 'click', function () {
				const key = option.getAttribute( 'fv' );
				const value = option.getAttribute( 'val' );

				navigateWithParams( root, function ( params ) {
					if ( key === 'sort_by' ) {
						if ( params.get( key ) === value ) {
							params.delete( key );
						} else {
							params.set( key, value );
						}

						return;
					}

					const values = getValues( params, key );
					const nextValues = values.includes( value )
						? values.filter( function ( item ) {
							return item !== value;
						} )
						: values.concat( value );

					setValues( params, key, nextValues );
				} );
			} );
		} );

		root.querySelectorAll( '.filder-item-cross[fv]' ).forEach( function ( button ) {
			button.addEventListener( 'click', function () {
				const key = button.getAttribute( 'fv' );
				const value = button.getAttribute( 'val' );

				navigateWithParams( root, function ( params ) {
					if ( key === 'price' ) {
						params.delete( 'min_price' );
						params.delete( 'max_price' );
						return;
					}

					if ( key === 'sort_by' ) {
						params.delete( 'sort_by' );
						return;
					}

					const values = getValues( params, key ).filter( function ( item ) {
						return item !== value;
					} );

					setValues( params, key, values );
				} );
			} );
		} );
	}

	function bindPriceSubmit( root ) {
		root.querySelectorAll( '.muukal-price-submit' ).forEach( function ( button ) {
			button.addEventListener( 'click', function () {
				const panel = button.closest( '.price-filter' );
				const min = panel.querySelector( '.muukal-price-input-min' ).value;
				const max = panel.querySelector( '.muukal-price-input-max' ).value;

				navigateWithParams( root, function ( params ) {
					if ( min ) {
						params.set( 'min_price', min );
					} else {
						params.delete( 'min_price' );
					}

					if ( max ) {
						params.set( 'max_price', max );
					} else {
						params.delete( 'max_price' );
					}
				} );
			} );
		} );
	}

	document.addEventListener( 'DOMContentLoaded', function () {
		document.querySelectorAll( '.muukal-filter-archive' ).forEach( function ( root ) {
			bindDropdowns( root );
			bindPriceSliders( root );
			bindFilterOptions( root );
			bindPriceSubmit( root );
		} );
	} );
}() );
