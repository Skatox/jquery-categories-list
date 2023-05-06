/**
 * WordPress dependencies
 */
import { createContext, useEffect, useState } from '@wordpress/element';
import JsCategoriesList from '../JsCategoriesList';

export const defaultConfig = {
	title: '',
	symbol: '0',
	effect: 'none',
	layout: 'left',
	expand: '',
	orderby: 'name',
	orderdir: 'ASC',
	showcount: false,
	show_empty: false,
	include_or_exclude: 'include',
	expandCategories: [],
};

export const ConfigContext = createContext( defaultConfig );

export const ConfigProvider = ( { attributes, children } ) => {
	const initialConfig = { ...defaultConfig, ...attributes };
	const [ config, updateContextConfig ] = useState( initialConfig );

	const setConfig = ( newConfig ) => {
		const parsedConfig = { ...defaultConfig, ...newConfig };
    
    /* global jclCurrentCat */
    if ( typeof jclCurrentCat !== 'undefined' ) {
      parsedConfig.expandCategories = jclCurrentCat.split(",").map(Number);
    }

		updateContextConfig( parsedConfig );
	};

	useEffect( () => {
		setConfig( attributes );
	}, [ attributes ] );

	return (
		<ConfigContext.Provider value={ { config, setConfig } }>
			{ children }
		</ConfigContext.Provider>
	);
};
