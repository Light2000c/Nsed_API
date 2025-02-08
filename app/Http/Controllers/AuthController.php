<?php

namespace App\Http\Controllers;

use App\Models\LoginCode;
use App\Models\User;
use App\Models\VerificationCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $rules = [
            "email" => "required|email",
            "auth_type" => "required"
        ];


        if ($request->auth_type === "password") {
            $rules["password"] = "required";
        } else {
            $rules["code"] = "required";
        }

        $request->validate($rules);


        try {

            $user = User::where("email", $request->email)->first();

            if (!$user) {
                return response()->json([
                    "message" => "User with email " . $request->email . " doesn't exist",
                ], 400);
            }

            if ($request->auth_type === "password") {

                $password_match = Hash::check($request->password, $user->password);

                if (!$password_match) {
                    return response()->json([
                        "message" => "Incorrect login credentials",
                    ], 400);
                }

                $user->tokens()->delete();
                $token = $user->createToken("user_token")->plainTextToken;

                $user["token"] = $token;
                return response()->json([
                    "message" => "login_success",
                    "user" => $user,
                ]);
                
                // return response()->json([
                //     "message" => "login_success",
                //     "user" => $user,
                //     "token" => $token
                // ])->withCookie(
                //     Cookie::make('auth_token', $token, 60, null, null, true, true, false, 'Lax')
                // );
            }

            if ($request->auth_type === "code") {
                $VerificationCode = LoginCode::where("email", $request->email)
                    ->where("code", $request->code)->first();

                if (!$VerificationCode || $VerificationCode->expires_at < now()) {
                    return response()->json([
                        "message" => "Invalid or expired verification code",
                    ], 400);
                }

                if ($user) {
                    $user->tokens()->delete();
                    $token = $user->createToken("user_token")->plainTextToken;

                    $user["token"] = $token;
                    
                    return response()->json([
                        "message" => "login_success",
                        "user" => $user,
                    ]);

                    // return response()->json([
                    //     "message" => "login_success",
                    //     "user" => $user,
                    //     "token" => $token
                    // ])->withCookie(
                    //     Cookie::make('auth_token', $token, 60, null, null, true, true, false, 'Lax')
                    // );
                }
            }
        } catch (\Exception $e) {
            return response()->json([
                "message" => "An unexpected error occurred. Please try again later.",
            ], 500);
        }
    }




    public function register(Request $request)
    {

        $request->validate([
            "name" => "required",
            "email" => "required|unique:users,email",
            "password" => "required",
            "code" => "required"
        ]);

        try {

            $VerificationCode = VerificationCode::where("email", $request->email)
                ->where("code", $request->code)->first();

            if (!$VerificationCode || $VerificationCode->expires_at < now()) {
                return response()->json([
                    "message" => "Invalid or expired verification code",
                ], 400);
            }

            $user = User::create($request->all());

            if ($user) {
                $token = $user->createToken("user_token")->plainTextToken;

                return response()->json([
                    "message" => "login_success",
                    "user" => $user,
                    "token" => $token
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                "message" => "An unexpected error occurred. Please try again later.",
            ], 500);
        }
    }



    public function logout(Request $request)
    {
        try {

            $user = $request->user();

            $user->tokens()->delete();

            return response()->json([
                "message" => "logout_success",
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "message" => "An error occurred while processing your request. Please try again later."
            ], 500);
        }
    }
}
