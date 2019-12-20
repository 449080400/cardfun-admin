<?php

namespace App\Admin\Controllers;

use app\models\Auth;
use App\Models\Level;
use App\Models\Shop;
use App\Models\User;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class UsersController extends Controller
{
    use ModelForm;

    public function index()
    {
        return Admin::content(function (Content $content) {
            // 页面标题
            $content->header('用户列表');
            $content->body($this->grid());
        });
    }

    /**
     * Edit interface.
     *
     * @param $id
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {
            $content->header('编辑用户');
            $content->body($this->form(true)->edit($id));
        });
    }

    protected function grid()
    {
        // 根据回调函数，在页面上
        return Admin::grid(User::class, function (Grid $grid) {
            $grid->model()->orderBy('id', 'desc');
            // 创建一个列名为 ID 的列，内容是用户的 id 字段，并且可以在前端页面点击排序
            $grid->id('ID')->sortable();
            // 创建一个列名为 用户名 的列，内容是用户的 name 字段
            $grid->name('用户名');
            $grid->avatar('头像')->image('', 50, 50);
            $grid->gender('性别')->display(function ($value) {
                if ($value == 1){
                    return '男';
                }elseif ($value == 2){
                    return '女';
                }else{
                    return '未知';
                }
            });
            //$grid->column('_level.name', '等级');
            //$grid->deposit('押金');
            //$grid->integral('积分');
            $grid->phone('手机号');
            $grid->created_at('注册时间');
            $grid->last_actived_at('最新登录时间')->sortable();
            // 不在页面显示 `新建` 按钮，因为我们不需要在后台新建用户
            $grid->disableCreateButton();
            $grid->actions(function ($actions) {
                $actions->disableView();
                // 不在每一行后面展示删除按钮
                $actions->disableDelete();
            });
            $grid->disableExport();
            $grid->tools(function ($tools) {
                // 禁用批量删除按钮
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
            });

            $grid->filter(function (Grid\Filter $filter) {
                $filter->like('name', '用户名');
                $filter->like('phone', '手机号');
            });

        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form($isEditing = false)
    {
        $form = new Form(new User);
        $form->display('id', 'ID');
        // 如果是编辑的情况
        $form->display('name', '用户名');
        $folder_name = "images/" . date("Ym", time()) . '/'.date("d", time());
        $form->image('avatar', '头像')->removable()->move($folder_name)->uniqueName();
        $form->radio('gender', '性别')->options(['1' => '男', '2' => '女']);
        $form->select('level', '等级')->options(Level::orderBy('level', 'asc')->get()->pluck('name', 'level'));
        //$form->display('deposit', '押金');
        //$form->text('integral', '积分')->rules('required|numeric|min:0');
        $form->text('phone', '手机号');
        $form->tools(function (Form\Tools $tools) {
            $tools->disableView();
        });

        $form->saving(function (Form $form) {
            if (is_null($form->level)){
                $form->level = 0;
            }
        });

        $form->footer(function ($footer) {

            // 去掉`重置`按钮
            //$footer->disableReset();

            // 去掉`提交`按钮
            //$footer->disableSubmit();

            // 去掉`查看`checkbox
            $footer->disableViewCheck();

            // 去掉`继续编辑`checkbox
            //$footer->disableEditingCheck();

            // 去掉`继续创建`checkbox
            $footer->disableCreatingCheck();

        });
        return $form;
    }



}
