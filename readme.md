# http_build_url() for PHP

[![Tests](https://github.com/ozh/http_build_url/actions/workflows/test.yml/badge.svg)](https://github.com/ozh/http_build_url/actions/workflows/test.yml)
[![Code Climate](https://codeclimate.com/github/jakeasmith/http_build_url/badges/gpa.svg)](https://codeclimate.com/github/jakeasmith/http_build_url)
[![Latest Stable Version](https://poser.pugx.org/jakeasmith/http_build_url/v/stable.png)](https://packagist.org/packages/jakeasmith/http_build_url)
[![Total Downloads](https://poser.pugx.org/jakeasmith/http_build_url/downloads.png)](https://packagist.org/packages/jakeasmith/http_build_url)

This simple library provides functionality for [`http_build_url()`](http://us2.php.net/manual/en/function.http-build-url.php) to environments without pecl_http. It aims to mimic the functionality of the pecl function in every way and ships with a full suite of tests that have been run against both the original function and the one in this package.

## Installation

The easiest way to install this library is to use [Composer](https://getcomposer.org/) from the command line.

``` 
$ composer require jakeasmith/http_build_url ^1
```

## Usage

The `http_build_url()` function builds a URL from an array of parts or merges two URLs together according to specified flags.

### Basic Examples

#### Building a URL from parts

```php
<?php
require 'vendor/autoload.php';

// Build a simple URL from an array of parts
$url = http_build_url([
    'scheme' => 'https',
    'host'   => 'www.example.com',
    'path'   => '/path/to/page',
    'query'  => 'foo=bar'
]);

echo $url; // https://www.example.com/path/to/page?foo=bar
```

#### Merging URL parts

```php
<?php
require 'vendor/autoload.php';

// Start with a base URL and modify specific parts
$base_url = "http://user:pass@www.example.com:8080/pub/index.php?a=b#files";

$new_url = http_build_url($base_url, [
    'scheme' => 'https',
    'host'   => 'secure.example.com',
    'path'   => '/new/path.php'
]);

echo $new_url; // https://user:pass@secure.example.com:8080/new/path.php?a=b#files
```

### Advanced Examples with Flags

The function accepts various flags to control how URLs are merged and manipulated:

#### Joining paths

```php
<?php
require 'vendor/autoload.php';

// Join paths together instead of replacing
$url = http_build_url(
    "http://example.com/foo/bar/",
    ['path' => 'baz/qux'],
    HTTP_URL_JOIN_PATH
);

echo $url; // http://example.com/foo/bar/baz/qux
```

#### Joining query strings

```php
<?php
require 'vendor/autoload.php';

// Merge query parameters instead of replacing
$url = http_build_url(
    "http://example.com/page?foo=bar&old=value",
    ['query' => 'foo=new&extra=param'],
    HTTP_URL_JOIN_QUERY
);

echo $url; // http://example.com/page?foo=new&old=value&extra=param
```

#### Stripping URL components

```php
<?php
require 'vendor/autoload.php';

// Remove authentication info and fragment
$url = http_build_url(
    "http://user:pass@www.example.com/path#section",
    [],
    HTTP_URL_STRIP_AUTH | HTTP_URL_STRIP_FRAGMENT
);

echo $url; // http://www.example.com/path
```

#### Complex example combining multiple operations

```php
<?php
require 'vendor/autoload.php';

// Change scheme to FTP, update host and path, merge query, and strip auth & fragment
$url = http_build_url(
    "http://user@www.example.com/pub/index.php?a=b#files",
    [
        'scheme' => 'ftp',
        'host'   => 'ftp.example.com',
        'path'   => 'files/current/',
        'query'  => 'a=c'
    ],
    HTTP_URL_STRIP_AUTH | HTTP_URL_JOIN_PATH | HTTP_URL_JOIN_QUERY | HTTP_URL_STRIP_FRAGMENT
);

echo $url; // ftp://ftp.example.com/pub/files/current/?a=c
```

### Available Flags

- `HTTP_URL_REPLACE` (default) - Replace parts of the first URL with parts from the second
- `HTTP_URL_JOIN_PATH` - Join relative paths instead of replacing
- `HTTP_URL_JOIN_QUERY` - Merge query parameters instead of replacing
- `HTTP_URL_STRIP_USER` - Remove username from URL
- `HTTP_URL_STRIP_PASS` - Remove password from URL
- `HTTP_URL_STRIP_AUTH` - Remove both username and password
- `HTTP_URL_STRIP_PORT` - Remove port from URL
- `HTTP_URL_STRIP_PATH` - Remove path from URL
- `HTTP_URL_STRIP_QUERY` - Remove query string from URL
- `HTTP_URL_STRIP_FRAGMENT` - Remove fragment (hash) from URL
- `HTTP_URL_STRIP_ALL` - Remove all components except scheme and host

Flags can be combined using the bitwise OR operator (`|`).

## License

This project is licensed under the MIT License - see the LICENSE file for details.
