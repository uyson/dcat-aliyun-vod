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
        $this->text('userId')
            ->help('必填，您可以使用阿里云账号访问账号中心（https://account.console.aliyun.com/），即可查看账号ID')
            ->required();
        $this->text('accessKeyId')->required();
        $this->text('accessKeySecret')->required();
        $this->text('region')
            ->default('cn-shanghai')
            ->help('上传到视频点播的地域，默认值为cn-shanghai')
            ->required();
    }
}
