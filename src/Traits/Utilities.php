<?php
/**
 * Utilities Trait
 *
 * Internal helper methods for email parsing and manipulation.
 *
 * @package     ArrayPress\EmailUtils
 * @subpackage  Traits
 * @copyright   Copyright (c) 2025, ArrayPress Limited
 * @license     GPL-2.0-or-later
 * @since       1.0.0
 * @author      ArrayPress
 */

declare( strict_types=1 );

namespace ArrayPress\EmailUtils\Traits;

/**
 * Trait Utilities
 *
 * Internal utility methods used across the Email class.
 */
trait Utilities {

	/**
	 * Extract the TLD from a domain.
	 *
	 * @param string $domain The domain name.
	 *
	 * @return string The TLD in lowercase.
	 */
	protected static function extract_tld( string $domain ): string {
		return strtolower( substr( strrchr( $domain, '.' ), 1 ) );
	}

	/**
	 * Sanitize a subaddress tag.
	 *
	 * Removes plus signs, replaces spaces with hyphens, and trims whitespace.
	 *
	 * @param string $tag The raw tag input.
	 *
	 * @return string The sanitized tag.
	 */
	protected static function sanitize_tag( string $tag ): string {
		return trim( str_replace( [ '+', ' ' ], [ '', '-' ], $tag ) );
	}

}