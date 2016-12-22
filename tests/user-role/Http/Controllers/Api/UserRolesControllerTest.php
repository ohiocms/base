<?php

use Mockery as m;
use Ohio\Core\Base\Testing;

use Ohio\Core\UserRole\UserRole;
use Ohio\Core\UserRole\Http\Requests\StoreUserRole;
use Ohio\Core\UserRole\Http\Requests\PaginateUserRoles;
use Ohio\Core\UserRole\Http\Requests\UpdateRequest;
use Ohio\Core\UserRole\Http\Controllers\Api\UserRolesController;
use Ohio\Core\Base\Http\Exceptions\ApiNotFoundHttpException;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiControllerTest extends Testing\OhioTestCase
{

    use Testing\CommonMocks;

    public function tearDown()
    {
        m::close();
    }

    /**
     * @covers \Ohio\Core\UserRole\Http\Controllers\Api\UserRolesController::__construct
     * @covers \Ohio\Core\UserRole\Http\Controllers\Api\UserRolesController::get
     * @covers \Ohio\Core\UserRole\Http\Controllers\Api\UserRolesController::show
     * @covers \Ohio\Core\UserRole\Http\Controllers\Api\UserRolesController::destroy
     * @covers \Ohio\Core\UserRole\Http\Controllers\Api\UserRolesController::store
     * @covers \Ohio\Core\UserRole\Http\Controllers\Api\UserRolesController::index
     */
    public function test()
    {
        $userRole1 = new UserRole();
        $userRole1->role_id = 1;

        $qbMock = $this->getPaginateQBMock(new PaginateUserRoles(), [$userRole1]);
        $qbMock->shouldReceive('with')->once();

        $userRoleRepository = m::mock(UserRole::class);
        $userRoleRepository->shouldReceive('find')->with(1)->andReturn($userRole1);
        $userRoleRepository->shouldReceive('find')->with(999)->andReturn(null);
        $userRoleRepository->shouldReceive('create')->andReturn($userRole1);
        $userRoleRepository->shouldReceive('query')->andReturn($qbMock);

        # construct
        $controller = new UserRolesController($userRoleRepository);
        $this->assertEquals($userRoleRepository, $controller->userRole);

        # get existing userRole
        $userRole = $controller->get(1);
        $this->assertEquals($userRole1->role_id, $userRole->role_id);

        # get userRole that doesn't exist
        try {
            $controller->get(999);
        } catch (\Exception $e) {
            $this->assertInstanceOf(ApiNotFoundHttpException::class, $e);
        }

        # show userRole
        $response = $controller->show(1);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $data = $response->getData();
        $this->assertEquals($userRole1->role_id, $data->role_id);

        # destroy userRole
        $response = $controller->destroy(1);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(204, $response->getStatusCode());

        # create userRole
        $response = $controller->store(new StoreUserRole());
        $this->assertInstanceOf(JsonResponse::class, $response);

        # index
        $response = $controller->index(new PaginateUserRoles());
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals($userRole1->role_id, $response->getData()->data[0]->role_id);

    }

}