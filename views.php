<?php

class locales_views {
    public static function timezoneInfo () {      
        $str = lang::translate('Current date and time according to setup');
        $str.= "<br />";
        $str.= strftime(config::getMainIni('date_format_long'));
        return $str;
    }
}