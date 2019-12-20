<?php

namespace App\Admin\Controllers;

use App\Handlers\ImageUploadHandler;
use App\Http\Controllers\Controller;
use Encore\Admin\Facades\Admin;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    /**
     * WangEditor图片上传
     */
    public function uploadImages(Request $request, ImageUploadHandler $uploader)
    {

        // 判断是否有上传文件，并赋值给 $file
        $files = $request->file("wangpic");
        $res = ['errno' => 1, 'errmsg' => '上传图片错误'];
        $data = [];
        foreach ($files as $key => $file) {
            // 保存图片到本地
            $result = $uploader->save($file, 'images', Admin::user()->id);
            // 图片保存成功的话
            if ($result) {
                $data[] = $result['path'];
            }
        }
        $res = ['errno' => 0, 'data' => $data];
        return json_encode($res);
    }

    /**
     * WangEditor图片上传
     */
    public function uploadImage(Request $request, ImageUploadHandler $uploader)
    {

        // 判断是否有上传文件，并赋值给 $file
        if ($file = $request->file('image')) {

        } elseif ($file = $request->file('logo')) {

        } elseif ($file = $request->file('cover')) {

        }
        $res = ['errno' => 1, 'errmsg' => '上传图片错误'];
        $data = [];
        // 保存图片到本地
        $result = $uploader->save($file, 'images', Admin::user()->id);
        // 图片保存成功的话
        if ($result) {
            $data[] = $result['path'];
        }
        $res = ['errno' => 0, 'data' => $data];
        return $data;
    }

}
