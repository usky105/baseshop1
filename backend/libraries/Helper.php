<?php

namespace backend\libraries;

use Yii;

class Helper 
{
	public static function getTVA($HT)
	{
		return number_format($HT * \Yii::$app->params['TVA'],2,",",".");
	}

	public static function getTTC($HT)
	{
		return number_format($HT * (1 + \Yii::$app->params['TVA']),2,",",".");
	}

	/**
     * 获取指定月份的第一天开始和最后一天结束的时间戳
     *
     * @param int $y 年份 $m 月份
     * @return array(本月开始时间，本月结束时间)
     */
	public static function getFristAndLasttimeline($y = "",$m = ""){
        if($y == "") $y = date("Y");
        if($m == "") $m = date("m");
        $m = sprintf("%02d",intval($m));
        $y = str_pad(intval($y),4,"0",STR_PAD_RIGHT);
         
        $m > 12 || $m < 1 ? $m = 1 : $m = $m;
        $firstday = strtotime($y.$m."01000000");
        $firstdaystr = date("Y-m-01",$firstday);
        $lastday = strtotime(date('Y-m-d 23:59:59', strtotime("$firstdaystr +1 month -1 day")));
        return array("firstday"=>$firstday,"lastday"=>$lastday);
    }

}



