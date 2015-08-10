<?php

use diversen\conf;
use diversen\db\q;
use diversen\file;
use diversen\html;
use diversen\html\helpers;
use diversen\lang;


class locales_db extends locales {
    
    /**
     * returns all languages in db where language_all has been loaded
     * @return type
     */
    public function getLanguageAllDb () {
        $rows = q::select('language')->
                filter('module_name =', 'language_all')->
                fetch();
        return $rows;
    }
    
    public function getLanguageSingleDb ($language) {
        $row = q::select('language')->
                filter('module_name =', 'language_all')->condition('AND')->
                filter('language =', $language)->
                fetchSingle();
        return $row;
    }
    
    public function getLanguageSingleModDb ($language) {
        $row = q::select('language')->
                filter('module_name =', 'language_all_mod')->condition('AND')->
                filter('language =', $language)->
                fetchSingle();
        return $row;
    }
    
    /**
     * displays reload language form
     */
    public function reloadForm () {
        echo helpers::confirmForm(
                lang::translate('Load language all into DB'), 
                lang::translate('submit'), 
                'load_all'
                );
    }

}
