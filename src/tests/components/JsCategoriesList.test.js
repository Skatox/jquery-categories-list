import { render } from '@testing-library/react';
import '@testing-library/jest-dom/extend-expect';
import {
	ConfigContext,
	defaultConfig,
} from '../../components/frontend/context/ConfigContext';
import useApi from '../../components/frontend/hooks/useApi';
import JsCategoriesList from '../../components/frontend/JsCategoriesList';

const testTitle = 'Test Title';
const noFoundText = 'There are no categories to show.';

jest.mock( '../../components/frontend/hooks/useApi', () =>
	jest.fn( () => ( {
		loading: false,
		data: null,
		apiClient: jest.fn(),
	} ) )
);

describe( 'Main component', () => {
	const categories = [
		{
			id: 100,
			count: 0,
			name: 'Test sub category A',
			url: 'test-sub-cat-a',
			child_num: 0,
		},
		{
			id: 101,
			count: 3,
			name: 'Test sub category B',
			url: 'test-sub-cat-b',
			child_num: 0,
		},
	];

	test( 'should display loading icon', () => {
		useApi.mockReturnValue( {
			loading: true,
			data: [],
			apiClient: jest.fn(),
		} );

		const { getByRole } = render(
			<ConfigContext.Provider value={ { config: defaultConfig } }>
				<JsCategoriesList />
			</ConfigContext.Provider>
		);

		const link = getByRole( 'progressbar' );
		expect( link ).toBeInTheDocument();
	} );

	test( 'should display no years found', () => {
		useApi.mockReturnValue( {
			loading: false,
			data: { categories: [] },
			apiClient: jest.fn(),
		} );

		const config = defaultConfig;

		const { getByRole } = render(
			<ConfigContext.Provider value={ { config } }>
				<JsCategoriesList />
			</ConfigContext.Provider>
		);

		const link = getByRole( 'list' );
		expect( link ).toHaveTextContent( noFoundText );
	} );

	test( 'should display list of categories', async () => {
		useApi.mockReturnValue( {
			loading: false,
			data: { categories },
			apiClient: jest.fn(),
		} );

		const config = defaultConfig;

		const { container } = render(
			<ConfigContext.Provider value={ { config } }>
				<JsCategoriesList />
			</ConfigContext.Provider>
		);

		// Post list should be empty
		const postList = container.querySelector( 'ul.jcl_widget' );
		expect( postList.children ).toHaveLength( categories.length );
	} );

	test( 'should display widget title', async () => {
		useApi.mockReturnValue( {
			loading: false,
			data: null,
			apiClient: jest.fn(),
		} );

		const config = defaultConfig;
		config.title = testTitle;

		const { getByText } = render(
			<ConfigContext.Provider value={ { config } }>
				<JsCategoriesList />
			</ConfigContext.Provider>
		);

		expect( getByText( testTitle ) ).toBeInTheDocument();
	} );
} );
