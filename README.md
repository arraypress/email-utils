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

### One-Liners with Nullsafe Operator

```php
$domain = Email::parse( $input )?->domain();
$score  = Email::parse( $input )?->spam_score() ?? 100;
$valid  = Email::parse( $input )?->valid() ?? false;
```

### Detection Methods

```php
$email = Email::parse( 'admin@harvard.edu' );

$email->valid();                // true
$email->is_subaddressed();      // false
$email->is_common_provider();   // false
$email->is_authority_provider();// false
$email->is_educational();       // true
$email->is_government();        // false
$email->is_role_based();        // true
$email->is_common_tld();        // true
$email->is_commercial_tld();    // false
$email->is_private();           // false
$email->has_mx();               // true (performs DNS lookup)
$email->has_year();             // false
$email->has_excessive_specials(); // false
$email->has_long_local();       // false
$email->has_long_domain();      // false
```

### Immutable Transformations

```php
$email = Email::parse( 'david@gmail.com' );

// Returns new Email instances
$tagged  = $email->with_subaddress( 'shopping' );  // david+shopping@gmail.com
$clean   = $tagged->without_subaddress();          // david@gmail.com
$yearly  = $email->with_tag( 'newsletter' );       // david+newsletter-2025@gmail.com
$changed = $email->with_domain( 'yahoo.com' );     // david@yahoo.com
$new     = $email->with_local( 'john' );           // john@gmail.com
```

### Output Transformations

```php
$email = Email::parse( 'david@gmail.com' );

$email->to_anonymized();           // 'da***@gm***.com'
$email->to_masked( 2, 1 );         // 'da***d@gmail.com'
$email->to_hashed();               // 'a1b2c3...@gmail.com'
$email->to_hashed( true );         // 'a1b2c3...@d4e5f6...'
$email->to_ascii();                // Punycode for international domains
$email->to_placeholder();          // 'deleted@site.invalid'
```

### Spam Scoring

```php
$email = Email::parse( 'xj382kd92@dodgy.xyz' );

$email->spam_score();        // 0-100 (higher = more suspicious)
$email->spam_score( true );  // Include MX check (adds latency)
$email->digit_count();       // Number of digits in local part
```

### Comparison

```php
$email1 = Email::parse( 'david+test@gmail.com' );
$email2 = Email::parse( 'DAVID+other@Gmail.com' );

$email1->equals( $email2 );       // false (different subaddress)
$email1->equals_base( $email2 );  // true (same base address)
$email1->same_domain( $email2 );  // true
```

### Full Analysis

```php
$email = Email::parse( 'david+test@gmail.com' );

// Get all data as array
$data = $email->to_array();

// Or JSON encode directly
echo json_encode( $email, JSON_PRETTY_PRINT );
```

Output:
```json
{
    "email": "david+test@gmail.com",
    "original": "david+test@gmail.com",
    "valid": true,
    "local": "david+test",
    "domain": "gmail.com",
    "tld": "com",
    "base_address": "david@gmail.com",
    "subaddress": "test",
    "subaddressed": true,
    "common_provider": true,
    "authority": true,
    "role_based": false,
    "government": false,
    "educational": false,
    "private": false,
    "common_tld": true,
    "commercial_tld": false,
    "country": null,
    "has_year": false,
    "digit_count": 0,
    "mx_valid": null,
    "spam_score": 0
}
```

### Country Detection

```php
Email::parse( 'user@example.co.uk' )?->country();  // 'GB'
Email::parse( 'user@example.de' )?->country();     // 'DE'
Email::parse( 'user@example.com' )?->country();    // null (generic TLD)
```

### Factory Methods

```php
// Parse from string
$email = Email::parse( 'user@example.com' );

// Create from parts
$email = Email::from_parts( 'user', 'example.com' );
```

## Available Methods

### Getters

| Method | Returns | Description |
|--------|---------|-------------|
| `valid()` | `bool` | Whether email is valid |
| `original()` | `string` | Original input string |
| `normalized()` | `string` | Lowercase, trimmed email |
| `local()` | `string` | Local part (before @) |
| `domain()` | `string` | Domain part (after @) |
| `tld()` | `string` | Top-level domain |
| `base_local()` | `string` | Local part without subaddress |
| `base_address()` | `string` | Email without subaddress |
| `subaddress()` | `?string` | Subaddress tag or null |
| `country()` | `?string` | ISO country code or null |
| `digit_count()` | `int` | Digits in local part |

### Detection

| Method | Returns | Description |
|--------|---------|-------------|
| `has_mx()` | `bool` | Has valid MX records |
| `is_subaddressed()` | `bool` | Contains + subaddress |
| `is_common_provider()` | `bool` | Gmail, Yahoo, etc. |
| `is_authority_provider()` | `bool` | High-trust provider |
| `supports_subaddressing()` | `bool` | Provider supports + addressing |
| `is_private()` | `bool` | Localhost, internal, etc. |
| `is_common_tld()` | `bool` | .com, .org, .net, etc. |
| `is_commercial_tld()` | `bool` | .io, .co, .ai, etc. |
| `is_role_based()` | `bool` | admin@, info@, etc. |
| `is_government()` | `bool` | .gov, .mil, etc. |
| `is_educational()` | `bool` | .edu, .ac.uk, etc. |
| `is_anonymized()` | `bool` | Contains * or is placeholder |
| `has_year()` | `bool` | Contains year pattern |
| `has_excessive_specials()` | `bool` | Too many -, _, or . |
| `has_long_local()` | `bool` | Local part > 20 chars |
| `has_long_domain()` | `bool` | Domain name > 15 chars |

### Transformations (Immutable)

| Method | Returns | Description |
|--------|---------|-------------|
| `with_local()` | `?Email` | New instance with different local |
| `with_domain()` | `?Email` | New instance with different domain |
| `with_subaddress()` | `?Email` | New instance with subaddress |
| `without_subaddress()` | `?Email` | New instance without subaddress |
| `with_tag()` | `?Email` | New instance with purpose+year tag |

### Output Transformations

| Method | Returns | Description |
|--------|---------|-------------|
| `to_anonymized()` | `string` | `da***@gm***.com` |
| `to_masked()` | `string` | `d***d@gmail.com` |
| `to_hashed()` | `string` | SHA-256 hashed |
| `to_ascii()` | `string` | Punycode encoded |
| `to_placeholder()` | `string` | `deleted@site.invalid` |
| `to_array()` | `array` | Full analysis array |

### Scoring & Comparison

| Method | Returns | Description |
|--------|---------|-------------|
| `spam_score()` | `int` | 0-100 suspicion score |
| `equals()` | `bool` | Exact match |
| `equals_base()` | `bool` | Base address match |
| `same_domain()` | `bool` | Domain match |

## Requirements

- PHP 8.0+

## License

GPL-2.0-or-later