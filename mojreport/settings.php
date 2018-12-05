<?php

defined('MOODLE_INTERNAL') || die;

$ADMIN->add('reports', new admin_externalpage('reportmojreport', get_string('pluginname', 
        'report_mojreport'), "$CFG->wwwroot/report/mojreport/index.php",'report/mojreport:view'));

// no report settings
$settings = null;
