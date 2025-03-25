<?php
require_once($CFG->dirroot.'/course/moodleform_mod.php');

class mod_textgrader_mod_form extends moodleform_mod {
    function definition() {
        $mform = $this->_form;
        $mform->addElement('header', 'general', 'Info');

        $mform->addElement('text', 'name', get_string('name'), ['size' => '64', 'placeholder'=>'Enter title for this activity']);
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');

        $this->standard_intro_elements();

        $this->standard_coursemodule_elements();
        
        $this->add_action_buttons();
    }
}
