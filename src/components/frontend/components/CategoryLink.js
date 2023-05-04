/**
 * WordPress dependencies
 */
import { useContext, useEffect, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { ConfigContext } from '../context/ConfigContext';
import Loading from '../components/Loading';

const CategoryLink = ( { category, loading } ) => {
    const { config } = useContext( ConfigContext );

    let linkContent = category.name;

    if ( config.showcount ) {
        linkContent += ` (${ category.count })`;
    }

    return (
        <a
            href={ category.url }
            title={ category.name }
        >
            { linkContent }
            <Loading loading={ loading } />
        </a>
    );
};

export default CategoryLink;
