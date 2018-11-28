<?php

namespace D3jn\Vizcache\Tests\Unit;

use D3jn\Vizcache\Manager;
use Mockery;
use PHPUnit\Framework\TestCase;

class ManagerTest extends TestCase
{
    public function tearDown()
    {
        Mockery::close();
    }

    public function testAutohashingUsedForUnexistingStats()
    {
        $arguments = ['foo' => 'bar'];

        $hasher = Mockery::mock('D3jn\Vizcache\Hasher');
        $hasher->shouldReceive('hash')
            ->twice()
            ->with([0 => $arguments])
            ->andReturn('_randomhash');

        $manager = new Manager($hasher);

        $manager->setAutoHashing(true);
        $this->assertSame('_randomhash', $manager->unexistingStat_hash($arguments));

        $manager->setAutoHashing(false);
        $this->assertNull($manager->unexistingStat_hash($arguments));

        $manager->setAutoHashStats(['unexistingStat']);
        $this->assertSame('_randomhash', $manager->unexistingStat_hash($arguments));
    }

    public function testDefaultTtlAndStoreValuesForUnexistingStats()
    {
        $manager = new Manager(Mockery::mock('D3jn\Vizcache\Hasher'));

        $this->assertNull($manager->unexistingStat_ttl());
        $this->assertNull($manager->unexistingStat_store());
    }

    public function testUnexistingMethodsWithInvalidNamesAreNotResolved()
    {
        $this->expectException('BadMethodCallException');

        $manager = new Manager(Mockery::mock('D3jn\Vizcache\Hasher'));
        $manager->unexistingStat_wrongOption();
    }
}
