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

            return $categories;
        } catch (\Exception $e) {
            return response()->json([
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

            Category::create($validated);


            return response()->json([
                "message" => "Category has been successfully created."
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
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
                    "message" => "No category was found with ID " . $id,
                ], 404);
            }

            return $category;
        } catch (\Exception $e) {
            return response()->json([
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
                "message" => "No data was provided to update the category."
            ]);
        }

        try {

            $category = Category::find($id);

            if (!$category) {
                return response()->json([
                    "message" => "No category was found with ID " . $id
                ], 404);
            }

            $category->update($validated);

            if ($category->wasChanged()) {
                return response()->json([
                    "message" => "Category has been successfully updated.",
                    "category" => $category,
                ], 200);
            } else {
                return response()->json([
                    "message" => "No changes were made to the category.",
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
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
                    "message" => "No category was found with ID " . $id
                ], 404);
            }

            $deleted = $category->delete();

            if ($deleted) {
                return response()->json([
                    "message" => "Category has been successfuly deleted.",
                ], 200);
            } else {
                return response()->json([
                    "message" => "Category was not successfuly deleted."
                ], 422);
            }
        } catch (\Exception $e) {
            return response()->json([
                "message" => "An unexpected error occurred. Please try again later.",
            ], 500);
        }
    }

    public function search(string $keyword)
    {

        try {
            $categorys = Category::where("name", "%" . $keyword . "%")->get();

            if (!$categorys) {
                return response()->json([
                    "message" => "Your search " . $keyword . " didn't return any result.",
                ], 404);
            }
        } catch (\Exception) {
            return response()->json([
                "message" => "An unexpected error occurred. Please try again later.",
            ], 500);
        }
    }
}
