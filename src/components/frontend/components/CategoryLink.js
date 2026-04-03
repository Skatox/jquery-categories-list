/**
 * WordPress dependencies
 */
import { useContext } from '@wordpress/element';

/**
 * Internal dependencies
 */
import { ConfigContext } from '../context/ConfigContext';

const CategoryLink = ( { category } ) => {
	const { config } = useContext( ConfigContext );

	let linkContent = category.name;

	if ( config.showcount ) {
		linkContent += ` (${ category.count })`;
	}

	return (
		<a
			href={ category.url }
			title={ category.name }
			target={ config.open_in_new_page ? '_blank' : undefined }
			rel={ config.open_in_new_page ? 'noopener noreferrer' : undefined }
		>
			{ linkContent }
		</a>
	);
};

export default CategoryLink;
