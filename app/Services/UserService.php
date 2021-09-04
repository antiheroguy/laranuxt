<?php

namespace App\Services;

use App\Models\Menu;
use App\Models\Role;
use App\Models\User;
use DB;

class UserService extends BaseService
{
    protected $filters = [
        'name' => ['name', '='],
        'name_like' => ['name', 'like'],
        'email' => ['email', '='],
        'email_like' => ['email', 'like'],
        'username' => ['username', '='],
        'username_like' => ['username', 'like'],
    ];

    /**
     * @return User
     */
    public function getModel()
    {
        return User::class;
    }

    /**
     * Get list user menus.
     *
     * @return Menu
     */
    public function getMenus(User $user)
    {
        if ($user->hasRole(Role::ADMIN) || !config('setting.permissions')) {
            return Menu::with('menus')->where('parent_id', 0)->orderBy('position', 'asc')->get();
        }

        $roles = $user->roles->pluck('id')->toArray();

        if (!count($roles)) {
            return [];
        }

        $menuIds = DB::table('model_has_roles')->select('model_id')->whereIn('role_id', $roles)->where('model_type', Menu::class)->get()->pluck('model_id')->toArray();

        if (!count($menuIds)) {
            return [];
        }

        $menus = Menu::whereIn('id', $menuIds)->orderBy('parent_id', 'asc')->orderBy('position', 'asc')->get();

        return $this->recursiveMenu($menus);
    }

    /**
     * Recursive menu.
     *
     * @param array $menus
     * @param int   $parentId
     *
     * @return Menu
     */
    public function recursiveMenu($menus = [], $parentId = 0)
    {
        return
            collect($menus)
                ->filter(function ($item) use ($parentId) {
                    return $item->parent_id == $parentId;
                })
                ->map(function ($item) use ($menus) {
                    $item->menus = $this->recursiveMenu($menus, $item->id);

                    return $item;
                })
                ->values();
    }

    /**
     * Get grant client.
     *
     * @return Collection
     */
    public function getGrantClient()
    {
        $client = DB::table('oauth_clients')
            ->where([
                'password_client' => 1,
                'personal_access_client' => 0,
                'provider' => $this->model->getTable(),
            ])
            ->first();

        if (!$client) {
            throw new \Exception('No client found');
        }

        return $client;
    }
}
