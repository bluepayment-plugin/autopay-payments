<?php // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.ShortPrefixPassed -- "bm" in package name is a pre-existing convention across this codebase.
/**
 * Google_Pay_Script_Policy — decides whether the Google Pay scripts may be loaded.
 *
 * @package Ilabs\BM_Woocommerce\Gateway\Google_Pay
 */

namespace Ilabs\BM_Woocommerce\Gateway\Google_Pay;

defined( 'ABSPATH' ) || exit;

use Exception;
use Ilabs\BM_Woocommerce\Domain\Service\Gateway_List\Gateway_List_Channels;
use Ilabs\BM_Woocommerce\Domain\Service\Settings\Settings_Manager;
use Ilabs\BM_Woocommerce\Gateway\Blue_Media_Gateway;
use Ilabs\BM_Woocommerce\Gateway\Currency\Payment_Currency_Context;
use Ilabs\BM_Woocommerce\Gateway\Recurring\Recurring_Context;

/**
 * The single decision about loading `pay.js` and `google-pay-atp.js`.
 *
 * The scripts are only loaded when the native Google Pay button can actually render,
 * so a shop without the native flow never issues a request to pay.google.com and never
 * gets Google cookies (AP-183).
 *
 * The two checkouts answer two of the conditions differently -- classic reads the
 * WooCommerce terms API, Checkout Block parses the block attributes, and each knows its
 * own page context -- so those two answers are passed in by the caller and every other
 * condition lives here, in one place.
 */
final class Google_Pay_Script_Policy {

	/**
	 * Filter: hard off-switch for the Google Pay scripts, independent of SID configuration.
	 *
	 * `add_filter( 'autopay_filter_can_load_google_pay_scripts', '__return_false' );`
	 */
	public const FILTER = 'autopay_filter_can_load_google_pay_scripts';

	/**
	 * The main Autopay gateway instance.
	 *
	 * @var Blue_Media_Gateway
	 */
	private Blue_Media_Gateway $gateway;

	/**
	 * Constructor.
	 *
	 * @param Blue_Media_Gateway $gateway The main Autopay gateway instance.
	 */
	public function __construct( Blue_Media_Gateway $gateway ) {
		$this->gateway = $gateway;
	}

	/**
	 * Whether the Google Pay scripts may be loaded for the current request.
	 *
	 * Conditions are evaluated cheapest-first, so the channel list is only fetched once
	 * every local condition has already passed.
	 *
	 * @param bool $is_checkout_context     Caller's answer to "is this the checkout page
	 *                                      (and not the cart)" -- the Cart block shares its
	 *                                      script handles with the Checkout block, so this
	 *                                      must be answered per integration.
	 * @param bool $terms_checkbox_enabled  Caller's answer to "does this checkout show the
	 *                                      terms & conditions checkbox Google Pay requires".
	 *
	 * @return bool
	 */
	public function can_load( bool $is_checkout_context, bool $terms_checkbox_enabled ): bool {
		$result = $is_checkout_context
			&& $terms_checkbox_enabled
			&& $this->gateway->is_whitelabel_mode_enabled()
			&& $this->is_native_gpay_mode_enabled()
			&& $this->is_ordinary_cart()
			&& $this->is_channel_available();

		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound -- self::FILTER is the literal, autopay_-prefixed string above; phpcs cannot resolve the class constant statically.
		return (bool) apply_filters( self::FILTER, $result );
	}

	/**
	 * Whether Google Pay is configured as the native (non-redirect) flow for the currency
	 * THIS payment is in.
	 *
	 * In the default `with_redirect` mode Google Pay is an ordinary channel handled by the
	 * Autopay gateway page, and `pay.js` is not involved at all.
	 *
	 * @return bool
	 */
	private function is_native_gpay_mode_enabled(): bool {
		// The payment's own currency, not get_woocommerce_currency(): on order-pay that
		// is the order's currency, which WooCommerce's shop-level currency never follows.
		return 'without_redirect' === $this->gateway->get_option(
			Settings_Manager::get_currency_option_key( 'gpay_type',
				( new Payment_Currency_Context() )->resolve_code() ),
			'with_redirect'
		);
	}

	/**
	 * Whether the current cart/request can render channel 1512 at all.
	 *
	 * `ORDINARY` is the only recurring classification that renders it:
	 * `SUPPORTED_SUBSCRIPTION_FIRST_PAYMENT` keeps only the recurring channels
	 * ({@see \Ilabs\BM_Woocommerce\Domain\Service\Gateway_List\Gateway_List_Recurring_Filter::filter()}),
	 * `UNSUPPORTED_SUBSCRIPTION_CART` renders a blocking message instead of a channel list,
	 * and `RENEWAL_ORDER` charges the saved instrument with no channel choice at all.
	 *
	 * @return bool
	 */
	private function is_ordinary_cart(): bool {
		return Recurring_Context::ORDINARY === $this->gateway
			->get_recurring_context()
			->classify_current_request();
	}

	/**
	 * Whether the Google Pay channel (1512) is offered for the current currency, i.e.
	 * whether the merchant configuration (SID) actually includes it.
	 *
	 * Fails open: when the channel list is unreachable the shop configuration alone
	 * decides, so a gatewayList outage never disables a working Google Pay.
	 *
	 * @return bool
	 */
	private function is_channel_available(): bool {
		try {
			$response = $this->gateway->gateway_list();
		} catch ( Exception $e ) {
			blue_media()
				->get_woocommerce_logger()
				->log_error( sprintf(
					'[Google_Pay_Script_Policy] gateway list unavailable: %s',
					$e->getMessage()
				) );

			return true;
		}

		return Gateway_List_Channels::contains( $response, Blue_Media_Gateway::GPAY_CHANNEL );
	}
}
