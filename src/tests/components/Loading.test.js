import { render, screen } from '@testing-library/react';
import '@testing-library/jest-dom';
import Loading from '../../components/frontend/components/Loading';

describe( 'Loading', () => {
	test( 'should show component when is loading', () => {
		const loading = true;

		render( <Loading loading={ loading } /> );
		expect( screen.getByRole( 'progressbar' ) ).toBeTruthy();
	} );

	test( 'should hide component when is not loading', () => {
		const loading = false;

		render( <Loading loading={ loading } /> );
		expect( screen.queryByRole( 'progressbar' ) ).toBeNull();
	} );
} );
