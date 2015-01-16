<?php
ini_set('display_errors','1');

require_once('../../config.php');
require_once($CFG->dirroot . '/report/graphic/lib/gcharts.php');

global $OUTPUT, $PAGE, $DB;

$actionurl = new moodle_url('/report/graphic/course.php');

$courseid = optional_param('courseid', 8, 'int');
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url('/report/graphic/course.php', array('courseid' =>$courseid));
$PAGE->set_title('Moodle Graphic Reports');
$PAGE->set_heading('Moodle Graphic Reports');



/////////////////////////////////////////////////////////////
//if ($CFG->version >= 2014051200) { // Moodle 2.7 and higher
//    $logmanger = get_log_manager();
//    $readers = $logmanger->get_readers('\core\log\sql_select_reader');
//    $reader = reset($readers);
//    $params = array('component' => 'core',
//        'eventname' => '\core\event\user_loggedin',
//        'guestid' => $CFG->siteguest,
//        'timestart' => $now - 30 * DAYSECS);
//    $select = "component = :component AND eventname = :eventname AND userid <> :guestid AND timecreated >= :timestart";
//    $rs = $reader->get_events_select($select, $params, 'timecreated DESC', 0, 0);
//
//    foreach ($rs as $record) {
//        foreach (array_reverse($lastmonth, true) as $timestamp => $loggedin) {
//            $date = usergetdate($timestamp);
//            if ($record->timecreated >= $timestamp) {
//                $lastmonth[$timestamp][$record->userid] = true;
//                break;
//            }
//        }
//    }
//
//}
/////////////////////////////////////////////////////////////////





$sql = "SELECT l.eventname, COUNT(*) as quant
        FROM mdl_logstore_standard_log l
        WHERE l.courseid = ".$courseid."
        GROUP BY l.eventname
        ORDER BY quant DESC";
$data = $DB->get_records_sql($sql);

$i = 1;
$eventdataset[0] = array('Event', 'Percentage');
foreach ($data as $eventdata) {
    // Retrieve event name.
    $event = $eventdata->eventname;
    $eventname = $event::get_name();

    $eventdataset[$i] = array($eventname, (int)$eventdata->quant);
    $i++;
}

$sql = "SELECT l.relateduserid, u.firstname, u.lastname, COUNT(*) as quant
        FROM mdl_logstore_standard_log l
        INNER JOIN mdl_user u ON u.id = l.relateduserid
        WHERE l.courseid = ".$courseid."
        GROUP BY l.relateduserid, u.firstname, u.lastname
        ORDER BY quant DESC";
$datauser = $DB->get_records_sql($sql);


$activeuserdataset[0] = array('User', 'Percentage');

$i = 1;
$inactiveusers[0] = array('User', 'Percentage');
foreach ($datauser as $userdata) {
    if ($i <= 10) {
        $activeuserdataset[$i] = array($userdata->firstname, (int)$userdata->quant);
    } else {
        $inactiveusers[$i] = array($userdata->firstname, (int)$userdata->quant);
    }
    $i++;
}
$inactiveusers = array_reverse($inactiveusers);
$inactiveusers[0] = array('User', 'Percentage');



$sql = "SELECT * FROM mdl_logstore_standard_log WHERE courseid =   ".$courseid;
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

//print_object($usersdataevents);
echo $OUTPUT->header();

// User Activity Pie Chart.
$gcharts = new Gcharts();
$gcharts->load(array('graphic_type' => 'PieChart'));
$gcharts->set_options(array('title' => 'Top 10 most active users'));
echo $gcharts->generate($activeuserdataset);

// Most triggered events.
$gcharts = new Gcharts();
$gcharts->load(array('graphic_type' => 'ColumnChart', 'yAxis' => array('title' => "Name", 'titleTextStyle' => array('color' => 'red'))));
$gcharts->set_options(array('title' => 'Most triggered events'));
echo $gcharts->generate($eventdataset);

$gcharts = new Gcharts();
$gcharts->load(array('graphic_type' => 'linechart'));
$gcharts->set_options(array('title' => 'Monthly', 'curveType'=> 'function'));

$array_test = array(array('Mes', 'Eric', 'Kyle', 'Stan', 'Kenny'),
    array('Dec 1',25, 40,35,24),
    array('Dec 5',14,34, 27,15),
    array('Dec 15',10, 26, 13, 29),
    array('Dec 20',5, 35, 10, 0),
    array('Dec 25',10, 55, 20, 25),
    array('Dec 30',0,10, 10,5),
    array('Jan 5',10,30, 35,30),
    array('Jan 10',5,10, 40,30),
    array('Jan 15',20,15, 15,10));
print_object($array_test);
echo $gcharts->generate($array_test);
//echo $gcharts->generate($usersdataevents);
echo $OUTPUT->footer();