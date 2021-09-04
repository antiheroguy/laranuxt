<?php

namespace App\Http\Controllers;

class WebController extends Controller
{
    /**
     * Get the SPA view.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke()
    {
        $path = public_path('_nuxt/index.html');

        if (!file_exists($path)) {
            throw new \Exception('Please run "npm run generate"');
        }

        return response()->file($path);
    }
}
