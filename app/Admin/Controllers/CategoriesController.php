<?php

namespace App\Admin\Controllers;

use App\Models\Category;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    use HasResourceActions;

    public function index(Content $content)
    {
        return $content
            ->header('类目列表')
            ->body($this->grid());
    }

    public function edit($id, Content $content)
    {
        return $content
            ->header('编辑类目')
            ->body($this->form(true)->edit($id));
    }


    public function create(Content $content)
    {
        return $content
            ->header('创建类目')
            ->body($this->form(false));
    }

    protected function grid()
    {
        // Laravel-Admin 1.5.19 之后的新写法，原写法也仍然可用
        $grid = new Grid(new Category);
        $grid->id('ID')->sortable();
        $grid->name('名称');
        $grid->logo('Logo')->image('', 50, 50);
        $grid->level('层级');
        $grid->is_directory('是否目录')->display(function ($value) {
            return $value ? '是' : '否';
        });
        $grid->path('类目路径');
        $states = [
            '1' => ['text' => 'YES'],
            '0' => ['text' => 'NO'],
        ];
        $grid->is_blocked('屏蔽')->switch($states);
        $grid->actions(function ($actions) {
            // 不展示 Laravel-Admin 默认的查看按钮
            $actions->disableView();
        });
        $grid->disableExport();
        $grid->filter(function (Grid\Filter $filter) {
            $filter->like('name', '分类名');
            $filter->equal('is_blocked', '屏蔽')->radio([
                1 => '是',
                0 => '否',
            ]);
        });
        return $grid;
    }

    protected function form($isEditing = false)
    {
        // Laravel-Admin 1.5.19 之后的新写法，原写法也仍然可用
        $form = new Form(new Category);

        $form->text('name', '类目名称')->rules('required');

        // 如果是编辑的情况
        if ($isEditing) {
            // 不允许用户修改『是否目录』和『父类目』字段的值
            // 用 display() 方法来展示值，with() 方法接受一个匿名函数，会把字段值传给匿名函数并把返回值展示出来
            $form->display('is_directory', '是否目录')->with(function ($value) {
                return $value ? '是' : '否';
            });
            // 支持用符号 . 来展示关联关系的字段
            $form->display('parent.name', '父类目');
        } else {
            // 定义一个名为『是否目录』的单选框
            $form->radio('is_directory', '是否目录')
                ->options(['1' => '是', '0' => '否'])
                ->default('0')
                ->rules('required');

            // 定义一个名为父类目的下拉框
            $form->select('parent_id', '父类目')->options('/admin/api/categories');
        }
        $folder_name = "images/" . date("Ym", time()) . '/' . date("d", time());
        $filename = Admin::user()->id . '_' . time() . '_' . str_random(10);
        $form->image('logo', 'Logo')->move($folder_name)->name(function ($file) use ($filename) {
            return $filename . '.' . $file->guessExtension();
        })->rules('required|image');
        $form->number('order', '排序（倒序）')->default(0);
        $form->textarea('description', '描述');
        $form->switch('is_blocked', '屏蔽');
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

    // 定义下拉框搜索接口
    public function apiIndex(Request $request)
    {
        // 用户输入的值通过 q 参数获取
        $search = $request->input('q');
        $result = Category::query()
            // 通过 is_directory 参数来控制
            ->where('is_directory', boolval($request->input('is_directory', true)))
            ->where('name', 'like', '%' . $search . '%')
            ->get();

        // 把查询出来的结果重新组装成 Laravel-Admin 需要的格式
        $result = ($result->map(function (Category $category) {
            return ['id' => $category->id, 'text' => $category->full_name];
        }));

        return $result;
    }
}
