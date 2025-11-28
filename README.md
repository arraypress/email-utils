# Email Utils

An immutable value object for working with email addresses. Provides parsing, validation, transformation, and analysis.

## Installation

```bash
composer require arraypress/email-utils
```

## Usage

### Basic Parsing

```php
use ArrayPress\EmailUtils\Email;

$email = Email::parse( 'david+newsletter@gmail.com' );

if ( $email ) {
    $email->domain();        // 'gmail.com'
    $email->local();         // 'david+newsletter'
    $email->tld();           // 'com'
    $email->base_address();  // 'david@gmail.com'
    $email->subaddress();    // 'newsletter'
}
```

### One-Liners

```php
$domain = Email::parse( $input )?->domain();
$score  = Email::parse( $input )?->spam_score() ?? 100;
$valid  = Email::parse( $input )?->valid() ?? false;
```

### Detection

```php
$email = Email::parse( 'admin@harvard.edu' );

$email->valid();                 // true
$email->is_educational();        // true
$email->is_role_based();         // true
$email->is_common_provider();    // false
$email->is_authority_provider(); // false
$email->is_government();         // false
$email->is_military();           // false
$email->is_private();            // false
$email->is_auto_generated();     // false
$email->has_mx();                // true (DNS lookup)
```

### Typo Detection

```php
$email = Email::parse( 'user@gmial.com' );

$email->has_typo();          // true
$email->suggested_domain();  // 'gmail.com'
$email->suggested_email();   // 'user@gmail.com'
```

### Transformations

```php
$email = Email::parse( 'david@gmail.com' );

// Immutable - returns new instances
$email->with_subaddress( 'shopping' );  // david+shopping@gmail.com
$email->with_tag( 'newsletter' );       // david+newsletter-2025@gmail.com
$email->with_domain( 'yahoo.com' );     // david@yahoo.com
$email->with_local( 'john' );           // john@gmail.com
$email->without_subaddress();           // david@gmail.com
```

### Output Formats

```php
$email = Email::parse( 'david@gmail.com' );

$email->to_anonymized();      // 'da***@gm***.com'
$email->to_masked( 2, 1 );    // 'da**d@gmail.com'
$email->to_hashed();          // 'a1b2c3...@gmail.com'
$email->to_hashed( true );    // 'a1b2c3...@d4e5f6...'
$email->to_placeholder();     // 'deleted@site.invalid'
```

### Spam Scoring

```php
$email = Email::parse( 'xj382kd92@dodgy.xyz' );

$email->spam_score();        // 0-100 (higher = worse)
$email->spam_score( true );  // Include MX check
$email->spam_rating();       // 'excellent', 'good', 'fair', 'poor', 'bad'
```

### Comparison

```php
$email1 = Email::parse( 'david+test@gmail.com' );
$email2 = Email::parse( 'david+other@gmail.com' );

$email1->equals( $email2 );       // false
$email1->equals_base( $email2 );  // true
$email1->same_domain( $email2 );  // true
```

### Country Detection

```php
Email::parse( 'user@example.co.uk' )?->country();  // 'GB'
Email::parse( 'user@example.de' )?->country();     // 'DE'
Email::parse( 'user@example.com' )?->country();    // null
```

### Array Output

```php
$email = Email::parse( 'david@gmail.com' );

// Simplified (for APIs)
$email->to_simple_array();

// Comprehensive
$email->to_array();

// JSON
echo json_encode( $email );
```

## Methods

### Getters

| Method           | Returns   | Description                   |
|------------------|-----------|-------------------------------|
| `valid()`        | `bool`    | Whether email is valid        |
| `original()`     | `string`  | Original input string         |
| `normalized()`   | `string`  | Lowercase, trimmed email      |
| `local()`        | `string`  | Local part (before @)         |
| `domain()`       | `string`  | Domain part (after @)         |
| `tld()`          | `string`  | Top-level domain              |
| `base_local()`   | `string`  | Local part without subaddress |
| `base_address()` | `string`  | Email without subaddress      |
| `subaddress()`   | `?string` | Subaddress tag or null        |
| `country()`      | `?string` | ISO country code or null      |
| `digit_count()`  | `int`     | Digits in local part          |

### Detection

| Method                     | Returns | Description                    |
|----------------------------|---------|--------------------------------|
| `has_mx()`                 | `bool`  | Has valid MX records           |
| `is_subaddressed()`        | `bool`  | Contains + subaddress          |
| `is_common_provider()`     | `bool`  | Gmail, Yahoo, etc.             |
| `is_authority_provider()`  | `bool`  | High-trust provider            |
| `supports_subaddressing()` | `bool`  | Provider supports + addressing |
| `is_private()`             | `bool`  | Localhost, internal, etc.      |
| `is_common_tld()`          | `bool`  | .com, .org, .net, etc.         |
| `is_commercial_tld()`      | `bool`  | .io, .co, .ai, etc.            |
| `is_role_based()`          | `bool`  | admin@, info@, etc.            |
| `is_government()`          | `bool`  | .gov, .gob, .gouv, etc.        |
| `is_educational()`         | `bool`  | .edu, .ac.uk, etc.             |
| `is_military()`            | `bool`  | .mil, military domains         |
| `is_anonymized()`          | `bool`  | Contains * or is placeholder   |
| `is_auto_generated()`      | `bool`  | Random-looking local part      |
| `has_typo()`               | `bool`  | Domain is a known typo         |
| `has_year()`               | `bool`  | Contains year pattern          |
| `has_excessive_specials()` | `bool`  | Too many -, _, or .            |
| `has_long_local()`         | `bool`  | Local part > 20 chars          |
| `has_long_domain()`        | `bool`  | Domain name > 15 chars         |

### Typo Suggestions

| Method               | Returns   | Description                  |
|----------------------|-----------|------------------------------|
| `suggested_domain()` | `?string` | Corrected domain or null     |
| `suggested_email()`  | `?string` | Full corrected email or null |

### Transformations

| Method                 | Returns   | Description                        |
|------------------------|-----------|------------------------------------|
| `with_local()`         | `?static` | New instance with different local  |
| `with_domain()`        | `?static` | New instance with different domain |
| `with_subaddress()`    | `?static` | New instance with subaddress       |
| `without_subaddress()` | `?static` | New instance without subaddress    |
| `with_tag()`           | `?static` | New instance with purpose+year tag |

### Output

| Method              | Returns  | Description            |
|---------------------|----------|------------------------|
| `to_anonymized()`   | `string` | `da***@gm***.com`      |
| `to_masked()`       | `string` | `d***d@gmail.com`      |
| `to_hashed()`       | `string` | SHA-256 hashed         |
| `to_ascii()`        | `string` | Punycode encoded       |
| `to_placeholder()`  | `string` | `deleted@site.invalid` |
| `to_simple_array()` | `array`  | Simplified for APIs    |
| `to_array()`        | `array`  | Full analysis          |

### Scoring

| Method          | Returns  | Description                  |
|-----------------|----------|------------------------------|
| `spam_score()`  | `int`    | 0-100 suspicion score        |
| `spam_rating()` | `string` | excellent/good/fair/poor/bad |

### Comparison

| Method          | Returns | Description        |
|-----------------|---------|--------------------|
| `equals()`      | `bool`  | Exact match        |
| `equals_base()` | `bool`  | Base address match |
| `same_domain()` | `bool`  | Domain match       |

## Requirements

- PHP 8.2+

## License

GPL-2.0-or-later