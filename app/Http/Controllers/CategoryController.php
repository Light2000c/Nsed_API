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

            return response()->json([
                "status" => "success",
                "categories" => $categories,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "status" => "failed",
                "message" => "An unexpected error occurred. Please try again later.",
            ], 500);
        }
    }


    public function store(Request $request) {}


    public function show(string $id)
    {
        try {

            $category = Category::find($id);

            if (!$category) {
                return response()->json([
                    "status" => "failed",
                    "message" => "No category was found with ID " . $id,
                ], 404);
            }

            return response()->json([
                "status" => "success",
                "categories" => $category,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "status" => "failed",
                "message" => "An unexpected error occurred. Please try again later.",
            ], 500);
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


    public function search(string $keyword)
    {

        try {
            $categories = Category::where("name", "like", "%" . $keyword . "%")->get();

            if ($categories->isEmpty()) {
                return response()->json([
                    "status" => "failed",
                    "message" => "Your search for ". $keyword ." didn't return any results.",
                ], 404);
            }

            return response()->json([
                "status" => "success",
                "categories" => $categories,
            ], 200);
        } catch (\Exception) {
            return response()->json([
                "status" => "failed",
                "message" => "An unexpected error occurred. Please try again later.",
            ], 500);
        }
    }
}
