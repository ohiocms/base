<?php

use Mockery as m;
use Belt\Core\Behaviors\HasService;
use Belt\Core\Testing;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Filesystem\Filesystem;

class HasServiceTest extends Testing\BeltTestCase
{

    public function tearDown()
    {
        m::close();
    }

    /**
     * @covers \Belt\Core\Behaviors\HasService::getServiceClass
     * @covers \Belt\Core\Behaviors\HasService::setService
     * @covers \Belt\Core\Behaviors\HasService::getService
     * @covers \Belt\Core\Behaviors\HasService::initService
     * @covers \Belt\Core\Behaviors\HasService::service
     */
    public function test()
    {
        //$stub = new HasServiceTestStub();
        //$this->assertInstanceOf(Filesystem::class, $stub->service());

        # getServiceClass (StubHasServiceUndefined)
        $stub = new StubHasServiceUndefined();
        try {
            $stub->getServiceClass();
            $this->exceptionNotThrown();
        } catch (\Exception $e) {

        }

        # getServiceClass
        $stub = new StubHasService();
        $this->assertEquals(StubHasServiceService::class, $stub->getServiceClass());

        # setService / getService
        $stub = new StubHasService();
        $stub->setService('foo');
        $this->assertEquals('foo', $stub->getService());
        $this->assertEquals('foo', $stub->service());

        # initService
        $stub = new StubHasService();
        $this->assertInstanceOf(StubHasServiceService::class, $stub->service());

    }

}

class StubHasServiceService
{


}

class StubHasServiceUndefined
{
    use HasService;
}

class StubHasService
{
    use HasService;

    protected $serviceClass = StubHasServiceService::class;
}