<?php
/**
 * Plugin Name: Taro iframe Block
 * Plugin URI: https://wordpress.org/plugins/taro-iframe-block/
 * Description: Add iframe block for block editor.
 * Author: Tarosky INC.
 * Version: nightly
 * Requires at least: 5.9
 * Requires PHP: 7.4
 * Author URI: https://tarosky.co.jp/
 * License: GPL3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: taro-iframe-block
 */

defined( 'ABSPATH' ) or die();

/**
 * Register assets from wp-dependencies.json.
 */
function taro_iframe_block_register_assets() {
	$json = __DIR__ . '/wp-dependencies.json';
	if ( ! file_exists( $json ) ) {
		return;
	}
	$dependencies = json_decode( file_get_contents( $json ), true );
	if ( empty( $dependencies ) ) {
		return;
	}
	$base = trailingslashit( plugin_dir_url( __FILE__ ) );
	foreach ( $dependencies as $dep ) {
		if ( empty( $dep['path'] ) ) {
			continue;
		}
		$url = $base . $dep['path'];
		switch ( $dep['ext'] ) {
			case 'css':
				wp_register_style( $dep['handle'], $url, $dep['deps'], $dep['hash'], $dep['media'] );
				break;
			case 'js':
				$footer = [ 'in_footer' => $dep['footer'] ];
				if ( in_array( $dep['strategy'], [ 'defer', 'async' ], true ) ) {
					$footer['strategy'] = $dep['strategy'];
				}
				wp_register_script( $dep['handle'], $url, $dep['deps'], $dep['hash'], $footer );
				if ( in_array( 'wp-i18n', $dep['deps'], true ) ) {
					wp_set_script_translations( $dep['handle'], 'taro-iframe-block' );
				}
				break;
		}
	}
}

/**
 * Register block and assets.
 */
function taro_iframe_block_assets() {
	taro_iframe_block_register_assets();
	register_block_type( 'taro/iframe-block', [
		'attributes'      => taro_iframe_option(),
		'render_callback' => 'taro_iframe_callback',
	] );
}

/**
 * Enqueue assets for editor.
 */
function taro_iframe_enqueue_editor() {
	wp_enqueue_script( 'taro-iframe-block-editor' );
	// see https://wordpress.slack.com/archives/C02RP50LK/p1635254887019500
	wp_localize_script( 'taro-iframe-block-editor', 'TaroIframeBlockEditor', taro_iframe_option() );
	wp_enqueue_style( 'taro-iframe-block-editor' );
}

/**
 * Enqueue assets for public.
 */
function taro_iframe_enqueue_theme() {
	wp_enqueue_style( 'taro-iframe-block' );
}

/**
 * Option for iframe block.
 *
 * @return array[]
 */
function taro_iframe_option() {
	return [
		'src'        => [
			'type'    => 'string',
			'default' => '',
		],
		'width'      => [
			'type'    => 'string',
			'default' => '',
		],
		'height'     => [
			'type'    => 'string',
			'default' => '',
		],
		'title'      => [
			'type'    => 'string',
			'default' => '',
		],
		'responsive' => [
			'type'    => 'boolean',
			'default' => true,
		],
		'loading'    => [
			'type'    => 'string',
			'default' => 'lazy',
		],
		'fullscreen' => [
			'type'    => 'boolean',
			'default' => false,
		],
		'other'      => [
			'type'    => 'string',
			'default' => '',
		],
		'align'      => [
			'type'    => 'string',
			'default' => '',
		],
	];
}

/**
 * Render dynamic block.
 *
 * @param array  $attributes Options.
 * @param string $content    Body.
 *
 * @return string
 */
function taro_iframe_callback( $attributes = [], $content = '' ) {
	// Create default args.
	$default = [];
	foreach ( taro_iframe_option() as $key => $option ) {
		$default[ $key ] = $option['default'];
	}
	$attributes = wp_parse_args( $attributes, $default );
	if ( ! $attributes['src'] ) {
		return '';
	}
	// Build attributes.
	$html_attributes = [];
	foreach ( [ 'src', 'width', 'height', 'title', 'loading' ] as $key ) {
		if ( ! empty( $attributes[ $key ] ) ) {
			$html_attributes[] = sprintf( '%s="%s"', $key, esc_attr( $attributes[ $key ] ) );
		}
	}
	// Create iframe.
	if ( $attributes['fullscreen'] ) {
		$html_attributes[] = 'allowfullscreen';
	}
	// Add other props.
	if ( ! empty( $attributes['other'] ) ) {
		$html_attributes[] = $attributes['other'];
	}
	$html_attributes = implode( ' ', $html_attributes );
	// Create html
	$iframe        = sprintf( '<iframe %s></iframe>', $html_attributes );
	$wrapper_class = [ 'taro-iframe-block-wrapper' ];
	if ( ! empty( $attributes['className'] ) ) {
		$wrapper_class[] = $attributes['className'];
	}
	switch ( $attributes['align'] ) {
		case 'full':
		case 'wide':
		case 'center':
			$wrapper_class[] = 'align' . $attributes['align'];
			break;
	}
	$block = '';
	if ( $attributes['responsive'] ) {
		$wrapper_class[] = 'taro-iframe-responsive';
		// Calculate width and height;
		$ratio = 0;
		if ( is_numeric( $attributes['width'] ) && is_numeric( $attributes['height'] ) && $attributes['width'] && $attributes['height'] ) {
			$ratio = $attributes['height'] / $attributes['width'] * 100;
		}
		$block = sprintf( '<div class="taro-iframe-responsive-spacer"%s></div>', ( 0 < $ratio ? sprintf( ' style="padding-top: %f%%;"', $ratio ) : '' ) );
		$block = apply_filters( 'taro_iframe_block_spacer_html', $block, $attributes, $ratio );
	}
	$wrapper_class = apply_filters( 'taro_iframe_block_wrapper_class', $wrapper_class, $attributes );
	return sprintf(
		'<div class="%1$s">%2$s %3$s</div>',
		esc_attr( implode( ' ', $wrapper_class ) ),
		wp_kses( $block, [ 'div' => [ 'class' => [], 'style' => [] ] ] ),
		wp_kses( $iframe, [
			'iframe' => [
				'src'             => [],
				'width'           => [],
				'height'          => [],
				'id'              => [],
				'class'           => [],
				'loading'         => [],
				'allowfullscreen' => [],
				'allow'           => [],
				'frameborder'     => [],
				'sandbox'         => [],
				'referrerpolicy'  => [],
				'title'           => [],
				'style'           => [],
				'name'            => [],
				'tabindex'        => [],
				'csp'             => [],
			],
		] )
	);
}

// Register hooks.
add_action( 'init', 'taro_iframe_block_assets' );
add_action( 'enqueue_block_editor_assets', 'taro_iframe_enqueue_editor' );
add_action( 'enqueue_block_assets', 'taro_iframe_enqueue_theme' );
