# Autopay Module Instructions for WooCommerce Platform

## Basic Information

Autopay is a payment module that enables cashless transactions in stores built on the WordPress (WooCommerce) platform. If you don't have the plugin yet, you can download it [here](https://github.com/bluepayment-plugin/autopay-payments/releases).

## What does the Autopay payment plugin offer?

The Autopay payment plugin offers a range of features supporting sales in your store:

- The most popular payment methods in Europe:
	- Online bank transfers ([Pay By Link](https://autopay.pl/baza-wiedzy/blog/ecommerce/platnosc-pay-by-link-na-czym-polega-i-co-mozesz-dzieki-niej-zyskac))
	- Fast bank transfers
	- [BLIK](https://autopay.pl/rozwiazania/blik)
	- Visa Mobile
	- [Google Pay](https://autopay.pl/rozwiazania/google-pay)
	- [Apple Pay](https://autopay.pl/rozwiazania/apple-pay)
	- Installment payments
	- Recurring payments 
	- International payments

- The most popular sales methods for the WooCommerce platform:
	- Purchase as a guest / registered user
	- Step-by-step or block checkout
	- Process payments either by redirecting to an external payment page or directly within the store (selected methods: cards, BLIK)

- Test environment support (perform test transactions to ensure correct installation and configuration of the plugin)
- Deferred and installment payments
- Native integration with Google Analytics 4 from within the Autopay plugin
- Automatic verification of the correctness of authorization data configuration in the plugin

[Register your store!](https://autopay.pl/oferta/platnosci-online?utm_campaign=woocommerce&utm_source=woocommerce_description&utm_medium=offer_cta#kalkulator)

## Installation

### Plugin installation requirements

- **WordPress** – tested on versions from `6.0` to `6.6.2`
- **WooCommerce Plugin** – tested on versions from `7.9.0` to `8.9.3`
- **PHP version** at least `7.4`

### Download from Wordpress.org

The [WordPress](https://en.wordpress.org/plugins/platnosci-online-blue-media/) platform offers various extensions compatible with WordPress/WooCommerce-based websites.  
For the Autopay payment plugin, you can find both official and unofficial versions on [WordPress.org](https://wordpress.org). You can find the latest version of the plugin developed by Autopay in the [WordPress marketplace](https://pl.wordpress.org/plugins/platnosci-online-blue-media/).

#### Manual installation of the Autopay plugin

1. The Autopay payment plugin is actively developed, with each new version offering a range of new features and improvements. To install the latest version of the plugin, we recommend visiting our [GitHub account](https://github.com/bluepayment-plugin/autopay-payments/).
2. Locate the plugin version labeled "latest" and download the `.zip` file.

   ![Download plugin archive instruction](https://github.com/bluepayment-plugin/autopay-payments/assets/screenshot_3.png)

3. Download the `.zip` file to install the plugin.
4. Log into the admin panel of your WordPress site.
5. From the menu on the left, select **Plugins** and then click **Add New Plugin**.
6. Select the previously downloaded `.zip` file containing the Autopay plugin and click **Upload plugin**.
7. The plugin will be automatically installed on your store. You can now proceed to configure the plugin.

## Configure the Plugin

Log into the admin panel and navigate to the **Payments** tab and find the **Autopay** method. Select **Configure** to start setting up the plugin, or use the toggle to **enable** or **disable** the plugin on your store.

If you encounter any issues during installation, visit our [FAQ section](#frequently-asked-questions).

### Authentication

The "Authentication" tab allows you to enter your Autopay account access data into the plugin, and also to decide whether Autopay payments should operate in the test or production environment.

1. **Sandbox mode**:
	- Set to **yes**: This is used to test the integration and configuration of the Autopay plugin on your store. In the test environment, the payer is not charged for any purchase, and you do not receive any payment for sales. Transactions are virtual only. Remember, never send a real transaction for any test transactions paid in the test environment!
	- Set to **no**: The plugin operates in the production environment, meaning transactions and payments are real. The payer is charged for purchases, and the seller receives funds from Autopay for sales.

2. **Service Identifier**: This is the ID of your Autopay account. You can find it by logging into your account, selecting "Service Settings" from the menu, and then for the "Technical Service Configuration" section, click "Choose." The service ID is the value of "Service Identifier."

3. **Configuration Key (hash)**: This value is specific to your website and your Autopay account. You can find it by logging into your account, selecting "Service Settings" from the menu, and then for the "Technical Service Configuration" section, click "Choose." It is labeled as "Configuration Key (hash)."

> **Sandbox mode vs. Service Identifier and Configuration Key (hash)**  
> The values of the Service Identifier and Configuration Key are different for the test and production environments. If you have created a new Autopay account and do not yet have access to the test environment, you can request access.

> **Note**: Select the verification category, complete the data, and in the message field, provide the ID of your current service and request the creation of a test environment for your store.

### Authentication Status

The Autopay plugin can check if it has been correctly configured and can process payments. For the selected environment and currency, click the "Check Connection" button. The table below explains the results:

| Message                                                            | Explanation                                                                                                         |
|---------------------------------------------------------------------|---------------------------------------------------------------------------------------------------------------------|
| works correctly                                                     | Everything is OK                                                                                                    |
| enter the service identifier                                         | The service identifier has not been entered                                                                         |
| enter the configuration key (hash)                                  | The configuration key has not been entered                                                                          |
| incorrect service identifier and/or configuration key (hash)        | The entered service identifier or key is incorrect – ensure that you have copied them correctly from your Autopay account |
| unable to verify access data correctness                            | We are unable to test the correctness of the access data you entered. You will need to verify it yourself by making a purchase on your store |
| unable to verify                                                    | We are unable to verify whether your store correctly receives status information from Autopay. You will need to verify this yourself by making a purchase on your store or check our FAQ |
| connection error                                                    | The plugin was unable to run the test. Make sure you have a stable internet connection and repeat the test after a few minutes. If the error persists, contact us using the form: [LINK] or check our FAQ |

### Transaction Status Notifications

Autopay communicates with the plugin via **ITN (Instant Transaction Notification)** – special messages containing the current status of the initiated payment (e.g., payment pending, purchase successfully paid). If there is a connection problem between Autopay and the store, payment and order statuses will not be updated. For example, a customer may make a payment for an order, but the store will still show that the payment has not been initiated.

### Payment Settings

1. **Payment Modes**: Payment methods can be displayed in your store in several ways depending on your preferences and store structure. Below is a table explaining the available modes and helping you choose the best one for your store.
- **Redirect to Autopay’s hosted payment page** - A single button will appear on the payment methods list, redirecting the customer to the Autopay-hosted payment page, where the payer will see the full list of available payment methods. |
- **Display each payment method separately** - A dedicated button for each available payment method will appear on the list, allowing the payer to select a method and be redirected directly to that payment page. |
- **BLIK payment type** - You can configure BLIK to work directly within the store without redirection or via the standard external page method. 
    - "redirect to BLIK page" - after selecting the BLIK payment method, the payer will be redirected to the BLIK page and asked to enter the payment code and confirm it in the app. Additionally, the payer can save our store on the device they are using. This means that in the future, when paying with BLIK in our store on the same device, they will no longer be asked to provide the BLIK code, and a payment confirmation request will appear directly in their banking app after being redirected to the BLIK page.
    - "enter BLIK code directly on the store" - after selecting the BLIK method, the payer will see a dedicated field on your store where they can enter the BLIK code. The payer will not be redirected anywhere, and the payment will take place directly on your store.

2. **Payment Statuses**: The configuration of payment statuses impacts how orders are processed. You can set different statuses for various stages, such as:
   - **Payment Started** - The payment process has just begun, and an order has been created in your store.
   - **Payment Accepted** - The payment was successfully completed, and you will receive funds from Autopay.
   - **Payment accepted for purchase of ONLY digital products** - Same as Payment Approved, but specific to digital products, allowing immediate order fulfillment.
   - **Payment rejected** - The payment was not successful, and you will not receive funds.

### Connect with Google Analytics

Autopay allows direct integration with Google Analytics to track sales conversions. The communication with Google Analytics is optional and not required for the plugin to work properly.  
To enable the connection, provide the correct Google Analytics account details:

- Google Analytics Measurement identifier
- Google Analytics Stream ID
- Google Analytics API secret

> **Note**: The plugin registers the event `purchase` when the order reaches the `Completed` status.

## Frequently Asked Questions

### What are ITN and have they been correctly configured?
ITN (Instant Payment Notification) is a message sent to your store by Autopay every time the status of a transaction changes. Using ITN enables the store to handle orders appropriately (e.g., shipping only after payment is received; blocking refunds for orders that haven’t been paid for, etc.).
There are two ways to check ITN configuration: directly in the plugin; and through your Autopay account.
To check the configuration in the plugin, run an automatic test in the "Authentication" tab (requires the Autopay plugin to be installed).

If you want to ensure that the ITN configuration was completed correctly on the Autopay account side:
1. Ensure that the following fields in the [production admin portal](https://portal.autopay.eu/panel) and/or [test admin portal](https://testportal.autopay.eu/panel) contain the correct store addresses.
2. Configuration of the payment return address `{Your store URL}/?bm_gateway_return`
3. Example: `https://my-store.com/?bm_gateway_return`
4. Configuration of the address where ITN is sent `{Your store URL}/?wc-api=wc_gateway_bluemedia`
5. Example: `https://my-store.com/?wc-api=wc_gateway_bluemedia`

### Can only selected payment methods be enabled or disabled?

Unfortunately, with WooCommerce Payments, this is not possible. If Autopay methods are enabled in the store, all methods available for the merchant will also appear in the store.

### How do I enable BLIK 0 (entering the BLIK code directly on the store page, without redirecting the payer to the BLIK page)?

To enable BLIK 0 (entering the BLIK code directly on the store page without redirecting the payer), go to the plugin settings, select the "Settings" section. Then choose the "Display each available method separately" payment mode and in the "BLIK payment mode" select "enter the BLIK code directly on the store."

### Can another currency be added?

Yes, starting from version `4.1.26`, the Autopay plugin allows adding a currency other than Polish zloty. However, keep in mind that this currency must also be configured within your Autopay account, which typically involves having separate authentication credentials.
You can check the currency supported on your Autopay account in the service configuration in the [Portal](https://portal.autopay.eu/portal).
To add another currency to your Autopay account, you need to via [this form](https://developers.autopay.pl/kontakt).

### How to request refunds (from the store or Autopay portal)?

Currently, refunds must be ordered from within the Autopay portal. Log in to [Portal](https://portal.autopay.eu/portal) and go to the ‘Transactions’ tab, then click ‘Request a refund’ in the details of the transaction to be refunded.

### Is it possible to "extract" just BLIK on whitelabel (displaying the BLIK method directly in the list of available payment methods in the store)?

Unfortunately, this is not possible. The plugin only allows:
- displaying all available payment methods directly in the payment methods list (including BLIK)
  or
- displaying a single "Pay" button, which redirects to a dedicated Autopay page containing a list of all payment methods available to the payer.

### During the configuration of the plugin in the payment settings, instead of displaying a list of available payment methods, I receive the message "no available payment methods for this currency" - what should I do?

For the currency you selected, there are no available payment methods. Contact us using [this form](https://developers.autopay.pl/kontakt) and ask the Autopay team to check the configuration of your account.
