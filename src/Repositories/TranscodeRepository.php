<?php

namespace Uyson\DcatAdmin\AliyunVod\Repositories;

use AlibabaCloud\SDK\Vod\V20170321\Models\ListTranscodeTemplateGroupRequest;
use AlibabaCloud\SDK\Vod\V20170321\Models\ListTranscodeTemplateGroupResponse;
use AlibabaCloud\Tea\Exception\TeaError;
use AlibabaCloud\Tea\Utils\Utils\RuntimeOptions;
use Dcat\Admin\Grid;
use Dcat\Admin\Repositories\Repository;

class TranscodeRepository extends Repository
{
    public function getKeyName()
    {
        return 'TranscodeTemplateGroupId';
    }
    public function getCreatedAtColumn()
    {
        return 'CreationTime';
    }

    public function getUpdatedAtColumn()
    {
        return null;
    }

    public function isSoftDeletes()
    {
        return false;
    }

    public function get(Grid\Model $model = null)
    {
        $client = app('uyson.aliyun.vod');
        $listTranscodeTemplateGroupRequest = new ListTranscodeTemplateGroupRequest([]);
        $runtime = new RuntimeOptions([]);
        try {
            /**
             * @var ListTranscodeTemplateGroupResponse $res
             */
            $res = $client->listTranscodeTemplateGroupWithOptions($listTranscodeTemplateGroupRequest, $runtime);
            return $res->body->toMap()['TranscodeTemplateGroupList'] ?? [];
        }
        catch (Exception $error) {
            if (!($error instanceof TeaError)) {
                // $error = new TeaError([], $error->getMessage(), $error->getCode(), $error);
                admin_error('出现错误', $error->getMessage());
            }
            admin_error('出现错误', '获取转发组失败');
        }
    }

}
