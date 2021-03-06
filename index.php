<?php
error_reporting(0);
ini_set('display_errors', 0);
include('config.php');
include 'inc/parsedown.php';
if ($protect) {
	require_once('protect.php');
}
?>

<html lang="en">
<!-- Author: Dmitri Popov, dmpop@linux.com
         License: GPLv3 https://www.gnu.org/licenses/gpl-3.0.txt -->

<head>
	<meta charset="utf-8">
	<title><?php echo $title; ?></title>
	<link rel="shortcut icon" href="favicon.png" />
	<link rel="stylesheet" href="css/milligram.min.css">
	<link rel="stylesheet" href="css/styles.css">
	<meta name="viewport" content="width=device-width, initial-scale=1">
</head>

<body>
	<!-- Suppress form re-submit prompt on refresh -->
	<script>
		if (window.history.replaceState) {
			window.history.replaceState(null, null, window.location.href);
		}
	</script>
	<div id="content">
		<div style="text-align: center; margin-bottom: 2em;">
			<img style="display: inline; height: 2.5em; border-radius: 0; vertical-align: middle;" src="favicon.svg" alt="logo" />
			<h1 style="display: inline; margin-left: 0.19em; vertical-align: middle; letter-spacing: 3px;"><?php echo $title ?></h1>
		</div>
		<table id="theTable">
			<?php
			// Start session
			if (session_status() == PHP_SESSION_NONE) {
				session_start();
			}
			// The $d parameter is used to detect a subdirectory
			if (isset($_GET['d'])) {
				$current_dir = $_GET['d'];
			} else {
				$current_dir = $root_dir;
			}
			$_SESSION["dir"] = $current_dir;
			$sub_dirs = array_filter(glob($current_dir . DIRECTORY_SEPARATOR . '*'), 'is_dir');
			// Generate sub-directory navigation
			if ((count($sub_dirs)) > 0 or (!empty($current_dir))) {
				$higher_dirs = explode("/", $current_dir);
				$higher_dir_cascade = "";
				foreach ($higher_dirs as $higher_dir) {
					if (!empty($higher_dir)) {
						if (!empty($higher_dir_cascade)) {
							$higher_dir_cascade = $higher_dir_cascade . DIRECTORY_SEPARATOR;
						}
						$higher_dir_cascade = $higher_dir_cascade . $higher_dir;
						echo "<a href='"  . basename($_SERVER['PHP_SELF']) . "?d=" . $higher_dir_cascade . "'>" . $higher_dir . "</a> /&nbsp;";
					}
				}
				// Populate a drop-down list with subdirectories
				echo '<select style="width: auto;" name="" onchange="javascript:location.href = this.value;">';
				echo '<option value="Default">Choose place</option>';
				foreach ($sub_dirs as $dir) {
					setlocale(LC_ALL,'C.UTF-8');
					$dir_name = basename($dir);
					$dir_option = str_replace('\'', '&apos;', $current_dir . DIRECTORY_SEPARATOR . $dir_name);
					echo "<option value='?d=" . ltrim($dir_option, '/') . "'>" . $dir_name . "</option>";
				}
				echo "</select>";
			}
			?>
			<form method='POST' action=''>
				<input style='display: inline; width: 9em; margin-left: 0.5em;' type='text' name='place'>
				<input class="button" style='display: inline; margin-left: 0.5em; margin-right: 0.5em;' type='submit' name='add' value='Add'>
				<input class="button button-clear" style='display: inline;' type='submit' name='weather' value='Weather'>
			</form>
			<?php
			// Get current weather from wttr.in
			if (isset($_POST["weather"])) {
				echo "<div>";
				// Create new directory
				$url = "https://wttr.in/" . end(explode("/", $current_dir)) . "?format=%c+%t,+%w,+%p,+%h";
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_URL, $url);
				$result = curl_exec($ch);
				echo $result;
			}
			echo '<a target="_blank" href="https://www.google.com/search?q=weather+forecast+' . end(explode("/", $current_dir)) . '"><img style="margin-left: 0.5em; vertical-align: -0.4em;" title="Weather forecast for the current location" src="svg/sun.svg" /></a></div>';
			// Create the current directory
			if (!file_exists($current_dir)) {
				mkdir($current_dir, 0755, true);
			}
			if (isset($_POST["add"]) && !empty($_POST["place"])) {
				// Create new directory
				mkdir($current_dir . DIRECTORY_SEPARATOR . $_POST["place"], 0755, true);
			}
			if (isset($_POST["delete"])) {
				// Remove existing directory
				echo "<div style='margin: auto; margin-top: 1.5em; border: 1px solid #969696; border-radius: 5px; width: 15em;'><span style='color: red;'>Do you really want to delete this place?</span> <form method='POST' action=''><input type='submit' name='confirm' value='Yes'></form></div>";
			}
			if (isset($_POST["confirm"])) {
				rmdir($current_dir);
			}
			// Read CSV file
			$csvfile = $current_dir . DIRECTORY_SEPARATOR . "data.csv";
			if (!is_file($csvfile)) {
				$HEADER = "Place;Link;Note\n";
				file_put_contents($csvfile, $HEADER);
			}
			$lines = count(file($csvfile));
			if ($lines > 1) {
				$row = 1;
				if (($handle = fopen($csvfile, "r")) !== FALSE) {
					while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
						$num = count($data);
						if ($row == 1) {
							echo '<thead><tr>';
						} else {
							echo '<tr>';
						}
						$value0 = $data[0];
						$value1 = $data[1];
						$value2 = $data[2];
						$Parsedown = new Parsedown();
						if ($row == 1) {
							echo '<th class="sortable" onclick="sortTable(0)">' . $value0 . '</th>';
							echo '<th class="sortable" onclick="sortTable(1)">' . $value2 . '</th>';
						} else {
							echo '<td><p>' . $value0 . ' <a target="_blank" href="' . $value1 . '"><img style="vertical-align: -0.3em;" src="svg/link.svg" /></a></p></td><td>' . $Parsedown->text($value2) . '</td>';
						}
						if ($row == 1) {
							echo '</tr></thead><tbody>';
						} else {
							echo '</tr>';
						}
						$row++;
					}
					fclose($handle);
				}
			} else {
				echo "<div style='margin-top: 1em; margin-bottom: 1.5em;'>So empty here. Press the <strong>Edit</strong> button to add places.</div>";
			}
			?>
			</tbody>
		</table>
		<div style="margin-top: 1.5em;"></div>
		<form style='display: inline;' method='POST' action=''>
			<input class="button button-outline" style='display: inline; margin-right: 0.5em;' type='submit' name='delete' value='Delete'>
		</form>
		<form style='display: inline;' method='POST' action='edit.php'>
			<button type='submit'>Edit</button>
		</form>
		<div style="margin-top: 1.5em;"><?php echo $footer; ?></div>
	</div>
	<script>
		function sortTable(n) {
			var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
			table = document.getElementById("theTable");
			switching = true;
			dir = "asc";
			while (switching) {
				switching = false;
				rows = table.rows;
				for (i = 1; i < (rows.length - 1); i++) {
					shouldSwitch = false;
					x = rows[i].getElementsByTagName("TD")[n];
					y = rows[i + 1].getElementsByTagName("TD")[n];
					if (dir == "asc") {
						if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
							shouldSwitch = true;
							break;
						}
					} else if (dir == "desc") {
						if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
							shouldSwitch = true;
							break;
						}
					}
				}
				if (shouldSwitch) {
					rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
					switching = true;
					switchcount++;
				} else {
					if (switchcount == 0 && dir == "asc") {
						dir = "desc";
						switching = true;
					}
				}
			}
		}
	</script>
</body>

</html>