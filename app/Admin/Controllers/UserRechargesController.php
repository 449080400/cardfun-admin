<?php

namespace App\Admin\Controllers;


use App\Models\User;
use App\Models\UserRecharge;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\MessageBag;

class UserRechargesController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {
            $content->header('充值列表');
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
            $content->header('编辑充值记录');
            $content->body($this->form(true)->edit($id));
        });
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) {
            $content->header('新增充值记录');
            $content->body($this->form(false));
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(UserRecharge::class, function (Grid $grid) {
            $grid->model()->orderBy('created_at', 'desc');
            $grid->id('ID')->sortable();
            $grid->column('user.id', '用户ID');
            $grid->column('user.name', '用户名');
            $grid->column('amount', '充值金额');
            $grid->created_at('创建时间');
            $grid->updated_at('更新时间');

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('user_id', '用户id');
                $filter->where(function ($query) {
                    $query->whereHas('user', function ($query) {
                        $query->where('name', 'like', "%{$this->input}%");
                    });
                }, '用户名');
                $filter->where(function ($query) {
                    $query->whereHas('user', function ($query) {
                        $query->where('phone', 'like', "%{$this->input}%");
                    });
                }, '用户手机号');
                $filter->between('created_at', '充值时间')->datetime();
            });
            $grid->actions(function ($actions) {
                $actions->disableView();
            });
            $grid->disableExport();
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form($isEditing = false)
    {
        $form = new Form(new UserRecharge);
        $form->display('id', 'ID');
        // 如果是编辑的情况
        if ($isEditing) {
            $form->display('user_id', '用户ID');
        } else {
            $form->text('user_id', '用户ID')->rules('required|numeric|min:0');
        }
        $form->text('amount', '充值金额')->rules('required|numeric|min:0');
        $form->saving(function (Form $form) {
            // 用户是否存在
            if (!$form->model()->id) {
                $user = User::find($form->user_id);
                if (!$user) {
                    $error = new MessageBag([
                        'title' => '用户ID',
                        'message' => '用户id不存在，请重新输入',
                    ]);
                    return back()->with(compact('error'));
                }
            }
        });
        $form->tools(function (Form\Tools $tools) {
            $tools->disableView();
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
