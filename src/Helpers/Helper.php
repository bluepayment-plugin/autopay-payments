<?php

namespace Ilabs\BM_Woocommerce\Helpers;

class Helper {

	public static function is_string_url( string $string ): bool {
		return false != filter_var( $string, FILTER_VALIDATE_URL );
	}

	public static function format_gateway_url( string $url ): string {
		return rtrim( $url, "/" ) . '/';
	}
}
