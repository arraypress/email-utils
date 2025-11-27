<?php
/**
 * Email Value Object
 *
 * An immutable value object for working with email addresses. Provides both
 * static convenience methods for quick operations and object methods for
 * comprehensive email analysis.
 *
 * @package ArrayPress\EmailUtils
 * @since   1.0.0
 * @author  ArrayPress
 * @license GPL-2.0-or-later
 */

declare( strict_types=1 );

namespace ArrayPress\EmailUtils;

use InvalidArgumentException;
use JsonSerializable;
use Stringable;

class Email implements JsonSerializable, Stringable {

	/** -------------------------------------------------------------------------
	 * Constants
	 * ---------------------------------------------------------------------- */

	/**
	 * The standard anonymized email placeholder.
	 */
	public const ANONYMIZED_PLACEHOLDER = 'deleted@site.invalid';

	/**
	 * Common email provider domains.
	 */
	public const COMMON_PROVIDERS = [
		'gmail.com',
		'yahoo.com',
		'hotmail.com',
		'outlook.com',
		'aol.com',
		'icloud.com',
		'protonmail.com',
		'mail.com',
		'zoho.com',
		'live.com',
	];

	/**
	 * Authoritative email providers (high trust, strong anti-fraud).
	 */
	public const AUTHORITY_PROVIDERS = [
		'gmail.com',
		'icloud.com',
		'outlook.com',
		'hotmail.com',
		'yahoo.com',
		'protonmail.com',
	];

	/**
	 * Email providers that support subaddressing (plus addressing).
	 */
	public const SUBADDRESS_PROVIDERS = [
		'gmail.com',
		'googlemail.com',
		'yahoo.com',
		'fastmail.com',
		'outlook.com',
		'hotmail.com',
		'protonmail.com',
	];

	/**
	 * Common TLDs for validation scoring.
	 */
	public const COMMON_TLDS = [
		'com',
		'org',
		'net',
		'edu',
		'gov',
		'io',
		'co',
		'us',
		'uk',
		'ca',
	];

	/**
	 * Commercial TLDs (ccTLDs used commercially rather than geographically).
	 */
	public const COMMERCIAL_TLDS = [
		'io',
		'co',
		'tv',
		'ai',
		'me',
		'cc',
		'ws',
		'fm',
		'gg',
		'ly',
	];

	/**
	 * Role-based email prefixes.
	 */
	public const ROLE_PREFIXES = [
		'admin',
		'administrator',
		'info',
		'support',
		'sales',
		'contact',
		'hello',
		'help',
		'marketing',
		'noreply',
		'no-reply',
		'postmaster',
		'webmaster',
		'abuse',
		'billing',
	];

	/**
	 * Country code TLD to ISO 3166-1 alpha-2 mapping.
	 */
	public const COUNTRY_CODES = [
		'ac' => 'SH',
		'ad' => 'AD',
		'ae' => 'AE',
		'af' => 'AF',
		'ag' => 'AG',
		'ai' => 'AI',
		'al' => 'AL',
		'am' => 'AM',
		'ao' => 'AO',
		'aq' => 'AQ',
		'ar' => 'AR',
		'as' => 'AS',
		'at' => 'AT',
		'au' => 'AU',
		'aw' => 'AW',
		'ax' => 'AX',
		'az' => 'AZ',
		'ba' => 'BA',
		'bb' => 'BB',
		'bd' => 'BD',
		'be' => 'BE',
		'bf' => 'BF',
		'bg' => 'BG',
		'bh' => 'BH',
		'bi' => 'BI',
		'bj' => 'BJ',
		'bl' => 'BL',
		'bm' => 'BM',
		'bn' => 'BN',
		'bo' => 'BO',
		'bq' => 'BQ',
		'br' => 'BR',
		'bs' => 'BS',
		'bt' => 'BT',
		'bv' => 'BV',
		'bw' => 'BW',
		'by' => 'BY',
		'bz' => 'BZ',
		'ca' => 'CA',
		'cc' => 'CC',
		'cd' => 'CD',
		'cf' => 'CF',
		'cg' => 'CG',
		'ch' => 'CH',
		'ci' => 'CI',
		'ck' => 'CK',
		'cl' => 'CL',
		'cm' => 'CM',
		'cn' => 'CN',
		'co' => 'CO',
		'cr' => 'CR',
		'cu' => 'CU',
		'cv' => 'CV',
		'cw' => 'CW',
		'cx' => 'CX',
		'cy' => 'CY',
		'cz' => 'CZ',
		'de' => 'DE',
		'dj' => 'DJ',
		'dk' => 'DK',
		'dm' => 'DM',
		'do' => 'DO',
		'dz' => 'DZ',
		'ec' => 'EC',
		'ee' => 'EE',
		'eg' => 'EG',
		'eh' => 'EH',
		'er' => 'ER',
		'es' => 'ES',
		'et' => 'ET',
		'eu' => 'EU',
		'fi' => 'FI',
		'fj' => 'FJ',
		'fk' => 'FK',
		'fm' => 'FM',
		'fo' => 'FO',
		'fr' => 'FR',
		'ga' => 'GA',
		'gb' => 'GB',
		'gd' => 'GD',
		'ge' => 'GE',
		'gf' => 'GF',
		'gg' => 'GG',
		'gh' => 'GH',
		'gi' => 'GI',
		'gl' => 'GL',
		'gm' => 'GM',
		'gn' => 'GN',
		'gp' => 'GP',
		'gq' => 'GQ',
		'gr' => 'GR',
		'gs' => 'GS',
		'gt' => 'GT',
		'gu' => 'GU',
		'gw' => 'GW',
		'gy' => 'GY',
		'hk' => 'HK',
		'hm' => 'HM',
		'hn' => 'HN',
		'hr' => 'HR',
		'ht' => 'HT',
		'hu' => 'HU',
		'id' => 'ID',
		'ie' => 'IE',
		'il' => 'IL',
		'im' => 'IM',
		'in' => 'IN',
		'io' => 'IO',
		'iq' => 'IQ',
		'ir' => 'IR',
		'is' => 'IS',
		'it' => 'IT',
		'je' => 'JE',
		'jm' => 'JM',
		'jo' => 'JO',
		'jp' => 'JP',
		'ke' => 'KE',
		'kg' => 'KG',
		'kh' => 'KH',
		'ki' => 'KI',
		'km' => 'KM',
		'kn' => 'KN',
		'kp' => 'KP',
		'kr' => 'KR',
		'kw' => 'KW',
		'ky' => 'KY',
		'kz' => 'KZ',
		'la' => 'LA',
		'lb' => 'LB',
		'lc' => 'LC',
		'li' => 'LI',
		'lk' => 'LK',
		'lr' => 'LR',
		'ls' => 'LS',
		'lt' => 'LT',
		'lu' => 'LU',
		'lv' => 'LV',
		'ly' => 'LY',
		'ma' => 'MA',
		'mc' => 'MC',
		'md' => 'MD',
		'me' => 'ME',
		'mf' => 'MF',
		'mg' => 'MG',
		'mh' => 'MH',
		'mk' => 'MK',
		'ml' => 'ML',
		'mm' => 'MM',
		'mn' => 'MN',
		'mo' => 'MO',
		'mp' => 'MP',
		'mq' => 'MQ',
		'mr' => 'MR',
		'ms' => 'MS',
		'mt' => 'MT',
		'mu' => 'MU',
		'mv' => 'MV',
		'mw' => 'MW',
		'mx' => 'MX',
		'my' => 'MY',
		'mz' => 'MZ',
		'na' => 'NA',
		'nc' => 'NC',
		'ne' => 'NE',
		'nf' => 'NF',
		'ng' => 'NG',
		'ni' => 'NI',
		'nl' => 'NL',
		'no' => 'NO',
		'np' => 'NP',
		'nr' => 'NR',
		'nu' => 'NU',
		'nz' => 'NZ',
		'om' => 'OM',
		'pa' => 'PA',
		'pe' => 'PE',
		'pf' => 'PF',
		'pg' => 'PG',
		'ph' => 'PH',
		'pk' => 'PK',
		'pl' => 'PL',
		'pm' => 'PM',
		'pn' => 'PN',
		'pr' => 'PR',
		'ps' => 'PS',
		'pt' => 'PT',
		'pw' => 'PW',
		'py' => 'PY',
		'qa' => 'QA',
		're' => 'RE',
		'ro' => 'RO',
		'rs' => 'RS',
		'ru' => 'RU',
		'rw' => 'RW',
		'sa' => 'SA',
		'sb' => 'SB',
		'sc' => 'SC',
		'sd' => 'SD',
		'se' => 'SE',
		'sg' => 'SG',
		'sh' => 'SH',
		'si' => 'SI',
		'sj' => 'SJ',
		'sk' => 'SK',
		'sl' => 'SL',
		'sm' => 'SM',
		'sn' => 'SN',
		'so' => 'SO',
		'sr' => 'SR',
		'ss' => 'SS',
		'st' => 'ST',
		'su' => 'RU',
		'sv' => 'SV',
		'sx' => 'SX',
		'sy' => 'SY',
		'sz' => 'SZ',
		'tc' => 'TC',
		'td' => 'TD',
		'tf' => 'TF',
		'tg' => 'TG',
		'th' => 'TH',
		'tj' => 'TJ',
		'tk' => 'TK',
		'tl' => 'TL',
		'tm' => 'TM',
		'tn' => 'TN',
		'to' => 'TO',
		'tr' => 'TR',
		'tt' => 'TT',
		'tv' => 'TV',
		'tw' => 'TW',
		'tz' => 'TZ',
		'ua' => 'UA',
		'ug' => 'UG',
		'uk' => 'GB',
		'us' => 'US',
		'uy' => 'UY',
		'uz' => 'UZ',
		'va' => 'VA',
		'vc' => 'VC',
		've' => 'VE',
		'vg' => 'VG',
		'vi' => 'VI',
		'vn' => 'VN',
		'vu' => 'VU',
		'wf' => 'WF',
		'ws' => 'WS',
		'ye' => 'YE',
		'yt' => 'YT',
		'za' => 'ZA',
		'zm' => 'ZM',
		'zw' => 'ZW',
	];

	/**
	 * Government domain patterns.
	 */
	public const GOVERNMENT_PATTERNS = [
		'.gov',
		'.mil',
		'.gov.uk',
		'.gov.au',
		'.gov.nz',
		'.gov.ca',
		'.gov.ie',
		'.gov.za',
		'.gov.sg',
		'.gov.hk',
		'.gov.ph',
		'.gov.my',
		'.gov.in',
		'.gov.pk',
		'.gov.bd',
		'.gov.ng',
		'.gov.gh',
		'.gov.ke',
		'.govt.nz',
		'.gob.mx',
		'.gob.es',
		'.gob.ar',
		'.gob.cl',
		'.gob.pe',
		'.gob.co',
		'.gob.ve',
		'.gob.ec',
		'.gob.bo',
		'.gob.py',
		'.gob.uy',
		'.gob.pa',
		'.gob.cr',
		'.gob.gt',
		'.gob.hn',
		'.gob.sv',
		'.gob.ni',
		'.gob.do',
		'.gob.cu',
		'.gob.pr',
		'.gov.br',
		'.gov.pt',
		'.gov.ao',
		'.gov.mz',
		'.gouv.fr',
		'.gouv.ca',
		'.gouv.be',
		'.gouv.ch',
		'.gouv.sn',
		'.gouv.ci',
		'.gouv.ml',
		'.gouv.bf',
		'.gouv.ne',
		'.gouv.tg',
		'.gouv.bj',
		'.gouv.mg',
		'.gouv.cd',
		'.gouv.cg',
		'.gouv.cm',
		'.gv.at',
		'.admin.ch',
		'.bund.de',
		'.overheid.nl',
		'.gov.be',
		'.gov.it',
		'.gov.pl',
		'.gov.cz',
		'.gov.hu',
		'.gov.ro',
		'.gov.bg',
		'.gov.ru',
		'.gov.ua',
		'.gov.il',
		'.gov.tr',
		'.gov.ae',
		'.gov.sa',
		'.gov.cn',
		'.gov.jp',
		'.go.jp',
		'.gov.kr',
		'.go.kr',
		'.go.th',
		'.gov.vn',
		'.gov.id',
		'.go.id',
		'.gov.',
		'.gob.',
		'.gouv.',
		'.govt.',
		'.go.',
		'.gv.',
	];

	/**
	 * Educational domain patterns.
	 */
	public const EDUCATIONAL_PATTERNS = [
		'.ac.uk',
		'.ac.jp',
		'.ac.kr',
		'.ac.nz',
		'.ac.za',
		'.ac.in',
		'.ac.th',
		'.ac.id',
		'.ac.cn',
		'.ac.tw',
		'.ac.il',
		'.ac.ir',
		'.ac.ae',
		'.ac.ke',
		'.ac.tz',
		'.ac.ug',
		'.ac.zw',
		'.ac.rw',
		'.ac.at',
		'.ac.be',
		'.ac.bd',
		'.ac.lk',
		'.ac.np',
		'.edu.au',
		'.edu.cn',
		'.edu.tw',
		'.edu.hk',
		'.edu.sg',
		'.edu.my',
		'.edu.ph',
		'.edu.vn',
		'.edu.pk',
		'.edu.br',
		'.edu.mx',
		'.edu.ar',
		'.edu.cl',
		'.edu.co',
		'.edu.pe',
		'.edu.ve',
		'.edu.ec',
		'.edu.bo',
		'.edu.pl',
		'.edu.tr',
		'.edu.sa',
		'.edu.qa',
		'.edu.ae',
		'.edu.eg',
		'.edu.ng',
		'.edu.gh',
		'.edu.ru',
		'.edu.ua',
		'.edu.gr',
		'.edu.it',
		'.edu.es',
		'.edu.pt',
		'.sch.uk',
		'.sch.id',
		'.sch.ir',
		'.sch.sa',
		'.sch.ae',
		'.sch.ng',
		'.sch.ke',
		'.sch.za',
		'.sch.nz',
		'.k12.tr',
		'.k12.ec',
		'.k12.il',
		'.edu.',
		'.ac.',
		'.sch.',
		'.k12.',
	];

	/**
	 * Common domain typos mapped to their corrections.
	 */
	private const DOMAIN_CORRECTIONS = [
		// Gmail typos
		'gmial.com'      => 'gmail.com',
		'gmali.com'      => 'gmail.com',
		'gmai.com'       => 'gmail.com',
		'gmail.co'       => 'gmail.com',
		'gmal.com'       => 'gmail.com',
		'gnail.com'      => 'gmail.com',
		'gmaill.com'     => 'gmail.com',
		'gamil.com'      => 'gmail.com',
		'gmailc.om'      => 'gmail.com',
		'gmail.cm'       => 'gmail.com',
		'gmail.om'       => 'gmail.com',
		'gmail.cim'      => 'gmail.com',
		'gmail.con'      => 'gmail.com',
		'gmaik.com'      => 'gmail.com',
		'gmsil.com'      => 'gmail.com',
		'gmeil.com'      => 'gmail.com',
		'fmail.com'      => 'gmail.com',
		'hmail.com'      => 'gmail.com',

		// Yahoo typos
		'yaho.com'       => 'yahoo.com',
		'yahooo.com'     => 'yahoo.com',
		'yhoo.com'       => 'yahoo.com',
		'yahho.com'      => 'yahoo.com',
		'yhaoo.com'      => 'yahoo.com',
		'yaoo.com'       => 'yahoo.com',
		'yahoo.co'       => 'yahoo.com',
		'yahoo.cm'       => 'yahoo.com',
		'yahoo.con'      => 'yahoo.com',
		'tahoo.com'      => 'yahoo.com',
		'uahoo.com'      => 'yahoo.com',

		// Hotmail typos
		'hotmal.com'     => 'hotmail.com',
		'hotmai.com'     => 'hotmail.com',
		'hotmial.com'    => 'hotmail.com',
		'hotamil.com'    => 'hotmail.com',
		'hotmali.com'    => 'hotmail.com',
		'hotmaill.com'   => 'hotmail.com',
		'hotmil.com'     => 'hotmail.com',
		'hotmail.co'     => 'hotmail.com',
		'hotmail.cm'     => 'hotmail.com',
		'hotmail.con'    => 'hotmail.com',
		'hitmail.com'    => 'hotmail.com',
		'hotnail.com'    => 'hotmail.com',
		'homail.com'     => 'hotmail.com',

		// Outlook typos
		'outlok.com'     => 'outlook.com',
		'outloo.com'     => 'outlook.com',
		'outlool.com'    => 'outlook.com',
		'outloook.com'   => 'outlook.com',
		'outluk.com'     => 'outlook.com',
		'outllok.com'    => 'outlook.com',
		'outlook.co'     => 'outlook.com',
		'outlook.cm'     => 'outlook.com',
		'outlook.con'    => 'outlook.com',
		'putlook.com'    => 'outlook.com',
		'outoolk.com'    => 'outlook.com',

		// iCloud typos
		'iclod.com'      => 'icloud.com',
		'icoud.com'      => 'icloud.com',
		'icloud.co'      => 'icloud.com',
		'icloud.cm'      => 'icloud.com',
		'icloud.con'     => 'icloud.com',
		'icluod.com'     => 'icloud.com',
		'iclould.com'    => 'icloud.com',

		// AOL typos
		'aol.co'         => 'aol.com',
		'aol.cm'         => 'aol.com',
		'aol.con'        => 'aol.com',
		'aoll.com'       => 'aol.com',
		'ao.com'         => 'aol.com',

		// Protonmail typos
		'protonmal.com'  => 'protonmail.com',
		'protonmai.com'  => 'protonmail.com',
		'protonmail.co'  => 'protonmail.com',
		'protonmail.cm'  => 'protonmail.com',
		'protonmail.con' => 'protonmail.com',
		'protonmial.com' => 'protonmail.com',
		'protnmail.com'  => 'protonmail.com',

		// Live typos
		'live.co'        => 'live.com',
		'live.cm'        => 'live.com',
		'live.con'       => 'live.com',
		'liv.com'        => 'live.com',
		'livee.com'      => 'live.com',
	];

	/** -------------------------------------------------------------------------
	 * Instance Properties
	 * ---------------------------------------------------------------------- */

	/**
	 * The original email string.
	 *
	 * @var string
	 */
	private string $original;

	/**
	 * The normalized (lowercased, trimmed) email.
	 *
	 * @var string
	 */
	private string $normalized;

	/**
	 * The local part (before @).
	 *
	 * @var string
	 */
	private string $local;

	/**
	 * The domain part (after @).
	 *
	 * @var string
	 */
	private string $domain;

	/**
	 * The TLD.
	 *
	 * @var string
	 */
	private string $tld;

	/**
	 * Whether the email is valid.
	 *
	 * @var bool
	 */
	private bool $valid;

	/** -------------------------------------------------------------------------
	 * Constructor & Factory Methods
	 * ---------------------------------------------------------------------- */

	/**
	 * Create a new Email instance.
	 *
	 * @param string $email The email address.
	 *
	 * @throws InvalidArgumentException If email is empty.
	 */
	public function __construct( string $email ) {
		$email = trim( $email );

		if ( $email === '' ) {
			throw new InvalidArgumentException( 'Email address cannot be empty.' );
		}

		$this->original = $email;
		$this->valid    = self::validate_email( $email );

		if ( $this->valid ) {
			$this->normalized = strtolower( $email );
			[ $this->local, $this->domain ] = explode( '@', $this->normalized );
			$this->tld = strtolower( substr( strrchr( $this->domain, '.' ), 1 ) );
		} else {
			$this->normalized = $email;
			$this->local      = '';
			$this->domain     = '';
			$this->tld        = '';
		}
	}

	/**
	 * Parse an email and return an Email instance or null if invalid.
	 *
	 * @param string $email The email address.
	 *
	 * @return self|null Email instance or null if invalid.
	 */
	public static function parse( string $email ): ?self {
		try {
			$instance = new self( $email );

			return $instance->valid() ? $instance : null;
		} catch ( InvalidArgumentException $e ) {
			return null;
		}
	}

	/**
	 * Create an Email instance from parts.
	 *
	 * @param string $local  The local part.
	 * @param string $domain The domain part.
	 *
	 * @return self|null Email instance or null if invalid.
	 */
	public static function from_parts( string $local, string $domain ): ?self {
		return self::parse( $local . '@' . $domain );
	}

	/** -------------------------------------------------------------------------
	 * Object Getters - Basic Parts
	 * ---------------------------------------------------------------------- */

	/**
	 * Get the original email string.
	 *
	 * @return string The original email.
	 */
	public function original(): string {
		return $this->original;
	}

	/**
	 * Get the normalized email (lowercase, trimmed).
	 *
	 * @return string The normalized email.
	 */
	public function normalized(): string {
		return $this->normalized;
	}

	/**
	 * Get the local part (before @).
	 *
	 * @return string The local part.
	 */
	public function local(): string {
		return $this->local;
	}

	/**
	 * Get the domain part (after @).
	 *
	 * @return string The domain.
	 */
	public function domain(): string {
		return $this->domain;
	}

	/**
	 * Get the TLD.
	 *
	 * @return string The TLD.
	 */
	public function tld(): string {
		return $this->tld;
	}

	/**
	 * Get the base local part (without subaddress).
	 *
	 * @return string The base local part.
	 */
	public function base_local(): string {
		return explode( '+', $this->local )[0];
	}

	/**
	 * Get the base email address (without subaddress).
	 *
	 * @return string The base email.
	 */
	public function base_address(): string {
		if ( ! $this->valid ) {
			return '';
		}

		return $this->base_local() . '@' . $this->domain;
	}

	/**
	 * Get the subaddress (tag after +).
	 *
	 * @return string|null The subaddress or null if none.
	 */
	public function subaddress(): ?string {
		if ( ! $this->is_subaddressed() ) {
			return null;
		}

		$parts = explode( '+', $this->local, 2 );

		return $parts[1] ?? null;
	}

	/**
	 * Get the country ISO code from TLD.
	 *
	 * @return string|null ISO 3166-1 alpha-2 code or null.
	 */
	public function country(): ?string {
		return self::COUNTRY_CODES[ $this->tld ] ?? null;
	}

	/**
	 * Count digits in the local part.
	 *
	 * @return int Number of digits.
	 */
	public function digit_count(): int {
		return (int) preg_match_all( '/\d/', $this->local );
	}

	/** -------------------------------------------------------------------------
	 * Object Detection Methods
	 * ---------------------------------------------------------------------- */

	/**
	 * Check if the email is valid.
	 *
	 * @return bool True if valid.
	 */
	public function valid(): bool {
		return $this->valid;
	}

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
	 * Check if from a common provider.
	 *
	 * @return bool True if common provider.
	 */
	public function is_common_provider(): bool {
		return in_array( $this->domain, self::COMMON_PROVIDERS, true );
	}

	/**
	 * Check if from an authority provider.
	 *
	 * @return bool True if authority provider.
	 */
	public function is_authority_provider(): bool {
		return in_array( $this->domain, self::AUTHORITY_PROVIDERS, true );
	}

	/**
	 * Check if the domain appears to be a typo.
	 *
	 * @return bool True if the domain matches a known typo.
	 */
	public function has_typo(): bool {
		return isset( self::DOMAIN_CORRECTIONS[ $this->domain ] );
	}

	/**
	 * Get the suggested correction for a typo domain.
	 *
	 * @return string|null The corrected domain or null if no typo detected.
	 */
	public function suggested_domain(): ?string {
		return self::DOMAIN_CORRECTIONS[ $this->domain ] ?? null;
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
	 * Check if provider supports subaddressing.
	 *
	 * @return bool True if subaddressing supported.
	 */
	public function supports_subaddressing(): bool {
		return in_array( $this->domain, self::SUBADDRESS_PROVIDERS, true );
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

		$patterns = [ 'localhost', '127.0.0.1', '192.168.', '10.', '172.16.', '.local', '.internal', '.test' ];

		foreach ( $patterns as $pattern ) {
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
		return in_array( $this->tld, self::COMMON_TLDS, true );
	}

	/**
	 * Check if TLD is commercially used.
	 *
	 * @return bool True if commercial TLD.
	 */
	public function is_commercial_tld(): bool {
		return in_array( $this->tld, self::COMMERCIAL_TLDS, true );
	}

	/**
	 * Check if role-based address.
	 *
	 * @param array $additional_prefixes Optional extra prefixes.
	 *
	 * @return bool True if role-based.
	 */
	public function is_role_based( array $additional_prefixes = [] ): bool {
		$prefixes   = array_merge( self::ROLE_PREFIXES, $additional_prefixes );
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

		foreach ( self::GOVERNMENT_PATTERNS as $pattern ) {
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

		foreach ( self::EDUCATIONAL_PATTERNS as $pattern ) {
			if ( str_contains( $this->domain, $pattern ) ) {
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

	/** -------------------------------------------------------------------------
	 * Immutable Transformation Methods (return new instance)
	 * ---------------------------------------------------------------------- */

	/**
	 * Create new instance with different local part.
	 *
	 * @param string $local The new local part.
	 *
	 * @return self|null New Email instance or null if invalid.
	 */
	public function with_local( string $local ): ?self {
		return self::parse( $local . '@' . $this->domain );
	}

	/**
	 * Create new instance with different domain.
	 *
	 * @param string $domain The new domain.
	 *
	 * @return self|null New Email instance or null if invalid.
	 */
	public function with_domain( string $domain ): ?self {
		return self::parse( $this->local . '@' . $domain );
	}

	/**
	 * Create new instance with subaddress added/changed.
	 *
	 * @param string $tag The subaddress tag.
	 *
	 * @return self|null New Email instance or null if not supported.
	 */
	public function with_subaddress( string $tag ): ?self {
		if ( ! $this->valid || ! $this->supports_subaddressing() ) {
			return null;
		}

		$tag = trim( str_replace( [ '+', ' ' ], [ '', '-' ], $tag ) );
		if ( $tag === '' ) {
			return null;
		}

		return self::parse( $this->base_local() . '+' . $tag . '@' . $this->domain );
	}

	/**
	 * Create new instance without subaddress.
	 *
	 * @return self|null New Email instance.
	 */
	public function without_subaddress(): ?self {
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
	 * @return self|null New Email instance or null if not supported.
	 */
	public function with_tag( string $purpose, ?string $suffix = null ): ?self {
		$tag = strtolower( $purpose );
		$tag .= '-' . ( $suffix ?? date( 'Y' ) );

		return $this->with_subaddress( $tag );
	}

	/** -------------------------------------------------------------------------
	 * Output Transformation Methods
	 * ---------------------------------------------------------------------- */

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
			$ascii_domain = idn_to_ascii( $this->domain, IDNA_NONTRANSITIONAL_TO_ASCII, INTL_IDNA_VARIANT_UTS46 );
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

	/** -------------------------------------------------------------------------
	 * Scoring & Analysis
	 * ---------------------------------------------------------------------- */

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
			'private'         => $this->is_private(),
			'common_tld'      => $this->is_common_tld(),
			'commercial_tld'  => $this->is_commercial_tld(),
			'country'         => $this->country(),
			'has_year'        => $this->has_year(),
			'digit_count'     => $this->digit_count(),
			'mx_valid'        => $check_mx ? $this->has_mx() : null,
			'spam_score'      => $this->spam_score( $check_mx ),
		];
	}

	/** -------------------------------------------------------------------------
	 * Comparison Methods
	 * ---------------------------------------------------------------------- */

	/**
	 * Check if this email equals another.
	 *
	 * @param Email|string $other Email to compare.
	 *
	 * @return bool True if equal.
	 */
	public function equals( $other ): bool {
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
	 * @param Email|string $other Email to compare.
	 *
	 * @return bool True if same base address.
	 */
	public function equals_base( $other ): bool {
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
	 * @param Email|string $other Email to compare.
	 *
	 * @return bool True if same domain.
	 */
	public function same_domain( $other ): bool {
		if ( is_string( $other ) ) {
			$other = self::parse( $other );
		}

		if ( ! $other instanceof self ) {
			return false;
		}

		return $this->domain === $other->domain();
	}

	/** -------------------------------------------------------------------------
	 * Interface Implementations
	 * ---------------------------------------------------------------------- */

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

	/** -------------------------------------------------------------------------
	 * Protected Methods
	 * ---------------------------------------------------------------------- */

	/**
	 * Validate an email address.
	 *
	 * @param string $email The email to validate.
	 *
	 * @return bool True if valid.
	 */
	protected static function validate_email( string $email ): bool {
		if ( function_exists( 'is_email' ) ) {
			return is_email( $email ) !== false;
		}

		if ( function_exists( 'is_valid_email' ) ) {
			return is_valid_email( $email );
		}

		return filter_var( $email, FILTER_VALIDATE_EMAIL ) !== false;
	}

	/**
	 * Get salt for hashing.
	 *
	 * @return string Salt string.
	 */
	protected function get_salt(): string {
		if ( function_exists( 'wp_salt' ) ) {
			return wp_salt() . wp_salt( 'secure_auth' ) . wp_salt( 'logged_in' ) . wp_salt( 'nonce' );
		}

		return hash( 'sha256', __DIR__ . php_uname( 'n' ) . PHP_VERSION );
	}

}