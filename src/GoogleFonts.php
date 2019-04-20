<?php
/**
 * Processes typography-related fields
 * and generates the google-font link.
 *
 * @package   kirki-framework/googlefonts
 * @author    Ari Stathopoulos (@aristath)
 * @copyright Copyright (c) 2019, Ari Stathopoulos (@aristath)
 * @license   https://opensource.org/licenses/MIT
 * @since     0.1
 */

namespace Kirki;

/**
 * Manages the way Google Fonts are enqueued.
 */
final class GoogleFonts {

	/**
	 * An array of our google fonts.
	 *
	 * @static
	 * @access public
	 * @since 0.1
	 * @var array
	 */
	public static $google_fonts;

	/**
	 * The class constructor.
	 *
	 * @access public
	 * @since 0.1
	 */
	public function __construct() {
		if ( function_exists ( 'add_action' ) ) {
			add_action( 'wp_ajax_kirki_fonts_google_all_get', [ $this, 'print_googlefonts_json' ] );
			add_action( 'wp_ajax_nopriv_kirki_fonts_google_all_get', [ $this, 'print_googlefonts_json' ] );
		}
	}

	/**
	 * Prints the googlefonts JSON file.
	 *
	 * @since 0.1
	 * @return void
	 */
	public function print_googlefonts_json() {
		include 'webfonts.json'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude
		if ( function_exists( 'wp_die' ) ) {
			wp_die();
		}
	}

	/**
	 * Return an array of all available Google Fonts.
	 *
	 * @access public
	 * @since 0.1
	 * @return array All Google Fonts.
	 */
	public function get_google_fonts() {

		// Get fonts from cache.
		if ( function_exists( 'get_site_transient' ) ) {
			self::$google_fonts = get_site_transient( 'kirki_googlefonts_cache' );
		}

		// If cache is populated, return cached fonts array.
		if ( self::$google_fonts ) {
			return self::$google_fonts;
		}

		// If we got this far, cache was empty so we need to get from JSON.
		ob_start();
		include 'webfonts.json'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude

		$fonts_json = ob_get_clean();
		$fonts      = json_decode( $fonts_json, true );

		self::$google_fonts = [];
		if ( is_array( $fonts ) ) {
			foreach ( $fonts['items'] as $font ) {
				self::$google_fonts[ $font['family'] ] = [
					'label'    => $font['family'],
					'variants' => $font['variants'],
					'category' => $font['category'],
				];
			}
		}

		$cache_time = 360;
		// Apply the 'kirki_fonts_google_fonts' filter.
		if ( function_exists( 'apply_filters' ) ) {
			self::$google_fonts = apply_filters( 'kirki_fonts_google_fonts', self::$google_fonts );
			
			// Save the array in cache.
			$cache_time = apply_filters( 'kirki_googlefonts_transient_time', $cache_time );
		}
		if ( function_exists( 'set_site_transient' ) ) {
			set_site_transient( 'kirki_googlefonts_cache', self::$google_fonts, $cache_time );
		}

		return self::$google_fonts;
	}
}
