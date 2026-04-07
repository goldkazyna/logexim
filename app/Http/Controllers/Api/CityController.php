<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CityDelivery;

class CityController extends Controller
{
    public function index()
    {
        $cities = CityDelivery::orderBy('title')->get(['id', 'title']);

        return response()->json(['cities' => $cities]);
    }
}
