<?php

namespace App\Http\Controllers;

use App\Mail\VerificationCodeMail;
use App\Models\LoginCode;
use App\Models\User;
use App\Models\VerificationCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class VerificationController extends Controller
{
    public function sendRegistrationCode(Request $request)
    {

        $request->validate([
            "email" => "required|email|unique:users,email",
        ]);

        try {
            $verificationCode = VerificationCode::where("email", $request->email)->first();

            if ($verificationCode) {

                if ($verificationCode->expires_at < now()) {

                    $verificationCode->delete();
                } else {

                    $details = [
                        "code" => $verificationCode->code,
                    ];

                    Mail::to($request->email)->send(new VerificationCodeMail($details));

                    return response()->json([
                        "message" => "success",
                    ]);
                }
            }

            $code = rand(100000, 999999);
            $expires_at = now()->addMinutes(2);

            $created = VerificationCode::create([
                "email" => $request->email,
                "code" => $code,
                "expires_at" => $expires_at,
            ]);

            if ($created) {

                $details = [
                    "code" => $created->code,
                ];

                Mail::to($request->email)->send(new VerificationCodeMail($details));

                return response()->json([
                    "message" => "success",
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                "message" => "An unexpected error occurred. Please try again later.",
            ], 500);
        }
    }


    public function sendLoginCode(Request $request)
    {

        $request->validate([
            "email" => "required|email|exists:users,email",
        ], [
            "email.exists" => "No existing account with this email."
        ]);

        try {
            $verificationCode = LoginCode::where("email", $request->email)->first();

            if ($verificationCode) {

                if ($verificationCode->expires_at < now()) {

                    $verificationCode->delete();
                } else {

                    $details = [
                        "code" => $verificationCode->code,
                    ];

                    Mail::to($request->email)->send(new VerificationCodeMail($details));

                    return response()->json([
                        "message" => "success",
                    ]);
                }
            }

            $code = rand(100000, 999999);
            $expires_at = now()->addMinutes(2);

            $created = LoginCode::create([
                "email" => $request->email,
                "code" => $code,
                "expires_at" => $expires_at,
            ]);

            if ($created) {

                $details = [
                    "code" => $created->code,
                ];

                Mail::to($request->email)->send(new VerificationCodeMail($details));

                return response()->json([
                    "message" => "success",
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                "message" => "An unexpected error occurred. Please try again later.",
            ], 500);
        }
    }
}
