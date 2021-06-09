/*!
 * iframe block
 *
 * @handle taro-iframe-block-editor
 * @deps wp-i18n, wp-components, wp-blocks, wp-block-editor, wp-server-side-render, wp-compose
 */

/* global TaroIframeBlockEditor:false */

const { registerBlockType } = wp.blocks;
const { __, sprintf } = wp.i18n;
const { InspectorControls } = wp.blockEditor;
const { PanelBody, ToggleControl, TextControl, TextareaControl, Button } = wp.components;
const { serverSideRender: ServerSideRender } = wp;
const { withState } = wp.compose;

const lineStyle = ( color = '#fff' ) => {
	return {
		fill: color,
		stroke: '#000',
		strokeMiterlimit: 10,
		strokeWidth: '10px',
	};
};

registerBlockType( 'taro/iframe-block', {

	title: __( 'iframe', 'taro-iframe-block' ),

	icon: (
		<svg viewBox="0 0 441 441">
			<rect x="5" y="5" width="431" height="431" rx="5" style={ lineStyle( '#e6e6e6' ) } />
			<circle cx="49" cy="44" r="19.5" style={ { fill: '#ff5f58' } } />
			<circle cx="109" cy="44" r="19.5" style={ { fill: '#ffbd2e' } } />
			<circle cx="169" cy="44" r="19.5" style={ { fill: '#39b54a' } } />
			<path d="M41,119h0Z" transform="translate(-35.5 -35.5)" style={ lineStyle( 'none' ) } />
			<rect x="59.5" y="160.5" width="322" height="198" style={ lineStyle() } />
			<polygon points="357.83 173.5 346.86 192.5 368.8 192.5 357.83 173.5" />
			<polygon points="357.83 348.83 368.8 329.83 346.86 329.83 357.83 348.83" />
			<line x1="335.5" y1="160.5" x2="335.5" y2="358.5" style={ lineStyle( 'none' ) } />
		</svg>
	),

	category: 'embed',

	supports: {
		alignWide: true,
		align: [ '', 'center', 'full', 'wide' ],
		className: true,
	},

	// Preview
	example: {
		src: 'https://wordpress.org',
		width: 320,
		height: 180
	},

	keywords: [ 'iframe', __( 'Embed', 'taro-iframe-block' ) ],

	attributes: TaroIframeBlockEditor,

	description: __( 'Add responsive iframe block which keep original aspect ratio.', 'taro-iframe-block' ),

	edit: withState( {
		html: '',
	} )( ( { attributes, setAttributes, setState, html } ) => {

		const convertHtmlToOptions = ( string ) => {
			if ( ! string.match( /<iframe ([^>]+)\/?>/i ) ) {
				// error.
				console.log( 'error' );
				return;
			}
			const newAttr = {};
			const other = [];
			let updated = false;
			RegExp.$1.split( ' ' ).forEach( ( part ) => {
				part = part.trim();
				if ( 'allowfullscreen' === part ) {
					newAttr.fullscreen = true;
					updated = true;
					return true;
				} else if ( ! part.match( /^([^=]+)=['"]([^'"]+)['"]$/i ) ) {
					return true;
				} else if ( attributes.hasOwnProperty( RegExp.$1 ) ) {
					newAttr[ RegExp.$1 ] = RegExp.$2;
				} else {
					other.push( part );
				}
				updated = true;
			} );
			if ( other.length ) {
				newAttr.other = other.join( ' ' );
			}
			// If changed, update.attributes.
			if ( updated ) {
				setState( { html: '' }, setAttributes( newAttr ) );
			}
		};

		const IframeInserter = () => {
			return (
				<>
					<TextareaControl label={ __( 'iframe tag', 'taro-iframe-block' ) } value={ html }
						onChange={ ( newHtml ) => setState( { html: newHtml } ) }
						help={ __( 'Paste html tag here and convert into options.',  'taro-iframe-block' ) }
						placeholder={ 'e.g. <iframe src="https://example.com" width="640" height="480" />' } rows={ 4 } />
					<Button onClick={ () => convertHtmlToOptions( html ) } isSecondary>
						{ __( 'Convert', 'taro-iframe-block' ) }
					</Button>
				</>
			);
		};
		let responsiveHelp;
		if ( attributes.responsive ) {
			if ( /^\d+$/.test( attributes.width ) && /^\d+$/.test( attributes.height ) && 0 < attributes.width * attributes.height ) {
				let ratio = attributes.height / attributes.width * 100;
				responsiveHelp = sprintf( __( 'Current aspect ratio:  %s', 'taro-iframe-bock' ), Math.floor( ratio ) + '%' );
			} else {
				responsiveHelp = __( 'Failed to convert aspect ratio. It will be 16:9(56.25%, default)', 'taro-iframe-bock' );
			}
		} else {
			responsiveHelp = __( 'iframe will be display in specified width and height.', 'taro-iframe-bock' );
		}
		return (
			<>
				<InspectorControls>
					<PanelBody defaultOpen={ true } title={ __( 'Display Setting', 'taro-iframe-block' ) } >
						<TextControl type="url" label={ __( 'SRC attribute', 'taro-iframe-block' ) } value={ attributes.src } onChange={ src => setAttributes( { src } ) } />
						<TextControl label={ __( 'Width', 'taro-iframe-block' ) } value={ attributes.width } onChange={ width => setAttributes( { width } ) } />
						<TextControl label={ __( 'Height', 'taro-iframe-block' ) } value={ attributes.height } onChange={ height => setAttributes( { height } ) } />
						<ToggleControl checked={ attributes.responsive } label={ __( 'Responsive', 'taro-iframe-block' ) } onChange={ responsive => setAttributes( { responsive } ) }
							help={ responsiveHelp } />
						<ToggleControl checked={ attributes.fullscreen } label={ __( 'Allow Fullscreen', 'taro-iframe-block' ) } onChange={ fullscreen => setAttributes( { fullscreen } ) } />
						<TextControl label={ __( 'Other Attributes', 'taro-iframe-block' ) }
							placeholder={ 'e.g. id="frame" name="my-map"' }
							help={ __( 'Add other attribute here.', 'taro-iframe-block' ) }
							value={ attributes.other } onChange={ other => setAttributes( { other } ) } />
					</PanelBody>
					<PanelBody defaultOpen={ false } title={ __( 'Converter', 'taro-iframe-block' ) }>
						<IframeInserter />
					</PanelBody>
				</InspectorControls>
				{ ( ! attributes.src.length ) ? (
					<div className="taro-iframe-block-editor">
						<IframeInserter />
					</div>
				) : (
					<ServerSideRender block="taro/iframe-block" attributes={ attributes } />
				) }
			</>
		);
	} ),

	save() {
		return null;
	},
} );