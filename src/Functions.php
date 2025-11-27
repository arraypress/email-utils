<?php
/**
 * Email Utils Helper Functions
 *
 * @package     ArrayPress\EmailUtils
 * @copyright   Copyright (c) 2025, ArrayPress Limited
 * @license     GPL2+
 * @version     1.0.0
 */

declare( strict_types=1 );

use ArrayPress\EmailUtils\Email;

if ( ! function_exists( 'parse_email' ) ) {
	/**
	 * Parse an email address into an Email object.
	 *
	 * @param string $email The email address to parse.
	 *
	 * @return Email|null Email instance or null if invalid.
	 */
	function parse_email( string $email ): ?Email {
		return Email::parse( $email );
	}
}