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
 * Graphic report
 *
 * @package    report_graphic
 * @copyright  2014 onwards Simey Lameze <lameze@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace report_graphic;

defined('MOODLE_INTERNAL') || die();

/**
 * Graphic report class.
 *
 * @package    report_graphic
 * @copyright  2015 onwards Simey Lameze <lameze@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class graphic_report extends Gcharts {

    /** @var \core\log\sql_select_reader instance */
    protected $logreader;

    /** @var  Log table name */
    protected $logtable;

    /**
     * Graphic report constructor.
     *
     * Retrieve events log data to be used by other methods.
     *
     * @param \core\log\sql_select_reader $logreader db log reader.
     * @param int $courseid course id.
     */
    public function __construct(\core\log\sql_select_reader $logreader, $courseid) {

        $this->logreader = $logreader;
        $this->courseid = $courseid;
        $this->logtable = $this->logreader->get_internal_log_table_name();
    }

    public function get_most_triggered() {
//        global $CFG;
//
//        require_once($CFG->dirroot.'/report/eventlist/classes/list_generator.php');
//        $eventlist = \report_eventlist_list_generator::get_all_events_list($detail = false);
//        //print_object($eventlist);
//        $selectwhere = "Courseid = ? AND eventname = ?";
//        $params = array(0 => $this->courseid);
//        $sort = "Eventname DESC";
//        $i = 1;
//        $eventdataset[0] = array('Event', 'Percentage');
//        foreach ($eventlist as $event => $description) {
//            $eventname = $event::get_name();
//            $params[1] = $event;
//            $eventscount = $this->logreader->get_events_select_count($selectwhere, $params);
//            $eventdataset[$i] = array($eventname, (int)$eventscount);
//            $i++;
//        }
//        asort($eventdataset);
//       print_object($eventdataset);
//        echo $eventscount = $this->logreader->get_events_select_count($selectwhere, $params);
///       $result = $this->logreader->get_events_select($selectwhere, $params, $sort, 0, 1000);
///       $result = $this->logreader->get_events_select($selectwhere, $params, $sort, 0, 1000);
//        print_object($result);
//
//        $i = 1;
//        $eventdataset[0] = array('Event', 'Percentage');
//        foreach ($result as $eventdata) {
//            // Retrieve event name.
//            $event = $eventdata->eventname;
//            $eventname = $event::get_name();
//            $params[1] = $
//            $eventscount = $this->logreader->get_events_select_count($selectwhere, $params);
//            $eventdataset[$i] = array($eventname, (int)$eventdata->quant);
//            $i++;
//        }

//        $this->load(array('graphic_type' => 'ColumnChart', 'yAxis' => array('title' => "Name", 'titleTextStyle' => array('color' => 'red'))));
//        $this->set_options(array('title' => 'Most triggered events'));
//        return $this->generate($eventdataset);
    }

    /**
     * Get most triggered events by course id.
     *
     * @return charts data.
     */
    public function get_most_triggered_events() {
        global $DB;

        $sql = "SELECT l.eventname, COUNT(*) as quant
                FROM {".$this->logtable."} l
                WHERE l.courseid = ".$this->courseid."
                GROUP BY l.eventname
                ORDER BY quant DESC";
        $data = $DB->get_records_sql($sql);

        // Graphic header, must be the first element of the array.
        $eventdataset[0] = array('Event', 'Quantity');

        $i = 1;
        foreach ($data as $eventdata) {
            $event = $eventdata->eventname;
            $eventdataset[$i] = array($event::get_name(), (int)$eventdata->quant);

            $i++;
        }

        $this->load(array('graphic_type' => 'ColumnChart', 'yAxis' => array('title' => "Name", 'titleTextStyle' => array('color' => 'red'))));
        $this->set_options(array('title' => 'Most triggered events'));

        return $this->generate($eventdataset);
    }

    /**
     * Get users that most triggered events by course id.
     *
     * @return charts data.
     */
    public function get_most_active_users() {
        global $DB;
        $sql = "SELECT l.relateduserid, u.firstname, u.lastname, COUNT(*) as quant
                FROM {".$this->logtable."} l
                INNER JOIN {user} u ON u.id = l.relateduserid
                WHERE l.courseid = ".$this->courseid."
                GROUP BY l.relateduserid, u.firstname, u.lastname
                ORDER BY quant DESC";
        $datauser = $DB->get_records_sql($sql);

        // Graphic header, must be the first element of the array.
        $activeuserdataset[0] = array('User', 'Percentage');

        $i = 1;
        foreach ($datauser as $userdata) {
            $username = $userdata->firstname . ' ' .$userdata->lastname;
            $activeuserdataset[$i] = array($username, (int)$userdata->quant);

            $i++;
        }

        $gcharts = new Gcharts();
        $gcharts->load(array('graphic_type' => 'PieChart'));
        $gcharts->set_options(array('title' => 'Top 10 most active users'));

        return $gcharts->generate($activeuserdataset);
    }

    public function get_monthly_user_activity() {
        global $DB;
        $sql = "SELECT * FROM {logstore_standard_log} WHERE courseid =   ".$this->courseid;
        $dataevents = $DB->get_records_sql($sql);
        //print_object($dataevents);
        $usersdataevents[0] = array(0 => 'Day', 1, 2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18);

        foreach ($dataevents as $value) {
            $monthyear = date('dmy', $value->timecreated);
            $userid = (int)$value->relateduserid;

            if (@count($usersdataevents[$userid]) == 0) {
                @$usersdataevents[$userid] = array_fill_keys(range(0, 18), 0);
            }

            $usersdataevents[$userid][0] = (int)$monthyear;

            if ($userid) {
                @$usersdataevents[$userid][$userid] = (int)$usersdataevents[$monthyear][$userid] + 1;
            }

        }
    }
    public function testing() {

        $gcharts = new Gcharts();
        $gcharts->load(array('graphic_type' => 'linechart'));
        $gcharts->set_options(array('title' => 'Monthly', 'curveType'=> 'function'));

        $array_test = array(
            array('Mes', 'Eric', 'Kyle', 'Stan', 'Kenny'),
            array('Dec 1',25, 40,35,24),
            array('Dec 5',14,34, 27,15),
            array('Dec 15',10, 26, 13, 29),
            array('Dec 20',5, 35, 10, 0),
            array('Dec 25',10, 55, 20, 25),
            array('Dec 30',0,10, 10,5),
            array('Jan 5',10,30, 35,30),
            array('Jan 10',5,10, 40,30),
            array('Jan 15',20,15, 15,10)

        );

        return $gcharts->generate($array_test);
    }
} 