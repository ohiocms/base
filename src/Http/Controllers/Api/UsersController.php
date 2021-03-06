<?php

namespace Belt\Core\Http\Controllers\Api;

use Belt;
use Belt\Core\User;
use Belt\Core\Http\Requests;
use Belt\Core\Http\Controllers\ApiController;
use Illuminate\Http\Request;

/**
 * Class UsersController
 * @package Belt\Core\Http\Controllers\Api
 */
class UsersController extends ApiController
{

    /**
     * The repository instance.
     *
     * @var User
     */
    public $users;

    /**
     * ApiController constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->users = $user;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $this->authorize(['view', 'create', 'update', 'delete'], User::class);

        $request = Requests\PaginateUsers::extend($request);

        $paginator = $this->paginator($this->users->query(), $request);

        return response()->json($paginator->toArray());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Requests\StoreUser $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Requests\StoreUser $request)
    {

        $this->authorize(['create', 'register'], User::class);

        $input = $request->all();

        $user = $this->users->create(['email' => $input['email']]);

        $this->set($user, $input, [
            'is_opted_in',
            'is_active',
            'is_verified',
            'has_agreed',
            'first_name',
            'last_name',
            'mi',
            'password',
        ]);

        $user->save();

        $this->itemEvent('created', $user);

        return response()->json($user, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param User $user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(User $user)
    {
        $this->authorize(['view', 'create', 'update', 'delete'], $user);

        return response()->json($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Requests\UpdateUser $request
     * @param User $user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Requests\UpdateUser $request, User $user)
    {
        $this->authorize('update', $user);

        $input = $request->all();

        $this->set($user, $input, [
            'is_opted_in',
            'is_active',
            'is_verified',
            'has_agreed',
            'first_name',
            'last_name',
            'mi',
            'email',
            //'password',
        ]);

        $this->setIfNotEmpty($user, $input, [
            'password',
        ]);

        $user->save();

        $this->itemEvent('updated', $user);

        return response()->json($user);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param User $user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        $this->itemEvent('deleted', $user);

        $user->delete();

        return response()->json(null, 204);
    }
}
