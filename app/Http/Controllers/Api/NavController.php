<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Taoran\Laravel\Exception\ApiException;

class NavController extends Controller
{

    public function index()
    {
        $list = \App\Logic\Api\NavLogic::getList();

        return response_json([], $list);
    }
}
