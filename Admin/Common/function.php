<?php
/**
 * 秒->年天时分秒
 */
function Sec2Days($time){
    if(is_numeric($time)){
        $value = array(
          "years" => 0, "days" => 0, "hours" => 0,
          "minutes" => 0, "seconds" => 0,
        );
        if($time >= 31556926){
          $value["years"] = floor($time/31556926);
          $time = ($time%31556926);
        }
        if($time >= 86400){
          $value["days"] = floor($time/86400);
          $time = ($time%86400);
        }
        if($time >= 3600){
          $value["hours"] = floor($time/3600);
          $time = ($time%3600);
        }
        if($time >= 60){
          $value["minutes"] = floor($time/60);
          $time = ($time%60);
        }
        $value["seconds"] = floor($time);

        $t = null;
        if ($value["years"]) $t .= $value["years"].'年';
        $t .= $value["days"].'天';

        return $t;
    } else {
        return false;
    }
 }