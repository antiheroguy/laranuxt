<?php

namespace App\Services;

use App\Models\Menu;

class MenuService extends BaseService
{
    protected $filters = [
        'ids' => ['id', 'in'],
    ];

    /**
     * @return Menu
     */
    public function getModel()
    {
        return Menu::class;
    }
}
