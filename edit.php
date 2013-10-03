<?php

if (!session::checkAccessControl('locales_allow')){
    return;
}

$template = config::getModuleIni('locales_language_all_template');
$l = new locales_db();
$loaded = $l->getLanguageAllDb();

    
$headline = lang::translate('Reload language_all files from <span class="notranslate">{template}</span> into DB', array ('template' => $template));
html::headline($headline);

$l->reloadForm();
if (isset($_POST['load_all'])) {
    $l->saveLanguageAllDb();
    http::locationHeader('/locales/edit', lang::translate('Languages has been reloaded'));
}

if (!empty($loaded)) {
    $l->editLanguageLinks();
}

$edit = uri::fragment(2);

if (isset($_POST['update_lang'])) {
    $org =  $l->getLanguageSingleDb($_POST['lang']);
    $org = unserialize($org['translation']);

    // decode both keys and values
    $i = 0;
    $ary  = array ();

    foreach ($org as $key => $val) {
        $post_val = $_POST['input_key'][$i];
        if ($post_val != $val) {
            log::error("$post_val\n$val\n");
            $ary[$key] = $post_val;
        } else {
            $ary[$key] = $val;
        }
        $i++;
    }
    
    // save just diff
    $diff = array_diff($ary, $org);

    $l->saveLanguageAllModsDb($_POST['lang'], $diff);
    http::locationHeader("/locales/edit/1/$_POST[lang]", lang::translate('DB translation was updated'));
}


if ($edit == 1) {
    
    $edit_lang = uri::fragment(3);
    $lang = $l->getOrgAndModLanguage($edit_lang);
    if (!$lang) { 
        http::locationHeader ("/locales/edit", lang::translate('No language has been loaded. Presss button to load'));
    }

    $l->displayEditLanguage($edit_lang, $lang);
}



