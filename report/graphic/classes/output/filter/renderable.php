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
 * Graphic report renderer class.
 *
 * @package    report_graphic
 * @copyright  2015 Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace report_graphic\output\filter;
defined('MOODLE_INTERNAL') || die;

/**
 * Graphic report renderable class.
 *
 * @package    report_graphic
 * @copyright  2015 Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderable implements \renderable {

//    public $showcourses;
//    public $course;
//    public $showusers;
//    public $groupid;

    /**
     * Constructor.
     *
     * @param stdClass|int $course (optional) course record or id
     */
    public function __construct() {

//        // Use site course id, if course is empty.
//        if (!empty($course) && is_int($course)) {
//            $course = get_course($course);
//        }
//        $this->course = $course;

    }

    /**
     * Return list of courses to show in selector.
     *
     * @return array list of courses.
     */
    public static function get_course_list() {
        global $DB;

        $courses = array(1 => 'course 1', 2 => 'course 2');
//
//        $sitecontext = context_system::instance();
//        // First check to see if we can override showcourses and showusers.
////        $numcourses = $DB->count_records("course");
////        if ($numcourses < COURSE_MAX_COURSES_PER_DROPDOWN && !$this->showcourses) {
////            $this->showcourses = 1;
////        }
//
//        // Check if course filter should be shown.
//        //if (has_capability('report/log:view', $sitecontext) && $this->showcourses) {
//            if ($courserecords = $DB->get_records("course", null, "fullname", "id,shortname,fullname,category")) {
//                foreach ($courserecords as $course) {
//                    if ($course->id == SITEID) {
//                        $courses[$course->id] = format_string($course->fullname) . ' (' . get_string('site') . ')';
//                    } else {
//                        $courses[$course->id] = format_string(get_course_display_name_for_list($course));
//                    }
//                }
//            }
//            core_collator::asort($courses);
//        //}
        return $courses;
    }

    /**
     * Return list of users.
     *
     * @return array list of users.
     */
    public function get_user_list() {
//        global $CFG, $SITE;
//
//        $courseid = $SITE->id;
//        if (!empty($this->course)) {
//            $courseid = $this->course->id;
//        }
//        $context = context_course::instance($courseid);
//        $limitfrom = empty($this->showusers) ? 0 : '';
//        $limitnum  = empty($this->showusers) ? COURSE_MAX_USERS_PER_DROPDOWN + 1 : '';
//        $courseusers = get_enrolled_users($context, '', $this->groupid, 'u.id, ' . get_all_user_name_fields(true, 'u'),
//            null, $limitfrom, $limitnum);
//
//        if (count($courseusers) < COURSE_MAX_USERS_PER_DROPDOWN && !$this->showusers) {
//            $this->showusers = 1;
//        }
//
//        $users = array();
//        if ($this->showusers) {
//            if ($courseusers) {
//                foreach ($courseusers as $courseuser) {
//                    $users[$courseuser->id] = fullname($courseuser, has_capability('moodle/site:viewfullnames', $context));
//                }
//            }
//            $users[$CFG->siteguest] = get_string('guestuser');
//        }
        $users = array(1 => 'Simey', 2 => 'Lameze');
        return $users;
    }
}