/**
 * Autopay Google Pay loader.
 *
 * Google's `pay.js` sets Google cookies the moment it is fetched, so it must not be
 * requested on pages where the Google Pay button will never render (AP-183). The server
 * already refuses to offer Google Pay when it can tell (whitelabel, gpay_type, channel
 * 1512, terms checkbox, recurring classification), but only the browser knows whether the
 * channel really survives into the DOM -- so the final decision, and therefore the
 * network request, happens here.
 *
 * Both checkouts use this one module: the classic template (templates/google_pay.php,
 * rendered only when the channel is offered) and the Checkout Block
 * (PaymentChannelItem.tsx, which calls load() in the branch that keeps the channel).
 *
 * Written in ES5 so gulp-uglify can process it without a transpile step.
 */
(function (window, document) {
	'use strict';

	if (window.AutopayGooglePayLoader) {
		return;
	}

	/**
	 * Every markup variant WooCommerce/themes use for the terms & conditions checkbox.
	 * Single source for both checkouts -- the Checkout Block reads it from here too.
	 */
	var TERMS_SELECTORS = [
		'#terms',
		'#terms-and-conditions',
		'input[name="terms"]',
		'.wc-block-components-checkbox input[name="terms"]'
	];

	/**
	 * How long to wait for the Google Pay API after injection before giving up.
	 * Deliberately generous: injection now starts when the channel renders rather than at
	 * page load, so a slow connection has less of a head start than it used to.
	 */
	var DEFAULT_READY_TIMEOUT_MS = 10000;
	var POLL_INTERVAL_MS = 100;

	var loadPromise = null;

	function config() {
		return window.autopayGooglePayLoader || {};
	}

	function readyTimeoutMs() {
		var configured = parseInt(config().readyTimeoutMs, 10);

		return configured > 0 ? configured : DEFAULT_READY_TIMEOUT_MS;
	}

	function logError(message, meta) {
		if (!window.console || typeof window.console.error !== 'function') {
			return;
		}
		window.console.error('Google Pay:', message, meta || {});
	}

	/**
	 * Append one script tag, resolving when it loads. Re-uses a tag already present for
	 * the same src, so repeated calls (React re-renders) never fetch twice.
	 */
	function injectScript(src) {
		return new Promise(function (resolve, reject) {
			if (!src) {
				reject(new Error('missing script url'));
				return;
			}

			var existing = document.querySelector('script[data-autopay-gpay="' + src + '"]');
			if (existing) {
				if (existing.getAttribute('data-autopay-gpay-loaded') === '1') {
					resolve();
				} else {
					existing.addEventListener('load', function () {
						resolve();
					});
					existing.addEventListener('error', function () {
						reject(new Error('failed to load ' + src));
					});
				}
				return;
			}

			var script = document.createElement('script');
			script.src = src;
			script.async = true;
			script.setAttribute('data-autopay-gpay', src);
			script.addEventListener('load', function () {
				script.setAttribute('data-autopay-gpay-loaded', '1');
				resolve();
			});
			script.addEventListener('error', function () {
				reject(new Error('failed to load ' + src));
			});

			(document.head || document.body || document.documentElement).appendChild(script);
		});
	}

	/**
	 * Inject the Google Pay scripts once. Idempotent: every caller shares one promise, so
	 * `pay.js` is requested at most once per page regardless of how many channels,
	 * re-renders or checkout refreshes ask for it.
	 *
	 * @return {Promise}
	 */
	function load() {
		if (loadPromise) {
			return loadPromise;
		}

		var urls = config();

		// The ATP helper defines window.GooglePay and must be present before the button is
		// built; Google's pay.js provides window.google.payments.api.
		loadPromise = injectScript(urls.atpJsUrl)
			.then(function () {
				return injectScript(urls.payJsUrl);
			})
			.catch(function (error) {
				logError('script injection failed', error);
				// Keep the rejected promise: retrying a blocked/offline CDN on every
				// re-render would only add requests, and waitForDeps() reports the timeout.
				throw error;
			});

		return loadPromise;
	}

	function isReady() {
		return typeof window.GooglePay !== 'undefined'
			&& !!window.google
			&& !!window.google.payments
			&& !!window.google.payments.api;
	}

	/**
	 * Load the scripts (if needed) and call `onReady` once the API is usable.
	 *
	 * Replaces the per-checkout `waitForDeps()` polling loops: one implementation, one
	 * timeout, used by the classic template and the Checkout Block alike.
	 *
	 * @param {Function} onReady   Called with no arguments when the API is available.
	 * @param {Function} [onFail]  Called when the API never became available in time.
	 */
	function whenReady(onReady, onFail) {
		var deadline = Date.now() + readyTimeoutMs();

		function poll() {
			if (isReady()) {
				onReady();
				return;
			}

			if (Date.now() > deadline) {
				logError('API not loaded');
				if (typeof onFail === 'function') {
					onFail();
				}
				return;
			}

			window.setTimeout(poll, POLL_INTERVAL_MS);
		}

		load().then(poll, poll);
	}

	/**
	 * Only an <input> counts: the Checkout Block has always required one (it reads
	 * `.checked`), and a stray element matching e.g. '#terms' must not be mistaken for the
	 * consent checkbox.
	 */
	function findTermsCheckbox() {
		for (var i = 0; i < TERMS_SELECTORS.length; i++) {
			var checkbox = document.querySelector(TERMS_SELECTORS[i]);
			if (checkbox && checkbox instanceof HTMLInputElement) {
				return checkbox;
			}
		}

		return null;
	}

	/**
	 * Google Pay requires an explicit terms & conditions checkbox on the checkout. The
	 * server approximates this (WooCommerce settings / block attributes); this is the
	 * authoritative answer, read from the rendered DOM.
	 */
	function canRender() {
		return findTermsCheckbox() !== null;
	}

	window.AutopayGooglePayLoader = {
		TERMS_SELECTORS: TERMS_SELECTORS,
		load: load,
		isReady: isReady,
		whenReady: whenReady,
		findTermsCheckbox: findTermsCheckbox,
		canRender: canRender
	};
})(window, document);
