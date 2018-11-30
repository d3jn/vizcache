<?php

namespace D3jn\Vizcache\Tests\Unit;

use D3jn\Vizcache\Helpers\FakeAnalyst;
use Illuminate\Support\Facades\App;
use Mockery;
use PHPUnit\Framework\TestCase;

class FakeAnalystTest extends TestCase
{
    public function tearDown()
    {
        Mockery::close();
    }

    public function testCanReturnStatValue()
    {
        App::shouldReceive('make')
            ->once()
            ->with('D3jn\Vizcache\StatValue', [
                'statName' => 'TestAnalyst@testStat',
                'parameters' => [0 => ['foo' => 'bar']]
            ])
            ->andReturn(Mockery::mock('D3jn\Vizcache\StatValue'));

        $fakeAnalyst = new FakeAnalyst('TestAnalyst');
        $this->assertInstanceOf('D3jn\Vizcache\StatValue', $fakeAnalyst->testStat(['foo' => 'bar']));
    }
}
