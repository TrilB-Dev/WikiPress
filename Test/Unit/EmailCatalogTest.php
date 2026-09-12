<?php

declare( strict_types=1 );

use PHPUnit\Framework\TestCase;

final class EmailCatalogTest extends TestCase {
	/**
	 * @return iterable<string, array{string, int}>
	 */
	public function catalog_provider(): iterable {
		yield 'admin' => [ 'AdminEmail.php', 6 ];
		yield 'comments' => [ 'CommentsEmail.php', 2 ];
		yield 'multisite' => [ 'MultisiteEmail.php', 2 ];
		yield 'user' => [ 'UserEmail.php', 5 ];
	}

	/**
	 * @dataProvider catalog_provider
	 */
	public function testCatalogContainsExpectedEntries( string $filename, int $expected_count ): void {
		$catalog = require dirname( __DIR__, 2 ) . '/src/Includes/Plugins/Exchange/Templates/WP/' . $filename;

		self::assertIsArray( $catalog );
		self::assertCount( $expected_count, $catalog );

		foreach ( $catalog as $id => $template ) {
			self::assertMatchesRegularExpression( '/^[a-z0-9_-]+$/', (string) $id );
			self::assertIsArray( $template );
			self::assertNotEmpty( $template['name'] ?? null );
			self::assertNotEmpty( $template['source'] ?? null );
			self::assertNotEmpty( $template['recipient'] ?? null );
			self::assertNotEmpty( $template['subject'] ?? null );
			self::assertNotEmpty( $template['body'] ?? null );
		}
	}

	/**
	 * @dataProvider catalog_provider
	 */
	public function testCatalogIdsAreUniqueAcrossEachFile( string $filename, int $expected_count ): void {
		$catalog = require dirname( __DIR__, 2 ) . '/src/Includes/Plugins/Exchange/Templates/WP/' . $filename;

		self::assertSame( $expected_count, count( array_unique( array_keys( $catalog ) ) ) );
	}
}