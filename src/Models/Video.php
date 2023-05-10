<?php

namespace Uyson\DcatAdmin\AliyunVod\Models;

use AlibabaCloud\SDK\Vod\V20170321\Models\GetPlayInfoResponseBody;
use Illuminate\Database\Eloquent\Model;
use Uyson\DcatAdmin\AliyunVod\Casts\PlayInfoList;
use Uyson\DcatAdmin\AliyunVod\Enums\Video\Status;

/**
 * Vod 视频
 *
 * @property string $id
 * @property string $title
 * @property Status $status
 * @property string $media_type
 * @property double $duration
 * @property GetPlayInfoResponseBody\playInfoList\playInfo[] $play_info_list
 */
class Video extends Model
{
    protected $table = 'vod_videos';

    protected $fillable = [
        'id',
        'title',
        'status',
        'media_type',
        'duration',
        'play_info_list',
    ];

    protected $casts = [
        'status' => Status::class,
        'play_info_list' => PlayInfoList::class,
    ];
}
