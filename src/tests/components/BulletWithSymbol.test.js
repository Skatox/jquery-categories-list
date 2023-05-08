import { render } from '@testing-library/react';
import '@testing-library/jest-dom/extend-expect';
import { useSymbol } from '../../components/frontend/hooks/useFrontend';
import BulletWithSymbol from '../../components/frontend/components/BulletWithSymbol';
import {
	ConfigContext,
	defaultConfig,
} from '../../components/frontend/context/ConfigContext';

const category = {
	title: 'Test category',
	url: 'category-slug',
};

describe( 'Expand/Collapse symbol', () => {
	test( 'should display collapse symbol when expanded', () => {
		let i;
		for ( i = 1; i <= 3; i++ ) {
			const expand = true;
			const config = defaultConfig;
			config.symbol = i.toString();

			// eslint-disable-next-line
			const {collapseSymbol} = useSymbol(i);

			const { container } = render(
				<ConfigContext.Provider value={ { config } }>
					<BulletWithSymbol
						expanded={ expand }
						title={ category.title }
						permalink={ category.url }
						onToggle={ null }
					/>
				</ConfigContext.Provider>
			);

			const componentSymbol =
				container.querySelector( '.jcl_symbol' ).innerHTML;
			expect( componentSymbol ).toBe( collapseSymbol );
		}
	} );

	test( 'should display expand symbol when collapsed', () => {
		let i;
		for ( i = 1; i <= 3; i++ ) {
			const expand = false;
			const config = defaultConfig;
			config.symbol = i.toString();
			config.layout = 'left';

			// eslint-disable-next-line
			const {expandSymbol} = useSymbol(i, 'left');

			const { container } = render(
				<ConfigContext.Provider value={ { config } }>
					<BulletWithSymbol
						expanded={ expand }
						title={ category.title }
						permalink={ category.url }
						onToggle={ null }
					/>
				</ConfigContext.Provider>
			);

			const componentSymbol =
				container.querySelector( '.jcl_symbol' ).innerHTML;
			expect( componentSymbol ).toBe( expandSymbol );
		}
	} );

	test( 'should show right layout symbol for the triangle symbol', () => {
		const expand = false;
		const config = defaultConfig;
		config.symbol = '1'; // Triangle symbol
		config.layout = 'right';

		// eslint-disable-next-line
    const {expandSymbol} = useSymbol(config.symbol, 'right');

		const { container } = render(
			<ConfigContext.Provider value={ { config } }>
				<BulletWithSymbol
					expanded={ expand }
					title={ category.title }
					permalink={ category.url }
					onToggle={ null }
				/>
			</ConfigContext.Provider>
		);

		const componentSymbol =
			container.querySelector( '.jcl_symbol' ).innerHTML;
		expect( componentSymbol ).toBe( expandSymbol );
	} );

	test( 'should be hidden if no symbol is selected in config', () => {
		const config = defaultConfig;
		config.symbol = 0;

		const { queryByRole } = render(
			<ConfigContext.Provider value={ { config } }>
				<BulletWithSymbol
					expanded={ false }
					title={ category.title }
					permalink={ category.url }
					onToggle={ null }
				/>
			</ConfigContext.Provider>
		);

		expect( queryByRole( 'link' ) ).toBeNull();
	} );
} );
