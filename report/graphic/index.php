<?php
//sini_set('display_errors','1');

require_once('../../config.php');
require_once($CFG->dirroot . '/report/graphic/lib/gcharts.php');
require_once($CFG->libdir.'/adminlib.php');
global $OUTPUT, $PAGE, $DB;
admin_externalpage_setup('report_graphic', '', null, '', array('pagelayout' => 'report'));
$actionurl = new moodle_url('/report/graphic/index.php');
$context = context_system::instance();
$PAGE->set_context($context);

$PAGE->set_url('/report/graphic/index.php');
$PAGE->set_title('Moodle Graphic Reports');
$PAGE->set_heading('Moodle Graphic Reports');
//$test = $PAGE->get_renderable('report_graphic');
$PAGE->set_pagelayout('report');

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

if ($courserecords = $DB->get_records("course", null, "fullname", "id,shortname,fullname,category")) {
    foreach ($courserecords as $course) {
        if ($course->id == SITEID) {
            $courses[$course->id] = format_string($course->fullname) . ' (' . get_string('site') . ')';
        } else {
            $courses[$course->id] = format_string(get_course_display_name_for_list($course));
        }
    }
}
core_collator::asort($courses);

echo $OUTPUT->header();
echo '<form action="course.php" method="post">';
echo '<strong>Select a course:&nbsp;</strong>';
echo '<select name="courseid">';
foreach ($courses as $courseid => $coursename) {
    echo '<option value="'.$courseid.'">'.$coursename.'</option>';
}
echo '</select>';
echo '<input type="submit" value="Generate">';
echo '</form>';
// Set properties and display the graph report.
$gcharts = new Gcharts();
$gcharts->load(array('graphic_type' => 'PieChart'));
$gcharts->set_options(array('title' => 'Course activity (Events by course)'));
echo $gcharts->generate($dataset);

