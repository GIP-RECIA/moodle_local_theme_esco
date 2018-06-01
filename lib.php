<?php
//  This file is part of Moodle - http://moodle.org
//
//  Moodle is free software: you can redistribute it and/or modify
//  it under the terms of the GNU General Public License as published by
//  the Free Software Foundation, either version 3 of the License, or
//  (at your option) any later version.
//
//  Moodle is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//  GNU General Public License for more details.
//
//  You should have received a copy of the GNU General Public License
//  along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

function local_theme_esco_extend_settings_navigation($settingsnav, $context)
{
    global $PAGE;

    // Only let users with the appropriate capability see this settings item.
    if (!is_siteadmin() && !has_capability("local/theme_esco:access", context_system::instance())) {
        return;
    }

    if ($settingnode = $settingsnav->find('root', navigation_node::TYPE_SITE_ADMIN)) {
        $label = get_string('menulink', 'local_theme_esco');
        $url = new moodle_url('/local/theme_esco/manage.php', array('id' => $PAGE->course->id));
        $foonode = navigation_node::create(
            $label,
            $url,
            navigation_node::NODETYPE_LEAF,
            'theme',
            'theme',
            new pix_icon('i/settings', $label)
        );
        if ($PAGE->url->compare($url, URL_MATCH_BASE)) {
            $foonode->make_active();
        }
        $settingnode->add_node($foonode);
    }
}

/**
 * Determine field type based on his name
 * @param $name
 * @return string
 */
function local_theme_esco_field_type($name)
{
    if (in_array($name, array("headerbgimage", "logo", "favicon", "p1", "p2", "p3")) !== false) {
        return "storedfile";
    }
    if (in_array($name, array("p1cap", "p2cap", "p3cap")) !== false) {
        return "htmleditor";
    }
    if (stripos($name, "color") !== false) {
        return "colourpicker";
    }
    if (stripos($name, "enabled") !== false) {
        return "checkbox";
    }
    if (stripos($name, "content") !== false && stripos($name, "footer") !== false) {
        return "htmleditor";
    }
    return "text";
}

/**
 * Calculate default value for the given field
 * @param $name
 * @param $value
 * @return mixed
 */
function local_theme_esco_field_default($name, $value)
{
    if(local_theme_esco_field_type($name) == "storedfile"){
        return $name;
    }
    return $value;
}

/**
 * Calculate fallback for given field
 * @param $name
 * @return null|string
 */
function local_theme_esco_field_callback($name)
{
    if(local_theme_esco_field_type($name) == "storedfile"){
        return "theme_reset_all_caches";
    }
    return null;
}

/**
 * Retrieve the name of the theme based on current_user establishment
 * @param $establishment
 * @return string
 */
function local_theme_esco_theme_name($establishment){
    if(empty($establishment)){
        return "";
    }
    if(is_null($establishment)){
        return "";
    }
    //Retrait des espaces
    $texte = strtolower(str_replace(" ","",$establishment));

    //Conversion en caractères HTML
    $texte = htmlentities($texte, ENT_NOQUOTES, $texte);

    //Remplacement des caratères accentués
    $texte = preg_replace('#&([A-za-z])(?:acute|grave|cedil|circ|orn|ring|slash|th|tilde|uml);#', '\1', $texte);

    //Remplacement des caractères "étranges" (oe, ...)
    $texte = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $texte);

    //Remplacement de tous le reste
    $texte = preg_replace('#&[^;]+;#', '', $texte);

    return $texte;
}

/**
 * Retrieve custom libs for the given theme
 * @param $establishment
 * @return array
 */
function local_theme_esco_get_libs($establishment){
    global $CFG;

    $folders_scan = array("lib","libs");
    $libs = array();

    foreach($folders_scan as $to_scan){
        $folder = $CFG->dirroot . "/theme/$establishment/$to_scan/";
        $files = scandir($folder);
        if($files === false){
            continue;
        }
        foreach($files as $file){
            if(in_array($file, array(".",".."))){
                continue;
            }
            $libs[] = $folder . $file;
        }
    }
    return $libs;

}