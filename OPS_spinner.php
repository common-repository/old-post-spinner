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


function ops_spinParse($pos, &$data) {
	
	global $leftchar, $rightchar, $splitchar;
	$startPos = $pos;
	#print "Start Position: $startPos | Length: ".strlen($data)."<br>";
	
	while ($pos++ < strlen($data)) {
		#print "Current Position: $pos | Character: ".substr($data, $pos, 1)."<br>";
		
		if (substr($data, $pos, strlen($leftchar)) == $leftchar) {
			$data = ops_spinParse($pos, $data);
		} elseif (substr($data, $pos, strlen($rightchar)) == $rightchar) {
			$entirespinner = substr($data, $startPos+strlen($leftchar), ($pos - $startPos)-strlen($rightchar));
			$processed = ops_spinProcess($entirespinner);
			$data = str_replace($leftchar.$entirespinner.$rightchar,$processed,$data);
		}
		
		//echo ($data."<br>");
	}
	
	
	return $data;
	
}


function ops_spinProcess($input) {
	global $splitchar;
	
	#echo ("Process Request: '$input'");
	$txt = explode($splitchar,$input);
	#print "->".count($txt);
	$selection = $txt[mt_rand(0,count($txt)-1)];
	#echo (" | Result: '$selection'<br>");
	return $selection;
	
}


function ops_run_spinner($text) {	
	global $leftchar, $rightchar, $splitchar;
	$startTime = time();

	$leftchar = OPS_LEFTCHAR;
	$rightchar = OPS_RIGHTCHAR;
	$splitchar = OPS_SPLITCHAR;
	//return the block of modified text

	$thearticle = ops_spinParse(-1, $text);
	
	//echo ("<br><br>Exec time: ".(time()-$startTime)." seconds.");
	return $thearticle;
}


?>