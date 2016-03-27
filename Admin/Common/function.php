<?php
//格式化标准时间
function mkDateTime($time=0, $his=1)
{
    !$time ? $time = TIMESTAMP : null;

    $format = $his ? 'Y-m-d H:i:s' : 'Y-m-d';
    return date($format, $time);
}