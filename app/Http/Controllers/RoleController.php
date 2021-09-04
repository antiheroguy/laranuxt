<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleRequest;
use App\Http\Requests\SearchRequest;
use App\Http\Resources\PermissionResource;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use App\Services\RoleService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 *  @OA\Tag(
 *      name="Role",
 *      description="Role Resource",
 * )
 *
 *  @OA\Schema(
 *      schema="role",
 *      @OA\Property(
 *          property="name",
 *          type="number",
 *          example=1,
 *      ),
 *      @OA\Property(
 *          property="guard_name",
 *          type="number",
 *          example=1,
 *      ),
 *  )
 */
class RoleController extends Controller
{
    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     *
     *  @OA\Get(
     *      path="/api/role",
     *      tags={"Role"},
     *      operationId="indexRole",
     *      summary="List Role",
     *      @OA\Parameter(ref="#/components/parameters/page"),
     *      @OA\Parameter(ref="#/components/parameters/limit"),
     *      @OA\Parameter(ref="#/components/parameters/order_by"),
     *      @OA\Parameter(ref="#/components/parameters/order_type"),
     *      @OA\Response(
     *          response=200,
     *          description="Listed",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(ref="#/components/schemas/role")
     *              ),
     *          ),
     *      ),
     *  )
     */
    public function index(SearchRequest $request)
    {
        $data = $request->all();

        if ($request->has('not_admin')) {
            $data['not'] = Role::ADMIN;
        }

        $roles = $this->roleService->index($data);

        return RoleResource::collection($roles);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     * @return Response
     *
     *  @OA\Post(
     *      path="/api/role",
     *      tags={"Role"},
     *      operationId="storeRole",
     *      summary="Create Role",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/role"),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Created",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/components/schemas/role",
     *              ),
     *          ),
     *      ),
     *  )
     */
    public function store(RoleRequest $request)
    {
        $data = $request->only('name');
        $data['guard_name'] = 'web';

        $role = $this->roleService->create($data);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return new RoleResource($role);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     *
     *  @OA\Get(
     *      path="/api/role/{id}",
     *      tags={"Role"},
     *      operationId="showRole",
     *      summary="Get Role",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          @OA\Schema(
     *              type="integer",
     *              format="int64",
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Getted",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/components/schemas/role",
     *              ),
     *          ),
     *      ),
     *  )
     */
    public function show(Role $role)
    {
        if ($role->name == Role::ADMIN) {
            return response()->json(['message' => 'Access denied'], 403);
        }

        return new RoleResource($role->load('permissions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     *
     *  @OA\Put(
     *      path="/api/role/{id}",
     *      tags={"Role"},
     *      operationId="updateRole",
     *      summary="Update Role",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          @OA\Schema(
     *              type="integer",
     *              format="int64",
     *          ),
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/role"),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Updated",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/components/schemas/role",
     *              ),
     *          ),
     *      ),
     *  )
     */
    public function update(RoleRequest $request, Role $role)
    {
        if ($role->name == Role::ADMIN) {
            return response()->json(['message' => 'Access denied'], 403);
        }

        $data = $request->only('name');
        $data['guard_name'] = 'web';

        $role->update($data);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return new RoleResource($role);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     *
     *  @OA\Delete(
     *      path="/api/role/{id}",
     *      tags={"Role"},
     *      operationId="deleteRole",
     *      summary="Delete Role",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          @OA\Schema(
     *              type="integer",
     *              format="int64",
     *          ),
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="Deleted",
     *      ),
     *  )
     */
    public function destroy(Role $role)
    {
        if ($role->name == Role::ADMIN) {
            return response()->json(['message' => 'Access denied'], 403);
        }

        $this->roleService->clean($role);

        $role->delete();

        return response()->json(null, 204);
    }

    /**
     * Get permission list.
     *
     * @return Response
     *
     *  @OA\Get(
     *      path="/api/permission",
     *      tags={"Role"},
     *      operationId="permission",
     *      summary="List Permission",
     *      @OA\Response(
     *          response=200,
     *          description="Listed",
     *      ),
     *  )
     */
    public function getPermissions()
    {
        $permissions = $this->roleService->getPermissions();

        return PermissionResource::collection($permissions);
    }
}
