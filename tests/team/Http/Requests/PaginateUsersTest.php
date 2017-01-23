<?php
use Mockery as m;
use Ohio\Core\Base\Testing;

use Ohio\Core\User\User;
use Ohio\Core\Team\Http\Requests\PaginateUsers;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class PaginateUsersTest extends Testing\OhioTestCase
{

    use Testing\CommonMocks;

    public function tearDown()
    {
        m::close();
    }

    /**
     * @covers \Ohio\Core\Team\Http\Requests\PaginateUsers::modifyQuery
     * @covers \Ohio\Core\Team\Http\Requests\PaginateUsers::userRepo
     * @covers \Ohio\Core\Team\Http\Requests\PaginateUsers::items
     * @covers \Ohio\Core\Team\Http\Requests\PaginateUsers::item
     */
    public function test()
    {
        $user1 = new User();
        $user1->id = 1;
        $user1->email = 'test@test.com';

        $qbMock = m::mock(Builder::class);
        $qbMock->shouldReceive('join')->once()->with('team_users', 'team_users.user_id', '=', 'users.id');
        $qbMock->shouldReceive('where')->once()->with('team_users.team_id', 1);
        $qbMock->shouldReceive('get')->once()->with(['users.id'])->andReturn(new Collection([$user1]));
        $qbMock->shouldReceive('find')->times(2)->with(1)->andReturn($user1);
        $qbMock->shouldReceive('leftJoin')->once()->with('team_users',
            m::on(function (\Closure $closure) {
                $subQBMock = m::mock(Builder::class);
                $subQBMock->shouldReceive('on')->once()->with('team_users.user_id', '=', 'users.id');
                $subQBMock->shouldReceive('where')->once()->with('team_users.team_id', 1);
                $closure($subQBMock);
                return is_callable($closure);
            })
        );
        $qbMock->shouldReceive('whereNull')->once()->with('team_users.id');

        $paginateRequest = new PaginateUsers(['user_id' => 1, 'team_id' => 1]);

        # userRepo
        $this->assertNull($paginateRequest->userRepo);
        $paginateRequest->userRepo();
        $this->assertInstanceOf(User::class, $paginateRequest->userRepo);

        # item
        $paginateRequest->userRepo = $qbMock;
        $this->assertEquals($user1, $paginateRequest->item(1));

        # items
        $paginateRequest->items($qbMock);

        # modifyQuery
        $paginateRequest->modifyQuery($qbMock);
        $paginateRequest->merge(['not' => true]);
        $paginateRequest->modifyQuery($qbMock);
    }

}