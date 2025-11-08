<?php
require dirname( __DIR__ ) . '/src/http_build_url.php';

// PHPUnit 6+ uses namespaced classes, create aliases for older PHPUnit versions compatibility
if ( class_exists( 'PHPUnit\Runner\Version' ) ) {
    // PHPUnit 6+
    if ( ! class_exists( 'PHPUnit_Framework_TestCase' ) ) {
        class_alias( 'PHPUnit\Framework\TestCase', 'PHPUnit_Framework_TestCase' );
    }
} else {
    // PHPUnit 4-5 - ensure forward compatibility classes exist
    if ( ! class_exists( 'PHPUnit\Framework\TestCase' ) && class_exists( 'PHPUnit_Framework_TestCase' ) ) {
        class_alias( 'PHPUnit_Framework_TestCase', 'PHPUnit\Framework\TestCase' );
    }
}

// Past this point, tests will start
