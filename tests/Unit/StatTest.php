<?php

namespace D3jn\Vizcache\Tests\Unit;

use D3jn\Vizcache\Stat;
use Illuminate\Support\Facades\Config;
use Mockery;
use PHPUnit\Framework\TestCase;

class StatTest extends TestCase
{
    public function tearDown()
    {
        Mockery::close();
    }

    public function testValueWhenCachingIsDisabled()
    {
        $analyst = Mockery::mock('D3jn\Vizcache\Analyst');
        $analyst->shouldReceive('cacheStore')
            ->once()
            ->with('testStat', ['foo' => 'bar'])
            ->andReturn(null);
        $analyst->shouldReceive('get')
            ->once()
            ->with('testStat', ['foo' => 'bar'])
            ->andReturn('result');
        $analyst->shouldNotReceive('getCachedValue');

        $stat = new Stat(
            $analyst,
            ['cache_store' => false],
            'TestAnalyst',
            'testStat',
            ['foo' => 'bar']
        );

        $this->assertSame('result', $stat->value());
    }

    public function testValueWhenCachingIsEnabled()
    {
        // TODO
    }

    public function testValueWhenOnlyGetFromCacheIsEnabled()
    {
        // TODO
    }

    public function testComputeIgnoresCacheStore()
    {
        $analyst = Mockery::mock('D3jn\Vizcache\Analyst');
        $analyst->shouldReceive('cacheStore')
            ->once()
            ->with('testStat', ['foo' => 'bar'])
            ->andReturn(false);
        $analyst->shouldReceive('get')
            ->once()
            ->with('testStat', ['foo' => 'bar'])
            ->andReturn('result');
        $analyst->shouldNotReceive('getCachedValue');

        $stat = new Stat(
            $analyst,
            ['cache_store' => false],
            'TestAnalyst',
            'testStat',
            ['foo' => 'bar']
        );

        $this->assertSame('result', $stat->value());
    }

    public function testName()
    {
        $stat = new Stat(
            Mockery::mock('D3jn\Vizcache\Analyst'),
            [],
            'TestAnalyst',
            'testStat',
            []
        );

        $this->assertSame('TestAnalyst@testStat', $stat->getName());
    }

    public function testStatUpdate()
    {
        // TODO
    }

    public function testStatTouch()
    {
        // TODO
    }

    public function testStatFlush()
    {
        // TODO
    }

    public function testStatForget()
    {
        // TODO
    }
}
