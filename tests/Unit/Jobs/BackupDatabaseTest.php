<?php namespace Tests\Belt\Core\Unit\Jobs;

use Belt\Core\Jobs\BackupDatabase;
use Belt\Core\Services\BackupService;
use Belt\Core\Tests;
use Mockery as m;

class BackupDatabaseTest extends Tests\BeltTestCase
{

    public function tearDown()
    {
        m::close();
    }

    /**
     * @covers \Belt\Core\Jobs\BackupDatabase::__construct
     * @covers \Belt\Core\Jobs\BackupDatabase::service
     * @covers \Belt\Core\Jobs\BackupDatabase::handle
     */
    public function test()
    {
        $key = 'foo';
        $job = new BackupDatabase($key);

        # service
        $this->assertInstanceOf(BackupService::class, $job->service());

        # handle
        $service = m::mock(BackupService::class . '[backup]');
        $service->shouldReceive('backup')->with($key)->andReturnSelf();
        $job->service = $service;
        $job->handle();
    }

}