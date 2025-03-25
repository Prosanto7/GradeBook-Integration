<?php
defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir . '/gradelib.php');
require_once($CFG->libdir . '/filelib.php');  // Moodle's core file


function mod_textgrader_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = []) {
    global $CFG;
    if ($filearea === 'icon') {
        return $CFG->wwwroot . '/mod/textgrader/pix/icon.png';
    }
    return false;
}

function textgrader_add_instance($data) {
    global $DB;
    $data->timecreated = time();
    $data->timemodified = $data->timecreated;

    $data->id = $DB->insert_record('textgrader', $data);
    $data->instance = $data->id;

    textgrader_grade_item_update($data);
    return $data->id;
}

function textgrader_delete_instance($id) {
    global $DB;

    if (!$textgrader = $DB->get_record('textgrader', array('id'=>$id))) {
        return false;
    }

    $cm = get_coursemodule_from_instance('textgrader', $id);
    \core_completion\api::update_completion_date_event($cm->id, 'textgrader', $id, null);

    // note: all context files are deleted automatically

    $DB->delete_records('textgrader', array('id'=>$textgrader->id));

    return true;
}


function textgrader_grade_item_update($textgrader) {
    $item = [
        'itemname' => get_string('pluginname', 'mod_textgrader'),
        'idnumber' => $textgrader->id,
        'gradetype' => GRADE_TYPE_VALUE,
        'grademax'  => 100,
        'grademin'  => 0,
    ];
    return grade_update('mod/textgrader', $textgrader->course, 'mod', 'textgrader', 
        $textgrader->id, 0, null, $item);
}

function textgrader_update_grades($textgrader, $userid = 0) {
    global $DB;

    $grades = [];
    if ($userid) {
        $grades[$userid] = $DB->get_field('textgrader_submissions', 'grade', ['userid' => $userid]);
    } else {
        $students = $DB->get_records('textgrader_submissions', ['submission' => $textgrader->id]);
        foreach ($students as $student) {
            $grades[$student->userid] = $student->grade;
        }
    }

    grade_update('mod/textgrader', $textgrader->course, 'mod', 'textgrader', 
        $textgrader->id, 0, $grades);
}

