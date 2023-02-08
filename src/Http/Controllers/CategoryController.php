<?php

namespace Uyson\DcatAdmin\AliyunVod\Http\Controllers;

use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Show;
use Uyson\DcatAdmin\AliyunVod\DcatAliyunVodServiceProvider;
use Uyson\DcatAdmin\AliyunVod\Repositories\CategoryRepository;

class CategoryController extends AdminController
{

    public function grid()
    {
        return Grid::make(new CategoryRepository(), function (Grid $grid) {
            $grid->disablePagination();
            $grid->quickCreate(function (Grid\Tools\QuickCreate $create) {
                $create->text('CateName', '名称');
            });
            // $grid->disableActions();
            $grid->actions(function(Grid\Displayers\Actions $actions) {
                $actions->disableView();
            });
            $grid->disableEditButton();
            $grid->disableViewButton();
            $grid->disableBatchActions();
            $grid->disableRowSelector();

            $grid->column('CateId', DcatAliyunVodServiceProvider::trans('category.fields.CateId'))->copyable();
            $grid->column('CateName', DcatAliyunVodServiceProvider::trans('category.fields.CateName'))
                ->editable();
        });
    }

    public function form()
    {
        return Form::make(new CategoryRepository(), function (Form $form) {
            $form->disableViewCheck();
            $form->keyName('CateId');
            $form->display('CateId',
                DcatAliyunVodServiceProvider::trans('category.fields.CateId'));
            $form->text('CateName',
                DcatAliyunVodServiceProvider::trans('category.fields.CateName'));
        });
    }

    public function show($id, Content $content)
    {
        return Show::make($id, new CategoryRepository(), function(Show $show) {
            $show->field('CateId',
                DcatAliyunVodServiceProvider::trans('category.fields.CateId'));
            $show->field('CateName',
                DcatAliyunVodServiceProvider::trans('category.fields.CateName'));
        });
    }
}
