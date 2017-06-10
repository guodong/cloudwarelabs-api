<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class IndexController extends Controller
{
    public function settings()
    {
        return [
            'proxy' => config('services.proxy.server'),
            'ide' => config('services.ide.server')
        ];
    }

}
