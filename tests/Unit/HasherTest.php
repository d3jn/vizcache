<?php

namespace D3jn\Vizcache\Tests\Unit;

use D3jn\Vizcache\Hasher;
use PHPUnit\Framework\TestCase;

class HasherTest extends TestCase
{
    public function testSeparatorIsUsedForProducedHash()
    {
        $hasher = new Hasher('-');
        $hash = $hasher->hash(['foo' => 'bar']);

        $this->assertStringStartsWith('-', $hash);
    }

    public function testDifferentArgumentsProduceDifferentHashes()
    {
        $hasher = new Hasher;

        $hash1 = $hasher->hash(['foo' => 'bar']);
        $hash2 = $hasher->hash(['foo' => 'qux']);

        $this->assertNotSame($hash1, $hash2);
    }

    public function testSameArgumentsProduceSameHashes()
    {
        $hasher = new Hasher;

        $hash1 = $hasher->hash(['foo' => 'bar']);
        $hash2 = $hasher->hash(['foo' => 'bar']);

        $this->assertSame($hash1, $hash2);
    }
}
