<?php

namespace Uyson\DcatAdmin\AliyunVod;

use Admin;
use Closure;
use Darabonba\OpenApi\Models\Config;
use AlibabaCloud\SDK\Vod\V20170321\Vod;
use Dcat\Admin\Extend\ServiceProvider;
use Uyson\DcatAdmin\AliyunVod\Vod\VodManager;

class DcatAliyunVodServiceProvider extends ServiceProvider
{
	protected $js = [
        'js/vue.min.js',
        'js/index.js',
         'js/lib/es6-promise.min.js',
         'js/lib/aliyun-oss-sdk-6.17.1.min.js',
         'js/aliyun-upload-sdk-1.5.4.min.js',
         'https://g.alicdn.com/de/prismplayer/2.13.2/aliplayer-min.js',

    ];
	protected $css = [
		'css/index.css',
        'https://g.alicdn.com/de/prismplayer/2.13.2/skins/default/aliplayer-min.css',
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

    protected $registered = false;


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
            $config->endpoint = sprintf("vod.%s.aliyuncs.com", self::setting('region'));
            return new Vod($config);
        });


        $this->app->bind('vod', fn($app) => new VodManager($app));

	}

	public function init()
	{
		parent::init();
        Admin::requireAssets('@uyson.dcat-aliyun-vod');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
	}

	public function settingForm()
	{
		return new Setting($this);
	}
}
