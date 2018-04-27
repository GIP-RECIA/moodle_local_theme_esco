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