<?php

namespace Uyson\DcatAdmin\AliyunVod;

use Darabonba\OpenApi\Models\Config;
use AlibabaCloud\SDK\Vod\V20170321\Vod;
use Dcat\Admin\Extend\ServiceProvider;

class DcatAliyunVodServiceProvider extends ServiceProvider
{
	protected $js = [
        'js/index.js',
    ];
	protected $css = [
		'css/index.css',
	];

    protected $menu = [
        [
            'title' => 'Aliyun Vod',
            'uri'   => '',
            'icon' => 'fa fa-file-video-o',
        ],
        [
            'parent' => 'Aliyun Vod',
            'title'  => 'Category',
            'uri'    => 'dcat-aliyun-vod/catgories',
        ],
        [
            'parent' => 'Aliyun Vod',
            'title'  => 'Videos',
            'uri'    => 'dcat-aliyun-vod/videos'
        ]
    ];

	public function register()
	{
        require_once __DIR__.'/helpers.php';

		$this->app->bind('uyson.aliyun.vod', function($app) {
            $config = new Config([
                // 必填，您的 AccessKey ID
                "accessKeyId" => self::setting('accessKeyId'),
                // 必填，您的 AccessKey Secret
                "accessKeySecret" => self::setting('accessKeySecret')
            ]);
            // 访问的域名
            $config->endpoint = self::setting('endpoint');
            return new Vod($config);
        });
	}

	public function init()
	{
		parent::init();
	}

	public function settingForm()
	{
		return new Setting($this);
	}
}
