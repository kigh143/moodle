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
 * Class to manage subscriptions.
 *
 * @package    report_monitor
 * @copyright  2014 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_monitor;

defined('MOODLE_INTERNAL') || die();

/**
 * Class to manage subscriptions.
 *
 * @package    report_monitor
 * @copyright  2014 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class subscription_manager {
    /**
     * Subscribe a user to a given rule.
     *
     * @param $ruleid
     * @param $courseid
     * @param $cmid
     * @param int $userid
     *
     * @return bool|int
     */
    public static function subscribe($ruleid, $courseid, $cmid, $userid = 0) {
        global $DB, $USER;

        if ($userid === 0) {
            $userid = $USER->id;
        }

        $subscription = new \stdClass();
        $subscription->ruleid = $ruleid;
        $subscription->courseid = $courseid;
        $subscription->cmid = $cmid;
        $subscription->userid = $userid;
        $subscription->timecreated = time();
        return $DB->insert_record('report_monitor_subscriptions', $subscription, true);
    }

    /**
     * Un subscribe a user when the subscription id is known.
     *
     * @param $subscriptionorid
     *
     * @return bool
     */
    public static function delete_subscription($subscriptionorid) {
        global $DB;
        if (is_object($subscriptionorid)) {
            $subscriptionid = $subscriptionorid->id;
        } else {
            $subscriptionid = $subscriptionorid;
        }
        return $DB->delete_records('report_monitor_subscriptions', array('id' => $subscriptionid));
    }

    /**
     * @param $ruleid
     *
     * @return bool
     */
    public static function remove_all_subscribers_for_rule($ruleid) {
        global $DB;
        return $DB->delete_records('report_monitor_subscriptions', array('ruleid' => $ruleid));
    }

    /**
     * @param $ruleid
     * @param $courseid
     * @param $cmid
     * @param int $userid
     *
     * @return bool|int
     */
    public static function unsubscribe($ruleid, $courseid, $cmid, $userid = 0) {
        global $DB;
        return $DB->delete_records('report_monitor_subscriptions', array('ruleid' => $ruleid, 'courseid' => $courseid, 'cmid' => $cmid,
                                                                  'userid' => $userid));
    }

    /**
     * @param $subscriptionid
     *
     * @return mixed
     */
    public static function get_subscription($subscriptionid) {
        global $DB;
        return $DB->get_record('report_monitor_subscriptions', array('id' => $subscriptionid), '*', MUST_EXIST);
    }

    /**
     * @param $courseid
     * @param int $userid
     *
     * @return array
     */
    public static function get_user_subscriptions_for_course($courseid, $userid = 0) {
        global $DB, $USER;
        if ($userid == 0) {
            $userid = $USER->id;
        }
        $sql = "SELECT s.*, r.courseid as rulecourseid, r.userid as ruleuserid, r.name, r.event, r.plugin
                  FROM {report_monitor_rules} r
                  JOIN {report_monitor_subscriptions} s
                     ON r.id = s.ruleid
                 WHERE s.courseid = :courseid AND s.userid = :userid";
        return $DB->get_records_sql($sql, array('courseid' => $courseid, 'userid' => $userid));
    }
}