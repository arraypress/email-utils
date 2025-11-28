<?php
/**
 * Private Domain Data
 *
 * Constants for private, reserved, and local domain patterns.
 *
 * @package     ArrayPress\EmailUtils
 * @subpackage  Data
 * @copyright   Copyright (c) 2025, ArrayPress Limited
 * @license     GPL-2.0-or-later
 * @since       1.0.0
 * @author      ArrayPress
 */

declare( strict_types=1 );

namespace ArrayPress\EmailUtils\Data;

/**
 * Class PrivateDomains
 *
 * Private and reserved domain pattern constants.
 */
final class PrivateDomains {

	/**
	 * Private/reserved domain patterns.
	 *
	 * Includes localhost, private IP ranges, and reserved TLDs.
	 */
	public const PATTERNS = [
		'localhost',
		'127.0.0.1',
		'192.168.',
		'10.',
		'172.16.',
		'.local',
		'.internal',
		'.test',
		'.example',
		'.invalid',
		'.localhost',
	];

}