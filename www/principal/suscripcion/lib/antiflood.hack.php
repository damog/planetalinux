<?php

	/*
	*	Antiflood - Generic antiflood protection
	*	------------------------------------------------------------------------
	*	Copyright (C) 2004-2006, J. Carlos Nieto <xiam@users.sourceforge.net>
	*	This program is Free Software.
	*
	*	@license	http://www.gnu.org/copyleft/gpl.html GNU/GPL License 2.0
	*	@author		J. Carlos Nieto <xiam@users.sourceforge.net>
	*	@version	CVS: $Id:$
	*	@link		http://xiam.menteslibres.org
	*/

	/*
		How to use?
		Create a "tmp" directory, it must be writable for http server

		include this script on the page that you want to protect:

		<?php
			include "antiflood.php";
		?>
	*/

	if (!defined("SAFE_INCLUDE")) die("Get a Life!");

	define("SCRIPT_ROOT", dirname(__FILE__));
	
	// number of allowed page requests for the user
	define("CONTROL_MAX_REQUESTS", 3);

	// time interval to start counting page requests (seconds)
	define("CONTROL_REQ_TIMEOUT", 60*60);

	// seconds to punish the user who has exceeded in doing requests
	define("CONTROL_BAN_TIME", 10*60);

	// writable directory to keep script data 
	define("SCRIPT_TMP_DIR", SCRIPT_ROOT."/../data/control");

	// you don't need to edit below this line
	define("USER_IP", $_SERVER["REMOTE_ADDR"]);

	define("CONTROL_DB", SCRIPT_TMP_DIR."/ctrl");
	define("CONTROL_LOCK_DIR", SCRIPT_TMP_DIR."/lock");
	define("CONTROL_LOCK_FILE", CONTROL_LOCK_DIR."/".md5(USER_IP));

	@mkdir(CONTROL_LOCK_DIR);
	@mkdir(SCRIPT_TMP_DIR);
	
	if (file_exists(CONTROL_LOCK_FILE)) {
		if (time()-filemtime(CONTROL_LOCK_FILE) > CONTROL_BAN_TIME) {
			// this user has complete his punishment
			unlink(CONTROL_LOCK_FILE);
		} else {
			// too many requests
			echo "<h1>Acceso Denegado</h1>";
			echo "Por precauci&oacute;n hemos restringido mometaneamente el acceso a este sitio. Intenta
			de nuevo m&aacute;s tarde.";
			touch(CONTROL_LOCK_FILE);
			die;
		}
	}

	function antiflood_countaccess() {
		// counting requests and last access time
		$control = Array();
	
		if (file_exists(CONTROL_DB)) {
			$fh = fopen(CONTROL_DB, "r");
			$control = array_merge($control, unserialize(fread($fh, filesize(CONTROL_DB))));
			fclose($fh);
		}
		
		if (isset($control[USER_IP])) {
			if (time()-$control[USER_IP]["t"] < CONTROL_REQ_TIMEOUT) {
				$control[USER_IP]["c"]++;
			} else {
				$control[USER_IP]["c"] = 1;
			}
		} else {
			$control[USER_IP]["c"] = 1;
		}
		$control[USER_IP]["t"] = time();
	
		if ($control[USER_IP]["c"] >= CONTROL_MAX_REQUESTS) {
			// this user did too many requests within a very short period of time
			$fh = fopen(CONTROL_LOCK_FILE, "w");
			fwrite($fh, USER_IP);
			fclose($fh);
		}

		// writing updated control table
		$fh = fopen(CONTROL_DB, "w");
		fwrite($fh, serialize($control));
		fclose($fh);
	}
?>