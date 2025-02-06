<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ConditionController extends Controller
{
    public function index()
    {

        $token = request()->cookie('auth_token');

        return response()->json([
            "status" => "Ok",
            "token" => $token,
        ]);
    }

    public function store()
    {

        $token = request()->cookie('auth_token');

        return response()->json([
            "status" => "Ok",
            "token" => $token,
        ]);
    }
}
