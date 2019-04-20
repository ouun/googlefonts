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
}
