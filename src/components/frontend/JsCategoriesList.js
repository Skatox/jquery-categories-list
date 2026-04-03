/**
 * WordPress dependencies
 */
import { useContext, useEffect, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import DisplayCategory from './components/DisplayCategory.js';
import useAnimation from './hooks/useAnimation';
import { ConfigContext } from './context/ConfigContext';
import useApi from './hooks/useApi';

import Loading from './components/Loading';

const JsCategoriesList = () => {
	const { config } = useContext( ConfigContext );
	const [ loaded, setLoaded ] = useState( false );
	const {
		loading,
		data: apiData,
		error,
		apiClient: loadCategories,
	} = useApi( '/jcl/v1/categories' );

	const animationFunction = useAnimation( config.effect );

	useEffect( () => {
		setLoaded( false );
		const request = loadCategories( config );

		if ( request && typeof request.then === 'function' ) {
			request.then( ( result ) => {
				if ( result !== false ) {
					setLoaded( true );
				}
			} );
		} else if ( request !== false ) {
			setLoaded( true );
		}
		// eslint-disable-next-line react-hooks/exhaustive-deps
	}, [ config ] );

	useEffect( () => {
		if ( ! loaded && error ) {
			setLoaded( true );
		}
	}, [ loaded, error ] );

	return (
		<div className={ `js-categories-list layout-${ config.layout }` }>
			<h2>{ config.title }</h2>
			{ loading ? (
				<div>
					<Loading loading={ loading } />
					{ __( 'Loading…', 'jquery-categories-list' ) }
				</div>
			) : (
				''
			) }
			{ ! loading && apiData && apiData.categories ? (
				<ul className="jcl_widget">
					{ apiData.categories.length === 0 ? (
						<li>
							{ __(
								'There are no categories to show.',
								'jquery-categories-list'
							) }
						</li>
					) : (
						apiData.categories.map( ( category ) => (
							<DisplayCategory
								key={ category.id }
								category={ category }
								animationFunction={ animationFunction }
							/>
						) )
					) }
				</ul>
			) : (
				''
			) }
			{ ( loaded || error ) && ! apiData
				? __( 'Cannot load categories.', 'jquery-categories-list' )
				: '' }
		</div>
	);
};

export default JsCategoriesList;
