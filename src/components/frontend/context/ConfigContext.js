/**
 * WordPress dependencies
 */
import {createContext, useEffect, useState} from '@wordpress/element';

export const defaultConfig = {
    title: '',
    symbol: '0',
    effect: 'none',
    layout: 'left',
    expand: '',
    orderby: 'name',
    orderdir: 'ASC',
    parent_expand: false,
    showcount: false,
    show_empty: false,
    include_or_exclude: 'include',
    categories: [],
    currentCategory: null,
};

export const ConfigContext = createContext(defaultConfig);

export const ConfigProvider = ({attributes, children}) => {
    const initialConfig = {...defaultConfig, ...attributes};
    const [config, updateContextConfig] = useState(initialConfig);

    const setConfig = (newConfig) => {
        const parsedConfig = {...newConfig};

        /* global jclCurrentCategory */
        if (typeof jclCurrentCategory !== 'undefined') {
            parsedConfig.currentPost = jclCurrentCategory;
        }

        // parsedConfig.parent_expand = !!parseInt(parsedConfig.parent_expand, 10);
        // parsedConfig.showcount = !!parseInt(parsedConfig.showcount, 10);
        // parsedConfig.show_empty = !!parseInt(parsedonfig.show_empty, 10);

        updateContextConfig(parsedConfig);
    };

    useEffect(() => {
      setConfig( attributes );
    }, [attributes])

    return (
        <ConfigContext.Provider value={{config, setConfig}}>
            {children}
        </ConfigContext.Provider>
    );
};

