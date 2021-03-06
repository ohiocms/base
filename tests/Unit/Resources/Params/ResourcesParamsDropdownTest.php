<?php namespace Tests\Belt\Core\Unit\Resources;

use Belt\Core\Resources\Params\DropDown;
use Belt\Core\Tests\BeltTestCase;

class ResourcesParamsDropdownTest extends BeltTestCase
{
    /**
     * @covers \Belt\Core\Resources\Params\DropDown::setOptions
     * @covers \Belt\Core\Resources\Params\DropDown::getOptions
     * @covers \Belt\Core\Resources\Params\DropDown::toArray
     */
    public function test()
    {
        $resource = StubResourcesParamsDropdownTest::make('foo');

        # set/get options
        $resource->setOptions('test');
        $this->assertEquals('test', $resource->getOptions());

        # toArray
        $array = $resource->toArray();
        $this->assertEquals('test', array_get($array, 'options'));
    }

}

class StubResourcesParamsDropdownTest extends DropDown
{

}