/**
 * WordPress dependencies
 */
import { useContext, useEffect, useMemo, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { ConfigContext } from '../context/ConfigContext';
import useApi from '../hooks/useApi';
import { useDisplayClass, initialExpand } from '../hooks/useFrontend';

import BulletWithSymbol from './BulletWithSymbol';
import CategoryLink from './CategoryLink';
import Loading from './Loading';

const DisplayCategory = ( { category } ) => {
	const {
		loading,
		data: apiData,
		apiClient: loadCategories,
	} = useApi( '/jcl/v1/categories' );

	const { config } = useContext( ConfigContext );
  const initialExpandVal = initialExpand( config, category.id ) ;
	const [ expand, setExpand ] = useState( initialExpandVal );

	const isLayoutLeft = useMemo( () => config.layout === 'left', [ config ] );
	const displayClass = useDisplayClass( { expand, effect: config.effect } );

	const handleToggle = ( event ) => {
		event.preventDefault();
		setExpand( ! expand );
	};

	const Toggler = () => {
		const totalChildCategories = parseInt( category.child_num, 10 );

		return totalChildCategories > 0 ? (
			<BulletWithSymbol
				expanded={ expand }
				permalink={ category.url }
				title={ category.name }
				onToggle={ handleToggle }
			/>
		) : (
			<div className="jcl_symbol no_child"></div>
		);
	};

	useEffect( () => {
		if ( expand && ( ! apiData || ! Array.isArray( apiData.categories ) ) ) {
			loadCategories( config, category.id );
		}
		// eslint-disable-next-line react-hooks/exhaustive-deps
	}, [ expand ] );

	const className = `jcl_category jcl_category__${ category.id } ${ displayClass }`;

	return (
		<li>
			{ isLayoutLeft ? <Toggler /> : '' }
			<CategoryLink category={ category } />
			{ ! isLayoutLeft ? <Toggler /> : '' }
			<Loading loading={ loading } />
			{ apiData && apiData.categories.length > 0 ? (
				<ul className={ className }>
					{ apiData.categories.map( ( category ) => (
						<DisplayCategory
							key={ category.id }
							category={ category }
						/>
					) ) }
				</ul>
			) : (
				''
			) }
		</li>
	);
};

export default DisplayCategory;
