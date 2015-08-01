<?php


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
        echo html_helpers::confirmForm(
                lang::translate('Load language all into DB'), 
                lang::translate('submit'), 
                'load_all'
                );
    }
    
        /**
     * displays reload language form
     */
    public function replaceForm () {
        $h = new html ();
        $h->formStart();
        $h->legend(lang::translate('Search and replace in all strings'));
        $h->label('search', lang::translate('Search string'));
        $h->text('seach');
        $h->label('replace', lang::translate('Replace string'));
        $h->text('replace');
        $h->submit('search_replace', lang::translate('Search and replace'));
        $h->formEnd();
        echo $h->getStr();
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
            q::replace('language', $values, $search);
            unset($_COS_LANG_MODULE);
        }
    }
    
    public function saveLanguageAllModsDb ($key, $save) {

        $s = serialize($save);
        $values = array ('translation' => $s, 'module_name' => 'language_all_mod', 'language' => $key);
        $search = array ('module_name =' => 'language_all_mod', 'language =' => $key  );
        q::replace('language', $values, $search);
    }
    

    
    /**
     * returns all language_all files from file system connected to ini template
     * @return array $ary e.g. array ('da_DK' => /path/to/templates/template/lang/da_DK/language_all.inc');
     */
    public function getLanguageAllFiles () {
        $template = conf::getModuleIni('locales_language_all_template');
        $lang_path = conf::pathHtdocs() . "/templates/$template/lang/";
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
    
    /**
     * displays a form for editing a language
     * @param string $key
     * @param array $lang
     * @param array $lang_mod
     */
    public function displayEditLanguage ($key, $lang, $lang_mod = null) {
        
        $f = new html ();
        $f->formStart('update_lang');
        $f->hidden('lang', $key);
        $f->legend('Change translation');
        $i = 0;
       
        //echo count($lang);
        $i = 0;
        
        $display_org_lang = conf::getModuleIni('locales_display_translate_language');
        $display = $this->getLanguageSingleDb($display_org_lang);
        $display = unserialize($display['translation']);
        
        foreach ($lang as $key => $val) {
            $display_from =  " '" . html::specialEncode($key) . "' (" . lang::translate('Key') . ")<br />";
            
            //$display_from.= "<hr />\n";
            if (isset($display[$key])) {
                $display_from.= " '" . html::specialEncode($display[$key]) . "'";
            } else {
                $not_translated = lang::translate('No translation in') . " ($display_org_lang)" . ""; 
                $display_from.=$not_translated;
            }
            $display_from.= " (" .lang::translate('Translation in') . " $display_org_lang" . ")";
            $display_from.="<br />";
            $f->addHtml($display_from);
            $f->textareaSmall("input_key[$i]", html::specialEncode($val));
            $i++;
        }
        $f->submit('update_lang', lang::translate('Update'));
        $f->formEnd();
        echo $f->getStr();
    }
    
    /**
     * echos links for doing language edit
     */
    public function editLanguageLinks () {
        $files = $this->getLanguageAllFiles();
        $langs = array_keys($files);
        foreach ($langs as $lang) {
            echo html::createLink("/locales/edit/1/$lang", lang::translate("Edit") . " " . $lang) . "<br />";
        }
    }
    
    /**
     * returns a database language and modifictations if ant
     * @param string $edit_lang
     * @return array $language
     */
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
    
    /**
     * loads single language from db
     * @param string $language
     * @return array $language
     */
    public static function loadLanguageFromDb ($language) {
        $l = new locales_db();
        return $l->getOrgAndModLanguage($language);
    }
}
