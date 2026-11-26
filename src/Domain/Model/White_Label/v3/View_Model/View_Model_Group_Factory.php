<?php

namespace Ilabs\BM_Woocommerce\Domain\Model\White_Label\v3\View_Model;

defined( 'ABSPATH' ) || exit;

use Ilabs\BM_Woocommerce\Domain\Model\White_Label\v3\Gateway;
use Ilabs\BM_Woocommerce\Domain\Model\White_Label\v3\Gateway_List_Response;
use Ilabs\BM_Woocommerce\Gateway\Blue_Media_Gateway;
use Ilabs\BM_Woocommerce\Gateway\Currency\Payment_Currency_Context;

class View_Model_Group_Factory {

	public function create(
		Gateway_List_Response $gateway_list_response,
		bool $filter_for_cart = false
	): array {
		/** @var View_Model_Group[] $groups */
		$groups = [];

		if ( $gateway_list_response->getGatewayGroups() ) {
			foreach ( $gateway_list_response->getGatewayGroups() as $groupData ) {
				$viewModelGroup = new View_Model_Group();
				$viewModelGroup->setType( $groupData->getType() );
				$viewModelGroup->setTitle( $groupData->getTitle() );
				$viewModelGroup->setShortDescription( $groupData->getShortDescription() );
				$viewModelGroup->setDescription( $groupData->getDescription() );
				$viewModelGroup->setOrder( $groupData->getOrder() );
				$viewModelGroup->setIconUrl( $groupData->getIconUrl() );

				if ( $groupData->getType() === 'PBL' ) {
					$viewModelGroup->setToggled( true );
					$gateway = blue_media()->get_blue_media_gateway();
					if ( $gateway instanceof Blue_Media_Gateway ) {
						$viewModelGroup->setIconUrl( $gateway->get_checkout_group_logo_url() );
					} else {
						$viewModelGroup->setIconUrl( blue_media()->get_plugin_images_url() . '/logo-group.svg' );
					}
				}

				$groups[ $groupData->getType() ] = $viewModelGroup;
			}
		}

		if ( $gateway_list_response->getGatewayList() ) {
			foreach ( $gateway_list_response->getGatewayList() as $gateway ) {
				if ( $filter_for_cart ) {
					if ( ! self::check_amount_range( $gateway ) ) {
						continue;
					}
				}
				$groupType = $gateway->getGroupType();
				if ( isset( $groups[ $groupType ] ) ) {
					$groups[ $groupType ]->addGateway( $gateway );
				}
			}
		}

		// Business require is to join FR and PBL items
		if ( isset( $groups['FR'] ) && isset( $groups['PBL'] ) ) {
			foreach ( $groups['FR']->getGateways() as $gateway ) {
				$gateway->setGroupType( 'PBL' );
				$groups['PBL']->addGateway( $gateway );
			}
			unset( $groups['FR'] );
		}

		// Remove groups with no gateways
		$groups = array_filter( $groups, function ( View_Model_Group $group ) {
			return count( $group->getGateways() ) > 0;
		} );

		// Sort gateways within each group by order
		foreach ( $groups as $group ) {
			$gateways = $group->getGateways();
			usort( $gateways, function ( Gateway $a, Gateway $b ) {
				return $a->getOrder() <=> $b->getOrder();
			} );
			$group->setGateways( $gateways );
		}

		// Sort groups by order
		usort( $groups, function ( View_Model_Group $a, View_Model_Group $b ) {
			return $a->getOrder() <=> $b->getOrder();
		} );

		return array_values( $groups );
	}


	/**
	 * Drop a channel whose min/max limits exclude the amount being paid.
	 *
	 * The amount and the currency it is compared in MUST come from the same place. This
	 * method has always taken the amount from the order on order-pay, but looked the limits
	 * up under `get_woocommerce_currency()` -- the shop's base currency, which does not
	 * follow the order. While the channel list itself was also fetched in the base currency
	 * the two happened to agree; now that the list is fetched for the order's currency, the
	 * lookup would never match and every channel would silently skip the amount check.
	 *
	 * @param Gateway $gateway_obj Channel to test.
	 *
	 * @return bool False when the amount falls outside the channel's limits.
	 */
	private function check_amount_range( Gateway $gateway_obj ): bool {
		if ( ! WC()->cart ) {
			return true;
		}

		$currency_context = new Payment_Currency_Context();
		$payment_currency = $currency_context->resolve_code();
		$order_pay_order  = $currency_context->order_pay_order();
		$woocommerce_cart = WC()->cart;
		$cart_total       = null;

		if ( ! $woocommerce_cart->is_empty() ) {
			$cart_total = (float) $woocommerce_cart->get_total( false );
		} elseif ( null !== $order_pay_order ) {
			$cart_total = (float) $order_pay_order->get_total();
		}

		if ( null === $cart_total ) {
			return true;
		}

		foreach ( $gateway_obj->getCurrencies() as $currency_info ) {

			if ( $currency_info->getCurrency() === $payment_currency ) {

				$min_amount = $currency_info->getMinAmount();
				$max_amount = $currency_info->getMaxAmount();

				if ( $min_amount ) {
					if ( $cart_total < $min_amount ) {
						return false;
					}
				}

				if ( $max_amount ) {
					if ( $cart_total > $max_amount ) {
						return false;
					}
				}

				return true;
			}
		}

		return true;
	}
}
