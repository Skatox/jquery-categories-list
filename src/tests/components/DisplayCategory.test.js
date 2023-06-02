import { fireEvent, render } from '@testing-library/react';
import '@testing-library/jest-dom/extend-expect';
import DisplayCategory from '../../components/frontend/components/DisplayCategory';
import {
	ConfigContext,
	defaultConfig,
} from '../../components/frontend/context/ConfigContext';
import useApi from '../../components/frontend/hooks/useApi';
import { useSymbol } from '../../components/frontend/hooks/useFrontend';

jest.mock( '../../components/frontend/hooks/useApi', () =>
	jest.fn( () => ( {
		loading: false,
		data: null,
		apiClient: jest.fn(),
	} ) )
);

describe( 'Display categories', () => {
	const category = {
		id: 12,
		count: 10,
		name: 'Test category',
		url: 'test-permalink',
		child_num: 3,
	};

	const subCategories = [
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

	const animationFunction = jest.fn();

	beforeEach( () => {
		jest.clearAllMocks();
	} );

	test( 'should render category link', () => {
		const config = defaultConfig;
		useApi.mockReturnValue( {
			loading: false,
			data: null,
			apiClient: jest.fn(),
		} );
		const { getByRole } = render(
			<ConfigContext.Provider value={ { config } }>
				<DisplayCategory
					category={ category }
					animationFunction={ animationFunction }
				/>
			</ConfigContext.Provider>
		);

		const link = getByRole( 'link' );
		expect( link ).toHaveTextContent( category.name );
		expect( link ).toHaveAttribute( 'title', category.name );
		expect( link ).toHaveAttribute( 'href', category.url );
	} );

	test( 'should show loading icon when loading is true', async () => {
		useApi.mockReturnValue( {
			loading: true,
			data: null,
			apiClient: jest.fn(),
		} );

		const { findByRole } = render(
			<ConfigContext.Provider value={ { config: defaultConfig } }>
				<DisplayCategory
					category={ category }
					animationFunction={ animationFunction }
				/>
			</ConfigContext.Provider>
		);

		expect( await findByRole( 'progressbar' ) ).toBeInTheDocument();
	} );

	test( 'should not render loading icon when loading is false', async () => {
		useApi.mockReturnValue( {
			loading: false,
			data: null,
			apiClient: jest.fn(),
		} );

		const noChildCategory = { ...category, child_num: 0 };

		const { queryByRole } = render(
			<ConfigContext.Provider value={ { config: defaultConfig } }>
				<DisplayCategory
					category={ noChildCategory }
					animationFunction={ animationFunction }
				/>
			</ConfigContext.Provider>
		);

		expect( queryByRole( 'progressbar' ) ).not.toBeInTheDocument();
	} );

	it( 'should render the toggler if the category has child categories', () => {
		const config = defaultConfig;
		config.symbol = '1';
		config.layout = 'left';
		const symbol = useSymbol( '1', config.layout );

		const { queryByText } = render(
			<ConfigContext.Provider value={ { config } }>
				<DisplayCategory
					category={ category }
					animationFunction={ animationFunction }
				/>
			</ConfigContext.Provider>
		);
		expect( queryByText( symbol.expandSymbol ) ).toBeInTheDocument();
	} );

	it( 'does not render the toggler if the category has no child categories', () => {
		const noChildCategory = { ...category, child_num: 0 };
		const config = defaultConfig;
		config.symbol = '1';
		config.layout = 'left';
		const symbol = useSymbol( config.symbol, config.layout );

		const { queryByText } = render(
			<ConfigContext.Provider value={ { config } }>
				<DisplayCategory
					category={ noChildCategory }
					animationFunction={ animationFunction }
				/>
			</ConfigContext.Provider>
		);

		expect( queryByText( symbol.expandSymbol ) ).not.toBeInTheDocument();
	} );

	test( 'should render categories under link when expanded', async () => {
		const config = defaultConfig;
		config.symbol = '2';
		const symbol = useSymbol( config.symbol );
		// Mock API call with posts
		useApi.mockReturnValue( {
			loading: false,
			data: { categories: subCategories },
			apiClient: jest.fn(),
		} );

		const { container, getByText } = render(
			<ConfigContext.Provider value={ { config } }>
				<DisplayCategory
					category={ category }
					animationFunction={ animationFunction }
				/>
			</ConfigContext.Provider>
		);

		fireEvent.click( getByText( symbol.expandSymbol ) );

		expect( animationFunction ).toHaveBeenCalled();

		const postList = container.querySelector( 'ul.jcl-hide' );
		expect( postList.children ).toHaveLength( subCategories.length );
	} );

	test( 'should show total posts next to link', () => {
		const config = defaultConfig;
		config.symbol = 0;
		config.showcount = true;

		useApi.mockReturnValue( {
			loading: false,
			data: null,
			apiClient: jest.fn(),
		} );

		const { getByRole } = render(
			<ConfigContext.Provider value={ { config } }>
				<DisplayCategory
					category={ category }
					animationFunction={ animationFunction }
				/>
			</ConfigContext.Provider>
		);

		const link = getByRole( 'link' );
		expect( link ).toHaveTextContent(
			`${ category.name } (${ category.count })`
		);
	} );

	test( 'should show symbol on the left side', async () => {
		const config = defaultConfig;
		config.layout = 'left';
		config.symbol = '2';
		const symbol = useSymbol( config.symbol );

		const { queryAllByRole } = render(
			<ConfigContext.Provider value={ { config } }>
				<DisplayCategory category={ category } />
			</ConfigContext.Provider>
		);

		const links = queryAllByRole( 'link' );

		expect( links[ 0 ] ).toHaveTextContent( symbol.expandSymbol );
	} );

	test( 'should show symbol on the right side', async () => {
		const config = defaultConfig;
		config.layout = 'right';
		config.symbol = '2';
		const symbol = useSymbol( config.symbol );

		const { queryAllByRole } = render(
			<ConfigContext.Provider value={ { config } }>
				<DisplayCategory category={ category } />
			</ConfigContext.Provider>
		);

		const links = queryAllByRole( 'link' );

		expect( links[ 1 ] ).toHaveTextContent( symbol.expandSymbol );
	} );
} );
