<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    //
    public function test(){
        return response()->json([
            'status'=>'200',
            'message'=>'Successful Integration of Laravel with Nextjs'
        ], 200, [
            'Content-Type'=>'text/json'
        ]);
    }
}
