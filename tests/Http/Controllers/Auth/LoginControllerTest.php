<?php

use Mockery as m;
use Belt\Core\Testing;
use Belt\Core\Http\Controllers\Auth\LoginController;
use Belt\Core\Team;
use Belt\Core\User;
use Illuminate\View\View;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Database\Eloquent\Collection;

class LoginControllerTest extends Testing\BeltTestCase
{
    public function tearDown()
    {
        m::close();
    }

    /**
     * @cover \Belt\Core\Http\Controllers\LoginController::__construct
     * @cover \Belt\Core\Http\Controllers\LoginController::showLoginForm
     * @cover \Belt\Core\Http\Controllers\LoginController::logout
     * @cover \Belt\Core\Http\Controllers\LoginController::redirectTo
     */
    public function test()
    {

        User::unguard();

        # __construct
        $controller = new LoginController();

        # showLoginForm
        $view = $controller->showLoginForm();
        $this->assertInstanceOf(View::class, $view);

        # redirect to (regular user)
        $controller = new LoginControllerStub1();
        $this->assertEquals('/home', $controller->redirectTo());

        # redirect to (admin user)
        $controller = new LoginControllerStub2();
        $this->assertEquals('/admin', $controller->redirectTo());

        # redirect to (team user)
        $controller = new LoginControllerStub3();
        $this->assertEquals('/admin', $controller->redirectTo());
    }

}

class LoginControllerStub1 extends LoginController
{
    public function guard()
    {
        $user = factory(User::class)->make(['is_super' => false]);
        $user->roles = new Collection();
        $user->teams = new Collection();

        $guard = m::mock(StatefulGuard::class);
        $guard->shouldReceive('user')->once()->andReturn($user);

        return $guard;
    }
}

class LoginControllerStub2 extends LoginController
{
    public function guard()
    {
        $user = factory(User::class)->make(['is_super' => true]);
        $user->teams = new Collection();

        $guard = m::mock(StatefulGuard::class);
        $guard->shouldReceive('user')->once()->andReturn($user);

        return $guard;
    }
}

class LoginControllerStub3 extends LoginController
{
    public function guard()
    {
        $team = factory(Team::class)->make();

        $user = factory(User::class)->make(['is_super' => false]);
        $user->teams = new Collection([$team]);

        $guard = m::mock(StatefulGuard::class);
        $guard->shouldReceive('user')->once()->andReturn($user);

        return $guard;
    }
}