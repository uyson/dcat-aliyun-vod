<?php

namespace Uyson\DcatAdmin\AliyunVod\Support\Facades;


use Illuminate\Support\Facades\Facade;

class Vod extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'vod';
    }
}
