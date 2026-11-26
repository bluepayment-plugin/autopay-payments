<?php // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.ShortPrefixPassed -- "bm" in package name is a pre-existing convention across this codebase.
/**
 * Gateway_List_Channels — channel lookups on a raw gatewayList/v3 response.
 *
 * @package Ilabs\BM_Woocommerce\Domain\Service\Gateway_List
 */

namespace Ilabs\BM_Woocommerce\Domain\Service\Gateway_List;

defined( 'ABSPATH' ) || exit;

/**
 * Lookups on the raw (decoded, not yet hydrated) gatewayList/v3 response.
 *
 * Callers that already hold a hydrated `Gateway_List_Response` should iterate its
 * `Gateway` objects instead -- this helper exists for the enqueue/registration paths
 * that only need a yes/no answer and must not pay for hydration.
 */
final class Gateway_List_Channels {

	/**
	 * Whether the given channel (GatewayID) is present in the response.
	 *
	 * Tolerates every shape the API/cache can return (missing key, non-array rows,
	 * numeric strings), because this is called from script-enqueue paths where a
	 * malformed response must degrade to "not present", never to a fatal.
	 *
	 * @param array<string, mixed> $gateway_list_response Raw decoded gatewayList/v3 response.
	 * @param int                  $channel_id            GatewayID to look for.
	 *
	 * @return bool
	 */
	public static function contains( array $gateway_list_response, int $channel_id ): bool {
		if ( empty( $gateway_list_response['gatewayList'] )
			|| ! is_array( $gateway_list_response['gatewayList'] ) ) {
			return false;
		}

		foreach ( $gateway_list_response['gatewayList'] as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}

			if ( $channel_id === (int) ( $row['gatewayID'] ?? 0 ) ) {
				return true;
			}
		}

		return false;
	}
}
