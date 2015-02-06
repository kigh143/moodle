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
 * Graphic report renderer.
 *
 * @package    report_graphic
 * @copyright  2015 Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace report_graphic\output\filter;

defined('MOODLE_INTERNAL') || die;

/**
 * Report log renderer's for printing reports.
 *
 * @package    report_graphic
 * @copyright  2015 Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_graphic_renderer extends \plugin_renderer_base {


    protected function render_graphic_report_filter(report_graphic_renderable $renderable) {
//        if (empty($reportlog->selectedlogreader)) {
//            echo $this->output->notification(get_string('nologreaderenabled', 'report_log'), 'notifyproblem');
//            return;
//        }
//        if ($reportlog->showselectorform) {
            //$this->report_selector_form($courseid);
//        }
//
//        if ($reportlog->showreport) {
//            $reportlog->tablelog->out($reportlog->perpage, true);
//        }
    }



    /**
     * This function is used to generate and display selector form
     *
     * @param report_graphic_renderable $renderable log report.
     */
    public function report_selector_form(report_graphic_renderable $renderable) {
        print_object($renderable);
    }
}

