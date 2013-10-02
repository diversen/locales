<?php

class locales_edit extends locales {
    
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
    
    public function getLanguageRealDb ($language) {
        $rows = db_q::select('language')->
                filter('module_name =', 'language_all_real')->condition('AND')->
                filter('language =', $language)->
                fetchSingle();
        return $rows;
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
    public function load () {
        $langs = $this->getLanguageAllFiles();
        foreach ($langs as $key => $file) {
            
            include $file;
            
            $s = serialize($_COS_LANG_MODULE);
            //$string = unserialize($s); //print_r($string); die;
            $values = array ('translation' => $s, 'module_name' => 'language_all', 'language' => $key);
            $search = array ('module_name =' => 'language_all', 'language =' => $key  );
            db_q::replace('language', $values, $search);
            unset($_COS_LANG_MODULE);
        }
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
    
    public function displayEditLanguage ($lang) {
        
        $f = new html ();
        $f->formStart('update_lang');
        $f->hidden('lang', $lang);
        $f->legend('Change translation');
        $i = 0;
        
        foreach ($lang as $key => $val) {
            $f->addHtml(html::specialEncode($key) . "<br />");
            $length = strlen($val);
            $options = array ('size' => $length);
            $f->text(html::specialEncode($key), html::specialEncode($val), $options);
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
}

$template = config::getModuleIni('locales_language_all_template');
$l = new locales_edit();
$loaded = $l->getLanguageAllDb();

    
$headline = lang::translate('Reload language_all files from <span class="notranslate">{template}</span> into DB', array ('template' => $template));
html::headline($headline);

$l->reloadForm();
if (isset($_POST['load_all'])) {
    $l->load();
    http::locationHeader('/locales/edit', lang::translate('Languages has been reloaded'));
}

if (!empty($loaded)) {
    $l->editLanguageLinks();
}

$edit = uri::fragment(2);
if ($edit == 1) {
    $edit_lang = uri::fragment(3);
    $org =  $l->getLanguageSingleDb($edit_lang);
    $lang = unserialize($org['translation']);
    $l->displayEditLanguage($lang);
}

if (isset($_POST['update_lang'])) {
    
}

