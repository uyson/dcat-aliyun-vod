<?php

namespace Uyson\DcatAdmin\AliyunVod\Http\Controllers;

use Dcat\Admin\Admin;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Show;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Uyson\DcatAdmin\AliyunVod\DcatAliyunVodServiceProvider;
use Uyson\DcatAdmin\AliyunVod\Exceptions\BaseException;
use Uyson\DcatAdmin\AliyunVod\Renderable\Player;
use Uyson\DcatAdmin\AliyunVod\Repositories\CategoryRepository;
use Uyson\DcatAdmin\AliyunVod\Repositories\TranscodeRepository;
use Uyson\DcatAdmin\AliyunVod\Repositories\VideoRepository;

class VideoController extends AdminController
{
    public function grid()
    {

        return Grid::make(new VideoRepository(), function (Grid $grid) {
            $cateRepo = new CategoryRepository();
            $categories = Arr::pluck($cateRepo->get(), 'CateName', 'CateId');

            $grid->actions(function (Grid\Displayers\Actions $actions) {
//                $actions->disableView();
                $actions->disableDelete();
//                $actions->disableEdit();
            });

            $grid->quickSearch('Title');
            $grid->filter(function (Grid\Filter $filter) use ($categories) {
                $filter->panel();
                $filter->equal('VideoId', DcatAliyunVodServiceProvider::trans('video.VideoId'))
                    ->width(4);
                $filter->equal(
                    'CateId',
                    DcatAliyunVodServiceProvider::trans('video.CateName')
                )->radio(Arr::prepend($categories, '全部', 0));
                $filter->equal(
                    'Status',
                    DcatAliyunVodServiceProvider::trans('video.Status'),
                )->radio(Arr::prepend(DcatAliyunVodServiceProvider::trans('video.options.Status'), '全部', 0));

            });

            $grid->column('VideoId',
                DcatAliyunVodServiceProvider::trans('video.VideoId')
            )->copyable();
            $grid->column('Title',
                DcatAliyunVodServiceProvider::trans('video.Title')
            )->editable();
            $grid->column('CoverURL',
                DcatAliyunVodServiceProvider::trans('video.CoverURL')
            )->image('', 100, 100);
            $grid->column('CateId',
                DcatAliyunVodServiceProvider::trans('video.CateName')
            )->select($categories);
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

    public function create(Content $content)
    {
        $transcodeRepo = new TranscodeRepository();
        $categoryRepo = new CategoryRepository();
        $transcodes = Arr::pluck($transcodeRepo->get(), 'Name', 'TranscodeTemplateGroupId');
        $categories = Arr::pluck($categoryRepo->get(), 'CateName', 'CateId');

        $userId = DcatAliyunVodServiceProvider::setting('userId');
        $region = DcatAliyunVodServiceProvider::setting('region');

        return $content
            ->title('点播上传')
            ->description('阿里云点播上传')
            ->body(
                Admin::view('uyson.dcat-aliyun-vod::vod.index',
                compact(
                    'transcodes',
                    'categories',
                    'userId',
                    'region'
                )
            )
            );
    }

    public function form()
    {
        return Form::make(new VideoRepository(), function (Form $form) {
            $cateRepo = new CategoryRepository();
            $form->disableViewCheck();
            $form->select('CateId')->options(Arr::pluck($cateRepo->get(), 'CateName', 'CateId'));
            $form->text('Title');
        });
    }

    public function detail($id)
    {
        return Show::make($id, new VideoRepository(), function (Show $show) use ($id) {
            $show->disableDeleteButton();
            $videoRepo = new VideoRepository();
            $playAuth = $videoRepo->getPlayAuth($id, 3000);

            $show->field('VideoId', DcatAliyunVodServiceProvider::trans('video.VideoId'));
            $show->field('Title', DcatAliyunVodServiceProvider::trans('video.Title'));
            $show->field('CateName');
            $show->field('VideoId', DcatAliyunVodServiceProvider::trans('video.video'))->view(
                'uyson.dcat-aliyun-vod::vod.player',
                compact('id', 'playAuth')
            );
        });
    }


    /**
     * @param Request $request
     * @return array|JsonResponse
     */
    public function createUploadVideoRequest(Request $request): JsonResponse|array
    {
        $filename = $request->post('filename');
        $title = $request->post('title');
        $cateId = $request->post('cateId');
        $templateGroupId = $request->post('templateGroupId');
        try {
            $videoRepo = new VideoRepository();
            return response()->json(
                $videoRepo->createUploadVideo($filename, $title, $cateId, $templateGroupId),
                201
            );
        } catch (BaseException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], $e->getCode());
        } catch (\Exception $e) {
            return response()->json([
                'message' => '未知错误',
                'code' => 400
            ], 400);
        }
    }

    public function refreshUploadVideoRequest(Request $request)
    {
        $videoId = $request->post('videoId');
        try {
            $videoRepo = new VideoRepository();
            return response()->json(
                $videoRepo->refreshUploadVideoRequest($videoId),
                201
            );
        } catch (BaseException $e) {
            \Log::error('refreshUploadVideoRequest', ['exception' => $e]);
            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], $e->getCode());
        } catch (\Exception $e) {
            \Log::error('refreshUploadVideoRequest', ['exception' => $e]);
            return response()->json([
                'message' => '未知错误',
                'code' => 400
            ], 400);
        }
    }
}
