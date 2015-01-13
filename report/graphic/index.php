<?php
ini_set('display_errors','1');

require('../../config.php');
require_once($CFG->dirroot . '/report/graphic/lib/gcharts.php');

global $OUTPUT, $PAGE, $DB;

$actionurl = new moodle_url('/report/graphic/index.php');
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url('/report/graphic/index.php');
$PAGE->set_title('Moodle Graphic Reports');
$PAGE->set_heading('Moodle Graphic Reports');

// Count events and group by course ID.
$sql = "SELECT l.courseid, c.shortname, COUNT(*)
        FROM mdl_logstore_standard_log l
        INNER JOIN mdl_course c ON c.id = l.courseid
        WHERE l.courseid = c.id
        GROUP BY l.courseid, c.shortname";
$data = $DB->get_records_sql($sql);

// Format the data to google charts.
$i = 1;
$dataset[0] = array('Course', 'Percentage');
foreach ($data as $courseid => $coursedata) {
    $dataset[$i] = array($coursedata->shortname, (int)$coursedata->count);
    $i++;
}

echo $OUTPUT->header();

// Set properties and display the graph report.
$gcharts = new Gcharts();
$gcharts->load(array('graphic_type' => 'PieChart'));
$gcharts->set_options(array('title' => 'Course activity (Events by course)'));
echo $gcharts->generate($dataset);

