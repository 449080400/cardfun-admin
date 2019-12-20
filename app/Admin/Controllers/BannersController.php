<?php

namespace App\Admin\Controllers;

use App\Models\Banner;
use App\Models\BannerPosition;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class BannersController extends Controller
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

            $content->header('banner列表');

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

            $content->header('编辑banner');

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

            $content->header('创建banner');

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
        return Admin::grid(Banner::class, function (Grid $grid) {
            $grid->model()->orderBy('id', 'desc');
            if (request('trashed') == 1) {
                $grid->model()->onlyTrashed();
            }

            $grid->id('ID')->sortable();

            $grid->title('标题')->editable();

            $grid->cover('封面图')->image('', 150);
            $grid->order('排序')->editable()->sortable();
            $grid->position('显示位置')->editable('select', BannerPosition::all()->pluck('name', 'code'));

            $states = [
                '1' => ['text' => 'YES'],
                '0' => ['text' => 'NO'],
            ];
            //$grid->language('语言')->using(['ch' => '中文', 'en' => '英文']);
            $grid->is_blocked('屏蔽')->switch($states);

            //$grid->start_at('开始时间');
            //$grid->stop_at('结束时间');
            $grid->created_at('创建时间');

            $grid->rows(function (Grid\Row $row) {
                if ($row->id % 2) {
                    $row->setAttributes(['style' => 'color:red;']);
                }
            });

            $grid->filter(function (Grid\Filter $filter) {

                $filter->like('title', '标题');

                //$filter->between('created_at', '创建时间')->datetime();

                $filter->equal('position', '显示位置')->select(BannerPosition::all()->pluck('name', 'code'));
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
    protected function form()
    {
        return Admin::form(Banner::class, function (Form $form) {
            $form->display('id', 'ID');
            $form->text('title', '标题')->rules('required');
            //图片上传
            $folder_name = "images/" . date("Ym", time()) . '/'.date("d", time());
            $filename = Admin::user()->id . '_' . time() . '_' . str_random(10);
            $form->image('cover', '封面图片')->move($folder_name)->resize(1000, null, function ($constraint) { $constraint->aspectRatio(); })->name(function ($file) use ($filename) {
                return $filename.'.'.$file->guessExtension();
            })->rules('required|image');
            $form->textarea('description', '描述');
            /*            $form->datetime('start_at','开始时间')->format('YYYY-MM-DD HH:mm:ss');
                        $form->datetime('stop_at','结束时间')->format('YYYY-MM-DD HH:mm:ss');*/
            $form->number('order', '排序（倒序）')->default(0);
            //$form->radio('language', '语言')->options(['ch' => '中文', 'en'=> '英文'])->default('ch');
            $form->select('position', '显示位置')->options(BannerPosition::all()->pluck('name', 'code'))->rules('required');
            $form->select('jump_type','跳转类型')->options(Banner::$jumpTypeMap);
            $form->text('jump_link','跳转ID');
            //$form->radio('target', '打开方式')->options(['_blank' => '新页面', '_self'=> '当前页'])->default('_blank');
            $form->switch('is_blocked','屏蔽');
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
