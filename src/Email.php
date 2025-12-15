<?php
/**
 * Email Value Object
 *
 * An immutable value object for working with email addresses. Provides both
 * static convenience methods for quick operations and object methods for
 * comprehensive email analysis.
 *
 * @package     ArrayPress\EmailUtils
 * @copyright   Copyright (c) 2025, ArrayPress Limited
 * @license     GPL-2.0-or-later
 * @since       1.0.0
 * @author      ArrayPress
 */

declare( strict_types=1 );

namespace ArrayPress\EmailUtils;

use ArrayPress\EmailUtils\Traits\Comparison;
use ArrayPress\EmailUtils\Traits\Core;
use ArrayPress\EmailUtils\Traits\Detection;
use ArrayPress\EmailUtils\Traits\Matching;
use ArrayPress\EmailUtils\Traits\Sanitize;
use ArrayPress\EmailUtils\Traits\Transformation;
use ArrayPress\EmailUtils\Traits\Scoring;
use ArrayPress\EmailUtils\Traits\Utilities;
use JsonSerializable;
use Stringable;

/**
 * Class Email
 *
 * Immutable value object representing an email address with comprehensive
 * validation, analysis, and transformation capabilities.
 */
class Email implements JsonSerializable, Stringable {

	/**
	 * The standard anonymized email placeholder.
	 */
	public const ANONYMIZED_PLACEHOLDER = 'deleted@site.invalid';

	use Core;
	use Detection;
	use Transformation;
	use Comparison;
	use Matching;
	use Sanitize;
	use Scoring;
	use Utilities;

	/**
	 * String representation.
	 *
	 * @return string The normalized email.
	 */
	public function __toString(): string {
		return $this->normalized;
	}

	/**
	 * JSON serialization.
	 *
	 * @return array Data for JSON encoding.
	 */
	public function jsonSerialize(): array {
		return $this->to_array();
	}

}