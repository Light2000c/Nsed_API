<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
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


    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                "name" => "required|unique:categories,name",
            ],
            [
                "name.required" => "category name is required",
                "name.unique" => "category name already exists"
            ]
        );

        try {

            $category =  Category::create($validated);

            return response()->json([
                "status" => "success",
                "message" => "Category has been successfully created.",
                "category" => $category,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                "status" => "failed",
                "message" => "An unexpected error occurred. Please try again later.",
            ], 500);
        }
    }


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

        $validated = $request->validate(
            [
                "name" => "sometimes|required|unique:categories,name",
            ],
            [
                "name.required" => "The category name is required.",
                "name.unique" => "This category name already exists.",
            ]
        );

        if (empty($validated)) {
            return response()->json([
                "status" => "failed",
                "message" => "No data was provided to update the category.",
            ]);
        }

        try {

            $category = Category::find($id);

            if (!$category) {
                return response()->json([
                   "status" => "failed",
                    "message" => "No category was found with ID " . $id
                ], 404);
            }

            $category->update($validated);

            if ($category->wasChanged()) {
                return response()->json([
                    "status" => "success",
                    "message" => "Category has been successfully updated.",
                    "category" => $category,
                ], 200);
            } else {
                return response()->json([
                    "status" => "failed",
                    "message" => "No changes were made to the category.",
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                "status" => "failed",
                "message" => "An unexpected error occurred. Please try again later.",
            ], 500);
        }
    }


    public function destroy(string $id)
    {
        try {

            $category = Category::find($id);

            if (!$category) {
                return response()->json([
                    "status" => "failed",
                    "message" => "No category was found with ID " . $id
                ], 404);
            }

            $deleted = $category->delete();

            if ($deleted) {
                return response()->json([
                    "status" => "success",
                    "message" => "Category has been successfuly deleted.",
                ], 200);
            } else {
                return response()->json([
                    "status" => "failed",
                    "message" => "Category was not successfuly deleted."
                ], 422);
            }
        } catch (\Exception $e) {
            return response()->json([
                "status" => "failed",
                "message" => "An unexpected error occurred. Please try again later.",
            ], 500);
        }
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
