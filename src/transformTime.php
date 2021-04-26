<?php


namespace YangJiSen\LaravelExecuted;


trait transformTime
{
    /**
     * @param $time
     * @return string
     */
    protected function transformTime($time)
    {
        if(is_null($time)) return '';

        if($time > 1000) {
            return number_format($time/1000, 2, '.', '').'s';
        }

        return number_format($time, 2, '.', '').'ms';
    }
}
