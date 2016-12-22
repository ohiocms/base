<?php

namespace Ohio\Core\Role\Http\Controllers\Api;

use Ohio\Core\Base\Http\Controllers\ApiController;

use Ohio\Core\Role;
use Ohio\Core\Role\Http\Requests;

use Illuminate\Http\Request;

class RolesController extends ApiController
{

    /**
     * @var Role\Role
     */
    public $role;

    /**
     * ApiController constructor.
     * @param Role\Role $role
     */
    public function __construct(Role\Role $role)
    {
        $this->role = $role;
    }

    public function get($id)
    {
        return $this->role->find($id) ?: $this->abort(404);
    }

    /**
     * Display a listing of the resource.
     *
     * @param $request
     * @return \Illuminate\Http\Response
     */
    public function index(Requests\PaginateRoles $request)
    {
        $request->reCapture();

        $paginator = $this->paginator($this->role->query(), $request);

        return response()->json($paginator->toArray());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Requests\StoreRole $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Requests\StoreRole $request)
    {

        $role = $this->role->create($request->all());

        return response()->json($role);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $role = $this->get($id);

        return response()->json($role);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Requests\UpdateRole $request
     * @param  string $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Requests\UpdateRole $request, $id)
    {
        $role = $this->get($id);

        $role->update($request->all());

        return response()->json($role);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $role = $this->get($id);

        $role->delete();

        return response()->json(null, 204);
    }
}
