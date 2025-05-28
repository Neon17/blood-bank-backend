<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    // all CRUD operations for User

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

    public function profile() {
        //we have to test this
        return response()->json(Auth::user(), 200);
    }
}
