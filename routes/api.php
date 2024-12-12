<?php

use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\gig\GigController;
use App\Http\Controllers\gig\GigFileController;
use App\Http\Controllers\gig\GigPackageController;
use App\Http\Controllers\gig\GigReviewController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\seller\SellerController;
use App\Http\Controllers\VerificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Auth Routes
Route::post("/register", [AuthController::class, "register"]);
Route::post("/login", [AuthController::class, "login"])->name("login");

Route::post("/google-auth", [GoogleAuthController::class, "SignInWithGoogle"]);

Route::post("/logout", [AuthController::class, "logout"])->middleware("auth:sanctum");

Route::post("/registration-code", [VerificationController::class, "sendRegistrationCode"]);

Route::post("/login-code", [VerificationController::class, "sendLoginCode"]);


//sellers
Route::get("/seller", [SellerController::class, "index"]);

Route::group(["middleware" => "auth:sanctum"], function () {
    Route::get("/seller/{id}", [SellerController::class, "show"]);
    Route::post("/seller", [SellerController::class, "register"]);
    Route::put("/seller/{id}", [SellerController::class, "update"]);

    //gig controller
    Route::get("/gigs", [GigController::class, "index"]);
    Route::get("/gigs/{id}", [GigController::class, "show"]);
    Route::post("/gigs", [GigController::class, "store"]);
    Route::put("/gigs/{id}", [GigController::class, "update"]);
    Route::delete("/gigs/{id}", [GigController::class, "destroy"]);
    Route::get("/gigs/search/{keyword}", [GigController::class, "search"]);

    //gig package controller
    Route::get("/gig/packages", [GigPackageController::class, "index"]);
    Route::get("/gig/packages/{id}", [GigPackageController::class, "show"]);
    Route::post("/gig/packages", [GigPackageController::class, "store"]);
    Route::put("/gig/packages/{id}", [GigPackageController::class, "update"]);
    Route::delete("/gig/packages/{id}", [GigPackageController::class, "destroy"]);

    //gig file controller
    Route::get("/gig/files", [GigFileController::class, "index"]);
    Route::get("/gig/files/{id}", [GigFileController::class, "show"]);
    Route::post("/gig/files", [GigFileController::class, "store"]);
    Route::put("/gig/files/{id}", [GigFileController::class, "update"]);
    Route::delete("/gig/files/{id}", [GigFileController::class, "destroy"]);

    //gig review controller
    Route::get("/gig/reviews", [GigReviewController::class, "index"]);
    Route::get("/gig/reviews/{id}", [GigReviewController::class, "show"]);
    Route::post("/gig/reviews", [GigReviewController::class, "store"]);
    Route::put("/gig/reviews/{id}", [GigReviewController::class, "update"]);
    Route::delete("/gig/reviews/{id}", [GigReviewController::class, "destroy"]);
});


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


// admin routes 
Route::get("/categories", [CategoryController::class, "index"]);
Route::get("/categories/{id}", [CategoryController::class, "show"]);
Route::post("/categories", [CategoryController::class, "store"]);
Route::put("/categories/{id}", [CategoryController::class, "update"]);
Route::delete("/categories/{id}", [CategoryController::class, "destroy"]);
