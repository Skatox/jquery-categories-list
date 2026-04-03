/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { SelectControl } from '@wordpress/components';
import { useEffect, useState } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';

const CategoryPicker = ( { selectedCats, onSelected, postType = 'post' } ) => {
	const [ categories, setCategories ] = useState( [] );
	const [ isLoading, setIsLoading ] = useState( true );

	useEffect( () => {
		let cancelled = false;
		setIsLoading( true );

		apiFetch( {
			path: `/jcl/v1/category-options?postType=${ encodeURIComponent(
				postType
			) }`,
		} )
			.then( ( response ) => {
				if ( cancelled ) {
					return;
				}

				setCategories( response?.categories ?? [] );
				setIsLoading( false );
			} )
			.catch( () => {
				if ( cancelled ) {
					return;
				}

				setCategories( [] );
				setIsLoading( false );
			} );

		return () => {
			cancelled = true;
		};
	}, [ postType ] );

	if ( isLoading ) {
		return <h3>{ __( 'Loading categories…', 'jquery-categories-list' ) }</h3>;
	}

	if ( categories.length === 0 ) {
		return <p>{ __( 'No categories found.', 'jquery-categories-list' ) }</p>;
	}

	return (
		<SelectControl
			hideLabelFromVision
			multiple
			options={ categories.map( ( { id, name } ) => ( {
				label: name,
				value: id,
			} ) ) }
			onChange={ ( selected ) => {
				onSelected( selected );
			} }
			style={ { minWidth: '250px', height: '100px' } }
			value={ selectedCats }
		/>
	);
};

export default CategoryPicker;
