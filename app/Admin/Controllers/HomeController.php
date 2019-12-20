<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\InfoBox;

class HomeController extends Controller
{
    public function index(Content $content)
    {
        $content->header('概况');
//        $content->row(function ($row) {
//            $today = date('Y-m-d');
//            $today_user_count = \App\Models\User::where('created_at', '>=', $today)->count();
//            $all_user_count = \App\Models\User::count();
//            $today_order_count = \App\Models\Order::where('created_at', '>=', $today)->whereNotNull('paid_at')->count();
//            $all_order_count = \App\Models\Order::whereNotNull('paid_at')->count();
//            $row->column(3, new InfoBox('新增用户', 'users', 'aqua', '/admin/users', $today_user_count));
//            $row->column(3, new InfoBox('全部用户', 'users', 'green', '/admin/users', $all_user_count));
//            $row->column(3, new InfoBox('今日订单', 'shopping-cart', 'yellow', '/admin/orders', $today_order_count));
//            $row->column(3, new InfoBox('全部订单', 'shopping-cart', 'red', '/admin/orders', $all_order_count));
//        });
        return $content;
    }


}
