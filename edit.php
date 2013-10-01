<?php

class locales_edit extends locales {
    
    public function getLanguageAll () {
        $rows = db_q::select('language')->filter('module_name =', 'language_all')->fetch();
        return $rows;
    }
    
    public function reloadForm () {
        echo html_helpers::confirmForm(lang::translate('Load language all into DB'), lang::translate('submit'), 'load_all') ;
    }
    
   
    public function load () {
        $langs = $this->getLanguageAllFiles();
        foreach ($langs as $key => $file) {
            include $file;
            
            $s = serialize($_COS_LANG_MODULE);
            $values = array ('translation' => $s, 'module_name' => 'language_all', 'language' => $key);
            $search = array ('module_name =' => 'language_all', 'language =' => $key  );
            db_q::replace('language', $values, $search);

        }
    }
    
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
                //print_r($a);
                //die;
                $ary[$a[1]] = $file;
            }
        }
        return $ary;
    }    
}

$template = config::getModuleIni('locales_language_all_template');
$l = new locales_edit();
$loaded = $l->getLanguageAll();

    
$headline = lang::translate('Reload language_all files from <span class="notranslate">{template}</span> into DB', array ('template' => $template));
html::headline($headline);

$l->reloadForm();


if (isset($_POST['load_all'])) {
    $l->load();
    http::locationHeader('/locales/edit');
    //$l->getLanguageAllFiles();
}
