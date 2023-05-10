<?php

namespace Uyson\DcatAdmin\AliyunVod;

use Dcat\Admin\Extend\Setting as Form;

class Setting extends Form
{

    public function title()
    {
        return $this->trans('vod.title');
    }

    protected function formatInput(array $input)
    {
        return parent::formatInput($input);
    }

    public function form()
    {
        $this->text('userId', $this->trans('vod.userId'))
            ->help('必填，您可以使用阿里云账号访问账号中心（https://account.console.aliyun.com/），即可查看账号ID')
            ->required();
        $this->text('accessKeyId')->required();
        $this->text('accessKeySecret')->required();
        $this->text('region', $this->trans('vod.region'))
            ->default('cn-shanghai')
            ->help('上传到视频点播的地域，默认值为cn-shanghai')
            ->required();
        $this->divider();
        $this->text('primary_key', $this->trans('vod.primary_key'))
            ->default('')
            ->help("开启<a href='https://help.aliyun.com/document_detail/120987.html?spm=5176.12672711.0.0.33d01fa3vjuQ8q' target='_blank'>URL鉴权</a>功能请填该主KEY");
        $this->number('valid_period', $this->trans('vod.valid_period'))
            ->default(4)
            ->help('鉴权有效期，单位小时');

    }
}
