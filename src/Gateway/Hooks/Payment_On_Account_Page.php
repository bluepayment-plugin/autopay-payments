<?php

namespace Ilabs\BM_Woocommerce\Gateway\Hooks;

class Payment_On_Account_Page {

	public function init() {
		if ( is_wc_endpoint_url( 'order-pay' ) ) {
			$this->payment_on_account_page_stage_1();
		}

		if ( isset( $_POST['autopay_checkout_on_account_page'] ) ) {
			$this->payment_on_account_page_stage_2();
		}

		if ( isset( $_REQUEST['autopay_payment_on_account_page'] )
		     && '1' === $_REQUEST['autopay_payment_on_account_page'] ) {
			$this->payment_on_account_page_stage_3();
		}
	}

	private function payment_on_account_page() {

	}

	private function payment_on_account_page_stage_2() {
		blue_media()
			->get_woocommerce_logger()
			->log_debug( '[payment_on_account_page_stage_2]' );

		add_filter( 'autopay_payment_on_account_page',
			function ( bool $return ) {
				return true;
			} );
	}

	private function payment_on_account_page_stage_3() {
		blue_media()
			->get_woocommerce_logger()
			->log_debug( '[payment_on_account_page_stage_3]' );

		add_filter( 'autopay_filter_can_redirect_to_payment_gateway',
			function ( bool $return ) {
				return true;
			} );
	}

	private function payment_on_account_page_stage_1() {
		blue_media()
			->get_woocommerce_logger()
			->log_debug( '[payment_on_account_page_stage_1]' );

		add_filter( 'autopay_filter_option_whitelabel',
			function ( string $whitelabel ) {
				return 'no';
			} );

		add_action( 'autopay_after_payment_field', function () {
			echo "<input type='hidden' name='autopay_checkout_on_account_page'  value='1' />";
		} );

	}
}
