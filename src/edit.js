/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import {
	CheckboxControl,
	SelectControl,
	TextControl,
	Panel,
	PanelBody,
	PanelRow,
	RadioControl,
} from '@wordpress/components';

/**
 * Internal dependencies
 */
import './editor.css';
import CategoryPicker from './components/admin/CategoryPicker';
import JsCategoriesList from './components/frontend/JsCategoriesList';
import { ConfigProvider } from './components/frontend/context/ConfigContext';

export default function Edit( { attributes, setAttributes } ) {
	const categories = Array.isArray( attributes.categories )
		? attributes.categories
		: [];

	return (
		<div { ...useBlockProps() }>
			<ConfigProvider attributes={ attributes }>
				<JsCategoriesList />
			</ConfigProvider>
			<InspectorControls key="setting">
				<div className="jcl-controls">
					<Panel>
						<PanelBody
							title={ __( 'General options', 'jquery-categories-list' ) }
							initialOpen={ true }
						>
							<TextControl
								label={ __( 'Title', 'jquery-categories-list' ) }
								value={ attributes.title }
								onChange={ ( val ) =>
									setAttributes( { title: val } )
								}
							/>
							<SelectControl
								label={ __( 'Trigger Symbol', 'jquery-categories-list' ) }
								value={ attributes.symbol }
								onChange={ ( val ) =>
									setAttributes( { symbol: val } )
								}
								options={ [
									{
										value: '0',
										label: __( 'Empty Space', 'jquery-categories-list' ),
									},
									{ value: '1', label: '► ▼' },
									{ value: '2', label: '(+) (–)' },
									{ value: '3', label: '[+] [–]' },
								] }
							/>
							<SelectControl
								label={ __( 'Symbol position', 'jquery-categories-list' ) }
								value={ attributes.layout }
								onChange={ ( val ) =>
									setAttributes( { layout: val } )
								}
								options={ [
									{
										value: 'left',
										label: __( 'Left', 'jquery-categories-list' ),
									},
									{
										value: 'right',
										label: __( 'Right', 'jquery-categories-list' ),
									},
								] }
							/>
							<SelectControl
								label={ __( 'Effect', 'jquery-categories-list' ) }
								value={ attributes.effect }
								onChange={ ( val ) =>
									setAttributes( { effect: val } )
								}
								options={ [
									{
										value: 'none',
										label: __( 'None', 'jquery-categories-list' ),
									},
									{
										value: 'slide',
										label: __(
											'Slide (Accordion)',
											'jquery-categories-list'
										),
									},
									{
										value: 'fade',
										label: __( 'Fade', 'jquery-categories-list' ),
									},
								] }
							/>
							<SelectControl
								label={ __( 'Order by', 'jquery-categories-list' ) }
								value={ attributes.orderby }
								onChange={ ( val ) =>
									setAttributes( { orderby: val } )
								}
								options={ [
									{
										value: 'name',
										label: __( 'Name', 'jquery-categories-list' ),
									},
									{
										value: 'id',
										label: __( 'Category ID', 'jquery-categories-list' ),
									},
									{
										value: 'count',
										label: __(
											'Entries count',
											'jquery-categories-list'
										),
									},
									{
										value: 'slug',
										label: __( 'Slug', 'jquery-categories-list' ),
									},
								] }
							/>
							<SelectControl
								label={ __( 'Order direction', 'jquery-categories-list' ) }
								value={ attributes.orderdir }
								onChange={ ( val ) =>
									setAttributes( { orderdir: val } )
								}
								options={ [
									{
										value: 'ASC',
										label: __( 'ASC', 'jquery-categories-list' ),
									},
									{
										value: 'DESC',
										label: __( 'DESC', 'jquery-categories-list' ),
									},
								] }
							/>
							<SelectControl
								label={ __( 'Expand', 'jquery-categories-list' ) }
								value={ attributes.expand }
								onChange={ ( val ) =>
									setAttributes( { expand: val } )
								}
								options={ [
									{
										value: '',
										label: __( 'None', 'jquery-categories-list' ),
									},
									{
										value: 'all',
										label: __(
											'All (warning: requires too many ajax calls on load)',
											'jquery-categories-list'
										),
									},
									{
										value: 'sel_cat',
										label: __(
											'Selected category',
											'jquery-categories-list'
										),
									},
								] }
							/>
						</PanelBody>
					</Panel>
					<Panel>
						<PanelBody
							title={ __( 'Extra options', 'jquery-categories-list' ) }
							initialOpen={ false }
						>
							<PanelRow>
								<CheckboxControl
									label={ __(
										'Show number of posts',
										'jquery-categories-list'
									) }
									checked={ attributes.showcount }
									onChange={ ( val ) =>
										setAttributes( { showcount: val } )
									}
								/>
							</PanelRow>
							<PanelRow>
								<CheckboxControl
									label={ __(
										'Show empty categories',
										'jquery-categories-list'
									) }
									checked={ attributes.show_empty }
									onChange={ ( val ) =>
										setAttributes( { show_empty: val } )
									}
								/>
							</PanelRow>
						</PanelBody>
					</Panel>
					<Panel>
						<PanelBody
							title={ __( 'Category management', 'jquery-categories-list' ) }
							initialOpen={ false }
						>
							<PanelRow>
								<RadioControl
									label={ __(
										'Include or exclude',
										'jquery-categories-list'
									) }
									selected={ attributes.include_or_exclude }
									options={ [
										{
											label: __(
												'Include the following categories',
												'jquery-categories-list'
											),
											value: 'include',
										},
										{
											label: __(
												'Exclude the following categories',
												'jquery-categories-list'
											),
											value: 'exclude',
										},
									] }
									onChange={ ( val ) =>
										setAttributes( {
											include_or_exclude: val,
										} )
									}
								/>
							</PanelRow>
							<PanelRow>
								<CategoryPicker
									selectedCats={ categories }
									onSelected={ ( val ) =>
										setAttributes( { categories: val } )
									}
								/>
							</PanelRow>
						</PanelBody>
					</Panel>
				</div>
			</InspectorControls>
		</div>
	);
}
