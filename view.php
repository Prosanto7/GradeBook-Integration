<?php
require_once('../../config.php');
require_once('lib.php');

$id = required_param('id', PARAM_INT);
$cm = get_coursemodule_from_id('textgrader', $id);
$context = context_module::instance($cm->id);
require_login($cm->course, false, $cm);

$PAGE->set_url('/mod/textgrader/view.php', ['id' => $id]);
$PAGE->set_title(get_string('submission', 'mod_textgrader'));
$PAGE->set_heading(get_string('submission', 'mod_textgrader'));

echo $OUTPUT->header();

if (has_capability('mod/textgrader:submit', $context)) {
    echo '<form method="post">
            <textarea name="submission" rows="5" cols="50"></textarea>
            <input type="submit" value="Submit">
          </form>';
}

if (has_capability('mod/textgrader:grade', $context)) {
    //$students = $DB->get_records('textgrader_submissions', ['submission' => $cm->instance]);

    // echo '<h3>Grade Submissions</h3>';
    // foreach ($students as $student) {
    //     echo "<p><strong>{$student->userid}</strong>: {$student->submission}</p>";
    //     echo '<form method="post">
    //             <input type="number" name="grade" min="0" max="100">
    //             <input type="submit" value="Grade">
    //           </form>';
    // }
}

echo $OUTPUT->footer();
