<?php
/**
 * Scoring Trait
 *
 * Methods for spam scoring and array output.
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
 * Trait Scoring
 *
 * Provides spam scoring and array output methods.
 */
trait Scoring {

	/**
	 * Calculate spam score.
	 *
	 * @param bool $check_mx Whether to check MX records.
	 *
	 * @return int Score 0-100 (higher = more suspicious).
	 */
	public function spam_score( bool $check_mx = false ): int {
		if ( ! $this->valid ) {
			return 100;
		}

		$score = 0;

		// Digit patterns
		$digit_count = $this->digit_count();
		if ( $digit_count > 3 ) {
			$score += $this->has_year() ? 5 : 10;
		}
		if ( $digit_count > 6 ) {
			$score += 5;
		}

		// Local part checks
		if ( $this->has_long_local() ) {
			$score += 10;
		}
		if ( $this->has_excessive_specials() ) {
			$score += 10;
		}

		// Domain checks
		if ( ! $this->is_common_tld() ) {
			$score += 15;
		}
		if ( $this->is_common_provider() ) {
			$score -= 10;
		}
		if ( $this->has_long_domain() ) {
			$score += 10;
		}

		// MX check
		if ( $check_mx && ! $this->has_mx() ) {
			$score += 25;
		}

		return max( 0, min( 100, $score ) );
	}

	/**
	 * Get a human-readable rating for the spam score.
	 *
	 * @param bool $check_mx Whether to include MX check in scoring.
	 *
	 * @return string The rating (excellent, good, fair, poor, bad).
	 */
	public function spam_rating( bool $check_mx = false ): string {
		$score = $this->spam_score( $check_mx );

		return match ( true ) {
			$score <= 10 => 'excellent',
			$score <= 25 => 'good',
			$score <= 50 => 'fair',
			$score <= 75 => 'poor',
			default => 'bad',
		};
	}

	/**
	 * Get simplified analysis array for API responses.
	 *
	 * @param bool $check_mx Whether to check MX records.
	 *
	 * @return array Simplified analysis data.
	 */
	public function to_simple_array( bool $check_mx = false ): array {
		$result = [
			'email'         => $this->normalized,
			'valid'         => $this->valid,
			'score'         => $this->spam_score( $check_mx ),
			'rating'        => $this->spam_rating( $check_mx ),
			'free_provider' => $this->is_common_provider(),
			'role_account'  => $this->is_role_based(),
			'domain'        => $this->domain,
		];

		// Add typo suggestion if detected
		if ( $this->has_typo() ) {
			$result['has_typo']   = true;
			$result['suggestion'] = $this->suggested_email();
		}

		return $result;
	}

	/**
	 * Get comprehensive analysis array.
	 *
	 * @param bool $check_mx Whether to check MX records.
	 *
	 * @return array Analysis data.
	 */
	public function to_array( bool $check_mx = false ): array {
		return [
			'email'           => $this->normalized,
			'original'        => $this->original,
			'valid'           => $this->valid,
			'local'           => $this->local,
			'domain'          => $this->domain,
			'tld'             => $this->tld,
			'base_address'    => $this->base_address(),
			'subaddress'      => $this->subaddress(),
			'subaddressed'    => $this->is_subaddressed(),
			'common_provider' => $this->is_common_provider(),
			'authority'       => $this->is_authority_provider(),
			'role_based'      => $this->is_role_based(),
			'government'      => $this->is_government(),
			'educational'     => $this->is_educational(),
			'military'        => $this->is_military(),
			'private'         => $this->is_private(),
			'common_tld'      => $this->is_common_tld(),
			'commercial_tld'  => $this->is_commercial_tld(),
			'country'         => $this->country(),
			'has_year'        => $this->has_year(),
			'digit_count'     => $this->digit_count(),
			'auto_generated'  => $this->is_auto_generated(),
			'has_typo'        => $this->has_typo(),
			'suggestion'      => $this->suggested_email(),
			'mx_valid'        => $check_mx ? $this->has_mx() : null,
			'spam_score'      => $this->spam_score( $check_mx ),
			'spam_rating'     => $this->spam_rating( $check_mx ),
		];
	}

}