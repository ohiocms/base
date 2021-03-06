<?php namespace Tests\Belt\Core\Unit\Http;

use Belt\Core\Http\Requests\PaginateAbilities;
use Belt\Core\Tests;
use Illuminate\Database\Eloquent\Builder;
use Mockery as m;

class PaginateAbilitiesTest extends Tests\BeltTestCase
{

    use Tests\CommonMocks;

    public function tearDown()
    {
        m::close();
    }

    /**
     * @covers \Belt\Core\Http\Requests\PaginateAbilities::modifyQuery
     */
    public function test()
    {
        # modifyQuery (w/entityType)
        $qbMock = m::mock(Builder::class);
        $qbMock->shouldReceive('where')->once()->with('entity_id', 1);
        $paginateRequest = new PaginateAbilities(['entity_id' => 1]);
        $paginateRequest->modifyQuery($qbMock);

        # modifyQuery (wo/entityType)
        $qbMock = m::mock(Builder::class);
        $qbMock->shouldReceive('whereNull')->once()->with('entity_id');
        $paginateRequest = new PaginateAbilities();
        $paginateRequest->modifyQuery($qbMock);
    }

}