<?php

namespace App\Http\Controllers;

use App\Models\BloodRequest;
use Illuminate\Http\Request;

class BloodRequestController extends Controller
{
    //

    public function index () {
        $bloodRequest = BloodRequest::with('user', 'bloodBank')->get();
        return $bloodRequest;
    }   
}
