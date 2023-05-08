/**
 * WordPress dependencies
 */
import { useState } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';

/**
 * Internal dependencies
 *
 * @param {string} url
 */
export default function useApi( url ) {
	const [ data, setData ] = useState( null );
	const [ error, setError ] = useState( null );
	const [ loading, setLoading ] = useState( false );

	/* global jclCurrentCat */
	const apiClient = async function ( config, parent = 0 ) {
		setLoading( true );

		const params = new URLSearchParams( {
			orderby: config.orderby,
			orderdir: config.orderdir,
			parent,
			showEmpty: config.show_empty,
			taxonomy: 'category',
			type: 'post',
		} );

		if ( typeof jclCurrentCat !== 'undefined' && config.onlycategory > 0 ) {
			params.append( 'currentCat', jclCurrentCat );
		}

		if ( config.categories ) {
			params.append( 'exclusionType', config.include_or_exclude );
			params.append( 'cats', config.categories );
		}

		return apiFetch( { path: `${ url }?${ params.toString() }` } )
			.then( ( response ) => {
				setData( response );
				setLoading( false );
			} )
			.catch( ( e ) => {
				setLoading( false );
				setError( e );
			} );
	};

	return { apiClient, data, error, loading, setLoading };
}
