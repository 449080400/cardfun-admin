<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $table = 'banners';

    // 跳转类型
    const JUMP_TYPE_USER_INFO = 'user_info';
    const JUMP_TYPE_CARD_INFO = 'card_info';
    const JUMP_TYPE_URL = 'url';
    public static $jumpTypeMap = [
        self::JUMP_TYPE_USER_INFO => '用户详情',
        self::JUMP_TYPE_CARD_INFO => '卡片详情',
        self::JUMP_TYPE_URL => 'URL',
    ];


}
