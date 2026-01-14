<?php

namespace Ilabs\BM_Woocommerce\Frontend;

/**
 * Handles visibility logic for platform-specific payment methods like Apple Pay and Google Pay.
 *
 * Separates presentation logic from configuration data.
 */
class PaymentMethodVisibilityHandler {

    /**
     * Generate JavaScript for toggling Apple Pay / Google Pay visibility.
     *
     * This script ensures only the appropriate payment method is shown
     * based on the user's device capabilities.
     */
    public function get_apple_google_pay_toggle_script(): string {
        return '<script>
        // Show Apple Pay on Apple devices, Google Pay on others
        if (window.ApplePaySession) {
            jQuery(".bm-group-gateway-1513").show();
            jQuery(".bm-group-gateway-1512").hide();
        } else {
            jQuery(".bm-group-gateway-1513").hide();
            jQuery(".bm-group-gateway-1512").show();
        }
        </script>';
    }

    /**
     * Check if the current payment method requires platform-specific visibility handling.
     */
    public function requires_visibility_script( array $gateway_ids ): bool {
        // Apple Pay (1513) or Google Pay (1512) require special handling
        return in_array( 1513, $gateway_ids, true ) || in_array( 1512, $gateway_ids, true );
    }

    /**
     * Get CSS selector for a payment method group by gateway ID.
     */
    public function get_group_selector( int $gateway_id ): string {
        return ".bm-group-gateway-{$gateway_id}";
    }
}
