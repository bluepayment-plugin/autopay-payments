<?php

defined( 'ABSPATH' ) || exit;
/**
 * @var array $response_data
 * @var string $environment
 * @var string $shopBaseCountryCode
 */

?>


<input
	name="atp_gpay_amount"
	type="hidden"
	value="<?php echo esc_attr( $response_data['cart_total'] ?? '' ); ?>"
/>
<input
	name="atp_gpay_currency"
	type="hidden"
	value="<?php echo esc_attr( $response_data['currency'] ?? '' ); ?>"
/><input
	id="atp_gpay_payment_token"
	name="atp_gpay_payment_token"
	type="hidden"
	value=""
/>

<div class="atp-gpay-wrapper">
	<p class="atp-gpay-subtitle">
		<?php
		echo esc_html__( 'Pay with Google Pay',
			'platnosci-online-blue-media' );
		?>
	</p>
	<div id="js-pay-button-wrapper"></div>
	<p id="atp-gpay-terms-error" class="atp-gpay-terms-error"
		style="display: none;">

		<?php
		echo esc_html__( 'Please read and accept the',
			'platnosci-online-blue-media' );
		?>
		<span class="atp-gpay-terms-error__strong">
        <?php
			echo esc_html__( 'Terms & Conditions',
				'platnosci-online-blue-media' );
			?>
		</span>
	</p>
</div>
<style>
	.atp-gpay-wrapper {
		padding: 10px 0 4px;
	}

	.atp-gpay-terms-error {
		margin-top: 10px;
		font-size: 13px;
		color: #c20000;
	}

	.atp-gpay-subtitle {
		margin: 14px 0 18px;
		padding: 2px 0 0 2px;
		font-size: 13px;
		color: #555555;
	}

	#payment .payment_methods li .atp-gpay-subtitle {
		margin-bottom: 18px !important;
	}

	.atp-gpay-terms-error__strong {
		font-weight: 600;
	}
</style>
<script>
	(function () {
		let responseData = <?php echo wp_json_encode( $response_data ); ?> ||
		{
		}


		let environment = "<?php echo esc_attr( $environment ); ?>";
		const shopBaseCountryCode = "<?php echo esc_attr( $shopBaseCountryCode ); ?>";
		// Resolved by waitForLoader() at the bottom of this script. The loader is enqueued for
		// this request, but an optimiser plugin, a deferred script or an unusual render order
		// can still leave it in flight while this inline script runs -- so it is polled for
		// rather than read once, the same way waitForDeps() polls for the Google Pay API.
		let loader = null;

		// How long to wait for the loader itself. It is a local file enqueued for this very
		// request, so this only has to cover ordering, not network distance -- the generous
		// budget for fetching pay.js over a slow link lives in the loader's own whenReady().
		const LOADER_WAIT_MS = 5000;

			function logError(message, meta) {
				if (!window.console) {
					return;
				}
				if (typeof window.console.error !== 'function') {
					return;
				}
				console.error('Google Pay:', message, meta || {});
			};


		function initGpay() {
			const requiredKeys = ['authJwt', 'merchantId', 'merchantOrigin', 'merchantName', 'acceptorId'];
			for (let i = 0; i < requiredKeys.length; i++) {
				if (!responseData[requiredKeys[i]]) {
					logError('Missing required field', {
						field: requiredKeys[i],
						responseData: responseData
					});
					return;
				}
			}


			try {
				const gpAtp = new GooglePay(
					environment,
					responseData.authJwt,
					responseData.merchantId,
					responseData.merchantOrigin || window.location.hostname,
					responseData.merchantName,
					responseData.acceptorId.toString(),
					['MASTERCARD', 'VISA'],
					['PAN_ONLY', 'CRYPTOGRAM_3DS']
				);

				gpAtp.setTransactionAmount(responseData.cart_total || '');
				gpAtp.setTransactionStatus('FINAL');
				gpAtp.setTransactionCurrencyCode(responseData.currency || '');
				gpAtp.setTransactionCountryCode(shopBaseCountryCode);

				gpAtp.init(function (data) {

					bm_checkout_locked_by = 'gpay';
					const tokenStr = JSON.stringify(data.paymentMethodData.tokenizationData.token)
					const tokenBase64 = btoa(tokenStr)
					document.getElementById('atp_gpay_payment_token').value = tokenBase64
					const placeOrderBtn = jQuery('button#place_order');
					if (placeOrderBtn) {
						bm_global_gpay_can_continue = true
						setTimeout(function () {
							placeOrderBtn.show().prop('disabled', false);
							placeOrderBtn.click();
						}, 50);
					}
				});
			} catch (e) {
				logError('Init failed', e);
			}

			enforceTermsBeforeGpay();
		}

		function enforceTermsBeforeGpay() {
			const wrapper = document.getElementById('js-pay-button-wrapper');
			const errorBox = document.getElementById('atp-gpay-terms-error');
			// Selectors and lookup live in the shared loader, so the classic checkout and the
			// Checkout Block always agree on what "the terms checkbox" is.
			const termsSelectors = loader.TERMS_SELECTORS;

			function areTermsAccepted() {
				const checkbox = loader.findTermsCheckbox();
				if (!checkbox) {
					return true;
				}
				if (checkbox.checked) {
					return true;
				}
				return false;
			}

			function getGpayButton() {
				if (!wrapper) {
					return null;
				}
				return wrapper.querySelector('button, div[role="button"]');
			}

			function handleGpayClick(event) {
				if (areTermsAccepted()) {
					if (errorBox) {
						errorBox.style.display = 'none';
					}
					return;
				}

				if (errorBox) {
					errorBox.style.display = 'block';
				}

				if (event) {
					if (typeof event.stopImmediatePropagation === 'function') {
						event.stopImmediatePropagation();
					}
					if (typeof event.preventDefault === 'function') {
						event.preventDefault();
					}
				}
			}

			function bindGpayClickHandler() {
				const gpayBtn = getGpayButton();
				if (!gpayBtn) {
					return;
				}

				if (gpayBtn.atpGpayTermsHandler) {
					gpayBtn.removeEventListener('click', gpayBtn.atpGpayTermsHandler, true);
				}

				gpayBtn.atpGpayTermsHandler = handleGpayClick;
				gpayBtn.addEventListener('click', gpayBtn.atpGpayTermsHandler, true);
			}

			function handleTermsChange(event) {
				if (!event) {
					return;
				}
				if (!event.target) {
					return;
				}

				for (let i = 0; i < termsSelectors.length; i++) {
					if (event.target.matches(termsSelectors[i])) {
						if (areTermsAccepted()) {
							if (errorBox) {
								errorBox.style.display = 'none';
							}
						}
						return;
					}
				}
			}

			if (wrapper) {
				if (typeof MutationObserver !== 'undefined') {
					new MutationObserver(function () {
						bindGpayClickHandler();
					}).observe(wrapper, {childList: true, subtree: true});
				}
			}

			document.addEventListener('change', handleTermsChange);

			bindGpayClickHandler();
		}

		function waitForLoader(onReady) {
			const deadline = Date.now() + LOADER_WAIT_MS;

			(function poll() {
				if (window.AutopayGooglePayLoader) {
					loader = window.AutopayGooglePayLoader;
					onReady();
					return;
				}

				if (Date.now() > deadline) {
					logError('loader script not available');
					return;
				}

				window.setTimeout(poll, 100);
			})();
		}

		// Injecting pay.js here -- rather than enqueueing it with the page -- is the whole
		// point: this template is rendered only when the Google Pay channel actually reaches
		// the DOM, so a checkout that never shows the button never touches pay.google.com
		// and never gets a Google cookie (AP-183).
		waitForLoader(function () {
			loader.whenReady(initGpay);
		});
	})();
</script>
