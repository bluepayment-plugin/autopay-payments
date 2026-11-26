<?php // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.ShortPrefixPassed -- "bm" in package name is a pre-existing convention across this codebase.
/**
 * Gateway_List_Service — fetches and caches the Autopay payment channel list.
 *
 * @package Ilabs\BM_Woocommerce\Gateway
 */

namespace Ilabs\BM_Woocommerce\Gateway;

defined( 'ABSPATH' ) || exit;

use Exception;
use Ilabs\BM_Woocommerce\Gateway\Currency\Payment_Currency_Context;

/**
 * Fetches and caches the Autopay payment channel list (gatewayList/v3 API).
 */
class Gateway_List_Service {

	/**
	 * The main Autopay gateway instance.
	 *
	 * @var Blue_Media_Gateway
	 */
	private Blue_Media_Gateway $gateway;

	/**
	 * Per-request memoization of successful lookups, keyed by currency+language cache key.
	 *
	 * The option cache below only lives for 10 minutes and is not written at all in test
	 * mode, so without this a single page render could hit gatewayList/v3 several times
	 * (channel rendering, script policy, Store API refresh). Bypassed by
	 * `$force_rebuild_cache`, which must always reach the API.
	 *
	 * @var array<string, array>
	 */
	private array $request_cache = [];

	/**
	 * Constructor.
	 *
	 * @param Blue_Media_Gateway $gateway The main Autopay gateway instance.
	 */
	public function __construct( Blue_Media_Gateway $gateway ) {
		$this->gateway = $gateway;
	}

	/**
	 * Return a cached (or freshly fetched) gateway list array.
	 *
	 * @param bool        $force_rebuild_cache Whether to bypass the cache.
	 * @param string|null $currency_code       ISO-4217 currency code; defaults to store currency.
	 *
	 * @return array
	 * @throws Exception When the API call fails.
	 */
	public function gateway_list(
		$force_rebuild_cache = false,
		?string $currency_code = null
	): array {
		if ( ! $currency_code ) {
			// No explicit currency means a customer-facing fetch, so the list must follow
			// the currency THIS payment is in and be signed with that currency's ServiceID.
			// Reading the memoised currency here is what offered a customer shopping in EUR
			// the PLN service's channels, which that payment's service does not have.
			// Callers that pass a currency (admin settings, Auditor, recurring) are
			// deliberately untouched -- they ask about a currency, not about a payment.
			$this->gateway->ensure_payment_currency_context( 'gateway_list' );
			$currency_code = ( new Payment_Currency_Context() )->resolve_code();
		}

		$currency_code     = esc_attr( $currency_code );
		$currency_code_opt = strtolower( $currency_code );
		$language          = $this->get_wordpress_language();
		$cache_key         = "{$currency_code_opt}_{$language}";

		if ( ! $force_rebuild_cache
			&& ! defined( 'BLUE_MEDIA_DISABLE_CACHE' )
			&& isset( $this->request_cache[ $cache_key ] ) ) {
			return $this->request_cache[ $cache_key ];
		}

		if ( defined( 'BLUE_MEDIA_DISABLE_CACHE' ) || $force_rebuild_cache || time()
																				- (int) get_option( "bm_gateway_list_cache_time_{$cache_key}" )
																				> 600// 10 minutes cache
		) {
			$gateway_list_cache = $this->api_get_gateway_list( $currency_code );

			if ( ! $this->gateway->resolve_is_test_mode() ) {
				update_option(
					"bm_gateway_list_cache_{$cache_key}",
					$gateway_list_cache
				);
				update_option(
					"bm_gateway_list_cache_time_{$cache_key}",
					time()
				);
			}
		} else {
			$gateway_list_cache = get_option( "bm_gateway_list_cache_{$cache_key}" );
			if ( empty( $gateway_list_cache ) ) {
				$gateway_list_cache = $this->api_get_gateway_list( $currency_code );
				update_option(
					"bm_gateway_list_cache_{$cache_key}",
					$gateway_list_cache
				);
				update_option(
					"bm_gateway_list_cache_time_{$cache_key}",
					time()
				);
			}
		}

		if ( is_array( $gateway_list_cache ) ) {
			$this->request_cache[ $cache_key ] = $gateway_list_cache;
		}

		return $gateway_list_cache;
	}

	/**
	 * Fetch the gateway list from the Autopay API (gatewayList/v3).
	 *
	 * @param string|null $currency_code ISO-4217 currency code.
	 *
	 * @return array|null
	 * @throws Exception When the API returns an error or an unexpected response.
	 */
	private function api_get_gateway_list(
		?string $currency_code = null
	): ?array {
		$service_id = $this->gateway->get_service_id();
		$message_id = substr( bin2hex( random_bytes( 32 ) ), 32 );
		$currencies = $currency_code ? $currency_code : ( new Payment_Currency_Context() )->resolve_code();
		$language   = $this->get_wordpress_language();

		$params = array(
			'ServiceID'  => $service_id,
			'MessageID'  => $message_id,
			'Currencies' => $currencies,
			'Language'   => $language,
		);

		$params_hash = $this->gateway->hash_transaction_parameters(
			$params,
		);

		$params = array_merge( $params, array( 'Hash' => $params_hash ) );

		$url = $this->gateway->get_gateway_url_not_modified_by_user() . 'gatewayList/v3';

		$wp_remote_post_args = array(
			'headers' => array(
				'content-type' => 'application/json',
			),
			'body'    => wp_json_encode( $params ),
		);

		$params_log         = $params;
		$params_log['Hash'] = '***';
		blue_media()->get_woocommerce_logger()->log_debug(
			sprintf(
				'[api_get_gateway_list request] [url: %s] [params: %s]',
				$url,
				wp_json_encode( $params_log ),
			)
		);

		$result = wp_remote_post(
			$url,
			$wp_remote_post_args,
		);

		if ( is_wp_error( $result ) ) {
			blue_media()->get_woocommerce_logger()->log_error(
				sprintf(
					'[gatewayList/v3] [error message: %s]',
					$result->get_error_message(),
				)
			);
		}

		$result_decoded = json_decode(
			wp_remote_retrieve_body( $result ),
			true
		);

		if ( is_array( $result_decoded )
			&& isset( $result_decoded['result'] )
			&& 'ERROR' === $result_decoded['result'] ) {
			$message = sprintf(
				'[gatewayList/v3] [URL: %s] [Error: %s]',
				$url,
				wp_json_encode( $result_decoded ),
			);
			blue_media()->get_woocommerce_logger()->log_error( $message );

			// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped -- Exception is thrown, not echoed; escaping belongs to the display layer.
			throw new Exception( $message );
		}

		if ( is_array( $result_decoded ) && isset( $result_decoded['gatewayList'] ) ) {
			if ( empty( $result_decoded['gatewayList'] ) ) {
				$message = sprintf(
					'[gatewayList/v3] [URL: %s] [Empty results: %s]',
					$url,
					wp_json_encode( $result_decoded ),
				);
				blue_media()->get_woocommerce_logger()->log_error( $message );

				// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped -- Exception is thrown, not echoed; escaping belongs to the display layer.
				throw new Exception( $message );
			}

			$gateway_ids = array_column( $result_decoded['gatewayList'], 'gatewayID' );
			blue_media()->get_woocommerce_logger()->log_debug(
				sprintf(
					'[api_get_gateway_list response] [gateway_count: %d] [gateway_ids: %s]',
					count( $result_decoded['gatewayList'] ),
					implode( ', ', $gateway_ids ),
				)
			);

			return $result_decoded;
		}

		$message = sprintf(
			'[gatewayList/v3] [URL: %s] [Failed decode results: %s]',
			$url,
			wp_json_encode( $result ),
		);
		blue_media()->get_woocommerce_logger()->log_error( $message );
		// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped -- Exception is thrown, not echoed; escaping belongs to the display layer.
		throw new Exception( $message );
	}

	/**
	 * Get WordPress language code for API requests.
	 *
	 * @return string Language code (e.g., 'PL', 'EN', 'DE', 'ES', 'IT').
	 */
	private function get_wordpress_language(): string {
		$locale = get_locale();

		// Map WordPress locales to Autopay supported language codes (2 characters).
		$language_map = array(
			'pl_PL' => 'PL',
			'en_US' => 'EN',
			'en_GB' => 'EN',
			'de_DE' => 'DE',
			'de_AT' => 'DE',
			'de_CH' => 'DE',
			'es_ES' => 'ES',
			'es_MX' => 'ES',
			'es_AR' => 'ES',
			'it_IT' => 'IT',
			'fr_FR' => 'FR',
			'fr_CA' => 'FR',
			'pt_PT' => 'PT',
			'pt_BR' => 'PT',
			'ru_RU' => 'RU',
			'uk'    => 'UK',
			'cs_CZ' => 'CS',
			'sk_SK' => 'SK',
			'hu_HU' => 'HU',
			'ro_RO' => 'RO',
			'bg_BG' => 'BG',
			'hr'    => 'HR',
			'sl_SI' => 'SL',
			'et'    => 'ET',
			'lv'    => 'LV',
			'lt'    => 'LT',
			'fi'    => 'FI',
			'sv_SE' => 'SV',
			'da_DK' => 'DA',
			'nl_NL' => 'NL',
			'nl_BE' => 'NL',
			'el'    => 'EL',
			'tr_TR' => 'TR',
		);

		$language_code = $language_map[ $locale ] ?? 'PL';

		// Log language detection for debugging.
		blue_media()->get_woocommerce_logger()->log_debug(
			sprintf(
				'[get_wordpress_language] [WordPress locale: %s] [Mapped language: %s]',
				$locale,
				$language_code,
			),
		);

		return $language_code;
	}

	/**
	 * Clear gateway list cache when language changes.
	 *
	 * @return void
	 */
	public function clear_gateway_list_cache(): void {
		global $wpdb;

		$this->request_cache = [];

		// Delete all gateway list cache options.
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Direct query required; WC CRUD does not expose bulk-delete-by-pattern for options. DELETE query is write-only; caching is not applicable.
		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
				'bm_gateway_list_cache_%',
				'bm_gateway_list_cache_time_%',
			),
		);

		wp_cache_delete( 'alloptions', 'options' );

		blue_media()->get_woocommerce_logger()->log_debug(
			'[clear_gateway_list_cache] Gateway list cache cleared due to language change',
		);
	}
}
