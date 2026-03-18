<?php
/**
 * Test iframe block render callback.
 *
 * @package taro-iframe-block
 */

class TestRender extends WP_UnitTestCase {

	/**
	 * Test empty src returns empty string.
	 */
	public function test_empty_src_returns_empty() {
		$this->assertSame( '', taro_iframe_callback( [] ) );
	}

	/**
	 * Test empty src attribute returns empty string.
	 */
	public function test_explicit_empty_src_returns_empty() {
		$this->assertSame( '', taro_iframe_callback( [ 'src' => '' ] ) );
	}

	/**
	 * Test basic iframe is rendered with src.
	 */
	public function test_basic_iframe_rendered() {
		$result = taro_iframe_callback( [ 'src' => 'https://example.com' ] );
		$this->assertStringContainsString( '<iframe', $result );
		$this->assertStringContainsString( 'src="https://example.com"', $result );
	}

	/**
	 * Test responsive wrapper class is added by default.
	 */
	public function test_responsive_class_by_default() {
		$result = taro_iframe_callback( [ 'src' => 'https://example.com' ] );
		$this->assertStringContainsString( 'taro-iframe-responsive', $result );
	}

	/**
	 * Test responsive ratio calculation.
	 */
	public function test_responsive_ratio_calculation() {
		$result = taro_iframe_callback( [
			'src'    => 'https://example.com',
			'width'  => '640',
			'height' => '480',
		] );
		// 480/640*100 = 75
		$this->assertStringContainsString( 'padding-top:', $result );
		$this->assertMatchesRegularExpression( '/padding-top:\s*75/', $result );
	}

	/**
	 * Test no padding-top when dimensions are missing.
	 */
	public function test_no_ratio_without_dimensions() {
		$result = taro_iframe_callback( [ 'src' => 'https://example.com' ] );
		$this->assertStringNotContainsString( 'padding-top:', $result );
	}

	/**
	 * Test non-responsive mode removes responsive class.
	 */
	public function test_non_responsive_mode() {
		$result = taro_iframe_callback( [
			'src'        => 'https://example.com',
			'responsive' => false,
		] );
		$this->assertStringNotContainsString( 'taro-iframe-responsive', $result );
		$this->assertStringNotContainsString( 'taro-iframe-responsive-spacer', $result );
	}

	/**
	 * Test fullscreen attribute is added.
	 */
	public function test_fullscreen_attribute() {
		$result = taro_iframe_callback( [
			'src'        => 'https://example.com',
			'fullscreen' => true,
		] );
		$this->assertStringContainsString( 'allowfullscreen', $result );
	}

	/**
	 * Test no fullscreen by default.
	 */
	public function test_no_fullscreen_by_default() {
		$result = taro_iframe_callback( [ 'src' => 'https://example.com' ] );
		$this->assertStringNotContainsString( 'allowfullscreen', $result );
	}

	/**
	 * Test alignment classes.
	 */
	public function test_alignment_full() {
		$result = taro_iframe_callback( [
			'src'   => 'https://example.com',
			'align' => 'full',
		] );
		$this->assertStringContainsString( 'alignfull', $result );
	}

	/**
	 * Test wide alignment class.
	 */
	public function test_alignment_wide() {
		$result = taro_iframe_callback( [
			'src'   => 'https://example.com',
			'align' => 'wide',
		] );
		$this->assertStringContainsString( 'alignwide', $result );
	}

	/**
	 * Test center alignment class.
	 */
	public function test_alignment_center() {
		$result = taro_iframe_callback( [
			'src'   => 'https://example.com',
			'align' => 'center',
		] );
		$this->assertStringContainsString( 'aligncenter', $result );
	}

	/**
	 * Test custom className is added.
	 */
	public function test_custom_class_name() {
		$result = taro_iframe_callback( [
			'src'       => 'https://example.com',
			'className' => 'my-custom-class',
		] );
		$this->assertStringContainsString( 'my-custom-class', $result );
	}

	/**
	 * Test loading attribute.
	 */
	public function test_loading_attribute() {
		$result = taro_iframe_callback( [
			'src'     => 'https://example.com',
			'loading' => 'lazy',
		] );
		$this->assertStringContainsString( 'loading="lazy"', $result );
	}

	/**
	 * Test title attribute.
	 */
	public function test_title_attribute() {
		$result = taro_iframe_callback( [
			'src'   => 'https://example.com',
			'title' => 'Test Frame',
		] );
		$this->assertStringContainsString( 'title="Test Frame"', $result );
	}

	/**
	 * Test wrapper always has base class.
	 */
	public function test_wrapper_has_base_class() {
		$result = taro_iframe_callback( [ 'src' => 'https://example.com' ] );
		$this->assertStringContainsString( 'taro-iframe-block-wrapper', $result );
	}

	/**
	 * Test width and height attributes are rendered.
	 */
	public function test_width_height_attributes() {
		$result = taro_iframe_callback( [
			'src'    => 'https://example.com',
			'width'  => '800',
			'height' => '600',
		] );
		$this->assertStringContainsString( 'width="800"', $result );
		$this->assertStringContainsString( 'height="600"', $result );
	}
}
