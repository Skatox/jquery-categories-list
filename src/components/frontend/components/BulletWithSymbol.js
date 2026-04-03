/**
 * WordPress dependencies
 */
import { useContext } from '@wordpress/element';

/**
 * Internal dependencies
 */
import { ConfigContext } from '../context/ConfigContext';
import { useSymbol } from '../hooks/useFrontend';

const BulletWithSymbol = ( { expanded, title, permalink, onToggle } ) => {
	const { config } = useContext( ConfigContext );
	const { expandSymbol, collapseSymbol } = useSymbol(
		config.symbol,
		config.layout
	);

	if ( config.symbol.toString() === '0' ) {
		return null;
	}

	const expandedClass = 'jcl_symbol ' + ( expanded ? 'expanded' : '' );
	const symbol = expanded ? collapseSymbol : expandSymbol;

	// Do not show the component if it's disabled in the options.
	if ( config.symbol.toString() === '0' ) {
		return '';
	}

	return (
		<a
			href={ permalink }
			title={ title }
			className={ expandedClass }
			target={ config.open_in_new_page ? '_blank' : undefined }
			rel={ config.open_in_new_page ? 'noopener noreferrer' : undefined }
			onClick={ onToggle }
		>
			{ symbol }
		</a>
	);
};

export default BulletWithSymbol;
