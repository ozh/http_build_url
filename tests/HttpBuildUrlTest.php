<?php

namespace Ozh\HttpBuildUrl\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class HttpBuildUrlTest extends TestCase
{
	private string $full_url = 'http://user:pass@www.example.com:8080/pub/index.php?a=b#files';

	/**
	 * Test example one.
	 *
	 * @see https://www.php.net/manual/en/function.http-build-url.php
	 */
	public function testExampleOne(): void
	{
		$expected = 'ftp://ftp.example.com/pub/files/current/?a=c';
		$actual   = http_build_url(
			'http://user@www.example.com/pub/index.php?a=b#files',
			[
				'scheme' => 'ftp',
				'host'   => 'ftp.example.com',
				'path'   => 'files/current/',
				'query'  => 'a=c',
			],
			HTTP_URL_STRIP_AUTH | HTTP_URL_JOIN_PATH | HTTP_URL_JOIN_QUERY | HTTP_URL_STRIP_FRAGMENT
		);

		$this->assertSame($expected, $actual);
	}

	public static function trailingSlashProvider(): array
	{
		return [
			[
				'http://example.com',
				[
					'scheme' => 'http',
					'host'   => 'example.com',
				],
			],
			[
				'http://example.com',
				[
					'scheme' => 'http',
					'host'   => 'example.com',
					'path'   => '',
				],
			],
			[
				'http://example.com/',
				[
					'scheme' => 'http',
					'host'   => 'example.com',
					'path'   => '/',
				],
			],
			[
				'http://example.com/yes',
				[
					'scheme' => 'http',
					'host'   => 'example.com',
					'path'   => 'yes',
				],
			],
			[
				'http://example.com/yes',
				[
					'scheme' => 'http',
					'host'   => 'example.com',
					'path'   => '/yes',
				],
			],
			[
				'http://example.com:81?a=b',
				[
					'scheme' => 'http',
					'host'   => 'example.com',
					'query'  => 'a=b',
					'port'   => 81,
				],
			],
		];
	}

	#[DataProvider('trailingSlashProvider')]
	public function testTrailingSlash(string $expected, array $config): void
	{
		$this->assertSame($expected, http_build_url($config));
	}

	public function testUrlQueryArrayIsIgnored(): void
	{
		$expected = 'http://user:pass@www.example.com:8080/pub/index.php#files';
		$url      = parse_url($this->full_url);
		parse_str($url['query'], $url['query']);
		$actual = http_build_url($url);

		$this->assertSame($expected, $actual);
	}

	public function testPartsQueryArrayIsIgnored(): void
	{
		$expected = $this->full_url;
		$actual   = http_build_url($this->full_url, ['query' => ['foo' => 'bar']]);

		$this->assertSame($expected, $actual);
	}

	public function testAcceptStrings(): void
	{
		$expected = 'http://user:pass@foobar.com:8080/pub/index.php?a=b#files';
		$actual   = http_build_url($this->full_url, 'http://foobar.com:8080');

		$this->assertSame($expected, $actual);
	}

	public function testAcceptArrays(): void
	{
		$expected = 'http://user:pass@foobar.com:8080/pub/index.php?a=b#files';
		$actual   = http_build_url(parse_url($this->full_url), parse_url('http://foobar.com:8080'));

		$this->assertSame($expected, $actual);
	}

	public function testDefaults(): void
	{
		$expected = $this->full_url;
		$actual   = http_build_url($this->full_url);

		$this->assertSame($expected, $actual);
	}

	public function testNewUrl(): void
	{
		$expected = parse_url($this->full_url);
		http_build_url($this->full_url, [], 0, $actual);

		$this->assertEquals($expected, $actual);
	}

	#[DataProvider('queryProvider')]
	public function testJoinQuery(string $query, string $expected): void
	{
		$actual = http_build_url($this->full_url, ['query' => $query], HTTP_URL_JOIN_QUERY);

		$this->assertSame($expected, $actual);
	}

	#[DataProvider('pathProvider')]
	public function testJoinPath(string $path, string $expected): void
	{
		$actual = http_build_url($this->full_url, ['path' => $path], HTTP_URL_JOIN_PATH);

		$this->assertSame($expected, $actual);
	}

	public function testJoinPathTwo(): void
	{
		$expected = 'http://site.testing.com/preview/testing/09-2013/p04/image/15.jpg';
		$actual   = http_build_url(
			'http://site.testing.com/preview/testing/09-2013/p04/?code=asdfghjkl',
			['path' => 'image/15.jpg'],
			HTTP_URL_JOIN_PATH | HTTP_URL_STRIP_FRAGMENT | HTTP_URL_STRIP_QUERY
		);

		$this->assertSame($expected, $actual);
	}

	/**
	 * Test for previous issue with URL losing their a's
	 *
	 * @see https://github.com/jakeasmith/http_build_url/issues/25
	 */
	public function testJoinPathThree(): void
	{
		$expected = 'http://site.testing.com/apreview/testing/a/09-20a13/pa0a4/image/15.jpg';
		$actual   = http_build_url(
			'http://site.testing.com/apreview/testing/a/09-20a13/pa0a4/?code=asdfghjkl',
			['path' => 'image/15.jpg'],
			HTTP_URL_JOIN_PATH | HTTP_URL_STRIP_FRAGMENT | HTTP_URL_STRIP_QUERY
		);

		$this->assertSame($expected, $actual);
	}

	#[DataProvider('bitmaskProvider')]
	public function testBitmasks(string $constant, string $expected): void
	{
		$actual = http_build_url($this->full_url, [], constant($constant));

		$this->assertSame($expected, $actual);
	}

	public static function pathProvider(): array
	{
		return [
			['/donuts/brownies', 'http://user:pass@www.example.com:8080/donuts/brownies?a=b#files'],
			['chicken/wings', 'http://user:pass@www.example.com:8080/pub/chicken/wings?a=b#files'],
			['sausage/bacon/', 'http://user:pass@www.example.com:8080/pub/sausage/bacon/?a=b#files'],
		];
	}

	public static function queryProvider(): array
	{
		return [
			['a=c', 'http://user:pass@www.example.com:8080/pub/index.php?a=c#files'],
			['d=a', 'http://user:pass@www.example.com:8080/pub/index.php?a=b&d=a#files'],
		];
	}

	public static function bitmaskProvider(): array
	{
		return [
			['HTTP_URL_REPLACE', 'http://user:pass@www.example.com:8080/pub/index.php?a=b#files'],
			['HTTP_URL_JOIN_PATH', 'http://user:pass@www.example.com:8080/pub/index.php?a=b#files'],
			['HTTP_URL_JOIN_QUERY', 'http://user:pass@www.example.com:8080/pub/index.php?a=b#files'],
			['HTTP_URL_STRIP_USER', 'http://www.example.com:8080/pub/index.php?a=b#files'],
			['HTTP_URL_STRIP_PASS', 'http://user@www.example.com:8080/pub/index.php?a=b#files'],
			['HTTP_URL_STRIP_AUTH', 'http://www.example.com:8080/pub/index.php?a=b#files'],
			['HTTP_URL_STRIP_PORT', 'http://user:pass@www.example.com/pub/index.php?a=b#files'],
			['HTTP_URL_STRIP_PATH', 'http://user:pass@www.example.com:8080?a=b#files'],
			['HTTP_URL_STRIP_QUERY', 'http://user:pass@www.example.com:8080/pub/index.php#files'],
			['HTTP_URL_STRIP_FRAGMENT', 'http://user:pass@www.example.com:8080/pub/index.php?a=b'],
			['HTTP_URL_STRIP_ALL', 'http://www.example.com'],
		];
	}
}
