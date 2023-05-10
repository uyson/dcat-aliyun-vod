<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vod_videos', function (Blueprint $table) {
            $table->char('id', 32)->primary();
            $table->string('title')->comment('标题');
            $table->enum('status', \Uyson\DcatAdmin\AliyunVod\Enums\Video\Status::getValues())
                ->comment('状态');
            $table->string('media_type')->comment('视频类型');
            $table->double('duration')->comment('播放时长');
            $table->text('play_info_list')->comment('播放列表');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vod_videos');
    }
};
