<?php

namespace Uyson\DcatAdmin\AliyunVod\Http\Controllers;

use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;
use Illuminate\Routing\Controller;

class DcatAliyunVodController extends Controller
{
    public function index(Content $content)
    {
        return $content
            ->title('Title')
            ->description('Description')
            ->body(Admin::view('uyson.dcat-aliyun-vod::index'));
    }
}