<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    //

    public function index () {
        $user = User::get();
        return $user;
    }
    public function test(){
        return response()->json([
            'status'=>'200',
            'message'=>'Successful Integration of Laravel with Nextjs'
        ], 200, [
            'Content-Type'=>'text/json'
        ]);
    }
}
