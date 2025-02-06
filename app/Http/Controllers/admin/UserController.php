<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{

    public function index()
    {
        $users = User::get();

        return response()->json($users, 200);
    }

    public function store(Request $request) {}


    public function show(string $id)
    {

        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    "message" => "No user was found with Id " . $id,
                ], 404);
            }

            return response()->json($user, 200);
        } catch (\Exception) {
            return response()->json([
                "message" => "An unexpected error occurred. Please try again later.",
            ], 500);
        }
    }


    public function update(Request $request, string $id)
    {

        $validated = $request->validate([
            "name" => "sometimes|required",
            "email" => "sometimes|required|unique:users,email",
        ]);

        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    "message" => "No user was found with ID " . $id,
                ], 404);
            }

            $user->update($validated);

            if ($user->wasChanged()) {
                return response()->json([
                    "message" => "User has been successfully updated",
                ], 200);
            } else {
                return response()->json([
                    "message" => "Something went wrong, user was not successfully updated",
                    "user" => $user
                ], 200);
            }
        } catch (\Exception) {
            return response()->json([
                "message" => "An unexpected error occurred. Please try again later.",
            ], 500);
        }
    }

    public function destroy(string $id)
    {

        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    "message" => "No user was found with ID " . $id,
                ], 404);
            }

            $deleted =  $user->delete();

            if ($deleted) {
                return response()->json([
                    "message" => "User have been successfully deleted",
                ], 200);
            } else {
                return response()->json([
                    "message" => "Something went wrong, user was not successfully deleted",
                ], 422);
            }
        } catch (\Exception) {
            return response()->json([
                "message" => "An unexpected error occurred. Please try again later.",
            ], 500);
        }
    }



    public function search(string $keyword)
    {

        try {
            $users = User::where("name", "LIKE", "%" . $keyword . "%")->orWhere("email", "LIKE", "%" . $keyword . "%")->get();

            if (!$users) {
                return response()->json([
                    "message" => "Your search " . $keyword . " didn't return any result.",
                ], 404);
            }

            return response()->json($users, 200);
        } catch (\Exception) {
            return response()->json([
                "message" => "An unexpected error occurred. Please try again later.",
            ], 500);
        }
    }
}
