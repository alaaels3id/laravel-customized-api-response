<?php

namespace App\Http\Controllers;

use App\Http\ApiResponse;
use App\Models\User;
use App\Http\Resources\UserResource;

class UserController
{
    public function getAllUsers()
    {
//        return ApiResponse::pagination(User::all(),UserResource::class);

//        return ApiResponse::warning('This is warning');

//        return ApiResponse::success();

//        return ApiResponse::validation('user name is required');

//        return ApiResponse::fails();

//        return ApiResponse::unAuth();

        return ApiResponse::authFails();
    }
}
