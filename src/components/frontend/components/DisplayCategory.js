/**
 * WordPress dependencies
 */
import {
	useContext,
	useEffect,
	useMemo,
	useRef,
	useState,
} from '@wordpress/element';

/**
 * Internal dependencies
 */
import { ConfigContext } from '../context/ConfigContext';
import useApi from '../hooks/useApi';
import { initialExpand } from '../hooks/useFrontend';

import BulletWithSymbol from './BulletWithSymbol';
import CategoryLink from './CategoryLink';
import Loading from './Loading';

const DisplayCategory = ( { category, animationFunction } ) => {
	const {
		loading,
		data: apiData,
		apiClient: loadCategories,
	} = useApi( '/jcl/v1/categories' );

	const { config } = useContext( ConfigContext );
	const [ expand, setExpand ] = useState(
		initialExpand( config, category.id )
	);

	const isLayoutLeft = useMemo( () => config.layout === 'left', [ config ] );
	const hasChilds = parseInt( category.child_num, 10 ) > 0;
	const listElement = useRef( null );

	const handleToggle = async ( event ) => {
		event.preventDefault();

		if ( ! apiData || ! Array.isArray( apiData.categories ) ) {
			await loadCategories( config, category.id );
		}

		setExpand( ! expand );
	};

	const animateList = () => {
		const categoriesList = [ ...listElement.current.children ].filter(
			( ch ) => ch.nodeName.toLowerCase() === 'ul'
		);

		if ( categoriesList.length > 0 )
			animationFunction( categoriesList[ 0 ] );
	};

	const Toggler = () => {
		return hasChilds ? (
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
		if (
			expand &&
			( ! apiData || ! Array.isArray( apiData.categories ) )
		) {
			loadCategories( config, category.id );
		}

		animateList();
		// eslint-disable-next-line react-hooks/exhaustive-deps
	}, [ expand ] );

	const childClassName = initialExpand( config, category.id )
		? ''
		: 'jcl-hide';

	return (
		<li ref={ listElement }>
			{ isLayoutLeft ? <Toggler /> : '' }
			<CategoryLink category={ category } />
			{ ! isLayoutLeft ? <Toggler /> : '' }
			<Loading loading={ loading } />
			{ hasChilds && apiData && apiData.categories.length > 0 ? (
				<ul className={ childClassName }>
					{ apiData.categories.map( ( subCategory ) => (
						<DisplayCategory
							key={ subCategory.id }
							category={ subCategory }
							animationFunction={ animationFunction }
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
