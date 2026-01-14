<?php

namespace Ilabs\BM_Woocommerce\Domain\Model\White_Label\v3\View_Model;

use Ilabs\BM_Woocommerce\Domain\Model\White_Label\v3\Gateway;
use Ilabs\BM_Woocommerce\Domain\Model\White_Label\v3\Gateway_List_Response;

class View_Model_Group_Factory {


	/**
	 * @param Gateway_List_Response $gateway_list_response
	 *
	 * @return View_Model_Group[]
	 */
	public function create( Gateway_List_Response $gateway_list_response
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

					$viewModelGroup->setIconUrl( blue_media()->get_plugin_images_url() . '/logo-group.svg' );
				}

				$groups[ $groupData->getType() ] = $viewModelGroup;
			}
		}


		if ( $gateway_list_response->getGatewayList() ) {
			foreach ( $gateway_list_response->getGatewayList() as $gateway ) {
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
}
