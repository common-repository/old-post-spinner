<?php
/*  Copyright 2010 Juergen Schulze, 1manfactory.com

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

	$ops_file=urldecode(getVar("ops_file"));
	
	# check for local file inclusion vulnerability
	if (preg_match("/%|\./", $ops_file)) {
		die();
	}
	header('Content-type: text/html; charset="utf-8"',true);
	if (@file_exists($ops_file.".log")) {
		print "<pre>";
		foreach (file($ops_file.".log") as $line) {
			print $line;
		}
		print "</pre>";
	} else {
		print '<br>No logfile until now';
	}
	
	# fixing Script vulnerable to null bytes
	# http://php.net/manual/en/security.filesystem.nullbytes.php
	function getVar($name) {
		$value = isset($_GET[$name]) ? $_GET[$name] : null;
		if (is_string($value)) {
			$value = str_replace("\0", '', $value);
		}
		return $value;
	}
?>