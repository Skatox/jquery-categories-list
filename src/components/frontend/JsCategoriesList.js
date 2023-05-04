/**
 * WordPress dependencies
 */
import { useContext, useEffect, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import DisplayCategory from './components/DisplayCategory.js';
import { ConfigContext } from './context/ConfigContext';
import useApi from './hooks/useApi';

import Loading from './components/Loading';

const JsCategoriesList = ( { attributes } ) => {
	const { config, setConfig } = useContext( ConfigContext );
	const [ loaded, setLoaded ] = useState( false );
	const {
		loading,
		data: apiData,
		error,
		apiClient: loadCategories,
	} = useApi( '/jcl/v1/categories' );

	/* eslint-disable react-hooks/exhaustive-deps */
	useEffect( () => {
		setConfig( { ...config, ...attributes } );
		loadCategories( config, 0 );
	}, [] );

	useEffect( () => {
		setConfig( { ...config, ...attributes } );
    loadCategories( config );
	}, [ attributes ] );

	useEffect( () => {
		if ( ! loaded && ( error || loaded ) ) {
			setLoaded( true );
		}
	}, [ loaded, error ] );

	return (
		<div className="js-categories-list dynamic">
			<h2>{ config.title }</h2>
			{ loading ? <div><Loading loading={ loading } />{ __( 'Loading…', 'jcl_i18n' ) }</div> : '' }
			{ apiData && apiData.categories ? (
				<ul className="jcl_widget">
					{ apiData.categories.length === 0 ? (
						<li>{ __( 'There are no categories to show.', 'jcl_i18n' ) }</li>
					) : (
						apiData.categories.map( ( category ) => (
							<DisplayCategory
								key={ category.id }
								category={ category }
							/>
						) )
					) }
				</ul>
			) : ''
			}
			{ ( loaded || error ) && ! apiData ? __( 'Cannot load categories.', 'jcl_i18n' ) : '' }
		</div>
	);
};

export default JsCategoriesList;
