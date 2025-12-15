<?php
/**
 * Sanitize Trait
 *
 * Methods for sanitizing email patterns and lists.
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
 * Trait Sanitize
 *
 * Provides methods for sanitizing email patterns and lists.
 */
trait Sanitize {

	/**
	 * Sanitize and filter a list of email patterns.
	 *
	 * Takes raw input (string or array) and returns a clean array of valid patterns.
	 * Handles trimming, lowercasing, deduplication, and validation.
	 *
	 * @param string|array $input     Raw input - newline-separated string or array.
	 * @param bool         $as_string Return as newline-separated string instead of array.
	 *
	 * @return array|string Sanitized valid patterns.
	 */
	public static function sanitize_pattern_list( $input, bool $as_string = false ) {
		// Convert string to array.
		if ( is_string( $input ) ) {
			$patterns = explode( "\n", $input );
		} else {
			$patterns = (array) $input;
		}

		// Clean up each pattern.
		$patterns = array_map( 'trim', $patterns );
		$patterns = array_map( 'strtolower', $patterns );
		$patterns = array_filter( $patterns );

		// WordPress sanitization if available.
		if ( function_exists( 'sanitize_text_field' ) ) {
			$patterns = array_map( 'sanitize_text_field', $patterns );
		}

		// Remove duplicates.
		$patterns = array_unique( $patterns );

		// Filter to valid patterns only.
		$patterns = self::filter_valid_patterns( $patterns );

		// Re-index array.
		$patterns = array_values( $patterns );

		return $as_string ? implode( "\n", $patterns ) : $patterns;
	}

	/**
	 * Sanitize a single email pattern.
	 *
	 * @param string $pattern Raw pattern input.
	 *
	 * @return string|null Sanitized pattern or null if invalid.
	 */
	public static function sanitize_pattern( string $pattern ): ?string {
		$pattern = trim( $pattern );
		$pattern = strtolower( $pattern );

		// WordPress sanitization if available.
		if ( function_exists( 'sanitize_text_field' ) ) {
			$pattern = sanitize_text_field( $pattern );
		}

		return self::is_valid_pattern( $pattern ) ? $pattern : null;
	}

	/**
	 * Sanitize an email address.
	 *
	 * @param string $email Raw email input.
	 *
	 * @return string|null Sanitized email or null if invalid.
	 */
	public static function sanitize_email( string $email ): ?string {
		$email = trim( $email );
		$email = strtolower( $email );

		// WordPress sanitization if available.
		if ( function_exists( 'sanitize_email' ) ) {
			$email = sanitize_email( $email );
		}

		return self::validate_email( $email ) ? $email : null;
	}

}