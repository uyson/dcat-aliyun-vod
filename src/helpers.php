<?php


if (function_exists('second2time')) {
    function second2time(int $seconds) {
        $seconds = round($seconds);
        return sprintf('%02d:%02d:%02d', ($seconds/ 3600),($seconds/ 60 % 60), $seconds% 60);
    }
}
