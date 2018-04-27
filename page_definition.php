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

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/adminlib.php');

function local_theme_field_type($name)
{
    if (in_array($name,array("headerbgimage","logo","favicon","p1","p2","p3")) !== false) {
        return "storedfile";
    }
    if (in_array($name,array("p1cap","p2cap","p3cap")) !== false) {
        return "htmleditor";
    }
    if (stripos($name,"color") !== false) {
        return "colourpicker";
    }
    if (stripos($name,"enabled") !== false) {
        return "checkbox";
    }
    return "text";
}

$admin_page = new admin_settingpage("theme_esco", "theme_esco", "local/theme_esco:access");

$slidercount = get_config("theme_$etablissement", 'slidercount');

foreach ($configs as $config) {
    if(preg_match("/(p)[0-9](\cap)?/", $config->name) == 1){
        continue;
    }
    $classname = "admin_setting_config" . local_theme_field_type($config->name);
    $name = $config->plugin . "/" . $config->name;
    $title = get_string($config->name, $config->plugin);
    $description = get_string($config->name . "desc", $config->plugin);
    $setting = new $classname($name, $title, $description, $config->value);
    $admin_page->add($setting);
}

if($slidercount !== false){
    for ($sliderindex = 1; $sliderindex <= $slidercount; $sliderindex++) {
        $fileid = 'p' . $sliderindex;
        $name = 'theme_amboise/p' . $sliderindex;
        $title = get_string('sliderimage', 'theme_' . $etablissement);
        $description = get_string('sliderimagedesc', 'theme_' . $etablissement);
        $setting = new admin_setting_configstoredfile($name, $title, $description, $fileid);
        $admin_page->add($setting);

        $name = 'theme_amboise/p' . $sliderindex . 'url';
        $title = get_string('sliderurl', 'theme_' . $etablissement);
        $description = get_string('sliderurldesc', 'theme_' . $etablissement);
        $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_URL);
        $admin_page->add($setting);

        $name = 'theme_amboise/p' . $sliderindex . 'cap';
        $title = get_string('slidercaption', 'theme_' . $etablissement);
        $description = get_string('slidercaptiondesc', 'theme_' . $etablissement);
        $default = '';
        $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
        $admin_page->add($setting);
    }
}