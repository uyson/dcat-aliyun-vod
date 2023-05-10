<?php

namespace Uyson\DcatAdmin\AliyunVod\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Support\Arr;
use InvalidArgumentException;

class PlayInfoList implements CastsAttributes
{

    /**
     * Cast the given value.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return mixed
     */
    public function get($model, string $key, $value, array $attributes)
    {
        $value = json_decode($value, true);
        return Arr::map($value, function ($item) {
            return \AlibabaCloud\SDK\Vod\V20170321\Models\GetPlayInfoResponseBody\playInfoList\playInfo::fromMap($item);
        });
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return mixed
     */
    public function set($model, string $key, $value, array $attributes)
    {
        if (!is_array($value)) {
            throw new InvalidArgumentException('The given value is not an playInfo instance.');
        }
        return [ 'play_info_list' => json_encode(Arr::map($value, function ($item) {
            /**
             * @var \AlibabaCloud\SDK\Vod\V20170321\Models\GetPlayInfoResponseBody\playInfoList\playInfo $item
             */
            return $item->toMap();
        }))];
    }

}
