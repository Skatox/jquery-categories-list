/**
 * Internal dependencies
 */
import { ConfigProvider } from './context/ConfigContext';
import JsCategoriesList from './JsCategoriesList';

const App = ( { attributes } ) => {
	return (
		<ConfigProvider attributes={ attributes }>
			<JsCategoriesList />
		</ConfigProvider>
	);
};

export default App;
