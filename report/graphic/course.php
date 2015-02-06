<?php
require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot . '/report/graphic/lib/gcharts.php');
require_once($CFG->dirroot . '/report/graphic/classes/graphic_report.php');
require_once($CFG->dirroot . '/report/graphic/classes/output/filter/renderable.php');
global $OUTPUT, $PAGE, $DB;

$courseid = optional_param('courseid', 2, 'int');
if (!$course = $DB->get_record('course', array('id'=>$courseid))) {
    print_error('nocourseid');
}

$actionurl = new moodle_url('/report/graphic/course.php');

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url('/report/graphic/course.php', array('courseid' =>$courseid));
$PAGE->set_title('Moodle Graphic Reports');
$PAGE->set_heading('Moodle Graphic Reports');

echo $OUTPUT->header();

$renderable = new \report_graphic\output\filter\renderable();
//print_object($renderable);
//$output = $PAGE->get_renderer('report_graphic');
//$output->report_selector_form($renderable);


$renderer = $PAGE->get_renderer('report_graphic');
echo $renderer->render($renderable);

$logreader = get_log_manager()->get_readers('\core\log\sql_select_reader');

$logreader = reset($logreader);

$graph_report = new \report_graphic\graphic_report($logreader, $courseid);

// User Activity Pie Chart.
echo $graph_report->get_most_active_users();

// Most triggered events.
echo $graph_report->get_most_triggered_events();

// Monthly user activity.
echo $graph_report->get_monthly_user_activity();

echo $graph_report->testing();

echo $OUTPUT->footer();
