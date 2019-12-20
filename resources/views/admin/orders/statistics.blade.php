<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">订单流水号：{{ $order->no }}</h3>
        <div class="box-tools">
            <div class="btn-group pull-right" style="margin-right: 10px">
                <a href="/admin/statistics_orders" class="btn btn-sm btn-default"><i class="fa fa-list"></i> 列表</a>
            </div>
        </div>
    </div>
    <div class="box-body">
        <table class="table table-bordered">
            <tbody>
            <tr>
                <td><strong>买家：</strong></td>
                <td>{{ $order->user->name }}</td>
                <td><strong>支付时间：</strong></td>
                <td>{{ $order->paid_at->format('Y-m-d H:i:s') }}</td>
            </tr>
            <tr>
                <td><strong>支付方式：</strong></td>
                <td>{{ !empty($order->payment_method) ? $order->payment_method : '' }}</td>
                <td><strong>支付渠道单号：</strong></td>
                <td>{{ $order->payment_no }}</td>
            </tr>
            <tr>
                <td><strong>收货地址</strong></td>
                <td colspan="3">{{ $order->address['full_address'] }} {{ $order->address['zip'] }} {{ $order->address['contact_name'] }} {{ $order->address['contact_phone'] }}</td>
            </tr>
            <tr>
                <td><strong>身份信息</strong></td>
                <td colspan="3">{{ $order->identity['real_name'] }} {{ $order->identity['idcard_no'] }}</td>
            </tr>
            <tr>
                <td><strong>清关状态</strong></td>
                <td>{{ \App\Models\Order::$customsStatusMap[$order->customs_status] }}</td>
                <td><strong>清关说明</strong></td>
                <td>{{ $order->customs_data }}</td>
            </tr>
            <tr>
                <td rowspan="{{ $order->items->count() + 1 }}"><strong>商品列表</strong></td>
                <td><strong>商品名称</strong></td>
                <td><strong>单价&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;税额&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;成本&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;利润</strong></td>
                <td><strong>数量</strong></td>
            </tr>
            @foreach($order->items as $item)
            <tr>
                <td>{{ $item->productSku->title }}</td>
                <td>￥{{ $item->price }} / {{ $item->tax }} / ￥{{$item->cost}} / ￥{{$item->profit}}</td>
                <td>{{ $item->amount }}</td>
            </tr>
            @endforeach
            <tr>
                <td><strong>税费：</strong></td>
                <td colspan="3">+￥{{ $order->tax_amount > 0 ? $order->tax_amount : '0.00' }}</td>
            </tr>
            <tr>
                <td><strong>运费：</strong></td>
                <td colspan="3">+￥{{ $order->freight > 0 ? $order->freight : '0.00' }}</td>
            </tr>
            <tr>
                <td><strong>优惠券：</strong></td>
                <td colspan="3">-￥{{ $order->couponCode->couponCode->value ? $order->couponCode->couponCode->value : '0.00'}}</td>
            </tr>
            <tr>
                <td><strong>订单金额：</strong></td>
                <td>￥{{ $order->total_amount }}</td>
                <!-- 这里也新增了一个发货状态 -->
                <td><strong>发货状态：</strong></td>
                <td>{{ \App\Models\Order::$shipStatusMap[$order->ship_status] }}</td>
            </tr>
            <tr>
                <td><strong>订单类型：</strong></td>
                <td>{{ \App\Models\Order::$typeMap[$order->type] }}</td>
                <!-- 这里也新增了一个团购状态 -->

                @if($order->groupItem->status)
                <td><strong>团购状态：</strong></td>
                <td>{{ \App\Models\GroupItem::$statusMap[$order->groupItem->status] }}</td>
                @endif
            </tr>
            <!-- 展示物流公司和物流单号 -->
            <tr>
                <td><strong>物流公司：</strong></td>
                <td>{{ $order->ship_data['express_company'] }}</td>
                <td><strong>物流单号：</strong></td>
                <td>{{ $order->ship_data['express_no'] }}</td>
            </tr>
            <tr>
                <td><strong>买家备注：</strong></td>
                <td colspan="3">{{ $order->remark }}</td>
            </tr>
            <tr>
                <td><strong>卖家备注：</strong></td>
                <td colspan="3">{{ $order->seller_remark }}</td>
            </tr>
            @if($order->refund_status !== \App\Models\Order::REFUND_STATUS_PENDING)
            {{--<tr>--}}
                {{--<td><strong>退款状态：</strong></td>--}}
                {{--<td colspan="2">{{ \App\Models\Order::$refundStatusMap[$order->refund_status] }}，理由：{{ $order->extra['refund_reason'] }}</td>--}}
                {{--<td>--}}
                    {{--<!-- 如果订单退款状态是已申请，则展示处理按钮 -->--}}
                    {{--@if($order->refund_status === \App\Models\Order::REFUND_STATUS_APPLIED)--}}
                    {{--<button class="btn btn-sm btn-success" id="btn-refund-agree">同意</button>--}}
                    {{--<button class="btn btn-sm btn-danger" id="btn-refund-disagree">不同意</button>--}}
                    {{--@endif--}}
                {{--</td>--}}
            {{--</tr>--}}
            @endif
            <!-- 身份信息开始 -->
            <!-- 清关失败，展示修改身份信息 -->
            @if($order->customs_status === \App\Models\Order::CUSTOMS_STATUS_FAILED || $order->customs_status === \App\Models\Order::CUSTOMS_STATUS_PENDING)
            {{--<tr>--}}
                {{--<td colspan="4">--}}
                    {{--<form action="{{ route('admin.orders.identity', [$order->id]) }}" method="post" class="form-inline">--}}
                        {{--<!-- 别忘了 csrf token 字段 -->--}}
                        {{--<input type="hidden" name="_token" value="{{ csrf_token() }}">--}}
                        {{--<div class="form-group {{ $errors->has('real_name') ? 'has-error' : '' }}">--}}
                            {{--<label for="real_name" class="control-label" style="width:70px"><strong>姓名：</strong></label>--}}
                            {{--<input style="width: 272px" type="text" id="real_name" name="real_name" value="{{ $order->identity['real_name'] }}" class="form-control" placeholder="输入真实姓名">--}}
                            {{--@if($errors->has('real_name'))--}}
                            {{--@foreach($errors->get('real_name') as $msg)--}}
                            {{--<span class="help-block">{{ $msg }}</span>--}}
                            {{--@endforeach--}}
                            {{--@endif--}}
                        {{--</div>--}}
                        {{--<div class="form-group {{ $errors->has('idcard_no') ? 'has-error' : '' }}">--}}
                            {{--<label for="express_no" class="control-label" style="width:70px"><strong>身份证号：</strong></label>--}}
                            {{--<input style="width: 272px" type="text" id="express_no" name="idcard_no" value="{{ $order->identity['idcard_no'] }}" class="form-control" placeholder="输入身份证号">--}}
                            {{--@if($errors->has('idcard_no'))--}}
                            {{--@foreach($errors->get('idcard_no') as $msg)--}}
                            {{--<span class="help-block">{{ $msg }}</span>--}}
                            {{--@endforeach--}}
                            {{--@endif--}}
                        {{--</div>--}}
                        {{--<button type="submit" class="btn btn-success" id="ship-btn">修改身份信息</button>--}}
                    {{--</form>--}}
                {{--</td>--}}
            {{--</tr>--}}
            @endif
            <!-- 身份信息结束 -->
            <!-- 订单发货开始 -->
            <!-- 如果订单未发货，展示发货表单 -->
            @if((($order->ship_status !== \App\Models\Order::SHIP_STATUS_RECEIVED) && ($order->type === \App\Models\Order::TYPE_NORMAL || $order->type === \App\Models\Order::TYPE_BARGAIN)) || ($order->ship_status !== \App\Models\Order::SHIP_STATUS_RECEIVED && $order->type === \App\Models\Order::TYPE_GROUP && $order->groupItem->status === \App\Models\GroupItem::STATUS_SUCCESS))
            {{--<tr>--}}
                {{--<td colspan="4">--}}
                    {{--<form action="{{ route('admin.orders.ship', [$order->id]) }}" method="post" class="form-inline">--}}
                        {{--<!-- 别忘了 csrf token 字段 -->--}}
                        {{--<input type="hidden" name="_token" value="{{ csrf_token() }}">--}}
                        {{--<div class="form-group {{ $errors->has('express_company') ? 'has-error' : '' }}">--}}
                            {{--<label for="express_company" class="control-label"><strong style="width:70px">物流公司：</strong></label>--}}
                            {{--<input style="width: 272px" type="text" id="express_company" name="express_company" value="" class="form-control" placeholder="输入物流公司">--}}
                            {{--@if($errors->has('express_company'))--}}
                            {{--@foreach($errors->get('express_company') as $msg)--}}
                            {{--<span class="help-block">{{ $msg }}</span>--}}
                            {{--@endforeach--}}
                            {{--@endif--}}
                        {{--</div>--}}
                        {{--<div class="form-group {{ $errors->has('express_company') ? 'has-error' : '' }}">--}}
                            {{--<label for="express_no" class="control-label"><strong style="width:70px">物流单号：</strong></label>--}}
                            {{--<input style="width: 272px" type="text" id="express_no" name="express_no" value="" class="form-control" placeholder="输入物流单号">--}}
                            {{--@if($errors->has('express_no'))--}}
                            {{--@foreach($errors->get('express_no') as $msg)--}}
                            {{--<span class="help-block">{{ $msg }}</span>--}}
                            {{--@endforeach--}}
                            {{--@endif--}}
                        {{--</div>--}}
                        {{--<button type="submit" class="btn btn-success" id="ship-btn">{{$order->ship_status === \App\Models\Order::SHIP_STATUS_PENDING  ? '发货' : '发货修改' }}</button>--}}
                    {{--</form>--}}
                {{--</td>--}}
            {{--</tr>--}}
            @endif
            <!-- 订单发货结束 -->

            {{--<tr>--}}
                {{--<td colspan="4">--}}
                    {{--<form action="{{ route('admin.orders.remark', [$order->id]) }}" method="post" class="form-inline">--}}
                        {{--<!-- 别忘了 csrf token 字段 -->--}}
                        {{--<input type="hidden" name="_token" value="{{ csrf_token() }}">--}}
                        {{--<div class="form-group {{ $errors->has('seller_remark') ? 'has-error' : '' }}">--}}
                            {{--<label for="seller_remark" class="control-label"><strong style="width:70px">卖家备注：</strong></label>--}}
                            {{--<input style="width: 620px" type="text" id="seller_remark" name="seller_remark" value="{{$order->seller_remark}}" class="form-control" placeholder="输入备注信息">--}}
                            {{--@if($errors->has('seller_remark'))--}}
                            {{--@foreach($errors->get('seller_remark') as $msg)--}}
                            {{--<span class="help-block">{{ $msg }}</span>--}}
                            {{--@endforeach--}}
                            {{--@endif--}}
                        {{--</div>--}}
                        {{--<button type="submit" class="btn btn-success" id="ship-btn">{{$order->seller_remark === '' ? '备注' : '修改备注' }}</button>--}}
                    {{--</form>--}}
                {{--</td>--}}
            {{--</tr>--}}

            </tbody>
        </table>
    </div>
</div>

<script>
    $(document).ready(function() {
        // 『不同意』按钮的点击事件
        $('#btn-refund-disagree').click(function() {
            swal({
                title: '输入拒绝退款理由',
                input: 'text',
                showCancelButton: true,
                confirmButtonText: "确认",
                cancelButtonText: "取消",
                showLoaderOnConfirm: true,
                preConfirm: function(inputValue) {
                    if (!inputValue) {
                        swal('理由不能为空', '', 'error')
                        return false;
                    }
                    // Laravel-Admin 没有 axios，使用 jQuery 的 ajax 方法来请求
                    return $.ajax({
                        url: '{{ route('admin.orders.handle_refund', [$order->id]) }}',
                        type: 'POST',
                        data: JSON.stringify({   // 将请求变成 JSON 字符串
                        agree: false,  // 拒绝申请
                        reason: inputValue,
                        // 带上 CSRF Token
                        // Laravel-Admin 页面里可以通过 LA.token 获得 CSRF Token
                        _token: LA.token,
                    }),
                        contentType: 'application/json',  // 请求的数据格式为 JSON
                });
                },
                allowOutsideClick: () => !swal.isLoading()
            }).then(function (ret) {
                // 如果用户点击了『取消』按钮，则不做任何操作
                if (ret.dismiss === 'cancel') {
                    return;
                }
                swal({
                    title: '操作成功',
                    type: 'success'
                }).then(function() {
                    // 用户点击 swal 上的按钮时刷新页面
                    location.reload();
                });
            });
        });

        // 『同意』按钮的点击事件
        $('#btn-refund-agree').click(function() {
            swal({
                title: '确认要将款项退还给用户？',
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: "确认",
                cancelButtonText: "取消",
                showLoaderOnConfirm: true,
                preConfirm: function() {
                    return $.ajax({
                        url: '{{ route('admin.orders.handle_refund', [$order->id]) }}',
                        type: 'POST',
                        data: JSON.stringify({
                        agree: true, // 代表同意退款
                        _token: LA.token,
                    }),
                        contentType: 'application/json',
                });
                }
            }).then(function (ret) {
                // 如果用户点击了『取消』按钮，则不做任何操作
                if (ret.dismiss === 'cancel') {
                    return;
                }
                swal({
                    title: '操作成功',
                    type: 'success'
                }).then(function() {
                    // 用户点击 swal 上的按钮时刷新页面
                    location.reload();
                });
            });
        });

    });
</script>
