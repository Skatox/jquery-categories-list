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
							title={ __( 'General options', 'jcl_i18n' ) }
							initialOpen={ true }
						>
							<TextControl
								label={ __( 'Title', 'jcl_i18n' ) }
								value={ attributes.title }
								onChange={ ( val ) =>
									setAttributes( { title: val } )
								}
							/>
							<SelectControl
								label={ __( 'Trigger Symbol', 'jcl_i18n' ) }
								value={ attributes.symbol }
								onChange={ ( val ) =>
									setAttributes( { symbol: val } )
								}
								options={ [
									{
										value: '0',
										label: __( 'Empty Space', 'jcl_i18n' ),
									},
									{ value: '1', label: '► ▼' },
									{ value: '2', label: '(+) (–)' },
									{ value: '3', label: '[+] [–]' },
								] }
							/>
							<SelectControl
								label={ __( 'Symbol position', 'jcl_i18n' ) }
								value={ attributes.layout }
								onChange={ ( val ) =>
									setAttributes( { layout: val } )
								}
								options={ [
									{
										value: 'left',
										label: __( 'Left', 'jcl_i18n' ),
									},
									{
										value: 'right',
										label: __( 'Right', 'jcl_i18n' ),
									},
								] }
							/>
							<SelectControl
								label={ __( 'Effect', 'jcl_i18n' ) }
								value={ attributes.effect }
								onChange={ ( val ) =>
									setAttributes( { effect: val } )
								}
								options={ [
									{
										value: 'none',
										label: __( 'None', 'jcl_i18n' ),
									},
									{
										value: 'slide',
										label: __(
											'Slide( Accordion )',
											'jcl_i18n'
										),
									},
									{
										value: 'fade',
										label: __( 'Fade', 'jcl_i18n' ),
									},
								] }
							/>
							<SelectControl
								label={ __( 'Order by', 'jcl_i18n' ) }
								value={ attributes.orderby }
								onChange={ ( val ) =>
									setAttributes( { orderby: val } )
								}
								options={ [
									{
										value: 'name',
										label: __( 'Name', 'jcl_i18n' ),
									},
									{
										value: 'id',
										label: __( 'Category ID', 'jcl_i18n' ),
									},
									{
										value: 'count',
										label: __(
											'Entries count',
											'jcl_i18n'
										),
									},
									{
										value: 'slug',
										label: __( 'Slug', 'jcl_i18n' ),
									},
								] }
							/>
							<SelectControl
								label={ __( 'Order direction', 'jcl_i18n' ) }
								value={ attributes.orderdir }
								onChange={ ( val ) =>
									setAttributes( { orderdir: val } )
								}
								options={ [
									{
										value: 'ASC',
										label: __( 'ASC', 'jcl_i18n' ),
									},
									{
										value: 'DESC',
										label: __( 'DESC', 'jcl_i18n' ),
									},
								] }
							/>
							<SelectControl
								label={ __( 'Expand', 'jcl_i18n' ) }
								value={ attributes.expand }
								onChange={ ( val ) =>
									setAttributes( { expand: val } )
								}
								options={ [
									{
										value: '',
										label: __( 'None', 'jcl_i18n' ),
									},
									{
										value: 'all',
										label: __(
											'All (warning: requires too many ajax calls on load)',
											'jcl_i18n'
										),
									},
									{
										value: 'sel_cat',
										label: __(
											'Selected category',
											'jcl_i18n'
										),
									},
								] }
							/>
						</PanelBody>
					</Panel>
					<Panel>
						<PanelBody
							title={ __( 'Extra options', 'jcl_i18n' ) }
							initialOpen={ false }
						>
							<PanelRow>
								<CheckboxControl
									label={ __(
										'Show number of posts',
										'jcl_i18n'
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
										'jcl_i18n'
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
							title={ __( 'Category management', 'jcl_i18n' ) }
							initialOpen={ false }
						>
							<PanelRow>
								<RadioControl
									label={ __(
										'Include or exclude',
										'jcl_i18n'
									) }
									selected={ attributes.include_or_exclude }
									options={ [
										{
											label: __(
												'Include the following categories',
												'jcl_i18n'
											),
											value: 'include',
										},
										{
											label: __(
												'Exclude the following categories ',
												'jcl_i18n'
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
