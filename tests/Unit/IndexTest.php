<?php namespace Tests\Belt\Core\Unit;

use Belt\Core\Index;
use Belt\Core\Tests\BeltTestCase;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class IndexTest extends BeltTestCase
{
    /**
     * @covers \Belt\Core\Index::indexable
     */
    public function test()
    {
        $index = new Index();

        $this->assertInstanceOf(MorphTo::class, $index->indexable());
    }

}