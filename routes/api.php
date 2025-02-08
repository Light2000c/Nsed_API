<?php

use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\admin\GigController as AdminGigController;
use App\Http\Controllers\admin\OrderController;
use App\Http\Controllers\admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\buyer\BuyerController;
use App\Http\Controllers\buyer\OrderController as BuyerOrderController;
use App\Http\Controllers\CategoryController as ControllersCategoryController;
use App\Http\Controllers\ConditionController;
use App\Http\Controllers\gig\GigController;
use App\Http\Controllers\gig\GigFileController;
use App\Http\Controllers\gig\GigPackageController;
use App\Http\Controllers\gig\GigReviewController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\seller\OrderController as SellerOrderController;
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

Route::get("/buyer", [BuyerController::class, "index"]);

Route::get("/gigs", [GigController::class, "index"]);
Route::get("/gigs/{id}", [GigController::class, "show"]);
Route::get("/gigs/search/{keyword}", [GigController::class, "search"]);

Route::get("/gig/packages", [GigPackageController::class, "index"]);
Route::get("/gig/packages/{id}", [GigPackageController::class, "show"]);


Route::get("/gig/files", [GigFileController::class, "index"]);
Route::get("/gig/files/{id}", [GigFileController::class, "show"]);

Route::get("/gig/reviews", [GigReviewController::class, "index"]);
Route::get("/gig/reviews/{id}", [GigReviewController::class, "show"]);

Route::get("status", [ConditionController::class, "index"]);

Route::group(["middleware" => "auth:sanctum"], function () {


    Route::get("/categories", [ControllersCategoryController::class, "index"]);
    Route::get("/categories/{id}", [ControllersCategoryController::class, "show"]);
    Route::get("/categories/search/{keyword}", [ControllersCategoryController::class, "search"]);

    // Route::get("status", [ConditionController::class, "index"]);
    Route::post("status", [ConditionController::class, "store"]);

    Route::get("/seller/{id}", [SellerController::class, "show"]);
    Route::post("/seller", [SellerController::class, "register"]);
    Route::put("/seller/{id}", [SellerController::class, "update"]);


    Route::get("/buyer/{id}", [BuyerController::class, "show"]);
    Route::post("/buyer", [BuyerController::class, "register"]);
    Route::put("/buyer/{id}", [BuyerController::class, "update"]);


    //gig controller
    Route::post("/gigs", [GigController::class, "store"]);
    Route::put("/gigs/{id}", [GigController::class, "update"]);
    Route::delete("/gigs/{id}", [GigController::class, "destroy"]);


    //gig package controller
    Route::post("/gig/packages", [GigPackageController::class, "store"]);
    Route::put("/gig/packages/{id}", [GigPackageController::class, "update"]);
    Route::delete("/gig/packages/{id}", [GigPackageController::class, "destroy"]);


    //gig file controller
    Route::post("/gig/files", [GigFileController::class, "store"]);
    Route::put("/gig/files/{id}", [GigFileController::class, "update"]);
    Route::delete("/gig/files/{id}", [GigFileController::class, "destroy"]);

    //gig review controller
    Route::post("/gig/reviews", [GigReviewController::class, "store"])->middleware("ensure_buyer");
    Route::put("/gig/reviews/{id}", [GigReviewController::class, "update"])->middleware("ensure_buyer");
    Route::delete("/gig/reviews/{id}", [GigReviewController::class, "destroy"]);
});


Route::group(["middleware" => ["auth:sanctum", "ensure_buyer"], "prefix" => "buyers"], function () {

    //order route  
    Route::get("/orders", [BuyerOrderController::class, "index"]);
    Route::get("/orders/{id}", [BuyerOrderController::class, "show"]);
    Route::get("/orders/search/{keyword}", [BuyerOrderController::class, "destroy"]);
    Route::post("/orders", [BuyerOrderController::class, "store"]);
    Route::put("/orders/{id}", [BuyerOrderController::class, "update"]);
    Route::delete("/orders/{id}", [BuyerOrderController::class, "destroy"]);
});


Route::group(["middleware" => ["auth:sanctum", "ensure_seller"], "prefix" => "sellers"], function () {
    Route::get("/orders", [SellerOrderController::class, "index"]);
    Route::get("/orders/{id}", [SellerOrderController::class, "show"]);
    Route::put("/orders/{id}", [SellerOrderController::class, "update"]);
});



// admin routes
Route::group(["middleware" => ["auth:sanctum", "is_admin"], "prefix" => "admin"], function () {
    Route::get("/categories", [CategoryController::class, "index"]);
    Route::get("/categories/{id}", [CategoryController::class, "show"]);
    Route::post("/categories", [CategoryController::class, "store"]);
    Route::put("/categories/{id}", [CategoryController::class, "update"]);
    Route::delete("/categories/{id}", [CategoryController::class, "destroy"]);
    Route::get("/categories/search/{keyword}", [CategoryController::class, "search"]);


    //gigs
    Route::get("/gigs", [AdminGigController::class, "index"]);
    Route::get("/gigs/{id}", [AdminGigController::class, "show"]);
    Route::get("/gigs/search/{keyword}", [AdminGigController::class, "search"]);
    Route::put("/gigs/{id}", [AdminGigController::class, "update"]);
    Route::delete("/gigs/{id}", [AdminGigController::class, "destroy"]);


    //orders
    Route::get("/orders", [OrderController::class, "index"]);
    Route::get("/orders/{id}", [OrderController::class, "show"]);
    Route::get("/orders/search/{keyword}", [OrderController::class, "search"]);
    Route::put("/orders/{id}", [OrderController::class, "update"]);
    Route::delete("/orders/{id}", [OrderController::class, "destroy"]);


    //user
    Route::get("/users", [UserController::class, "index"]);
    Route::get("/users/{id}", [UserController::class, "show"]);
    Route::get("/users/search/{keyword}", [UserController::class, "search"]);
    Route::put("/users/{id}", [UserController::class, "update"]);
    Route::delete("/users/{id}", [UserController::class, "destroy"]);
});


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
