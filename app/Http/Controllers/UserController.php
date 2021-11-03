<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use App\Http\Requests\ProviderRequest;
use App\Http\Requests\SearchRequest;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Socialite\Facades\Socialite;

/**
 *  @OA\Tag(
 *      name="User",
 *      description="User Resource",
 * )
 *
 *  @OA\Schema(
 *      schema="user",
 *      @OA\Property(
 *          property="name",
 *          type="number",
 *          example=1,
 *      ),
 *      @OA\Property(
 *          property="email",
 *          type="number",
 *          example=1,
 *      ),
 *  )
 *
 *  @OA\Schema(
 *      schema="auth",
 *      @OA\Property(
 *          property="email",
 *          type="string",
 *          example="admin@admin.com",
 *      ),
 *      @OA\Property(
 *          property="password",
 *          type="string",
 *          example="123456",
 *      ),
 *  )
 */
class UserController extends Controller
{
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     *
     *  @OA\Get(
     *      path="/api/user",
     *      tags={"User"},
     *      operationId="indexUser",
     *      summary="List User",
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
     *                  @OA\Items(ref="#/components/schemas/user")
     *              ),
     *          ),
     *      ),
     *  )
     */
    public function index(SearchRequest $request)
    {
        $data = $request->all();
        $data['with'] = ['roles'];

        $users = $this->userService->index($data);

        return UserResource::collection($users);
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
     *      path="/api/user",
     *      tags={"User"},
     *      operationId="storeUser",
     *      summary="Create User",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/user"),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Created",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/components/schemas/user",
     *              ),
     *          ),
     *      ),
     *  )
     */
    public function store(UserRequest $request)
    {
        $data = $request->only('name', 'email');
        $data['password'] = bcrypt($request->password);

        $user = $this->userService->create($data);

        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        }

        return new UserResource($user);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     *
     *  @OA\Get(
     *      path="/api/user/{id}",
     *      tags={"User"},
     *      operationId="showUser",
     *      summary="Get User",
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
     *                  ref="#/components/schemas/user",
     *              ),
     *          ),
     *      ),
     *  )
     */
    public function show(User $user)
    {
        return new UserResource($user->load('roles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     *
     *  @OA\Put(
     *      path="/api/user/{id}",
     *      tags={"User"},
     *      operationId="updateUser",
     *      summary="Update User",
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
     *          @OA\JsonContent(ref="#/components/schemas/user"),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Updated",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/components/schemas/user",
     *              ),
     *          ),
     *      ),
     *  )
     */
    public function update(UserRequest $request, User $user)
    {
        $data = $request->only('name', 'email');

        if ($request->has('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);

        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        }

        return new UserResource($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     *
     *  @OA\Delete(
     *      path="/api/user/{id}",
     *      tags={"User"},
     *      operationId="deleteUser",
     *      summary="Delete User",
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
    public function destroy(User $user)
    {
        if ($user->id == auth()->guard('api')->id()) {
            return response()->json(['message' => 'Access denied'], 403);
        }

        $user->delete();

        return response()->json(null, 204);
    }

    /**
     * Login user.
     *
     * @return UserResource
     *
     *  @OA\Post(
     *      path="/api/login",
     *      tags={"User"},
     *      operationId="loginUser",
     *      summary="Login User",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/auth"),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Logged in",
     *      ),
     *  )
     */
    public function login(AuthRequest $request)
    {
        if (!config('setting.oauth')) {
            if (!auth()->attempt(['email' => $request->email, 'password' => $request->password])) {
                return response()->json(['message' => 'Email/Password do not match'], 401);
            }

            $user = auth()->user();
            $token = $user->createToken($user->email)->accessToken;

            return response()->json(['access_token' => $token]);
        }

        $client = $this->userService->getGrantClient();

        $response = Request::create('oauth/token', 'post', [
            'grant_type' => 'password',
            'client_id' => $client->id,
            'client_secret' => $client->secret,
            'username' => $request->email,
            'password' => $request->password,
        ]);

        $data = app()->handle($response);

        if ($data->status() !== 200) {
            return response()->json(['message' => 'Email/Password do not match'], 401);
        }

        return $data->content();
    }

    /**
     * Login user.
     *
     * @param AuthRequest $request
     *
     * @return UserResource
     *
     *  @OA\Post(
     *      path="/api/refresh",
     *      tags={"User"},
     *      operationId="refreshUser",
     *      summary="Refresh User",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="refresh_token",
     *                  type="string",
     *                  example="demo",
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Refreshed",
     *      ),
     *  )
     */
    public function refresh(Request $request)
    {
        $client = $this->userService->getGrantClient();

        $response = Request::create('/oauth/token', 'post', [
            'grant_type' => 'refresh_token',
            'client_id' => $client->id,
            'client_secret' => $client->secret,
            'refresh_token' => $request->refresh_token,
        ]);

        $data = app()->handle($response);

        if ($data->status() !== 200) {
            return response()->json(['message' => 'Refresh token is invalid'], 403);
        }

        return $data->content();
    }

    /**
     * Get auth user info.
     *
     * @return UserResource
     *
     *  @OA\Get(
     *      path="/api/me",
     *      tags={"User"},
     *      operationId="getProfileUser",
     *      summary="Get Auth User",
     *      @OA\Response(
     *          response=200,
     *          description="Getted",
     *      ),
     *  )
     */
    public function getProfile()
    {
        $user = auth()->guard('api')->user();

        $user->menus = $this->userService->getMenus($user);

        return new UserResource($user);
    }

    /**
     * Logout user.
     *
     * @return Response
     *
     *  @OA\Post(
     *      path="/api/logout",
     *      tags={"User"},
     *      operationId="logoutUser",
     *      summary="Logout User",
     *      @OA\Response(
     *          response=204,
     *          description="Logged out",
     *      ),
     *  )
     */
    public function logout()
    {
        $user = auth()->guard('api')->user();

        $user->token()->revoke();

        return response()->json(null, 204);
    }

    /**
     * Get provider uri.
     *
     * @return Response
     *
     *  @OA\GET(
     *      path="/api/redirect-uri",
     *      tags={"User"},
     *      operationId="GetRedirectURI",
     *      summary="Get Redirect URI",
     *      @OA\Parameter(
     *          name="provider",
     *          in="query",
     *          required=true,
     *          @OA\Schema(
     *              type="string",
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Getted",
     *      ),
     *  )
     */
    public function getRedirectURI(ProviderRequest $request)
    {
        $url = Socialite::driver($request->provider)->stateless()->redirect()->getTargetUrl();

        return response()->json(['url' => $url]);
    }

    /**
     * Handle provider callback.
     *
     * @return Response
     *
     *  @OA\Post(
     *      path="/api/handle-callback",
     *      tags={"User"},
     *      operationId="HandleCallback",
     *      summary="Handle Callback",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="provider",
     *                  type="string",
     *                  example="demo",
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Logged in",
     *      ),
     *  )
     */
    public function handleCallback(ProviderRequest $request)
    {
        $authUser = Socialite::driver($request->provider)->stateless()->user();

        $user = $this->userService->updateOrCreate(
            [
                'email' => $authUser->email,
            ],
            [
                'name' => $authUser->name,
                'password' => bcrypt($authUser->id),
            ]
        );

        if (!config('setting.oauth')) {
            $token = $user->createToken($user->email)->accessToken;

            return response()->json(['access_token' => $token]);
        }

        $client = $this->userService->getGrantClient();

        $response = Request::create('oauth/token', 'post', [
            'grant_type' => 'password',
            'client_id' => $client->id,
            'client_secret' => $client->secret,
            'username' => $user->email,
            'password' => $authUser->id,
        ]);

        $data = app()->handle($response);

        if ($data->status() !== 200) {
            return response()->json(['message' => 'Email/Password do not match'], 401);
        }

        return $data->content();
    }
}
