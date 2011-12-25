<?php

/**
 * File containing file for settings locales
 * @package locales
 */

include_once "intl.php";
include_module('configdb');

/**
 * Class for setting locales
 * @package locales
 */
class locales {  
    
    /**
     * displays a form for setting default timezone
     */
    public static function displaySetTimezone () {
        if (isset($_POST['timezone'])) {
            if (intl::validTimezone($_POST['timezone'])) {
                configdb::set('date_default_timezone', $_POST['timezone'], 'main');
                session::setActionMessage(lang::translate('locales_timezone_updated'));
                header("Location: /locales/index");
                exit;
            } else {
                session::setActionMessage(lang::translate('locales_timezone_not_valid'));
            }
        }

        $dropdown = intl::getTimezones();
        $default = get_main_ini('date_default_timezone');

        html::formStart('timezone');
        html::legend(lang::translate('locales_timezone_legend'));
        html::select('timezone', $dropdown, 'zone', 'id', $default, array(), null);
        html::submit('submit', lang::system('system_submit'));
        html::formEnd();

        echo html::getStr();   
    }
    
    /**
     * displays a form for setting default UTF8 locales
     */
    public static function displaySetLocaleUTF8 () {
        if (isset($_POST['locale'])) {
            if (intl::validLocaleUTF8($_POST['locale'])) {
                configdb::set('locale', $_POST['locale'], 'main');
                session::setActionMessage(lang::translate('locales_locale_updated'));

                header("Location: /locales/index");
                exit;
            } else {
                session::setActionMessage(lang::translate('locales_locale_not_valid'));
            }
        }

        $dropdown = intl::getSystemLocalesUTF8();
        $default = get_main_ini('locale');

        html::formStart('locale');
        html::legend(lang::translate('locales_set_locale_legend'));
        html::select('locale', $dropdown, 'locale', 'id', $default, array(), null);
        html::submit('submit', lang::system('system_submit'));
        html::formEnd();

        echo html::getStr();   
    }
    
    /**
     * method for getting system translations (languages)
     * @return array $rows rows with system languages for populating dropdown
     */
    public static function getLanguages () {
        $db = new db();
        $rows = $db->selectAll('language', array ('DISTINCT(language)'));
        $ary = array();
        foreach ($rows as $key => $val) {
            $ary[] = array ('id' => $val['language'], 'language' => $val['language']);
        }
        return $ary; 
    }
    
    /**
     * method for checking if system translation (language) exists 
     * @param string $language the language to check for
     * @return boolean true if language exists else false
     */
    public static function validLanguage ($language) {
        $langs = self::getLanguages();
        foreach ($langs as $key => $val) {
            if ($val['id'] == $language) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * method for displaying a form for setting system translation (language)
     */
    public static function displaySetLanguage () {
        if (isset($_POST['language'])) {
            if (self::validLanguage($_POST['language'])) {
                // set interface language
                configdb::set('language', $_POST['language'], 'main');
                
                // set html lang ="" attr
                $lang = str_replace('_', '-', $_POST['language']);
                configdb::set('lang', $lang, 'main');
                
                session::setActionMessage(lang::translate('locales_language_updated'));
                header("Location: /locales/index");
                exit;
            } else {
                session::setActionMessage(lang::translate('locales_language_not_valid'));
            }
        }

        $dropdown = self::getLanguages();
        $default = get_main_ini('language');

        html::formStart('language');
        html::legend(lang::translate('locales_set_language_lengend'));
        html::select('language', $dropdown, 'language', 'id', $default, array(), null);
        html::submit('submit', lang::system('system_submit'));
        html::formEnd();

        echo html::getStr();   
    } 
}
