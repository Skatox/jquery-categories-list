/**
 * WordPress dependencies
 */
import domReady from '@wordpress/dom-ready';
import { render } from '@wordpress/element';

/**
 * Internal dependencies
 */
import App from './components/frontend/App';

domReady( function () {
	const jclInstances = document.querySelectorAll(
		'.wp-block-jquery-categories-list-categories-block'
	);

	jclInstances.forEach( ( container ) => {
		const attributes = { ...container.dataset };
		render( <App attributes={ attributes } />, container );
	} );
} );
