# GradeBook-Integration
This Repository contains step by step instruction on how to integrate gradebook in a mod plugin.

`Remember to replace 'pluginname' by the name of your plugin`

### Step 01: Define Capabilities in db/access.php
Create mod/pluginname/db/access.php:
```php
<?php
defined('MOODLE_INTERNAL') || die();

$capabilities = [
    'mod/pluginname:submit' => [
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => [
            'student' => CAP_ALLOW
        ],
    ],
    'mod/pluginname:grade' => [
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => [
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW
        ],
    ],
];

```
### Step 02: Import gradelib.php in lib file
```php
<?php
defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir . '/gradelib.php');
```

### Step 03: Update the Add instance function GradeBook in lib.php
Here, we have to call pluginname_grade_item_update() when Course Module will be created.
```php
function pluginname_add_instance($data) {
    global $DB;
    $data->timecreated = time();
    $data->id = $DB->insert_record('pluginname', $data);
    pluginname_grade_item_update($data);
    return $data->id;
}
```
- This function adds a new plugin  activity in Moodle.
- $data->timecreated = time(); → Stores the timestamp of creation.
- $DB->insert_record('pluginname', $data); → Inserts the data into the plugin table.
- textsubmission_grade_item_update($data); → Calls another function to set up the grade book entry.
- Returns the new instance ID.

### Step 04: Creating / Updating Grade Item
This function ensures that a grade item exists in the Gradebook when the plugin is used.
```php
function pluginname_grade_item_update($pluginname) {
    $item = [
        'itemname' => $pluginname->name,
        'gradetype' => GRADE_TYPE_VALUE,
        'grademax'  => 100,
        'grademin'  => 0,
    ];
    grade_update('mod/pluginname', $pluginname->course, 'mod', 'pluginname', 
        $pluginname->id, 0, null, $item);
}
```

- It creates or updates a grade entry for the submission activity.
- It defines a grade item ($item) with:
- itemname → Activity name.
- gradetype → Uses numeric grading (GRADE_TYPE_VALUE).
- grademax and grademin → Set the grade range (0–100).
- Calls grade_update() to update Moodle's grade book.

### Updating Student Grades When Needed
Whenever a user submits an activity, update their grade.
```php
function pluginname_update_grades($pluginname, $userid = 0) {
    global $DB;
    require_once($CFG->libdir . '/gradelib.php');

    $grades = [];
    if ($userid) {
        $grades[$userid] = $DB->get_field('pluginname_grade_table', 'grade', ['userid' => $userid]);
    } else {
        $students = $DB->get_records('pluginname_grade_table', ['submission' => $pluginname->id]);
        foreach ($students as $student) {
            $grades[$student->userid] = $student->grade;
        }
    }

    grade_update('mod/pluginname', $pluginname->course, 'mod', 'pluginname', 
        $pluginname->id, 0, $grades);
}

```
This function updates user grades in Moodle's Gradebook for a specific student ($userid) or all students who submitted.

- If $userid is provided, It fetches the grade of that student from pluginname_grade_table.
- If $userid is 0 (default), It retrieves all student grades for the given submission.
- Updates the Moodle grade book using grade_update().
