<?php

namespace Uyson\DcatAdmin\AliyunVod\Http\Controllers;

use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;
use Uyson\DcatAdmin\AliyunVod\DcatAliyunVodServiceProvider;
use Uyson\DcatAdmin\AliyunVod\Repositories\VideoRepository;

class VideoController extends AdminController
{
    public function grid()
    {
        return Grid::make(new VideoRepository(), function (Grid $grid) {

            $grid->quickSearch('Title');
            $grid->filter(function (Grid\Filter $filter) {
                $filter->panel();
                $filter->equal('VideoId', DcatAliyunVodServiceProvider::trans('video.VideoId'))
                    ->width(4);
                $filter->equal(
                    'Status',
                    DcatAliyunVodServiceProvider::trans('video.Status'),
                )
                    ->radio(DcatAliyunVodServiceProvider::trans('video.options.Status'));

            });


            $grid->column('VideoId',
                DcatAliyunVodServiceProvider::trans('video.VideoId')
            )->copyable();
            $grid->column('Title',
                DcatAliyunVodServiceProvider::trans('video.Title')
            );
            $grid->column('CoverURL',
                DcatAliyunVodServiceProvider::trans('video.CoverURL')
            )->image('', 100, 100);

            $grid->column('CateName',
                DcatAliyunVodServiceProvider::trans('video.CateName')
            );
            $grid->column('Duration',
                DcatAliyunVodServiceProvider::trans('video.Duration')
            )->display(fn($seconds) => sprintf('%02d:%02d:%02d', ($seconds/ 3600),($seconds/ 60 % 60), $seconds% 60));
            $grid->column('Status',
                DcatAliyunVodServiceProvider::trans('video.Status')
            )->using(DcatAliyunVodServiceProvider::trans('video.options.Status'));
            $grid->column('CreationTime',
                DcatAliyunVodServiceProvider::trans('video.CreationTime')
            );
        });
    }

}
