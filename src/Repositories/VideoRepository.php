<?php

namespace Uyson\DcatAdmin\AliyunVod\Repositories;

use AlibabaCloud\SDK\Vod\V20170321\Models\CreateUploadVideoRequest;
use AlibabaCloud\SDK\Vod\V20170321\Models\CreateUploadVideoResponse;
use AlibabaCloud\SDK\Vod\V20170321\Models\GetPlayInfoRequest;
use AlibabaCloud\SDK\Vod\V20170321\Models\GetPlayInfoResponse;
use AlibabaCloud\SDK\Vod\V20170321\Models\GetPlayInfoResponseBody\playInfoList\playInfo;
use AlibabaCloud\SDK\Vod\V20170321\Models\GetVideoInfoRequest;
use AlibabaCloud\SDK\Vod\V20170321\Models\GetVideoInfoResponse;
use AlibabaCloud\SDK\Vod\V20170321\Models\GetVideoPlayAuthRequest;
use AlibabaCloud\SDK\Vod\V20170321\Models\GetVideoPlayAuthResponse;
use AlibabaCloud\SDK\Vod\V20170321\Models\RefreshUploadVideoRequest;
use AlibabaCloud\SDK\Vod\V20170321\Models\RefreshUploadVideoResponse;
use AlibabaCloud\SDK\Vod\V20170321\Models\SearchMediaRequest;
use AlibabaCloud\SDK\Vod\V20170321\Models\SearchMediaResponse;
use AlibabaCloud\SDK\Vod\V20170321\Models\SearchMediaResponseBody\mediaList;
use AlibabaCloud\SDK\Vod\V20170321\Models\UpdateCategoryRequest;
use AlibabaCloud\SDK\Vod\V20170321\Models\UpdateVideoInfoRequest;
use AlibabaCloud\Tea\Exception\TeaError;
use AlibabaCloud\Tea\Utils\Utils\RuntimeOptions;
use Carbon\Carbon;
use Dcat\Admin\Form;
use Dcat\Admin\Grid\Model;
use Dcat\Admin\Show;
use Dcat\Admin\Repositories\Repository;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Laravel\Octane\Exceptions\DdException as DdExceptionAlias;
use Uyson\DcatAdmin\AliyunVod\DcatAliyunVodServiceProvider;
use Uyson\DcatAdmin\AliyunVod\Exceptions\BaseException;
use Uyson\DcatAdmin\AliyunVod\Exceptions\GetPlayAuthFailException;
use Uyson\DcatAdmin\AliyunVod\Exceptions\GetPlayInfoFailException;
use Uyson\DcatAdmin\AliyunVod\Models\Video;

class VideoRepository extends Repository
{
    public function getKeyName()
    {
        return 'VideoId';
    }
    public function getCreatedAtColumn()
    {
        return null;
    }

    public function getUpdatedAtColumn()
    {
        return null;
    }

    public function isSoftDeletes()
    {
        return false;
    }

    public function get(\Dcat\Admin\Grid\Model $model)
    {
        $quickSearch = $model->grid()->quickSearch()->value();
        $currentPage = $model->getCurrentPage();
        $perPage = $model->getPerPage();
        $params = [
            "pageNo" => $currentPage,
            "pageSize" => $perPage,
            "fields" => "VideoId,Title,CoverURL,CateName,Duration,Status,CreationTime",
        ];
        $inputs = $model->filter()->inputs();

        $matchs = [];
        if ($quickSearch) {
            $matchs[] = "Title='{$quickSearch}'";
        }
        if (isset($inputs['VideoId']) && $inputs['VideoId']) {
            $matchs[] = "VideoId='{$inputs['VideoId']}'";
        }
        if (isset($inputs['Status']) && $inputs['Status']) {
            $matchs[] = "Status in ('{$inputs['Status']}')";
        }
        if (isset($inputs['CateId']) && $inputs['CateId']) {
            $matchs[] = "CateId={$inputs['CateId']}";
        }

        if(count($matchs)) {
            $params['match'] = implode(" and ", $matchs);
        }

        $client = app('uyson.aliyun.vod');
        $searchMediaRequest = new SearchMediaRequest($params);
        $runtime = new RuntimeOptions([]);
        try {
            /**
             * @var SearchMediaResponse $res
             */
            $res = $client->searchMediaWithOptions($searchMediaRequest, $runtime);
            $total = $res->body->total;
            $videos = [];
            if ($total) {
                $videos = array_map(function(mediaList $media){
                    return $media->video->toMap();
                }, $res->body->mediaList);
            }

            return $model->makePaginator(
                $total,
                $videos
            );

        }
        catch (Exception $error) {
            if (!($error instanceof TeaError)) {
                // $error = new TeaError([], $error->getMessage(), $error->getCode(), $error);
                admin_error('出现错误', $error->getMessage());
            }
            admin_error('出现错误', '视频搜索结果获取失败');
        }
    }

    protected function search(Model $model, string $keyword, int $currentPage, int $perPage) {
        $client = app('uyson.aliyun.vod');
        $searchMediaRequest = new SearchMediaRequest([
            "pageNo" => $currentPage,
            "pageSize" => $perPage,
            "fields" => "VideoId,Title,CoverURL,CateName,Duration,Status,CreationTime",
            "match" => "Title='{$keyword}'"
        ]);
        $runtime = new RuntimeOptions([]);
        try {
            /**
             * @var SearchMediaResponse $res
             */
            $res = $client->searchMediaWithOptions($searchMediaRequest, $runtime);
            $total = $res->body->total;
            $videos = [];
            if ($total) {
                $videos = array_map(function(mediaList $media){
                    return $media->video->toMap();
                }, $res->body->mediaList);
            }

            return $model->makePaginator(
                $total,
                $videos
            );

        }
        catch (Exception $error) {
            if (!($error instanceof TeaError)) {
                // $error = new TeaError([], $error->getMessage(), $error->getCode(), $error);
                admin_error('出现错误', $error->getMessage());
            }
            admin_error('出现错误', '视频搜索结果获取失败');
        }
    }

    public function store(\Dcat\Admin\Form $form)
    {
        return true;
    }

    public function detail(Show $show)
    {
        return $this->find($show->getKey());
    }

    public function edit(Form $form)
    {
        return $this->find($form->getKey());
    }

    protected function find($id) {
        $client = app('uyson.aliyun.vod');
        $getCategoriesRequest = new GetVideoInfoRequest([
            'videoId' => $id
        ]);

        $runtime = new RuntimeOptions([]);
        try {
            // 复制代码运行请自行打印 API 的返回值
            /**
             * @var GetVideoInfoResponse $res
             */
            $res = $client->getVideoInfoWithOptions($getCategoriesRequest, $runtime);

            return $res->body->video->toMap();
        }
        catch (Exception $error) {
            if (!($error instanceof TeaError)) {
                // $error = new TeaError([], $error->getMessage(), $error->getCode(), $error);
                admin_error('出现错误', $error->getMessage());
            }
            admin_error('出现错误', '点播分类获取失败');
        }
    }

    public function updating(Form $form)
    {
        return [];
    }

    public function update(Form $form)
    {
        $id = $form->getKey();

        $attributes = $form->updates();
        $params = [
            'videoId' => $id
        ];
        foreach ($attributes as $key => $value) {
            $params[lcfirst($key)] = $value;
        }
        $client = app('uyson.aliyun.vod');
        $updateCategoryRequest = new UpdateVideoInfoRequest($params);
        $runtime = new RuntimeOptions([]);
        try {
            // 复制代码运行请自行打印 API 的返回值
            $client->updateVideoInfoWithOptions($updateCategoryRequest, $runtime);
        }
        catch (Exception $error) {
            if (!($error instanceof TeaError)) {
                // $error = new TeaError([], $error->getMessage(), $error->getCode(), $error);
                admin_error('出现错误', $error->getMessage());
            }
            admin_error('出现错误', '点播分类获取失败');
        }
        return true;
    }

    public function createUploadVideo($fileName, $title, $cateId, $templateGroupId)
    {
        $client = app('uyson.aliyun.vod');
        $createUploadVideoRequest = new CreateUploadVideoRequest([
            "fileName" => $fileName,
            "title" => $title,
            "cateId" => $cateId,
            "templateGroupId" => $templateGroupId
        ]);
        $runtime = new RuntimeOptions([]);
        try {
            /**
             * @var CreateUploadVideoResponse $res
             */
            $res = $client->createUploadVideoWithOptions($createUploadVideoRequest, $runtime);
            return $res->body->toMap();
        }
        catch (Exception $error) {
            if (!($error instanceof TeaError)) {
                throw new CreateUploadVideoRequestFailException($error->getMessage(), 400);
            }

            throw new CreateUploadVideoRequestFailException("创建上传视频失败", 400);
        }
    }

    public function refreshUploadVideoRequest($videoId)
    {
        $client = app('uyson.aliyun.vod');
        $refreshUploadVideoRequest = new RefreshUploadVideoRequest([
            "videoId" => $videoId
        ]);
        $runtime = new RuntimeOptions([]);
        try {
            /**
             * @var RefreshUploadVideoResponse $res
             */
            $res = $client->refreshUploadVideoWithOptions($refreshUploadVideoRequest, $runtime);
            return $res->body->toMap();
        }
        catch (Exception $error) {
            if (!($error instanceof TeaError)) {
                throw new RefreshUploadVideoRequestFailException($error->getMessage(), 400);
            }
            throw new RefreshUploadVideoRequestFailException("更新上传凭证失败", 400);
        }
    }

    public function getPlayAuth(string $videoId, int $authInfoTimeout = 100)
    {
        $client = app('uyson.aliyun.vod');
        $getVideoPlayAuthRequest = new GetVideoPlayAuthRequest([
            'videoId' => $videoId,
            'authInfoTimeout' => $authInfoTimeout,
        ]);
        $runtime = new RuntimeOptions([]);
        try {
            /**
             * @var GetVideoPlayAuthResponse $res
             */
            $res = $client->getVideoPlayAuthWithOptions($getVideoPlayAuthRequest, $runtime);
            return $res->body->playAuth;
        }
        catch (Exception $error) {
            if (!($error instanceof TeaError)) {
                $error = new TeaError([], $error->getMessage(), $error->getCode(), $error);
                throw new GetPlayAuthFailException($error->getMessage(), 400);
            }
            Log::error('getvideoplayauthrequest-fail', [ 'exception' => $error ]);
            throw new GetPlayAuthFailException('获取播放凭证失败', 400);
        }
    }

    /**
     * @param string $videoId
     * @return \AlibabaCloud\SDK\Vod\V20170321\Models\GetPlayInfoResponseBody
     * @throws GetPlayInfoFailException
     * @throws DdExceptionAlias
     */
    private function getPlayInfo(string $videoId)
    {
        $client = app('uyson.aliyun.vod');
        $getPlayInfoRequest = new GetPlayInfoRequest([
            "videoId" => $videoId
        ]);
        $runtime = new RuntimeOptions([]);
        try {
            /**
             * @var GetPlayInfoResponse $res
             */
            $res = $client->getPlayInfoWithOptions($getPlayInfoRequest, $runtime);
            return $res->body;
        }
        catch (Exception $error) {
            Log::error('getvideoplayauthrequest-fail', [ 'exception' => $error ]);
//            if (!($error instanceof TeaError)) {
//                $error = new TeaError([], $error->getMessage(), $error->getCode(), $error);
//                throw new GetPlayInfoFailException($error->getMessage(), 400);
//            }
            throw new GetPlayInfoFailException('获取播放地地址失败', 400);
        }
    }

    /**
     * @param string $videoId
     * @return mixed
     * @throws GetPlayInfoFailException
     * @throws DdExceptionAlias
     */
    public function updatePlayInfo(string $videoId) {
        $playInfo = $this->getPlayInfo($videoId);
        $videoData = [
            'id' => $playInfo->videoBase->videoId,
            'title' => $playInfo->videoBase->title,
            'status' => $playInfo->videoBase->status,
            'media_type' => $playInfo->videoBase->mediaType,
            'duration' => $playInfo->videoBase->duration,
            'play_info_list' => $playInfo->playInfoList->playInfo,
        ];
        try {
            return $video = Video::upsert($videoData, ['id']);
        } catch (\Exception $e) {
            throw new GetPlayInfoFailException('获取播放地地址失败', 400);
        }
    }

    /**
     * @param string $videoId
     * @param int $uid
     * @param $preview
     * @param Carbon|null $expiresAt
     * @return array
     * @throws GetPlayInfoFailException
     */
    public function getPlayUrl(string $videoId, int $uid, $preview = 300,Carbon $expiresAt = null) {
        if ($expiresAt == null) {
            $expiresAt = now()->addHour(DcatAliyunVodServiceProvider::setting('valid_period') ?? 4);
        }
        $key = DcatAliyunVodServiceProvider::setting('primary_key');
        /**
         * @var Video $video
         */
        $video = Video::find($videoId);
        if (!$video) {
            $video = $this->updatePlayInfo($videoId);
        }
       $data = [];
        foreach ($video->play_info_list as $playInfo) {
            /**
             * @var playInfo $playInfo
             */

            if ($playInfo->definition == 'AUTO') continue ;
            $data[$playInfo->definition] = $this->urlAuth($uid, $playInfo->playURL, $key, $preview, $expiresAt);
        }

        return $data;
    }

    /**
     * @param int $uid
     * @param string $url
     * @param string $key
     * @param int $preview
     * @param Carbon $expiresAt
     * @return string
     */
    private function urlAuth(int $uid, string $url, string $key,  int $preview, Carbon $expiresAt)
    {
        $parse = parse_url($url);
        $baseUrl = sprintf("%s://%s%s", $parse['scheme'], $parse['host'], $parse['path']);
        $url = $baseUrl . '?auth_key=' . $this->getAuthKey($uid, $parse['path'], $key, $expiresAt, $preview);
        if ($preview) {
            $url .= '&end=' . $preview;
        }
        return $url;
    }

    /**
     * @param int $uid
     * @param string $path
     * @param string $key
     * @param Carbon $expiresAt
     * @param int $previewTime
     * @return string
     */
    private function getAuthKey(int $uid, string $path, string $key, Carbon $expiresAt, int $previewTime)
    {
        $uid = $uid;
        $rand = mt_rand(1000, 9999);
        if(empty($key)) {
            return '';
        }
        $authStr = $expiresAt->timestamp . '-' . $rand . '-' . $uid;
        $md5Str = $path . '-' . $authStr . '-' . $key;
        if ($previewTime!=0) {
            $md5Str = $md5Str . '-' . $previewTime;
        }
        $authKey = $authStr . '-' . md5($md5Str);
        return $authKey;
    }
}
