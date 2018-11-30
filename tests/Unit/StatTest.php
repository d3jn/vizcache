<?php

namespace D3jn\Vizcache\Tests\Unit;

use Closure;
use D3jn\Vizcache\Stat;
use Illuminate\Support\Facades\Cache;
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
            ->andReturnNull();
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
        Config::shouldReceive('get')
            ->once()
            ->with('vizcache.no_caching_when_testing', false)
            ->andReturn(false);

        $cacheStore = Mockery::mock('Illuminate\Contracts\Cache\Store');

        $cacheRepository = Mockery::mock('Illuminate\Contracts\Cache\Repository');
        $cacheRepository->shouldReceive('getStore')
            ->once()
            ->andReturn($cacheStore);
        $cacheRepository->shouldReceive('remember')
            ->once()
            ->with('_TestAnalyst_testStat@key_name', 1, Mockery::on(function ($argument) {
                return $argument instanceof Closure;
            }))
            ->andReturn('result');

        Cache::shouldReceive('store')
            ->once()
            ->with('existing_store')
            ->andReturn($cacheRepository);

        $analyst = Mockery::mock('D3jn\Vizcache\Analyst');
        $analyst->shouldReceive('timeToLive')
            ->once()
            ->with('testStat', ['foo' => 'bar'])
            ->andReturnNull();
        $analyst->shouldReceive('cacheStore')
            ->with('testStat', ['foo' => 'bar'])
            ->andReturn('existing_store');
        $analyst->shouldReceive('hash')
            ->once()
            ->with('testStat', ['foo' => 'bar'])
            ->andReturn('key_name');

        $analyst->shouldNotReceive('get');

        $stat = new Stat(
            $analyst,
            ['cache_store' => 'existing_store', 'only_get_from_cache' => false, 'time_to_live' => 1],
            'TestAnalyst',
            'testStat',
            ['foo' => 'bar']
        );

        $this->assertSame('result', $stat->value());
    }

    public function testValueWhenOnlyGetFromCacheIsEnabled()
    {
        $analyst = Mockery::mock('D3jn\Vizcache\Analyst');
        $analyst->shouldReceive('cacheStore')
            ->with('testStat', ['foo' => 'bar'])
            ->andReturn('existing_store');
        $analyst->shouldReceive('hash')
            ->once()
            ->with('testStat', ['foo' => 'bar'])
            ->andReturn('key_name');

        $cacheRepository = Mockery::mock('Illuminate\Contracts\Cache\Repository');
        $cacheRepository->shouldReceive('has')
            ->once()
            ->with('_TestAnalyst_testStat@key_name')
            ->andReturn(true);
        $cacheRepository->shouldReceive('get')
            ->once()
            ->andReturn('result');

        Cache::shouldReceive('store')
            ->once()
            ->with('existing_store')
            ->andReturn($cacheRepository);

        $stat = new Stat(
            $analyst,
            ['cache_store' => 'existing_store', 'only_get_from_cache' => true, 'time_to_live' => 1],
            'TestAnalyst',
            'testStat',
            ['foo' => 'bar']
        );

        $this->assertSame('result', $stat->value());
    }

    public function testValueWhenOnlyGetFromCacheIsEnabledButValueDoesntExistInCache()
    {
        $analyst = Mockery::mock('D3jn\Vizcache\Analyst');
        $analyst->shouldReceive('cacheStore')
            ->with('testStat', ['foo' => 'bar'])
            ->andReturn('existing_store');
        $analyst->shouldReceive('hash')
            ->once()
            ->with('testStat', ['foo' => 'bar'])
            ->andReturn('key_name');

        $cacheRepository = Mockery::mock('Illuminate\Contracts\Cache\Repository');
        $cacheRepository->shouldReceive('has')
            ->once()
            ->with('_TestAnalyst_testStat@key_name')
            ->andReturn(false);

        $cacheRepository->shouldNotReceive('get');

        Cache::shouldReceive('store')
            ->once()
            ->with('existing_store')
            ->andReturn($cacheRepository);

        $stat = new Stat(
            $analyst,
            ['cache_store' => 'existing_store', 'only_get_from_cache' => true, 'time_to_live' => 1],
            'TestAnalyst',
            'testStat',
            ['foo' => 'bar']
        );

        $this->assertNull($stat->value());
    }

    public function testComputeIgnoresCacheStore()
    {
        $analyst = Mockery::mock('D3jn\Vizcache\Analyst');
        $analyst->shouldReceive('cacheStore')
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

    public function testUpdate()
    {
        $analyst = Mockery::mock('D3jn\Vizcache\Analyst');
        $analyst->shouldReceive('cacheStore')
            ->with('testStat', ['foo' => 'bar'])
            ->andReturn('existing_store');
        $analyst->shouldReceive('hash')
            ->once()
            ->with('testStat', ['foo' => 'bar'])
            ->andReturn('key_name');
        $analyst->shouldReceive('timeToLive')
            ->once()
            ->with('testStat', ['foo' => 'bar'])
            ->andReturnNull();
        $analyst->shouldReceive('get')
            ->once()
            ->with('testStat', ['foo' => 'bar'])
            ->andReturn('result');

        $cacheStore = Mockery::mock('Illuminate\Contracts\Cache\Store');

        $cacheRepository = Mockery::mock('Illuminate\Contracts\Cache\Repository');
        $cacheRepository->shouldReceive('getStore')
            ->once()
            ->andReturn($cacheStore);
        $cacheRepository->shouldReceive('put')
            ->once()
            ->with('_TestAnalyst_testStat@key_name', 'result', 1);

        Cache::shouldReceive('store')
            ->once()
            ->with('existing_store')
            ->andReturn($cacheRepository);

        $stat = new Stat(
            $analyst,
            ['cache_store' => 'existing_store', 'only_get_from_cache' => false, 'time_to_live' => 1],
            'TestAnalyst',
            'testStat',
            ['foo' => 'bar']
        );

        $stat->update();
        $this->assertTrue(true);
    }

    public function testUpdateForTaggableStores()
    {
        $analyst = Mockery::mock('D3jn\Vizcache\Analyst');
        $analyst->shouldReceive('cacheStore')
            ->with('testStat', ['foo' => 'bar'])
            ->andReturn('existing_store');
        $analyst->shouldReceive('hash')
            ->once()
            ->with('testStat', ['foo' => 'bar'])
            ->andReturn('key_name');
        $analyst->shouldReceive('timeToLive')
            ->once()
            ->with('testStat', ['foo' => 'bar'])
            ->andReturnNull();
        $analyst->shouldReceive('get')
            ->once()
            ->with('testStat', ['foo' => 'bar'])
            ->andReturn('result');

        $cacheStore = Mockery::mock('Illuminate\Contracts\Cache\Store, Illuminate\Cache\TaggableStore');

        $taggedCache = Mockery::mock('Illuminate\Cache\TaggedCache');
        $taggedCache->shouldReceive('put')
            ->once()
            ->with('_TestAnalyst_testStat@key_name', 'result', 1);

        $cacheRepository = Mockery::mock('Illuminate\Contracts\Cache\Repository');
        $cacheRepository->shouldReceive('getStore')
            ->once()
            ->andReturn($cacheStore);
        $cacheRepository->shouldReceive('tags')
            ->once()
            ->with(['TestAnalyst', 'testStat'])
            ->andReturn($taggedCache);

        $cacheRepository->shouldNotReceive('put');

        Cache::shouldReceive('store')
            ->once()
            ->with('existing_store')
            ->andReturn($cacheRepository);

        $stat = new Stat(
            $analyst,
            ['cache_store' => 'existing_store', 'only_get_from_cache' => false, 'time_to_live' => 1],
            'TestAnalyst',
            'testStat',
            ['foo' => 'bar']
        );

        $stat->update();
        $this->assertTrue(true);
    }

    public function testTouch()
    {
        $cacheRepository = Mockery::mock('Illuminate\Contracts\Cache\Repository');
        $cacheRepository->shouldReceive('has')
            ->once()
            ->with('_TestAnalyst_testStat@key_name')
            ->andReturn(false);
        $cacheRepository->shouldReceive('put')
            ->once()
            ->with('_TestAnalyst_testStat@key_name', 'result', 1);

        Cache::shouldReceive('store')
            ->once()
            ->with('existing_store')
            ->andReturn($cacheRepository);

        $analyst = Mockery::mock('D3jn\Vizcache\Analyst');
        $analyst->shouldReceive('get')
            ->once()
            ->with('testStat', ['foo' => 'bar'])
            ->andReturn('result');
        $analyst->shouldReceive('timeToLive')
            ->once()
            ->with('testStat', ['foo' => 'bar'])
            ->andReturnNull();
        $analyst->shouldReceive('cacheStore')
            ->with('testStat', ['foo' => 'bar'])
            ->andReturn('existing_store');
        $analyst->shouldReceive('hash')
            ->once()
            ->with('testStat', ['foo' => 'bar'])
            ->andReturn('key_name');

        $stat = new Stat(
            $analyst,
            ['cache_store' => 'existing_store', 'only_get_from_cache' => false, 'time_to_live' => 1],
            'TestAnalyst',
            'testStat',
            ['foo' => 'bar']
        );

        $stat->touch();
        $this->assertTrue(true);
    }

    public function testFlushWorksForTaggableStores()
    {
        $cacheStore = Mockery::mock('Illuminate\Contracts\Cache\Store, Illuminate\Cache\TaggableStore');

        $taggedCache = Mockery::mock('Illuminate\Cache\TaggedCache');
        $taggedCache->shouldReceive('flush')->once();

        $cacheRepository = Mockery::mock('Illuminate\Contracts\Cache\Repository');
        $cacheRepository->shouldReceive('getStore')
            ->once()
            ->andReturn($cacheStore);
        $cacheRepository->shouldReceive('tags')
            ->once()
            ->with(['TestAnalyst', 'testStat'])
            ->andReturn($taggedCache);

        Cache::shouldReceive('store')
            ->once()
            ->with('existing_store')
            ->andReturn($cacheRepository);

        $analyst = Mockery::mock('D3jn\Vizcache\Analyst');
        $analyst->shouldReceive('cacheStore')
            ->with('testStat', ['foo' => 'bar'])
            ->andReturn('existing_store');

        $stat = new Stat(
            $analyst,
            ['cache_store' => 'existing_store', 'only_get_from_cache' => false, 'time_to_live' => 1],
            'TestAnalyst',
            'testStat',
            ['foo' => 'bar']
        );

        $stat->flush();
        $this->assertTrue(true);
    }

    public function testFlushDoesntWorkForNotTaggableStores()
    {
        $this->expectException('D3jn\Vizcache\Exceptions\StatCantBeFlushedException');

        $analyst = Mockery::mock('D3jn\Vizcache\Analyst');
        $analyst->shouldReceive('cacheStore')
            ->with('testStat', ['foo' => 'bar'])
            ->andReturn('existing_store');

        $cacheStore = Mockery::mock('Illuminate\Contracts\Cache\Store');

        $cacheRepository = Mockery::mock('Illuminate\Contracts\Cache\Repository');
        $cacheRepository->shouldReceive('getStore')
            ->once()
            ->andReturn($cacheStore);

        Cache::shouldReceive('store')
            ->once()
            ->with('existing_store')
            ->andReturn($cacheRepository);

        $stat = new Stat(
            $analyst,
            ['cache_store' => 'existing_store', 'only_get_from_cache' => false, 'time_to_live' => 1],
            'TestAnalyst',
            'testStat',
            ['foo' => 'bar']
        );

        $stat->flush();
    }

    public function testForget()
    {
        $analyst = Mockery::mock('D3jn\Vizcache\Analyst');
        $analyst->shouldReceive('cacheStore')
            ->with('testStat', ['foo' => 'bar'])
            ->andReturn('existing_store');
        $analyst->shouldReceive('hash')
            ->once()
            ->with('testStat', ['foo' => 'bar'])
            ->andReturn('key_name');

        $cacheStore = Mockery::mock('Illuminate\Contracts\Cache\Store');

        $cacheRepository = Mockery::mock('Illuminate\Contracts\Cache\Repository');
        $cacheRepository->shouldReceive('forget')
            ->once()
            ->with('_TestAnalyst_testStat@key_name');

        Cache::shouldReceive('store')
            ->once()
            ->with('existing_store')
            ->andReturn($cacheRepository);

        $stat = new Stat(
            $analyst,
            ['cache_store' => 'existing_store', 'only_get_from_cache' => false, 'time_to_live' => 1],
            'TestAnalyst',
            'testStat',
            ['foo' => 'bar']
        );

        $stat->forget();

        $this->assertTrue(true);
    }
}
