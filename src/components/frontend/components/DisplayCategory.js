/**
 * WordPress dependencies
 */
import { useContext, useEffect, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { ConfigContext } from '../context/ConfigContext';
import useApi from '../hooks/useApi';

import BulletWithSymbol from './BulletWithSymbol';
import CategoryLink from './CategoryLink';

const DisplayCategory = ( { category } ) => {
	const { config, setConfig } = useContext( ConfigContext );
	const [ expand, setExpand ] = useState( false );
	// const [ loaded, setLoaded ] = useState( false );
	const {
		loading,
		data: apiData,
		apiClient: loadCategories,
	} = useApi( '/jcl/v1/categories' );

  const Toggler = () => (
      <BulletWithSymbol
				expanded={ expand }
				title={ category.name }
				permalink={ category.url }
				onToggle={ () => setExpand( !expand ) }
			/>
  );

 //  useEffect( () => {
	// 	if ( expand && ( ! apiData || ! Array.isArray( apiData.months ) ) ) {
	// 		apiClient( config );
	// 	}
	// 	// eslint-disable-next-line react-hooks/exhaustive-deps
	// }, [] );

	return (
    <li>
      { config.layout === 'left' ? <Toggler /> : '' }
      <CategoryLink category={ category } />
			{ apiData && category.count > 0 ? (
				<ul className={ `jcl_categories jcl_category__${ category.id }` }>
				</ul>
			) : (
				''
			) }
      { config.layout === 'right' ? <Toggler /> : '' }
		</li>
	);
};

export default DisplayCategory;

