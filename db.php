<?php

class locales_db extends locales {
    
    /**
     * returns all languages in db where language_all has been loaded
     * @return type
     */
    public function getLanguageAllDb () {
        $rows = db_q::select('language')->
                filter('module_name =', 'language_all')->
                fetch();
        return $rows;
    }
    
    public function getLanguageSingleDb ($language) {
        $row = db_q::select('language')->
                filter('module_name =', 'language_all')->condition('AND')->
                filter('language =', $language)->
                fetchSingle();
        return $row;
    }
    
    public function getLanguageSingleModDb ($language) {
        $row = db_q::select('language')->
                filter('module_name =', 'language_all_mod')->condition('AND')->
                filter('language =', $language)->
                fetchSingle();
        return $row;
    }
    
    /**
     * displays reload language form
     */
    public function reloadForm () {
        echo html_helpers::confirmForm(
                lang::translate('Load language all into DB'), 
                lang::translate('submit'), 
                'load_all'
                );
    }
    
    /**
     * reloads all language_all from file system to DB
     */
    public function saveLanguageAllDb () {
        $langs = $this->getLanguageAllFiles();
        foreach ($langs as $key => $file) {
            
            include $file;
            
            $s = serialize($_COS_LANG_MODULE);
            $values = array ('translation' => $s, 'module_name' => 'language_all', 'language' => $key);
            $search = array ('module_name =' => 'language_all', 'language =' => $key  );
            db_q::replace('language', $values, $search);
            unset($_COS_LANG_MODULE);
        }
    }
    
    public function saveLanguageAllModsDb ($key, $save) {
            
        $s = serialize($save);
        $values = array ('translation' => $s, 'module_name' => 'language_all_mod', 'language' => $key);
        $search = array ('module_name =' => 'language_all_mod', 'language =' => $key  );
        db_q::replace('language', $values, $search);
    }
    

    
    /**
     * returns all language_all files from file system connected to ini template
     * @return array $ary e.g. array ('da_DK' => /path/to/templates/template/lang/da_DK/language_all.inc');
     */
    public function getLanguageAllFiles () {
        $template = config::getModuleIni('locales_language_all_template');
        $lang_path = _COS_HTDOCS . "/templates/$template/lang/";
        $langs = file::getDirsGlob($lang_path);

        $ary = array ();
        foreach ($langs as $lang) {
            $file = $lang . "/language-all.inc";
            if (file_exists($file)) {
                $a = explode("/", $file);
                $a = array_reverse($a);
                $ary[$a[1]] = $file;
            }
        }
        return $ary;
    }
    
    public function displayEditLanguage ($key, $lang, $lang_mod = null) {
        
        
        
        $f = new html ();
        $f->formStart('update_lang');
        $f->hidden('lang', $key);
        $f->legend('Change translation');
        $i = 0;
       
        echo count($lang);
        $i = 0;
        foreach ($lang as $key => $val) {
            $f->addHtml(html::specialEncode($key) . "<br />");
            $length = strlen($val);
            //$options = array ('size' => $length);
            $f->textareaSmall("input_key[$i]", html::specialEncode($val));
            $i++;
        }
        $f->submit('update_lang', lang::translate('Update'));
        $f->formEnd();
        echo $f->getStr();
    }
    
    public function editLanguageLinks () {
        $files = $this->getLanguageAllFiles();
        $langs = array_keys($files);
        foreach ($langs as $lang) {
            echo html::createLink("/locales/edit/1/$lang", lang::translate("Edit") . " " . $lang) . "<br />";
        }
    }
    
    public function getOrgAndModLanguage ($edit_lang) {
        $org =  $this->getLanguageSingleDb($edit_lang);
        if (empty($org)) { 
            return false;
        }
        
        $lang = unserialize($org['translation']);

        $mod = $this->getLanguageSingleModDb($edit_lang);
        if (isset($mod['translation'])) {
            $mod_lang = unserialize($mod['translation']);
            $lang = array_merge($lang, $mod_lang);
        } 
        return $lang;
    }
    
    public static function loadLanguageFromDb ($language) {
        $l = new locales_db();
        return $l->getOrgAndModLanguage($language);
    }
}