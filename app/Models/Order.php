<?php

namespace App\Models;

use Encore\Admin\Facades\Admin;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class Order extends Model
{
    const REFUND_STATUS_PENDING = 'pending';
    const REFUND_STATUS_APPLIED = 'applied';
    const REFUND_STATUS_PROCESSING = 'processing';
    const REFUND_STATUS_SUCCESS = 'success';
    const REFUND_STATUS_FAILED = 'failed';

    const SHIP_STATUS_PENDING = 'pending';
    const SHIP_STATUS_PRINTED_PENDING = 'printed_pending';
    const SHIP_STATUS_DELIVERED = 'delivered';
    const SHIP_STATUS_RECEIVED = 'received';

    const CUSTOMS_STATUS_PENDING = 'pending';
    const CUSTOMS_STATUS_PROCESSING = 'processing';
    const CUSTOMS_STATUS_SUCCESS = 'success';
    const CUSTOMS_STATUS_FAILED = 'failed';
    const CUSTOMS_STATUS_CHARGEBACK = 'chargeback';

    const TYPE_NORMAL = 'normal';
    const TYPE_GROUP = 'group';
    const TYPE_BARGAIN = 'bargain';

    public static $refundStatusMap = [
        self::REFUND_STATUS_PENDING    => '未退款',
        self::REFUND_STATUS_APPLIED    => '已申请退款',
        self::REFUND_STATUS_PROCESSING => '退款中',
        self::REFUND_STATUS_SUCCESS    => '退款成功',
        self::REFUND_STATUS_FAILED     => '退款失败',
    ];

    public static $shipStatusMap = [
        self::SHIP_STATUS_PENDING   => '未发货',
        self::SHIP_STATUS_PRINTED_PENDING   => '已打印待发货',
        self::SHIP_STATUS_DELIVERED => '已发货',
        self::SHIP_STATUS_RECEIVED  => '已收货',
    ];

    public static $customsStatusMap = [
        self::CUSTOMS_STATUS_PENDING   => '未清关',
        self::CUSTOMS_STATUS_PROCESSING => '清关中',
        self::CUSTOMS_STATUS_SUCCESS  => '清关成功',
        self::CUSTOMS_STATUS_FAILED  => '清关失败',
        self::CUSTOMS_STATUS_CHARGEBACK  => '海关退单',
    ];

    public static $typeMap = [
        self::TYPE_NORMAL => '普通商品',
        self::TYPE_GROUP => '团购商品',
        self::TYPE_BARGAIN => '砍价商品',
    ];

    protected $fillable = [
        'type',
        'no',
        'address',
        'identity',
        'total_amount',
        'remark',
        'seller_remark',
        'paid_at',
        'payment_method',
        'payment_no',
        'refund_status',
        'refund_no',
        'closed',
        'reviewed',
        'ship_status',
        'ship_data',
        'delivered_at',
        'received_at',
        'extra',
    ];

    protected $casts = [
        'closed'    => 'boolean',
        'reviewed'  => 'boolean',
        'address'   => 'json',
        'ship_data' => 'json',
        'extra'     => 'json',
        'identity'     => 'json',
    ];

    protected $dates = [
        'paid_at',
        'delivered_at',
        'received_at',
    ];

    protected static function boot()
    {
        parent::boot();
        // 监听模型创建事件，在写入数据库之前触发
        static::creating(function ($model) {
            // 如果模型的 no 字段为空
            if (!$model->no) {
                // 调用 findAvailableNo 生成订单流水号
                $model->no = static::findAvailableNo();
                // 如果生成失败，则终止创建订单
                if (!$model->no) {
                    return false;
                }
            }
            // 创建时自动填充店铺id
            if (Admin::user()->shop_id){
                $model->shop_id = Admin::user()->shop_id;
            } else {
                return false;
            }

        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function couponCode()
    {
        return $this->belongsTo(UserCouponCode::class,'coupon_code_id','id')->withDefault();
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
    public function groupItem()
    {
        return $this->hasOne(GroupItem::class,'id','group_id')->withDefault(function ($group_item) {
        });
    }
    public static function findAvailableNo()
    {
        // 订单流水号前缀
        $prefix = date('YmdHis');
        for ($i = 0; $i < 10; $i++) {
            // 随机生成 6 位的数字
            $no = $prefix.str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            // 判断是否已经存在
            if (!static::query()->where('no', $no)->exists()) {
                return $no;
            }
            usleep(100);
        }
        \Log::warning(sprintf('find order no failed'));

        return false;
    }

    public static function getAvailableRefundNo()
    {
        do {
            // Uuid类可以用来生成大概率不重复的字符串
            $no = Uuid::uuid4()->getHex();
            // 为了避免重复我们在生成之后在数据库中查询看看是否已经存在相同的退款订单号
        } while (self::query()->where('refund_no', $no)->exists());

        return $no;
    }
}
