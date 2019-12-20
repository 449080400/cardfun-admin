<?php

namespace App\Admin\Controllers;

use App\Models\Tag;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class TagsController extends Controller
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
            $content->header('标签列表');
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

            $content->header('编辑标签');
            $content->body($this->form()->edit($id));
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
            $content->header('创建标签');
            $content->body($this->form());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(Tag::class, function (Grid $grid) {

            $grid->id('ID')->sortable();

            $states = [
                'on' => ['text' => 'YES'],
                'off' => ['text' => 'NO'],
            ];
            $grid->name('标签名')->editable();
            $grid->recommend('首页推荐')->switch($states);
            $grid->order('排序')->editable()->sortable();
            $grid->created_at();
            $grid->updated_at();

            $grid->actions(function ($actions) {
                // 不展示 Laravel-Admin 默认的查看按钮
                $actions->disableView();
            });
            $grid->disableExport();
            $grid->filter(function (Grid\Filter $filter) {
                $filter->like('name', '标签名');
            });
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Tag::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->text('name', '标签名')->rules('required');
            //
            /*            $form->switch('hot');
                        $form->switch('new');*/
            $form->switch('recommend', '推荐');
            $form->number('order', '排序（倒序）')->default(0);
            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
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
        });
    }
}
