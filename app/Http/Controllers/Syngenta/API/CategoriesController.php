<?php

namespace App\Http\Controllers\Syngenta\API;

use App\Http\Controllers\Controller;
use App\Models\Business;
use Illuminate\Http\Request;
use App\Models\ContentCategory;

class CategoriesController extends Controller
{
    public function getCategories(Request $request)
    {
        $categories = ContentCategory::all();
        return response()->json([
            'data' => $categories,
            'lastUpdated' => $categories->sortBy('updated_at')->first()->updated_at,
        ]);
    }
}
