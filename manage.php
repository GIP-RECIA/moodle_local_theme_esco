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

/**
 * Edit current user etablissement theme
 *
 * @package    theme_esco
 * @copyright  2018 GIP RECIA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once($CFG->libdir . '/adminlib.php');

require_login(0, false);


require_capability("local/theme_esco:access", context_system::instance());

$PAGE->set_context(context_system::instance());
$PAGE->set_url('/local/theme_esco/manage.php');
$PAGE->set_pagetype('admin-setting-theme');
$PAGE->set_pagelayout('admin');


if (!empty($USER->profile['etablissement'])) {
    $etablissement = strtolower(str_replace(" ","",$USER->profile['etablissement']));
} else {
    $etablissement = "";
}

$configs = $DB->get_records('config_plugins', array("plugin" => "theme_$etablissement"));

if (!empty($configs)) {
    require_once(__DIR__ . '/page_definition.php');

    if ($data = data_submitted() and confirm_sesskey()) {
        $data = ((array)$data);
        $settings = ((array)$admin_page->settings);
        foreach ($settings as $setting) {
            $full_name = $setting->get_full_name();
            $value = $data[$full_name];
            $setting->write_setting($value);

        }

        $configs = $DB->get_records('config_plugins', array("plugin" => "theme_$etablissement"));
    }

}

$PAGE->set_title("theme_" . $etablissement);
$PAGE->set_heading("theme_" . $etablissement);

echo $OUTPUT->header();
echo $OUTPUT->box(get_string('intro', 'local_theme_esco'));


$pageparams = $PAGE->url->params();

if(!empty($configs)){
    $context = [
        'actionurl' => $PAGE->url->out(false),
        'params' => array_map(function ($param) use ($pageparams) {
            return [
                'name' => $param,
                'value' => $pageparams[$param]
            ];
        }, array_keys($pageparams)),
        'sesskey' => sesskey(),
        'return' => "",
        'title' => $admin_page->visiblename,
        'settings' => $admin_page->output_html(),
        'showsave' => $admin_page->show_save()
    ];
    echo $OUTPUT->render_from_template('core_admin/settings', $context);
}else{
    print_error("themenotexisting", "local_theme_esco");
}

$PAGE->requires->yui_module('moodle-core-formchangechecker',
    'M.core_formchangechecker.init',
    array(array(
        'formid' => 'adminsettings'
    ))
);
$PAGE->requires->string_for_js('changesmadereallygoaway', 'moodle');

echo $OUTPUT->footer();