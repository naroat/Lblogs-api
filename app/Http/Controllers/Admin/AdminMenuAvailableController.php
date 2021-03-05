<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminMenuAvailableController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $list = \App\Logic\Admin\AdminMenuAvailableLogic::getAdminMenuList();
        return response_json($list);
    }
}
