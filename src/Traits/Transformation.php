<?php
/**
 * Transformation Trait
 *
 * Methods for transforming emails and creating new instances.
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
 * Trait Transformation
 *
 * Provides with_*, to_*, and comparison methods.
 */
trait Transformation {

	/**
	 * Create new instance with different local part.
	 *
	 * @param string $local The new local part.
	 *
	 * @return static|null New Email instance or null if invalid.
	 */
	public function with_local( string $local ): ?static {
		return self::parse( $local . '@' . $this->domain );
	}

	/**
	 * Create new instance with different domain.
	 *
	 * @param string $domain The new domain.
	 *
	 * @return static|null New Email instance or null if invalid.
	 */
	public function with_domain( string $domain ): ?static {
		return self::parse( $this->local . '@' . $domain );
	}

	/**
	 * Create new instance with subaddress added/changed.
	 *
	 * @param string $tag The subaddress tag.
	 *
	 * @return static|null New Email instance or null if not supported.
	 */
	public function with_subaddress( string $tag ): ?static {
		if ( ! $this->valid || ! $this->supports_subaddressing() ) {
			return null;
		}

		$tag = self::sanitize_tag( $tag );
		if ( $tag === '' ) {
			return null;
		}

		return self::parse( $this->base_local() . '+' . $tag . '@' . $this->domain );
	}

	/**
	 * Create new instance without subaddress.
	 *
	 * @return static|null New Email instance.
	 */
	public function without_subaddress(): ?static {
		if ( ! $this->valid ) {
			return null;
		}

		return self::parse( $this->base_address() );
	}

	/**
	 * Create tagged email for specific purpose.
	 *
	 * @param string      $purpose Purpose tag.
	 * @param string|null $suffix  Optional suffix (defaults to year).
	 *
	 * @return static|null New Email instance or null if not supported.
	 */
	public function with_tag( string $purpose, ?string $suffix = null ): ?static {
		$tag = strtolower( $purpose );
		$tag .= '-' . ( $suffix ?? date( 'Y' ) );

		return $this->with_subaddress( $tag );
	}

	/**
	 * Get anonymized version of email.
	 *
	 * @return string Anonymized email (e.g., 'da***@gm***.com').
	 */
	public function to_anonymized(): string {
		if ( ! $this->valid ) {
			return '';
		}

		$anon_local = substr( $this->local, 0, 2 ) . str_repeat( '*', max( strlen( $this->local ) - 2, 3 ) );

		$domain_parts = explode( '.', $this->domain );
		if ( count( $domain_parts ) > 1 ) {
			$first       = $domain_parts[0];
			$anon_first  = substr( $first, 0, 2 ) . str_repeat( '*', max( strlen( $first ) - 2, 3 ) );
			$remaining   = array_slice( $domain_parts, 1 );
			$anon_domain = $anon_first . '.' . implode( '.', $remaining );
		} else {
			$anon_domain = substr( $this->domain, 0, 2 ) . str_repeat( '*', max( strlen( $this->domain ) - 2, 3 ) );
		}

		return $anon_local . '@' . $anon_domain;
	}

	/**
	 * Get masked version of email.
	 *
	 * @param int $show_first Characters to show at start.
	 * @param int $show_last  Characters to show at end.
	 *
	 * @return string Masked email (e.g., 'd***k@gmail.com').
	 */
	public function to_masked( int $show_first = 1, int $show_last = 1 ): string {
		if ( ! $this->valid ) {
			return '';
		}

		$length = strlen( $this->local );

		if ( $show_first + $show_last >= $length ) {
			return $this->normalized;
		}

		$masked_length = $length - $show_first - $show_last;
		$masked_local  = substr( $this->local, 0, $show_first )
		                 . str_repeat( '*', $masked_length )
		                 . substr( $this->local, - $show_last );

		return $masked_local . '@' . $this->domain;
	}

	/**
	 * Get hashed version of email.
	 *
	 * @param bool   $hash_domain Whether to hash domain too.
	 * @param int    $length      Truncate length (0 = full).
	 * @param string $salt        Custom salt.
	 *
	 * @return string Hashed email.
	 */
	public function to_hashed( bool $hash_domain = false, int $length = 0, string $salt = '' ): string {
		if ( ! $this->valid ) {
			return '';
		}

		if ( $salt === '' ) {
			$salt = $this->get_salt();
		}

		$hashed_local = hash( 'sha256', $this->local . $salt );

		if ( $hash_domain ) {
			$hashed_domain = hash( 'sha256', $this->domain . $salt );
			$result        = $hashed_local . '@' . $hashed_domain;
		} else {
			$result = $hashed_local . '@' . $this->domain;
		}

		if ( $length > 0 && $length < strlen( $result ) ) {
			return substr( $result, 0, $length );
		}

		return $result;
	}

	/**
	 * Get ASCII/Punycode version.
	 *
	 * @return string ASCII email.
	 */
	public function to_ascii(): string {
		if ( ! $this->valid ) {
			return '';
		}

		if ( function_exists( 'idn_to_ascii' ) ) {
			$ascii_domain = idn_to_ascii( $this->domain, IDNA_NONTRANSITIONAL_TO_ASCII );
			if ( $ascii_domain !== false ) {
				return $this->local . '@' . $ascii_domain;
			}
		}

		return $this->normalized;
	}

	/**
	 * Get placeholder version.
	 *
	 * @return string The anonymized placeholder.
	 */
	public function to_placeholder(): string {
		return self::ANONYMIZED_PLACEHOLDER;
	}

}