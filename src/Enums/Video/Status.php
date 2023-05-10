<?php

namespace Uyson\DcatAdmin\AliyunVod\Enums\Video;

use Illuminate\Support\Arr;

enum Status: string
{
    /**
     * 上传中
     */
    case Uploading = 'Uploading';

    /**
     * 上传失败
     */
    case UploadFail = 'UploadFail';

    /**
     * 完成
     */
    case UploadSucc = 'UploadSucc';

    /**
     * 转码中
     */
    case Transcoding = 'Transcoding';

    /**
     * 转码失败
     */
    case TranscodeFail = 'TranscodeFail';

    /**
     * 审核中
     */
    case Checking = 'Checking';

    /**
     * 屏蔽
     */
    case Blocked = 'Blocked';

    /**
     * 正常
     */
    case Normal = 'Normal';

    /**
     * 合成失败
     */
    case ProduceFail = 'ProduceFail';

    /**
     * @return string
     */
    public static function getValues(): array
    {
        return Arr::map(self::cases(), fn(Status $status) => $status->value);
    }
}
