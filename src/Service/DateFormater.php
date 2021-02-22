<?php

namespace App\Service;
use DateTime;

class DateFormater
{
    public function todate($dateStr)
    {
        return DateTime::createfromformat('d M Y',$dateStr);
        #return date("Y-m-d", strtotime($dateStr));
    }

    public function tostr($date)
    {       
        return $date->format('d M Y');
        #return date("d M Y", strtotime($date));
    }

}
