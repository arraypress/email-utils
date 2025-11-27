<?php
/**
 * Email Utils Test Suite
 *
 * @package ArrayPress\EmailUtils\Tests
 */

declare( strict_types=1 );

namespace ArrayPress\EmailUtils\Tests;

use ArrayPress\EmailUtils\Email;
use PHPUnit\Framework\TestCase;

class EmailTest extends TestCase {

	/** -------------------------------------------------------------------------
	 * Parsing Tests
	 * ---------------------------------------------------------------------- */

	public function test_parse_valid_email(): void {
		$email = Email::parse( 'user@example.com' );

		$this->assertInstanceOf( Email::class, $email );
		$this->assertTrue( $email->valid() );
	}

	public function test_parse_invalid_email_returns_null(): void {
		$this->assertNull( Email::parse( 'invalid' ) );
		$this->assertNull( Email::parse( '@example.com' ) );
		$this->assertNull( Email::parse( 'user@' ) );
		$this->assertNull( Email::parse( '' ) );
	}

	public function test_parse_trims_whitespace(): void {
		$email = Email::parse( '  user@example.com  ' );

		$this->assertNotNull( $email );
		$this->assertEquals( 'user@example.com', $email->normalized() );
	}

	public function test_parse_normalizes_to_lowercase(): void {
		$email = Email::parse( 'USER@EXAMPLE.COM' );

		$this->assertEquals( 'user@example.com', $email->normalized() );
		$this->assertEquals( 'USER@EXAMPLE.COM', $email->original() );
	}

	public function test_from_parts(): void {
		$email = Email::from_parts( 'user', 'example.com' );

		$this->assertNotNull( $email );
		$this->assertEquals( 'user', $email->local() );
		$this->assertEquals( 'example.com', $email->domain() );
	}

	/** -------------------------------------------------------------------------
	 * Getter Tests
	 * ---------------------------------------------------------------------- */

	public function test_getters(): void {
		$email = Email::parse( 'david+test@gmail.com' );

		$this->assertEquals( 'david+test', $email->local() );
		$this->assertEquals( 'gmail.com', $email->domain() );
		$this->assertEquals( 'com', $email->tld() );
		$this->assertEquals( 'david', $email->base_local() );
		$this->assertEquals( 'david@gmail.com', $email->base_address() );
		$this->assertEquals( 'test', $email->subaddress() );
	}

	public function test_subaddress_returns_null_when_not_present(): void {
		$email = Email::parse( 'david@gmail.com' );

		$this->assertNull( $email->subaddress() );
	}

	public function test_country_returns_iso_code(): void {
		$this->assertEquals( 'GB', Email::parse( 'user@example.uk' )?->country() );
		$this->assertEquals( 'DE', Email::parse( 'user@example.de' )?->country() );
		$this->assertEquals( 'JP', Email::parse( 'user@example.jp' )?->country() );
	}

	public function test_country_returns_null_for_generic_tld(): void {
		$this->assertNull( Email::parse( 'user@example.com' )?->country() );
		$this->assertNull( Email::parse( 'user@example.org' )?->country() );
	}

	public function test_digit_count(): void {
		$this->assertEquals( 0, Email::parse( 'david@gmail.com' )?->digit_count() );
		$this->assertEquals( 3, Email::parse( 'david123@gmail.com' )?->digit_count() );
		$this->assertEquals( 4, Email::parse( 'user1980@gmail.com' )?->digit_count() );
	}

	/** -------------------------------------------------------------------------
	 * Detection Tests
	 * ---------------------------------------------------------------------- */

	public function test_is_subaddressed(): void {
		$this->assertTrue( Email::parse( 'user+tag@gmail.com' )?->is_subaddressed() );
		$this->assertFalse( Email::parse( 'user@gmail.com' )?->is_subaddressed() );
	}

	public function test_is_common_provider(): void {
		$this->assertTrue( Email::parse( 'user@gmail.com' )?->is_common_provider() );
		$this->assertTrue( Email::parse( 'user@yahoo.com' )?->is_common_provider() );
		$this->assertTrue( Email::parse( 'user@outlook.com' )?->is_common_provider() );
		$this->assertFalse( Email::parse( 'user@mycompany.com' )?->is_common_provider() );
	}

	public function test_is_authority_provider(): void {
		$this->assertTrue( Email::parse( 'user@gmail.com' )?->is_authority_provider() );
		$this->assertTrue( Email::parse( 'user@icloud.com' )?->is_authority_provider() );
		$this->assertFalse( Email::parse( 'user@mail.com' )?->is_authority_provider() );
	}

	public function test_supports_subaddressing(): void {
		$this->assertTrue( Email::parse( 'user@gmail.com' )?->supports_subaddressing() );
		$this->assertTrue( Email::parse( 'user@outlook.com' )?->supports_subaddressing() );
		$this->assertFalse( Email::parse( 'user@mycompany.com' )?->supports_subaddressing() );
	}

	public function test_is_private(): void {
		$this->assertTrue( Email::parse( 'user@myapp.local' )?->is_private() );
		$this->assertTrue( Email::parse( 'user@server.internal' )?->is_private() );
		$this->assertTrue( Email::parse( 'user@app.test' )?->is_private() );
		$this->assertFalse( Email::parse( 'user@gmail.com' )?->is_private() );
	}

	public function test_is_common_tld(): void {
		$this->assertTrue( Email::parse( 'user@example.com' )?->is_common_tld() );
		$this->assertTrue( Email::parse( 'user@example.org' )?->is_common_tld() );
		$this->assertTrue( Email::parse( 'user@example.io' )?->is_common_tld() );
		$this->assertFalse( Email::parse( 'user@example.xyz' )?->is_common_tld() );
	}

	public function test_is_commercial_tld(): void {
		$this->assertTrue( Email::parse( 'user@example.io' )?->is_commercial_tld() );
		$this->assertTrue( Email::parse( 'user@example.ai' )?->is_commercial_tld() );
		$this->assertTrue( Email::parse( 'user@example.co' )?->is_commercial_tld() );
		$this->assertFalse( Email::parse( 'user@example.com' )?->is_commercial_tld() );
	}

	public function test_is_role_based(): void {
		$this->assertTrue( Email::parse( 'admin@example.com' )?->is_role_based() );
		$this->assertTrue( Email::parse( 'support@example.com' )?->is_role_based() );
		$this->assertTrue( Email::parse( 'info@example.com' )?->is_role_based() );
		$this->assertTrue( Email::parse( 'noreply@example.com' )?->is_role_based() );
		$this->assertFalse( Email::parse( 'david@example.com' )?->is_role_based() );
	}

	public function test_is_role_based_with_additional_prefixes(): void {
		$email = Email::parse( 'orders@example.com' );

		$this->assertFalse( $email->is_role_based() );
		$this->assertTrue( $email->is_role_based( [ 'orders', 'shipping' ] ) );
	}

	public function test_is_government(): void {
		$this->assertTrue( Email::parse( 'user@whitehouse.gov' )?->is_government() );
		$this->assertTrue( Email::parse( 'user@mod.gov.uk' )?->is_government() );
		$this->assertTrue( Email::parse( 'user@hacienda.gob.mx' )?->is_government() );
		$this->assertFalse( Email::parse( 'user@gmail.com' )?->is_government() );
	}

	public function test_is_educational(): void {
		$this->assertTrue( Email::parse( 'user@harvard.edu' )?->is_educational() );
		$this->assertTrue( Email::parse( 'user@oxford.ac.uk' )?->is_educational() );
		$this->assertTrue( Email::parse( 'user@tokyo.ac.jp' )?->is_educational() );
		$this->assertFalse( Email::parse( 'user@gmail.com' )?->is_educational() );
	}

	public function test_is_anonymized(): void {
		// Test via constructor with invalid/anonymized email
		// Note: Anonymized emails with asterisks won't parse as valid
		$email = Email::parse( 'david@gmail.com' );
		$this->assertFalse( $email->is_anonymized() );

		// Test placeholder detection
		$placeholder = Email::parse( 'deleted@site.invalid' );
		$this->assertNotNull( $placeholder );
		$this->assertTrue( $placeholder->is_anonymized() );
	}

	public function test_has_year(): void {
		$this->assertTrue( Email::parse( 'david1980@gmail.com' )?->has_year() );
		$this->assertTrue( Email::parse( 'user2023@gmail.com' )?->has_year() );
		$this->assertFalse( Email::parse( 'david@gmail.com' )?->has_year() );
		$this->assertFalse( Email::parse( 'user123@gmail.com' )?->has_year() );
	}

	public function test_has_excessive_specials(): void {
		$this->assertTrue( Email::parse( 'user.name.here.now@gmail.com' )?->has_excessive_specials() );
		$this->assertFalse( Email::parse( 'user-name@gmail.com' )?->has_excessive_specials() );
		$this->assertFalse( Email::parse( 'user.name@gmail.com' )?->has_excessive_specials() );
		$this->assertFalse( Email::parse( 'username@gmail.com' )?->has_excessive_specials() );
	}

	public function test_has_long_local(): void {
		$longLocal = str_repeat( 'a', 21 );
		$this->assertTrue( Email::parse( $longLocal . '@gmail.com' )?->has_long_local() );
		$this->assertFalse( Email::parse( 'david@gmail.com' )?->has_long_local() );
	}

	public function test_has_long_domain(): void {
		$longDomain = str_repeat( 'a', 16 ) . '.com';
		$this->assertTrue( Email::parse( 'user@' . $longDomain )?->has_long_domain() );
		$this->assertFalse( Email::parse( 'user@gmail.com' )?->has_long_domain() );
	}

	/** -------------------------------------------------------------------------
	 * Immutable Transformation Tests
	 * ---------------------------------------------------------------------- */

	public function test_with_local(): void {
		$email = Email::parse( 'david@gmail.com' );
		$new = $email->with_local( 'john' );

		$this->assertEquals( 'david@gmail.com', $email->normalized() );
		$this->assertEquals( 'john@gmail.com', $new->normalized() );
	}

	public function test_with_domain(): void {
		$email = Email::parse( 'david@gmail.com' );
		$new = $email->with_domain( 'yahoo.com' );

		$this->assertEquals( 'david@gmail.com', $email->normalized() );
		$this->assertEquals( 'david@yahoo.com', $new->normalized() );
	}

	public function test_with_subaddress(): void {
		$email = Email::parse( 'david@gmail.com' );
		$tagged = $email->with_subaddress( 'newsletter' );

		$this->assertEquals( 'david@gmail.com', $email->normalized() );
		$this->assertEquals( 'david+newsletter@gmail.com', $tagged->normalized() );
	}

	public function test_with_subaddress_replaces_existing(): void {
		$email = Email::parse( 'david+old@gmail.com' );
		$new = $email->with_subaddress( 'new' );

		$this->assertEquals( 'david+new@gmail.com', $new->normalized() );
	}

	public function test_with_subaddress_returns_null_for_unsupported_provider(): void {
		$email = Email::parse( 'david@mycompany.com' );

		$this->assertNull( $email->with_subaddress( 'test' ) );
	}

	public function test_without_subaddress(): void {
		$email = Email::parse( 'david+newsletter@gmail.com' );
		$clean = $email->without_subaddress();

		$this->assertEquals( 'david+newsletter@gmail.com', $email->normalized() );
		$this->assertEquals( 'david@gmail.com', $clean->normalized() );
	}

	public function test_with_tag(): void {
		$email = Email::parse( 'david@gmail.com' );
		$tagged = $email->with_tag( 'shopping', '2025' );

		$this->assertEquals( 'david+shopping-2025@gmail.com', $tagged->normalized() );
	}

	public function test_with_tag_defaults_to_current_year(): void {
		$email = Email::parse( 'david@gmail.com' );
		$tagged = $email->with_tag( 'newsletter' );
		$year = date( 'Y' );

		$this->assertEquals( "david+newsletter-{$year}@gmail.com", $tagged->normalized() );
	}

	/** -------------------------------------------------------------------------
	 * Output Transformation Tests
	 * ---------------------------------------------------------------------- */

	public function test_to_anonymized(): void {
		$email = Email::parse( 'david@gmail.com' );
		$anon = $email->to_anonymized();

		$this->assertStringStartsWith( 'da', $anon );
		$this->assertStringContainsString( '***', $anon );
		$this->assertStringEndsWith( '.com', $anon );
	}

	public function test_to_masked(): void {
		$email = Email::parse( 'david@gmail.com' );

		$this->assertEquals( 'd***d@gmail.com', $email->to_masked( 1, 1 ) );
		$this->assertEquals( 'da**d@gmail.com', $email->to_masked( 2, 1 ) );
		$this->assertEquals( 'd**id@gmail.com', $email->to_masked( 1, 2 ) );
	}

	public function test_to_masked_returns_full_email_when_show_exceeds_length(): void {
		$email = Email::parse( 'ab@gmail.com' );

		$this->assertEquals( 'ab@gmail.com', $email->to_masked( 2, 2 ) );
	}

	public function test_to_hashed(): void {
		$email = Email::parse( 'david@gmail.com' );
		$hashed = $email->to_hashed();

		$this->assertStringContainsString( '@gmail.com', $hashed );
		$this->assertEquals( 64 + 1 + 9, strlen( $hashed ) ); // 64 char hash + @ + gmail.com
	}

	public function test_to_hashed_with_domain(): void {
		$email = Email::parse( 'david@gmail.com' );
		$hashed = $email->to_hashed( true );

		$this->assertStringContainsString( '@', $hashed );
		$this->assertStringNotContainsString( 'gmail.com', $hashed );
	}

	public function test_to_hashed_with_length(): void {
		$email = Email::parse( 'david@gmail.com' );
		$hashed = $email->to_hashed( false, 20 );

		$this->assertEquals( 20, strlen( $hashed ) );
	}

	public function test_to_placeholder(): void {
		$email = Email::parse( 'david@gmail.com' );

		$this->assertEquals( 'deleted@site.invalid', $email->to_placeholder() );
	}

	/** -------------------------------------------------------------------------
	 * Spam Score Tests
	 * ---------------------------------------------------------------------- */

	public function test_spam_score_low_for_clean_email(): void {
		$email = Email::parse( 'david@gmail.com' );

		$this->assertLessThanOrEqual( 10, $email->spam_score() );
	}

	public function test_spam_score_increases_with_digits(): void {
		// Use non-common provider to avoid the -10 bonus
		$clean = Email::parse( 'david@example.com' );
		$digits = Email::parse( 'david12345@example.com' );

		$this->assertGreaterThan( $clean->spam_score(), $digits->spam_score() );
	}

	public function test_spam_score_increases_with_uncommon_tld(): void {
		$common = Email::parse( 'user@example.com' );
		$uncommon = Email::parse( 'user@example.xyz' );

		$this->assertGreaterThan( $common->spam_score(), $uncommon->spam_score() );
	}

	public function test_spam_score_decreases_for_common_provider(): void {
		$gmail = Email::parse( 'user123456@gmail.com' );
		$random = Email::parse( 'user123456@randomsite.com' );

		$this->assertLessThan( $random->spam_score(), $gmail->spam_score() );
	}

	/** -------------------------------------------------------------------------
	 * Comparison Tests
	 * ---------------------------------------------------------------------- */

	public function test_equals(): void {
		$email1 = Email::parse( 'david@gmail.com' );
		$email2 = Email::parse( 'DAVID@Gmail.com' );
		$email3 = Email::parse( 'john@gmail.com' );

		$this->assertTrue( $email1->equals( $email2 ) );
		$this->assertTrue( $email1->equals( 'david@gmail.com' ) );
		$this->assertFalse( $email1->equals( $email3 ) );
	}

	public function test_equals_base(): void {
		$email1 = Email::parse( 'david+test@gmail.com' );
		$email2 = Email::parse( 'david+other@gmail.com' );
		$email3 = Email::parse( 'john@gmail.com' );

		$this->assertTrue( $email1->equals_base( $email2 ) );
		$this->assertFalse( $email1->equals_base( $email3 ) );
	}

	public function test_same_domain(): void {
		$email1 = Email::parse( 'david@gmail.com' );
		$email2 = Email::parse( 'john@gmail.com' );
		$email3 = Email::parse( 'david@yahoo.com' );

		$this->assertTrue( $email1->same_domain( $email2 ) );
		$this->assertFalse( $email1->same_domain( $email3 ) );
	}

	/** -------------------------------------------------------------------------
	 * Serialization Tests
	 * ---------------------------------------------------------------------- */

	public function test_to_string(): void {
		$email = Email::parse( 'DAVID@Gmail.com' );

		$this->assertEquals( 'david@gmail.com', (string) $email );
	}

	public function test_json_serialize(): void {
		$email = Email::parse( 'david@gmail.com' );
		$json = json_encode( $email );
		$data = json_decode( $json, true );

		$this->assertIsArray( $data );
		$this->assertEquals( 'david@gmail.com', $data['email'] );
		$this->assertEquals( 'david', $data['local'] );
		$this->assertEquals( 'gmail.com', $data['domain'] );
		$this->assertTrue( $data['valid'] );
	}

	public function test_to_array(): void {
		$email = Email::parse( 'david@gmail.com' );
		$data = $email->to_array();

		$this->assertArrayHasKey( 'email', $data );
		$this->assertArrayHasKey( 'local', $data );
		$this->assertArrayHasKey( 'domain', $data );
		$this->assertArrayHasKey( 'tld', $data );
		$this->assertArrayHasKey( 'spam_score', $data );
		$this->assertArrayHasKey( 'common_provider', $data );
	}

}