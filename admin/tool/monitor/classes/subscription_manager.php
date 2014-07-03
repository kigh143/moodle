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
 * @package    tool_monitor
 * @copyright  2014 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_monitor;

defined('MOODLE_INTERNAL') || die();

/**
 * Class to manage subscriptions.
 *
 * @since      Moodle 2.8
 * @package    tool_monitor
 * @copyright  2014 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class subscription_manager {
    /**
     * Subscribe a user to a given rule.
     *
     * @param int $ruleid  Rule id.
     * @param int $courseid Course id.
     * @param int $cmid Course module id.
     * @param int $userid User who is subscribing, defaults to $USER.
     *
     * @return bool|int returns id of the created subscription.
     */
    public static function create_subscription($ruleid, $courseid, $cmid, $userid = 0) {
        global $DB, $USER;

        $subscription = new \stdClass();
        $subscription->ruleid = $ruleid;
        $subscription->courseid = $courseid;
        $subscription->cmid = $cmid;
        $subscription->userid = empty($userid) ? $USER->id : $userid;
        $subscription->timecreated = time();

        return $DB->insert_record('tool_monitor_subscriptions', $subscription);
    }

    /**
     * Delete a subscription.
     *
     * @param subscription|int $subscriptionorid an instance of subscription class or id.
     * @param bool $checkuser Check if the subscription belongs to current user before deleting.
     *
     * @return bool
     * @throws \coding_exception if $checkuser is true and the subscription doesn't belong to the current user.
     */
    public static function delete_subscription($subscriptionorid, $checkuser = true) {
        global $DB, $USER;
        if (is_object($subscriptionorid)) {
            $subscription = $subscriptionorid->id;
        } else {
            $subscription = self::get_subscription($subscriptionorid);
        }
        if ($checkuser && $subscription->userid != $USER->id) {
            throw new \coding_exception('Invalid subscription supplied');
        }
        return $DB->delete_records('tool_monitor_subscriptions', array('id' => $subscription->id));
    }

    /**
     * Delete all subscribers for a given rule.
     *
     * @param int $ruleid rule id.
     *
     * @return bool
     */
    public static function remove_all_subscriptions_for_rule($ruleid) {
        global $DB;
        return $DB->delete_records('tool_monitor_subscriptions', array('ruleid' => $ruleid));
    }

    /**
     * Get a subscription instance for an given subscription id.
     *
     * @param int $subscriptionid Id of the subscription to fetch.
     *
     * @return subscription, returns a instance of subscription class.
     */
    public static function get_subscription($subscriptionid) {
        global $DB;
        $sql = self::get_subscription_join_rule_sql();
        $sql .= "WHERE s.id = :id";
        $sub = $DB->get_record_sql($sql, array('id' => $subscriptionid), MUST_EXIST);
        return new subscription($sub);
    }

    /**
     * Get an array of subscriptions for a given user in a given course.
     *
     * @param int $courseid course id.
     * @param int $userid Id of the user for which the subscription needs to be fetched. Defaults to $USER;
     *
     * @return array list of subscriptions
     */
    public static function get_user_subscriptions_for_course($courseid, $userid = 0) {
        global $DB, $USER;
        if ($userid == 0) {
            $userid = $USER->id;
        }
        $sql = self::get_subscription_join_rule_sql();
        $sql .= "WHERE s.courseid = :courseid AND s.userid = :userid";

        return $DB->get_records_sql($sql, array('courseid' => $courseid, 'userid' => $userid));
    }

    /**
     * Return a list of subscriptions for a given event.
     *
     * @param \core\event\base $event the event object.
     *
     * @return array
     */
    public static function get_subscriptions_by_event(\core\event\base $event) {
        global $DB;

        $sql = self::get_subscription_join_rule_sql();
        if ($event->contextlevel == CONTEXT_MODULE && $event->contextinstanceid != 0) {
            $sql .= "WHERE r.eventname = :eventname AND s.courseid = :courseid AND (s.cmid = :cmid OR s.cmid = 0)";
        } else {
            $sql .= "WHERE r.eventname = :eventname AND s.courseid = :courseid";
        }

        $params = array('eventname' => $event->eventname, 'courseid' => $event->courseid, 'cmid' => $event->contextinstanceid);

        return $DB->get_records_sql($sql, $params);
    }

    /**
     * Return sql to join rule and subscription table.
     *
     * @return string the sql.
     */
    public static function get_subscription_join_rule_sql() {
        $sql = "SELECT s.*, r.description, r.name, r.userid as ruleuserid, r.courseid as rulecourseid, r.plugin,
                    r.eventname, r.message_template, r.frequency, r.timewindow
                  FROM {tool_monitor_rules} r
                  JOIN {tool_monitor_subscriptions} s
                        ON r.id = s.ruleid ";
        return $sql;
    }
}
