<?php

namespace App\Models;

use Encore\Admin\Facades\Admin;
use Illuminate\Database\Eloquent\Model;

class Expresses extends Model
{
    protected $fillable = [
        'name',
        'code',
        'logo',
        'introduction',
        'is_banned',
        'shop_id',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            // 创建时自动填充店铺id
            if (Admin::user()->shop_id){
                $model->shop_id = Admin::user()->shop_id;
            } else {
                return false;
            }
        });
    }


}
