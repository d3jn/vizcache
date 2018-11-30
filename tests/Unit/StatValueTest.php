<?php

namespace D3jn\Vizcache\Tests\Unit;

use D3jn\Vizcache\Facades\Vizcache;
use D3jn\Vizcache\StatValue;
use Mockery;
use PHPUnit\Framework\TestCase;

class StatValueTest extends TestCase
{
    public function tearDown()
    {
        Mockery::close();
    }

    public function testCanGet()
    {
        $statValue = new StatValue('testStat', ['foo' => 'bar']);

        Vizcache::shouldReceive('get')
            ->once()
            ->with('testStat', 'default', ['foo' => 'bar'])
            ->andReturn('some_result');

        $this->assertSame('some_result', $statValue->get('default'));
    }

    public function testCanTouch()
    {
        $statValue = new StatValue('testStat', ['foo' => 'bar']);

        Vizcache::shouldReceive('touch')
            ->once()
            ->with('testStat', ['foo' => 'bar']);

        $statValue->touch('testStat', ['foo' => 'bar']);
        $this->assertTrue(true);
    }

    public function testCanUpdate()
    {
        $statValue = new StatValue('testStat', ['foo' => 'bar']);

        Vizcache::shouldReceive('update')
            ->once()
            ->with('testStat', 'default', ['foo' => 'bar']);

        $statValue->update('default');
        $this->assertTrue(true);
    }

    public function testCanForget()
    {
        $statValue = new StatValue('testStat', ['foo' => 'bar']);

        Vizcache::shouldReceive('forget')
            ->once()
            ->with('testStat', ['foo' => 'bar']);

        $statValue->forget();
        $this->assertTrue(true);
    }
}
