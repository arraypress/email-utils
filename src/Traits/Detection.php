<?php
/**
 * Detection Trait
 *
 * Methods for detecting email characteristics and patterns.
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

use ArrayPress\EmailUtils\Data\Educational;
use ArrayPress\EmailUtils\Data\Government;
use ArrayPress\EmailUtils\Data\Military;
use ArrayPress\EmailUtils\Data\PrivateDomains;
use ArrayPress\EmailUtils\Data\Providers;
use ArrayPress\EmailUtils\Data\Roles;
use ArrayPress\EmailUtils\Data\Tlds;
use ArrayPress\EmailUtils\Data\Typos;

/**
 * Trait Detection
 *
 * Provides is_* and has_* detection methods.
 */
trait Detection {

	/**
	 * Check if the email has valid MX records.
	 *
	 * @return bool True if MX records exist.
	 */
	public function has_mx(): bool {
		if ( ! $this->valid || $this->domain === '' ) {
			return false;
		}

		return checkdnsrr( $this->domain );
	}

	/**
	 * Check if email has subaddressing (plus sign).
	 *
	 * @return bool True if subaddressed.
	 */
	public function is_subaddressed(): bool {
		return $this->valid && str_contains( $this->local, '+' );
	}

	/**
	 * Check if domain is a common free email provider.
	 *
	 * Also returns true if the domain is a typo of a common provider.
	 *
	 * @return bool True if common provider.
	 */
	public function is_common_provider(): bool {
		if ( in_array( $this->domain, Providers::COMMON, true ) ) {
			return true;
		}

		$corrected = $this->suggested_domain();
		if ( $corrected !== null ) {
			return in_array( $corrected, Providers::COMMON, true );
		}

		return false;
	}

	/**
	 * Check if domain is an authoritative provider with strong anti-fraud.
	 *
	 * Also returns true if the domain is a typo of an authority provider.
	 *
	 * @return bool True if authority provider.
	 */
	public function is_authority_provider(): bool {
		if ( in_array( $this->domain, Providers::AUTHORITY, true ) ) {
			return true;
		}

		$corrected = $this->suggested_domain();
		if ( $corrected !== null ) {
			return in_array( $corrected, Providers::AUTHORITY, true );
		}

		return false;
	}

	/**
	 * Check if the domain appears to be a typo.
	 *
	 * @return bool True if the domain matches a known typo.
	 */
	public function has_typo(): bool {
		return isset( Typos::CORRECTIONS[ $this->domain ] );
	}

	/**
	 * Get the suggested correction for a typo domain.
	 *
	 * @return string|null The corrected domain or null if no typo detected.
	 */
	public function suggested_domain(): ?string {
		return Typos::CORRECTIONS[ $this->domain ] ?? null;
	}

	/**
	 * Get the full suggested email address with corrected domain.
	 *
	 * @return string|null The corrected email or null if no typo detected.
	 */
	public function suggested_email(): ?string {
		$corrected = $this->suggested_domain();

		if ( $corrected === null ) {
			return null;
		}

		return $this->local . '@' . $corrected;
	}

	/**
	 * Check if provider supports subaddressing (plus addressing).
	 *
	 * Also returns true if the domain is a typo of a supporting provider.
	 *
	 * @return bool True if subaddressing is supported.
	 */
	public function supports_subaddressing(): bool {
		if ( in_array( $this->domain, Providers::SUBADDRESS, true ) ) {
			return true;
		}

		$corrected = $this->suggested_domain();
		if ( $corrected !== null ) {
			return in_array( $corrected, Providers::SUBADDRESS, true );
		}

		return false;
	}

	/**
	 * Check if from a private/reserved domain.
	 *
	 * @return bool True if private domain.
	 */
	public function is_private(): bool {
		if ( ! $this->valid ) {
			return false;
		}

		foreach ( PrivateDomains::PATTERNS as $pattern ) {
			if ( str_contains( $this->domain, $pattern ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Check if TLD is common.
	 *
	 * @return bool True if common TLD.
	 */
	public function is_common_tld(): bool {
		return in_array( $this->tld, Tlds::COMMON, true );
	}

	/**
	 * Check if TLD is commercially used.
	 *
	 * @return bool True if commercial TLD.
	 */
	public function is_commercial_tld(): bool {
		return in_array( $this->tld, Tlds::COMMERCIAL, true );
	}

	/**
	 * Check if role-based address.
	 *
	 * @param array $additional_prefixes Optional extra prefixes.
	 *
	 * @return bool True if role-based.
	 */
	public function is_role_based( array $additional_prefixes = [] ): bool {
		$prefixes   = array_merge( Roles::PREFIXES, $additional_prefixes );
		$base_local = $this->base_local();

		return in_array( strtolower( $base_local ), $prefixes, true );
	}

	/**
	 * Check if from a government domain.
	 *
	 * @return bool True if government domain.
	 */
	public function is_government(): bool {
		if ( ! $this->valid ) {
			return false;
		}

		if ( in_array( $this->tld, [ 'gov', 'mil' ], true ) ) {
			return true;
		}

		foreach ( Government::PATTERNS as $pattern ) {
			if ( str_contains( $this->domain, $pattern ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Check if from an educational domain.
	 *
	 * @return bool True if educational domain.
	 */
	public function is_educational(): bool {
		if ( ! $this->valid ) {
			return false;
		}

		if ( $this->tld === 'edu' ) {
			return true;
		}

		foreach ( Educational::PATTERNS as $pattern ) {
			if ( str_contains( $this->domain, $pattern ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Check if email is from a military domain.
	 *
	 * @return bool True if military domain detected.
	 */
	public function is_military(): bool {
		if ( in_array( $this->tld, Military::TLDS, true ) ) {
			return true;
		}

		foreach ( Military::DOMAINS as $pattern ) {
			if ( $this->domain === $pattern || str_ends_with( $this->domain, '.' . $pattern ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Check if email is anonymized.
	 *
	 * @return bool True if anonymized.
	 */
	public function is_anonymized(): bool {
		return str_contains( $this->original, '*' ) || $this->normalized === self::ANONYMIZED_PLACEHOLDER;
	}

	/**
	 * Check if email local part appears to be auto-generated.
	 *
	 * Detects patterns like: a8f3k2j4@, user123456789@, xkcd82hf92@
	 * Uses digit-to-letter ratio and vowel pattern analysis.
	 *
	 * @return bool True if likely auto-generated.
	 */
	public function is_auto_generated(): bool {
		$local = $this->base_local();

		// High ratio of digits to letters (more than 1:1)
		$digits  = preg_match_all( '/[0-9]/', $local );
		$letters = preg_match_all( '/[a-zA-Z]/', $local );

		if ( $letters > 0 && $digits / $letters > 1 ) {
			return true;
		}

		// Random-looking: 7+ chars with no vowel pairs or multiple vowels
		if ( strlen( $local ) > 6 && ! preg_match( '/[aeiou]{2}|[aeiou].*[aeiou]/i', $local ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Check if local part contains a year pattern.
	 *
	 * @return bool True if contains year.
	 */
	public function has_year(): bool {
		return $this->valid && preg_match( '/19\d{2}|20\d{2}/', $this->local ) === 1;
	}

	/**
	 * Check if local part has excessive special characters.
	 *
	 * @return bool True if excessive specials.
	 */
	public function has_excessive_specials(): bool {
		if ( ! $this->valid ) {
			return false;
		}

		return substr_count( $this->local, '-' ) > 1
		       || substr_count( $this->local, '_' ) > 1
		       || substr_count( $this->local, '.' ) > 2;
	}

	/**
	 * Check if domain name is excessively long.
	 *
	 * @param int $max_length Maximum allowed length.
	 *
	 * @return bool True if too long.
	 */
	public function has_long_domain( int $max_length = 15 ): bool {
		if ( ! $this->valid ) {
			return false;
		}

		$parts = explode( '.', $this->domain );

		return strlen( $parts[0] ) > $max_length;
	}

	/**
	 * Check if local part is excessively long.
	 *
	 * @param int $max_length Maximum allowed length.
	 *
	 * @return bool True if too long.
	 */
	public function has_long_local( int $max_length = 20 ): bool {
		return $this->valid && strlen( $this->local ) > $max_length;
	}

}