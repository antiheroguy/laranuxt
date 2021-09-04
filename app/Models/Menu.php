<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class Menu extends Model
{
    use HasRoles;

    protected $guard_name = 'web';

    protected $fillable = [
        'title',
        'link',
        'icon',
        'parent_id',
        'position',
    ];

    public function menus()
    {
        return $this->hasMany(self::class, 'parent_id')->with('menus')->orderBy('position', 'asc');
    }

    /**
     * @param array $menus
     * @param array $roles
     *
     * @return void
     */
    public function syncRolesDeep($menus, $roles)
    {
        foreach ($menus as $menu) {
            $menu->syncRoles($roles);
            $this->syncRolesDeep($menu->menus, $roles);
        }
    }

    /**
     * Get increment position.
     *
     * @return int
     */
    public function getIncrementPosition()
    {
        $lastPosition = $this->max('position') ?? 0;

        return $lastPosition + 1;
    }
}
