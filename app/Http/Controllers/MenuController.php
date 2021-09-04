<?php

namespace App\Http\Controllers;

use App\Http\Requests\MenuRequest;
use App\Http\Requests\MoveRequest;
use App\Http\Resources\MenuResource;
use App\Models\Menu;
use App\Services\MenuService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 *  @OA\Tag(
 *      name="Menu",
 *      description="Menu Resource",
 * )
 *
 *  @OA\Schema(
 *      schema="menu",
 *      @OA\Property(
 *          property="title",
 *          type="number",
 *          example=1,
 *      ),
 *      @OA\Property(
 *          property="link",
 *          type="number",
 *          example=1,
 *      ),
 *      @OA\Property(
 *          property="icon",
 *          type="number",
 *          example=1,
 *      ),
 *      @OA\Property(
 *          property="parent_id",
 *          type="number",
 *          example=1,
 *      ),
 *      @OA\Property(
 *          property="position",
 *          type="number",
 *          example=1,
 *      ),
 *  )
 *
 *  @OA\Schema(
 *      schema="list",
 *      @OA\Property(
 *          property="id",
 *          type="number",
 *          example=1,
 *      ),
 *      @OA\Property(
 *          property="parent_id",
 *          type="number",
 *          example=1,
 *      ),
 *      @OA\Property(
 *          property="position",
 *          type="number",
 *          example=1,
 *      ),
 *  )
 */
class MenuController extends Controller
{
    public function __construct(MenuService $menuService)
    {
        $this->menuService = $menuService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     *
     *  @OA\Get(
     *      path="/api/menu",
     *      tags={"Menu"},
     *      operationId="indexMenu",
     *      summary="List Menu",
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
     *                  @OA\Items(ref="#/components/schemas/menu")
     *              ),
     *          ),
     *      ),
     *  )
     */
    public function index(Request $request)
    {
        $data = $request->all();
        $data['with'] = ['roles'];

        $menus = $this->menuService->index($data);

        return MenuResource::collection($menus);
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
     *      path="/api/menu",
     *      tags={"Menu"},
     *      operationId="storeMenu",
     *      summary="Create Menu",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/menu"),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Created",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/components/schemas/menu",
     *              ),
     *          ),
     *      ),
     *  )
     */
    public function store(MenuRequest $request)
    {
        $data = $request->all();
        $data['position'] = $this->menuService->getIncrementPosition();

        $menu = $this->menuService->create($data);

        if ($request->has('roles')) {
            $menu->syncRoles($request->roles);
        }

        return new MenuResource($menu);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     *
     *  @OA\Get(
     *      path="/api/menu/{id}",
     *      tags={"Menu"},
     *      operationId="showMenu",
     *      summary="Get Menu",
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
     *                  ref="#/components/schemas/menu",
     *              ),
     *          ),
     *      ),
     *  )
     */
    public function show(Menu $menu)
    {
        return new MenuResource($menu->load('roles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     *
     *  @OA\Put(
     *      path="/api/menu/{id}",
     *      tags={"Menu"},
     *      operationId="updateMenu",
     *      summary="Update Menu",
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
     *          @OA\JsonContent(ref="#/components/schemas/menu"),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Updated",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/components/schemas/menu",
     *              ),
     *          ),
     *      ),
     *  )
     */
    public function update(MenuRequest $request, Menu $menu)
    {
        $menu->update($request->all());

        if ($request->has('roles')) {
            $menu->syncRolesDeep([$menu->load('menus')], $request->roles);
        }

        return new MenuResource($menu);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     *
     *  @OA\Delete(
     *      path="/api/menu/{id}",
     *      tags={"Menu"},
     *      operationId="deleteMenu",
     *      summary="Delete Menu",
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
    public function destroy(Menu $menu)
    {
        $menu->delete();

        return response()->json(null, 204);
    }

    /**
     * Moving a list menu.
     *
     * @return Response
     *
     *  @OA\Post(
     *      path="/api/menu/move",
     *      tags={"Menu"},
     *      operationId="moveMenu",
     *      summary="Move Menu",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="list",
     *                  type="array",
     *                  @OA\Items(ref="#/components/schemas/list")
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Moved",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(ref="#/components/schemas/menu")
     *              ),
     *          ),
     *      ),
     *  )
     */
    public function move(MoveRequest $request)
    {
        $data = [];
        foreach ($request->list as $key => $values) {
            $data[$values['id']] = [
                'position' => $values['position'],
                'parent_id' => $values['parent_id'],
            ];
        }

        $this->menuService->edit($data);

        return response()->json(null, 204);
    }
}
