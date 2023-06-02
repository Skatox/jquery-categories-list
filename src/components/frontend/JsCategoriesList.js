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
		loadCategories( config );
		// eslint-disable-next-line react-hooks/exhaustive-deps
	}, [] );

	useEffect( () => {
		if ( ! loaded && ( error || loaded ) ) {
			setLoaded( true );
		}
	}, [ loaded, error ] );

	return (
		<div className={ `js-categories-list layout-${ config.layout }` }>
			<h2>{ config.title }</h2>
			{ loading ? (
				<div>
					<Loading loading={ loading } />
					{ __( 'Loading…', 'jcl_i18n' ) }
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
								'jcl_i18n'
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
				? __( 'Cannot load categories.', 'jcl_i18n' )
				: '' }
		</div>
	);
};

export default JsCategoriesList;
