// CategoryLink.test.js
import { render, screen } from '@testing-library/react';
import '@testing-library/jest-dom/extend-expect';
import CategoryLink from '../../components/frontend/components/CategoryLink';
import {
	ConfigContext,
	defaultConfig,
} from '../../components/frontend/context/ConfigContext';

const category = {
	id: 1,
	name: 'Test Category',
	url: 'test-url',
	count: 11,
};

describe( 'Category', () => {
	test( 'should display the category link', () => {
		render(
			<ConfigContext.Provider value={ { config: defaultConfig } }>
				<CategoryLink category={ category } />
			</ConfigContext.Provider>
		);

		expect( screen.getByText( category.name ) ).toHaveTextContent(
			category.name
		);

		const linkElement = screen.getByRole( 'link', {
			href: category.url,
		} );
		expect( linkElement ).toBeInTheDocument();
	} );

	test( 'should not display count', () => {
		const config = defaultConfig;
		config.showcount = false;

		const { getByRole } = render(
			<ConfigContext.Provider value={ { config } }>
				<CategoryLink category={ category } />
			</ConfigContext.Provider>
		);

		const linkElement = getByRole( 'link', {
			href: category.url,
		} );

		expect( linkElement ).toHaveTextContent( category.name );
	} );

	test( 'should display category count after name', () => {
		const config = defaultConfig;
		config.showcount = true;

		const { getByRole } = render(
			<ConfigContext.Provider value={ { config } }>
				<CategoryLink category={ category } />
			</ConfigContext.Provider>
		);

		const linkElement = getByRole( 'link', {
			href: category.url,
		} );

		const linkContent = `${ category.name } (${ category.count })`;
		expect( linkElement ).toHaveTextContent( linkContent );
	} );
} );
