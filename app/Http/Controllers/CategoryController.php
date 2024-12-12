<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    public function index()
    {
        try {

            $categories = Category::get();

            return $categories;
        } catch (\Exception $e) {
            return response()->json([
                "message" => "Internal server error"
            ]);
        }
    }


    public function store(Request $request) {}


    public function show(string $id)
    {
        try {

            $category = Category::find($id);

            if (!$category) {
                return response()->json([
                    "message" => "No category was found with id " . $id,
                ]);
            }

            return $category;
        } catch (\Exception $e) {
            return response()->json([
                "message" => "Internal server error"
            ]);
        }
    }

    public function update(Request $request, string $id)
    {
        //
    }


    public function destroy(string $id)
    {
        //
    }
}
