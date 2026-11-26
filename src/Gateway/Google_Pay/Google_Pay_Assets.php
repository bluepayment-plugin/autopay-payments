<?php // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.ShortPrefixPassed -- "bm" in package name is a pre-existing convention across this codebase.
/**
 * Google_Pay_Assets — registers the Google Pay loader script for both checkouts.
 *
 * @package Ilabs\BM_Woocommerce\Gateway\Google_Pay
 */

namespace Ilabs\BM_Woocommerce\Gateway\Google_Pay;

defined( 'ABSPATH' ) || exit;

/**
 * The only place that knows how the Google Pay scripts reach the browser.
 *
 * Neither `pay.js` nor the ATP helper is enqueued by WordPress any more: they are injected
 * from the browser, by `AutopayGooglePayLoader`, at the exact point where the channel is
 * kept in the DOM (AP-183). Only this small local loader is enqueued, and only where the
 * policy already allows Google Pay -- it issues no external request on its own.
 */
final class Google_Pay_Assets {

	/**
	 * Script handle of the loader. Also used as a Checkout Block script dependency, so the
	 * loader is guaranteed to be defined before the block bundle runs.
	 */
	public const LOADER_HANDLE = 'autopay-google-pay-loader';

	/**
	 * Register the loader and hand it the URLs it must inject.
	 *
	 * Registration only -- callers decide whether to enqueue it (classic checkout) or to
	 * declare it as a bundle dependency (Checkout Block).
	 *
	 * @return string The registered handle.
	 */
	public static function register_loader(): string {
		if ( ! wp_script_is( self::LOADER_HANDLE, 'registered' ) ) {
			wp_register_script(
				self::LOADER_HANDLE,
				blue_media()->get_plugin_js_url() . '/google-pay-loader.js',
				[],
				blue_media()->get_plugin_version(),
				false
			);

			wp_localize_script(
				self::LOADER_HANDLE,
				'autopayGooglePayLoader',
				[
					'payJsUrl'       => 'https://pay.google.com/gp/p/js/pay.js',
					'atpJsUrl'       => blue_media()->get_plugin_js_url() . '/google-pay-atp.js',
					'readyTimeoutMs' => 10000,
				]
			);
		}

		return self::LOADER_HANDLE;
	}

	/**
	 * Register and enqueue the loader (classic checkout).
	 *
	 * @return void
	 */
	public static function enqueue_loader(): void {
		wp_enqueue_script( self::register_loader() );
	}
}
