<?php

namespace Uyson\DcatAdmin\AliyunVod\Vod;

use AlibabaCloud\SDK\Vod\V20170321\Models\GetPlayInfoResponseBody\playInfoList\playInfo;
use Carbon\Carbon;
use Illuminate\Foundation\Application;
use Illuminate\Support\Arr;
use Uyson\DcatAdmin\AliyunVod\Models\Video;
use Uyson\DcatAdmin\AliyunVod\Repositories\VideoRepository;

class VodManager
{

    /**
     * The application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected \Illuminate\Contracts\Foundation\Application $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function getPlayInfo(string $videoId, int $uid, int $preview = 300, Carbon $expiresAt = null)
    {
        $repo = new VideoRepository();
        return $repo->getPlayUrl($videoId, $uid, $preview, $expiresAt);
    }

}
