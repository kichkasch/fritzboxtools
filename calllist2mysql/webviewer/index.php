<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php
/* 
	CallList2MySql WebViewer
	by Michael Pilgermann (kichkasch@gmx.de)
	Copyright (C) 2012 Michael Pilgermann
 
This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.
 
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/
?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Calllist2MySQL WebViewer</title>
<meta name="Description" content="" />
<meta name="Keywords" content="fritzbox mysql calllog call history php" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />


<style type="text/css" title="currentStyle">
	@import "datatables/media/css/demo_page.css";
	@import "datatables/media/css/jquery.dataTables.css";
	@import "datatables/media/css/ui-darkness/jquery-ui-1.9.2.custom.css"; 
</style>
<script type="text/javascript" language="javascript" src="datatables/media/js/jquery.js"></script>
<script type="text/javascript" language="javascript" src="datatables/media/js/jquery.dataTables.js"></script>
<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		$('#table_list').dataTable({
			"aaSorting": [[ 1, "desc" ]],
			"iDisplayLength": 25,
			"bJQueryUI": true
		});
	} );
</script>


</head>

<?php
include 'config.php';

$linkID = mysql_connect($host, $user, $pass) or die("Could not connect to host.");
mysql_select_db($database, $linkID) or die("Could not find database.");
?>

			<h1>Call list viewer</h1>
			<div id="demo">
<table cellpadding="0" cellspacing="0" border="0" class="display" id="table_list" width="100%">
	<thead>
		<tr>
			<th>Typ</th>
			<th>Date and Time</th>
			<th>Caller's name</th>
			<th>Caller's number</th>
			<th>Local number</th>
			<th>Duration (in minutes)</th>
		</tr>
	</thead>
	<tbody>

<?php
$query = "SELECT typname, Datum, Name, Rufnummer, EigeneRufnummer, Dauerminuten from calllog, calltyp WHERE calllog.typ = calltyp.typid";
$resultID = mysql_query($query, $linkID) or die("Data not found.");
for($x = 0 ; $x < mysql_num_rows($resultID) ; $x++){
 $row = mysql_fetch_assoc($resultID);
 print("<tr>");
 print("<td>" . $row['typname'] . "</td>");
 print('<td>' . $row['Datum'] . '</td>');
 print('<td>' . $row['Name'] . '</td>');
 print('<td>' . $row['Rufnummer'] . '</td>');
 print('<td>' . $row['EigeneRufnummer'] . '</td>');
 print('<td>' . $row['Dauerminuten'] . '</td>');
 print('</tr>');
}
?>

	</tbody>
	<tfoot>
		<tr>
			<th>Typ</th>
			<th>Date and Time</th>
			<th>Caller's name</th>
			<th>Caller's number</th>
			<th>Local number</th>
			<th>Duration (in minutes)</th>
		</tr>
	</tfoot>
</table>

</div>

<body>

</body>
</html>

