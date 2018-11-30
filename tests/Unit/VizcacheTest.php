<?php

namespace D3jn\Vizcache\Tests\Unit;

use D3jn\Vizcache\Vizcache;
use Illuminate\Support\Facades\Config;
use Mockery;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class VizcacheTest extends TestCase
{
    protected $stat;

    protected $vizcache;

    public function tearDown()
    {
        Mockery::close();
    }

    public function testCanGet()
    {
        $this->setUpWithParameters(['foo' => 'bar']);

        $this->stat->shouldReceive('value')
            ->once()
            ->andReturn('result');

        $this->assertSame('result', $this->vizcache->get('TestAnalyst@testStat', null, ['foo' => 'bar']));
    }

    public function testCanCompute()
    {
        $this->setUpWithParameters(['foo' => 'bar']);

        $this->stat->shouldReceive('compute')
            ->once()
            ->andReturn('result');

        $this->assertSame('result', $this->vizcache->compute('TestAnalyst@testStat', null, ['foo' => 'bar']));
    }

    public function testCanForget()
    {
        $this->setUpWithParameters(['foo' => 'bar']);

        $this->stat->shouldReceive('forget')->once();

        $this->vizcache->forget('TestAnalyst@testStat', ['foo' => 'bar']);

        $this->assertTrue(true);
    }

    public function testCanFlush()
    {
        $this->setUpWithParameters([]);

        $this->stat->shouldReceive('flush')->once();

        $this->vizcache->flush('TestAnalyst@testStat');

        $this->assertTrue(true);
    }

    public function testCanTouch()
    {
        $this->setUpWithParameters(['foo' => 'bar']);

        $this->stat->shouldReceive('touch')->once();

        $this->vizcache->touch('TestAnalyst@testStat', ['foo' => 'bar']);

        $this->assertTrue(true);
    }

    public function testCanUpdate()
    {
        $this->setUpWithParameters(['foo' => 'bar']);

        $this->stat->shouldReceive('update')->once();

        $this->vizcache->update('TestAnalyst@testStat', null, ['foo' => 'bar']);

        $this->assertTrue(true);
    }

    public function testCanFakeAnalyst()
    {
        $fakeAnalyst = Mockery::mock('D3jn\Vizcache\Helpers\FakeAnalyst');

        $app = Mockery::mock('Illuminate\Contracts\Foundation\Application');
        $app->shouldReceive('make')
            ->once()
            ->with('D3jn\Vizcache\Helpers\FakeAnalyst', ['name' => 'TestAnalyst'])
            ->andReturn($fakeAnalyst);

        $this->vizcache = new Vizcache($app);

        $this->assertSame($fakeAnalyst, $this->vizcache->TestAnalyst());
    }

    protected function setUpWithParameters(array $parameters)
    {
        $app = Mockery::mock('Illuminate\Contracts\Foundation\Application');

        $this->vizcache = new Vizcache($app);
        $configuration = $this->vizcache->getDefaultConfigurationForStat();

        $analyst = Mockery::mock('App\TestAnalyst, D3jn\Vizcache\Analyst');
        $analyst->shouldReceive('cacheStore')
            ->with('testStat', $parameters)
            ->andReturnNull();

        $this->stat = Mockery::mock('D3jn\Vizcache\Stat');

        Config::shouldReceive('get')
            ->once()
            ->with('vizcache.analysts.TestAnalyst')
            ->andReturn('D3jn\Vizcache\Analyst');

        Config::shouldReceive('get')
            ->once()
            ->with('vizcache.configuration')
            ->andReturn($configuration);

        $app->shouldReceive('make')
            ->once()
            ->with('D3jn\Vizcache\Analyst')
            ->andReturn($analyst);
        $app->shouldReceive('make')
            ->once()
            ->with('D3jn\Vizcache\Stat', [
                'analyst' => $analyst,
                'configuration' => $configuration,
                'analystName' => 'TestAnalyst',
                'methodName' => 'testStat',
                'parameters' => $parameters
            ])
            ->andReturn($this->stat);
    }
}
