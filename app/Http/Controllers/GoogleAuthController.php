<?php

namespace App\Http\Controllers;

use App\Models\User;
use Google_Client;
use Illuminate\Http\Request;

class GoogleAuthController extends Controller
{
    public function SignInWithGoogle(Request $request)
    {

        $request->validate([
            "id" => "required",
        ]);

        try {

            $client = new Google_Client();
            $client->setApplicationName('SocialApp');
            $client->setClientId(env('GOOGLE_CLIENT_ID'));

            $payload = $client->verifyIdToken($request->id);

            if ($payload) {

                $googleEmail = $payload['email'];
                $googleName = $payload['name'];
                $googleId = $payload['sub'];


                $user = User::where("email", $googleEmail)->first();

                if ($user) {

                    if (!$user->google_id) {
                        $user->update([
                            'google_id' => $googleId,
                        ]);
                    }

                    $user->makeHidden(["google_id"]);

                    $token = $user->createToken("user_token")->plainTextToken;


                    return response()->json([
                        "message" => "login_success",
                        "user" => $user,
                        "token" => $token
                    ]);
                } else {

                    $new_user = User::create([
                        'name' => $googleName,
                        'email' => $googleEmail,
                        'google_id' => $googleId,
                    ]);

                    if ($new_user) {

                        $new_user->makeHidden(["google_id"]);

                        $token = $new_user->createToken("user_token")->plainTextToken;

                        return response()->json([
                            "message" => "login_success",
                            "user" => $user,
                            "token" => $token,
                        ], 200);
                    } else {
                        return response()->json([
                            "message" => "Something went wrong while trying to sign in."
                        ], 400);
                    }
                }
            } else {
                return response()->json(['messagee' => 'Invalid ID Token'], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                "message" => "An error occurred while processing your request. Please try again later. " . $e->getMessage()
            ], 500);
        }
    }
}





// $user = User::where("email", $request->email)->first();

// if ($user) {

//     if (!$user->google_id) {
//         $user->update([
//             'google_id' => $request->id,
//         ]);
//     }

//     $token = $user->createToken("user_token")->plainTextToken;

//     return response()->json([
//         "message" => "login_success",
//         "user" => $user,
//         "token" => $token
//     ]);
// } else {

//     $new_user = User::create([
//         'name' => $request->name,
//         'email' => $request->email,
//         'google_id' => $request->id,
//     ]);

//     if ($new_user) {
//         $token = $new_user->createToken("user_token")->plainTextToken;

//         return response()->json([
//             "message" => "login_success",
//             "user" => $new_user,
//             "token" => $token
//         ]);
//     } else {
//         return response()->json([
//             "message" => "Something went wrong while trying to sign in."
//         ]);
//     }
// }
