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
        $this->text('accessKeyId')->required();
        $this->text('accessKeySecret')->required();
        $this->text('endpoint')->required();
    }
}
