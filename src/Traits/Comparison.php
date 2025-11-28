<?php
/**
 * Comparison Trait
 *
 * Methods for comparing email addresses.
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
 * Trait Comparison
 *
 * Provides methods for comparing email instances.
 */
trait Comparison {

	/**
	 * Check if this email equals another.
	 *
	 * @param self|string $other Email to compare.
	 *
	 * @return bool True if equal.
	 */
	public function equals( self|string $other ): bool {
		if ( is_string( $other ) ) {
			$other = self::parse( $other );
		}

		if ( ! $other instanceof self ) {
			return false;
		}

		return $this->normalized === $other->normalized();
	}

	/**
	 * Check if base addresses match (ignoring subaddress).
	 *
	 * @param self|string $other Email to compare.
	 *
	 * @return bool True if same base address.
	 */
	public function equals_base( self|string $other ): bool {
		if ( is_string( $other ) ) {
			$other = self::parse( $other );
		}

		if ( ! $other instanceof self ) {
			return false;
		}

		return $this->base_address() === $other->base_address();
	}

	/**
	 * Check if same domain.
	 *
	 * @param self|string $other Email to compare.
	 *
	 * @return bool True if same domain.
	 */
	public function same_domain( self|string $other ): bool {
		if ( is_string( $other ) ) {
			$other = self::parse( $other );
		}

		if ( ! $other instanceof self ) {
			return false;
		}

		return $this->domain === $other->domain();
	}

}