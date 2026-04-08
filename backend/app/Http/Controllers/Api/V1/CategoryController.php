<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * List all categories (for onboarding + filters)
     */
    public function index()
    {
        $categories = Category::orderBy('name_en')->get();
        return response()->json(['categories' => $categories]);
    }
}
