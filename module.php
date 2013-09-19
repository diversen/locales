<?php

/**
 * File containing file for settings locales
 * @package locales
 */

include_once "intl.php";
moduleloader::includeModule('configdb');

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
                session::setActionMessage(lang::translate('Timezone has been updated'));
                header("Location: /locales/index");
                exit;
            } else {
                session::setActionMessage(lang::translate('Timezone is not valid'));
            }
        }

        $dropdown = intl::getTimezones();
        $default = config::getMainIni('date_default_timezone');

        html::formStart('timezone');
        html::legend(lang::translate('Set timezone for your system'));
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
                session::setActionMessage(lang::translate('Locale has been updated'));

                header("Location: /locales/index");
                exit;
            } else {
                session::setActionMessage(lang::translate('Locale is not valid'));
            }
        }

        $dropdown = intl::getSystemLocalesUTF8();
        $default = config::getMainIni('locale');

        html::formStart('locale');
        html::legend(lang::translate('Set locale. Set e.g. dates and money symbols to your language specifics'));
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
                
                session::setActionMessage(lang::translate('Locale has been updated'));
                http::locationHeader('/locales/index');
            } else {
                session::setActionMessage(lang::translate('Language is not valid'));
            }
        }

        $dropdown = self::getLanguages();
        $default = config::getMainIni('language');

        html::formStart('language');
        html::legend(lang::translate('Set language of interface and HTML document'));
        html::select('language', $dropdown, 'language', 'id', $default, array(), null);
        html::submit('submit', lang::system('system_submit'));
        html::formEnd();

        echo html::getStr();   
    } 
    
    public static function displayReloadLang () {
        
        if (isset($_POST['language_reload'])) {
            
            $reload = new moduleinstaller();
            $reload->reloadCosLanguages();
            $reload->reloadLanguages();
            session::setActionMessage(lang::translate('Locale has been updated'));
            http::locationHeader('/locales/index');
        }
        
        html::formStart('language_reload');
        html::legend(lang::translate('Update all language files (may take a few minutes)'));
        //html::select('language', $dropdown, 'language', 'id', $default, array(), null);
        html::submit('language_reload', lang::system('system_submit'));
        html::formEnd();
        
        echo html::getStr();  

    }
}
