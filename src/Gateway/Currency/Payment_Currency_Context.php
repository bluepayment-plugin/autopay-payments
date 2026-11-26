<?php // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.ShortPrefixPassed -- "bm" in package name is a pre-existing convention across this codebase.
/**
 * Payment_Currency_Context — which currency a payment is in, and whether Autopay can take it.
 *
 * @package Ilabs\BM_Woocommerce\Gateway\Currency
 */

namespace Ilabs\BM_Woocommerce\Gateway\Currency;

defined( 'ABSPATH' ) || exit;

use Ilabs\BM_Woocommerce\Domain\Service\Currency\Interfaces\Currency_Interface;
use WC_Order;

/**
 * Single source of truth for the currency a payment is denominated in.
 *
 * The rule used to live in three disagreeing places: plugin registration read the latched
 * shop currency, the gateway list read the same latched value, and the outbound request read
 * the order. This class answers all three questions in one place:
 *
 * - {@see self::resolve_code()}  — which currency is THIS payment in?
 * - {@see self::is_configured()} — can this store take Autopay payments in it?
 * - {@see self::bind()}          — make it the request-global context.
 *
 * `resolve_code()` deliberately reads `get_woocommerce_currency()` rather than
 * `blue_media()->resolve_blue_media_currency_symbol()`. The latter memoises into a static on
 * its first read of the PHP request, which `Plugin::init()` performs on `init`:10 — before a
 * multi-currency plugin (WPML/WCML and friends) has resolved the customer's currency. Reading
 * the latched value is what let an order in EUR be sent to Autopay as PLN (AP-184).
 * `get_woocommerce_currency()` applies the `woocommerce_currency` filter on every call, so it
 * follows whatever currency the customer is actually shopping in.
 */
class Payment_Currency_Context {

	/**
	 * Currency the payment in the current request is denominated in.
	 *
	 * On the order-pay endpoint that is the order's own currency — the order already exists
	 * and its total is fixed, so nothing else may override it. Everywhere else (cart,
	 * checkout, Store API) it is the live WooCommerce currency.
	 *
	 * @return string ISO-4217 code; empty string when it cannot be determined.
	 */
	public function resolve_code(): string {
		$order = $this->order_pay_order();

		if ( $order instanceof WC_Order ) {
			$order_currency = (string) $order->get_currency();

			if ( '' !== $order_currency ) {
				return $order_currency;
			}
		}

		if ( ! function_exists( 'get_woocommerce_currency' ) ) {
			return '';
		}

		return (string) get_woocommerce_currency();
	}

	/**
	 * Order targeted by the WooCommerce order-pay endpoint.
	 *
	 * @return WC_Order|null Order when this request is an order-pay request, null otherwise.
	 */
	public function order_pay_order(): ?WC_Order {
		if ( ! function_exists( 'get_query_var' ) ) {
			return null;
		}

		$order_pay_id = absint( get_query_var( 'order-pay' ) );

		if ( $order_pay_id <= 0 ) {
			return null;
		}

		$order = wc_get_order( $order_pay_id );

		return $order instanceof WC_Order ? $order : null;
	}

	/**
	 * Whether this store can take an Autopay payment in the given currency.
	 *
	 * Both halves matter: Autopay supports a fixed set of currencies, and the merchant
	 * enables a subset of them in the plugin settings. A currency that is supported but not
	 * enabled has no ServiceID/key of its own, so a payment in it would otherwise be signed
	 * with the PLN credentials (`Settings_Manager::get_currency_option_key()` maps PLN to the
	 * non-postfixed legacy keys, which are practically always populated).
	 *
	 * @param string $code ISO-4217 currency code.
	 *
	 * @return bool
	 */
	public function is_configured( string $code ): bool {
		if ( '' === $code ) {
			return false;
		}

		$currency_manager = blue_media()->get_currency_manager();

		return null !== $currency_manager->get_currency( $code )
				&& $currency_manager->is_currency_selected( $code );
	}

	/**
	 * Make the given currency the request-global Autopay currency context.
	 *
	 * `Currency::reconfigure()` silently falls back to the shop currency for a code it does
	 * not support, so its RESULT is compared against what was asked for; a caller must never
	 * have to repeat that check.
	 *
	 * @param string $code ISO-4217 currency code.
	 *
	 * @return Currency_Interface|null The bound currency, or null when it is not supported.
	 */
	public function bind( string $code ): ?Currency_Interface {
		if ( '' === $code ) {
			return null;
		}

		$resolved = blue_media()->get_currency_manager()->reconfigure( $code );

		if ( ! $resolved instanceof Currency_Interface || $resolved->get_code() !== $code ) {
			return null;
		}

		return $resolved;
	}
}
