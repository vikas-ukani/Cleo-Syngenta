<?php

namespace App\Http\Controllers\Syngenta;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FrontendMasterPageController extends Controller
{
    function route(Request $request)
    {

        return view('app', [
            'businesses' => [
                'seedcare' => [
                    'image' => null,
                    'title' => 'Seedcare',
                ],
                'crop-protection' => [
                    'image' => null,
                    'title' => 'Crop Protection',
                ],
            ],
        ]);
    }
}
