<?php

require_once('../../config.php');
require_once("$CFG->libdir/formslib.php");

class mojreport_form extends moodleform {

    public function definition() {
        global $DB;
        global $CFG;
        $mform = & $this->_form;
        
		$options = array();
        $options[0] = 'Biraj:';
        $options += $this->_customdata['courses']; 
        $mform->addElement('select', 'course', "Kolegij:", $options, 'align="right"');
        $mform->setType('course', PARAM_ALPHANUMEXT);
        
		
        
        $mform->addElement('submit', 'save', 'Prikaži', get_string('report_mojreport'), 'align="right"');
		}
}
?>