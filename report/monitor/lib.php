<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Libs, public API.
 *
 * @package    report_monitor
 * @copyright  2014 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * This function extends the navigation with the report items
 *
 * @global stdClass $CFG
 * @global core_renderer $OUTPUT
 * @param navigation_node $navigation The navigation node to extend
 * @param stdClass        $course     The course to object for the report
 * @param context         $context    The context of the course
 */
function report_monitor_extend_navigation_course($navigation, $course, $context) {
    $node = navigation_node::create(get_string('pluginname', 'report_monitor'), null, navigation_node::TYPE_SETTING, null, null,
            new pix_icon('i/report', ''));

    if (has_capability('report/monitor:subscribe', $context)) {
        $url = new moodle_url('/report/monitor/index.php', array('id' => $course->id));
        $subsnode = navigation_node::create(get_string('managesubscriptions', 'report_monitor'), $url,
                navigation_node::TYPE_SETTING, null, null, new pix_icon('i/settings', ''));
    }

    if (has_capability('report/monitor:managerules', $context)) {
        $url = new moodle_url('/report/monitor/managerules.php', array('id' => $course->id));
        $settingsnode = navigation_node::create(get_string('managerules', 'report_monitor'), $url, navigation_node::TYPE_SETTING,
            null, null, new pix_icon('i/settings', ''));
    }

    if (isset($subsnode) || isset($settingsnode)) {
        // Add the node only if there are sub pages.
        $node = $navigation->add_node($node);

        // Our navigation lib can not handle nodes that have active child, so we need to always add parent first without
        // children.
        if ($subsnode) {
            $node->add_node($subsnode);
        }

        if ($settingsnode) {
            $node->add_node($settingsnode);
        }
    }
}
