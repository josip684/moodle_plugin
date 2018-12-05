<?php

/* index.php */
require_once('../../config.php');
require($CFG->dirroot . '/report/mojreport/index_form.php');
// Get the system context.
$systemcontext = context_system::instance();
$url = new moodle_url('/report/mojreport/index.php');
// Check basic permission.
require_capability('report/mojreport:view', $systemcontext);
// Get the language strings from language file.
//
$strgrade = get_string('grade', 'report_mojreport');
$strcourse = get_string('course', 'report_mojreport');
$strmojreport = get_string('mojreport', 'report_mojreport');
$strname = get_string('name', 'report_mojreport');
$strtitle = get_string('title', 'report_mojreport');
// Set up page object.
$PAGE->set_url($url);
$PAGE->set_context($systemcontext);
$PAGE->set_title($strtitle);
$PAGE->set_pagelayout('report');
$PAGE->set_heading($strtitle);

$userid = $USER->id;
// Get the courses.

$sql = "SELECT id, fullname
        FROM mdl_course
        WHERE visible = :visible AND id != :siteid
        ORDER BY fullname";

		
$courses = $DB->get_records_sql_menu($sql, array('visible' => 1, 'siteid' => SITEID));
$courseid = $_POST[course];

// Load up the form.
$mform = new mojreport_form('', array('courses' => $courses, 'tests' => $tests));
echo $OUTPUT->header();
echo $OUTPUT->heading($strtitle);
$mform->display();

//POSTAVKE DATUMA
date_default_timezone_set("Europe/Zagreb");//VREMESKA ZONA
$datum = date('l, j F Y, h:i A');//DATUM TRENUTNI
$datum2 = new DateTime($datum);

//GLEDA DA LI JE ODABRAN IJEDAN KOLEGIJ, SVAKI KOLEGIJ IMA ID != 0
if ($courseid != 0 ) {
//___TESTOVI____
	echo '<br><br><b>Testovi na odabranom kolegiju:</b>';
	$niz_kvizova = 
	"SELECT  name, timeclose
        FROM mdl_quiz 
        where course = $courseid";
	
	$kvizovi = $DB->get_records_sql_menu($niz_kvizova, array('visible' => 1, 'course' => courseid));
	$br = 0;
	foreach($kvizovi as $key => $value)
	{
		echo "<br>".$key ." -> Krajnji datum: " .userdate($value);
		$value2 = new DateTime(userdate($value));
		if($datum2 < $value2)//ako je današnji datum manji od datuma kad se test zatvara onda mi uvećaj brojač za 1
		{
			$br++;
		}
	}
	echo "<br>";
	echo "<h4>Testova za rješiti: " .$br ."</h4>";
//___LEKCIJE______
	echo '<br><br><b>Lekcije na odabranom kolegiju:</b>';
	$niz_lekcija = "
	SELECT  name, deadline
        FROM mdl_lesson 
        where course = $courseid
	";
	$lekcije = $DB->get_records_sql_menu($niz_lekcija, array('visible' => 1, 'course' => courseid));
	$br2=0;	
	foreach($lekcije as $key => $value)
	{
		echo "<br>".$key ." -> Krajnji datum: " .userdate($value);
		$value2 = new DateTime(userdate($value));
		if($datum2 < $value2)
		{
			$br2++;
		}
	}
	echo "<br>";
	echo "<h4>Lekcija za rješiti: " .$br2 ."</h4>";
//____DOMAĆE ZADAĆE_______
	echo '<br><br><b>Domaće zadaće na odabranom kolegiju:</b>';
	$niz_zadaca = "
	SELECT  name, duedate
        FROM mdl_assign 
        where course = $courseid
	";
	$zadace = $DB->get_records_sql_menu($niz_zadaca, array('visible' => 1, 'course' => courseid));
	$br3=0;
	foreach($zadace as $key => $value)
	{
		echo "<br>".$key ." -> Krajnji datum: " .userdate($value);
		$value2 = new DateTime(userdate($value));
		if($datum2 < $value2)
		{
			$br3++;
		}
	}
	echo "<br>";
	echo "<h4>Domaćih zadaća za rješiti: " .$br3 ."</h4>";
	echo "<br>";
} else{
	echo 'Trebate odabrati kolegij!';
}
echo "<br>";
echo $datum;

//OVO ĆE SE ISPISATI NA GRAFU
$pieData = array(
				array('Zadatak','Broj zadataka'),
				array('Kvizovi',(double)$br),
				array('Lekcije',(double)$br2),
				array('Domaće zadaće',(double)$br3)
);

//OVO MORAMO ZBOG JAVESCRIPTA NAPRAVITI
$jsonTable=json_encode($pieData);
?>

<!DOCTYPE html>
<html>
  <head>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load("current", {packages:["corechart"]});
      google.charts.setOnLoadCallback(drawChart);
	  
      function drawChart() {
        var data = google.visualization.arrayToDataTable(<?php echo $jsonTable ?>);

        var options = {
          title: 'Stanje obaveza',
          is3D: true,
        };

        var chart = new google.visualization.PieChart(document.getElementById('donutchart'));
        chart.draw(data, options);
      }
    </script>
  </head>
  <body>
    <div id="donutchart" style="width: 900px; height: 500px;"></div>
  </body>
</html>

<?php
echo $OUTPUT->footer();
?>