const jclFunctions = {
	isVisible( e ) {
		return e.classList.contains( 'jcl-fade-in' ) ||
			e.classList.contains( 'jcl-slide-down' ) ||
			e.classList.contains( 'jcl-show' );
	},
	hideOpened( clickedObj, classes, options ) {
		const parentSiblings = jclFunctions.siblings( clickedObj.parentNode, 'li' );

		if ( Array.isArray( parentSiblings ) ) {
			parentSiblings.forEach( ( sibling ) => {
				if ( sibling.classList.contains( 'expanded' ) && sibling.children.length > 0 ) {
					for ( let i = 0; i < sibling.children.length; i++ ) {
						if ( sibling.children[ i ].tagName === 'UL' ) {
							const changeSymbol = jclCreateChangeSymbol( sibling.children[ 0 ], options );
							jclFunctions.toggleClass( clickedObj, [ sibling.children[ i ] ], classes );
							changeSymbol( sibling.children[ i ] );
						}
					}
					sibling.classList.remove( 'expanded' );
				}
			} );
		}
	},
	siblings( el, filterType ) {
		if ( el.parentNode === null ) {
			return [];
		}

		return [ ...el.parentElement.children ].filter( ( ch ) => {
			return el !== ch && ch.nodeName.toLowerCase() === filterType.toLowerCase();
		} );
	},
	toggleClass( clickedObj, listElements, classes, changeSymbol ) {
		listElements.forEach( ( listElement ) => {
			if ( listElement.classList.contains( classes.show ) ) {
				listElement.classList.remove( classes.show );
				listElement.classList.add( classes.hide );

				if ( classes.hide === 'jcl-slide-up' ) {
					listElement.style.height = 0;
				}
			} else {
				listElement.classList.remove( classes.hide );
				listElement.classList.add( classes.show );

				if ( classes.show === 'jcl-slide-down' ) {
					clickedObj.closest( 'ul' ).style.height = 'auto';

					listElement.style.height = Array.prototype.reduce.call( listElement.childNodes, function( p, c ) {
						return p + ( c.offsetHeight || 0 );
					}, 0 ) + 'px';
				}
			}

			if ( changeSymbol ) {
				changeSymbol( listElement );
			}
		} );
	},
};

function jclCreateChangeSymbol( clickedObj, options ) {
	return function( ele ) {
		for ( const child of clickedObj.children ) {
			if ( child.matches( '.jcl_symbol' ) ) {
				child.innerHTML = jclFunctions.isVisible( ele ) ? options.conSym : options.expSym;
			}
		}
	};
}

function jsCategoriesListAnimate( clickedObj, listElements, options ) {
	const changeSymbol = jclCreateChangeSymbol( clickedObj, options );
	let classes;

	switch ( options.fxIn ) {
		case 'fadeIn':
			classes = { show: 'jcl-fade-in', hide: 'jcl-fade-out' };
			break;
		case 'slideDown':
			classes = { show: 'jcl-slide-down', hide: 'jcl-slide-up' };
			break;
		default:
			classes = { show: 'jcl-show', hide: 'jcl-hide' };
			break;
	}

	if ( options.accordion > 0 ) {
		jclFunctions.hideOpened( clickedObj, classes, options );
	}

	jclFunctions.toggleClass( clickedObj, listElements, classes, changeSymbol );

	if ( clickedObj.parentNode.classList.contains( 'expanded' ) ) {
		clickedObj.parentNode.classList.remove( 'expanded' );
	} else {
		clickedObj.parentNode.classList.add( 'expanded' );
	}
}

function jsCategoriesListClickEvent( options ) {
	return function( e ) {
		for ( let target = e.target; target && target !== this; target = target.parentNode ) {
			if ( target.matches( '.jcl_link' ) ) {
				const itemsToAnimate = jclFunctions.siblings( target, 'ul' );

				if ( itemsToAnimate.length ) {
					e.preventDefault();
					jsCategoriesListAnimate( target, itemsToAnimate, options );
					break;
				}
			}
		}
	};
}

/**
* Assigns event listeners to the archive list.
*/
function jsArchiveListEvents() {
	document.querySelectorAll( '.jcl_widget.legacy.preload' ).forEach( ( widget ) => {
		widget.classList.remove( 'preload' );
		
    const options = {
			fxIn: widget.dataset.fx_in,
			expSym: widget.dataset.ex_sym,
			conSym: widget.dataset.con_sym,
		};

		widget.addEventListener( 'click', jsCategoriesListClickEvent( options ), false );
	} );
}

/**
 * Event listener for clicks, it will start applying animation and expanding items
 * if clicked element belows to the widget.
 */
document.addEventListener( 'DOMContentLoaded', jsArchiveListEvents );
	

if ( ! jQuery( this ).siblings( '.parent_expand' ).length )
			elementsClicked = 'a.jcl_link';

