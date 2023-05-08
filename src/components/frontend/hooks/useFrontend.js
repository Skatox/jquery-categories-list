/**
 * WordPress dependencies
 */
import { useState, useEffect } from '@wordpress/element';

export function useDisplayClass( { expand, effect } ) {
	const [ className, setClassName ] = useState( '' );

	useEffect( () => {
		switch ( effect ) {
			case 'fade':
				setClassName( expand ? 'jcl-fade-in' : 'jcl-fade-out' );
				break;
			case 'slide':
				setClassName( expand ? 'jcl-slide-down' : 'jcl-slide-up' );
				break;
			default:
				setClassName( expand ? 'jcl-show' : 'jcl-hide' );
				break;
		}
	}, [ expand, effect ] );

	return className;
}

export function useSymbol( symbol, layout = 'left' ) {
	let collapseSymbol = '';
	let expandSymbol = '';

	switch ( symbol.toString() ) {
		case '1':
			collapseSymbol = '▼';
			expandSymbol = layout === 'left' ? '►' : '◄';
			break;
		case '2':
			collapseSymbol = '(–)';
			expandSymbol = '(+)';
			break;
		case '3':
			collapseSymbol = '[–]';
			expandSymbol = '[+]';
			break;
	}

	return { collapseSymbol, expandSymbol };
}

export function initialExpand( config, categoryId ) {
	if ( config.expand === 'all' ) return true;

	return (
		config.expand === 'sel_cat' &&
		config.expandCategories.includes( categoryId )
	);
}
