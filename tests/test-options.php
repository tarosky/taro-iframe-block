<?php
/**
 * Test block option schema.
 *
 * @package taro-iframe-block
 */

class TestOptions extends WP_UnitTestCase {

	/**
	 * Test default options have all expected keys.
	 */
	public function test_default_options_keys() {
		$options = taro_iframe_option();
		$expected_keys = [ 'src', 'width', 'height', 'title', 'responsive', 'loading', 'fullscreen', 'other', 'align' ];
		$this->assertSame( $expected_keys, array_keys( $options ) );
	}

	/**
	 * Test default values.
	 */
	public function test_default_values() {
		$options = taro_iframe_option();
		$this->assertSame( '', $options['src']['default'] );
		$this->assertTrue( $options['responsive']['default'] );
		$this->assertSame( 'lazy', $options['loading']['default'] );
		$this->assertFalse( $options['fullscreen']['default'] );
	}
}
