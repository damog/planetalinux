<?php

	/*
	*	Gekko - Open Source Web Development Framework
	*	------------------------------------------------------------------------
	*	Copyright (C) 2004-2006, J. Carlos Nieto <xiam@users.sourceforge.net>
	*	This program is Free Software.
	*
	*	@package	Gekko
	*	@license	http://www.gnu.org/copyleft/gpl.html GNU/GPL License 2.0
	*	@author		J. Carlos Nieto <xiam@users.sourceforge.net>
	*	@link		http://www.gekkoware.org
	*/

	if (!defined("IN-GEKKO")) die("Get a life!");

	require_once "remote.lib.php";

	/*
		*** httpSession basic usage example ***

		if (($http = new httpSession("example.com", "80")) !== false) {
			header("content-type: image/png");
			echo $http->get("/somedir/somefile.png");
			$http->close();
		}
	*/
	class httpSession {

		var $userAgent = "Gekko/0.6";
		var $conn, $info, $body, $header, $head;

		function status() {
			return $this->conn->status();
		}

		function httpSession($host, $port = 80, $user = null, $pass = null) {
			$this->info["host"] = $host;
			$this->info["port"] = $port ? $port : 80;
			$this->info["user"] = $user;
			$this->info["pass"] = $pass;
			return $this->open($this->info["host"], $this->info["port"]);
		}
		function open($host, $port) {
			if (($this->conn = new remoteConnection($host, $port)) !== false) {
				return true;
			}
			return false;
		}
		function parseResponse(&$buff) {

			preg_match("/^(.*?)\r\n\r\n(.*?)$/s", $buff, $match);

			if (isset($match[2])) {
				$this->header = $match[1];
				$this->body = $match[2];

				$headlines = explode("\n", $this->header);

				// this->head
				$status = false;
				//$stop = false;
				foreach ($headlines as $header) {
					if (!$status) {
						$status = $header;
						// expecting HTTP/1.1 200 OK
						if (!strpos($status, "200")) {
							return false;
						}
					} else {
						preg_match("/^([^:]*?):\s*(.*?)[\r]$/i", $header, $htmp);
						if (isset($htmp[2]))
							$this->head[strtolower($htmp[1])] = $htmp[2];
					}
				}

				// inflating gzip'ed pages
				if ((isset($this->head["content-encoding"]) && ($this->head["content-encoding"] == "gzip")) || (isset($this->head["vary"]) && strtolower($this->head["vary"]) == "accept-encoding")) {
					// Read http://www.php.net/manual/en/function.gzinflate.php
					$this->body = gzinflate(substr($this->body, 10));
				}
			}
		}
		function sendCommonHeaders() {
			$this->conn->write("Host: ".$this->info["host"].":".$this->info["port"]."\r\n");
			$this->conn->write("User-Agent: ".$this->userAgent."\r\n");
			if ($this->info["user"])
				$this->conn->write("Authorization: Basic ".base64_encode($this->info["user"].":".$this->info["pass"])."\r\n");
			$this->conn->write("Accept: */*\r\n");
			$this->conn->write("Accept-Encoding: gzip,deflate\r\n");
		}
		function post($where, $variables) {

			if (!$this->status())
				return false;

			$buff = "";
			foreach ($variables as $field => $value)
				$buff[] = "$field=".urlencode($value);
			$variables = implode("&", $buff);

			$this->conn->write("POST $where HTTP/1.1\r\n");
			$this->conn->write("Content-Length: ".strlen($variables)."\r\n");
			$this->conn->write("Content-Type: application/x-www-form-urlencoded\r\n");
			$this->sendCommonHeaders();
			$this->conn->write("\r\n");
			$this->conn->write($variables);

			$buff = $this->conn->read();

			$this->parseResponse($buff);

			return $this->body;

		}
		function getHeaders() {
			$buff = "";
			do {
				$line = fgets($this->conn->socket);
				$buff .= $line;
				if ($line == "\r\n")
					return $buff;
			} while (!feof($this->conn->socket));
		}
		function get($what) {

			if (!$this->status())
				return false;

			$this->conn->write("GET $what HTTP/1.0\r\n");
			$this->sendCommonHeaders();
			$this->conn->write("\r\n");

			$buff = $this->conn->read(-1);

			$this->parseResponse($buff);

			return $this->body;
		}
		function close() {
			$this->conn->close();
		}
	}

	class smtpSession {
		var $conn;
		var $info;

		// Initializing Class
		function smtpSession($host, $port = "25", $user = null, $pass = null) {
			$this->info["host"] = $host;
			$this->info["port"] = $port ? $port : "25";
			$this->info["user"] = $user;
			$this->info["pass"] = $pass;

			if (class_exists("conf")) {
				$this->info["from-mail"] = conf::getkey("core", "site.contact_mail");
				$this->info["from-name"] = conf::getkey("core", "site.contact_mail");
			} else {
				// if you're using this file outside Gekko
				$this->info["from-mail"] = GEKKO_SMTP_FROM_EMAIL;
				$this->info["from-name"] = GEKKO_SMTP_FROM_NAME;
			}

			return $this->connect();
		}
		// Opens a connection with SMTP server
		function connect() {
			$this->conn = new remoteConnection($this->info["host"], $this->info["port"]);
			if ($this->conn->status()) {
				// connected, now saying hello!
				return $this->login();
			} else {
				// there was a connection error for some strange reason
				trigger_error("Couldn't open connection to SMTP server.",E_USER_ERROR);
				return false;
			}
		}

		// Sending creentials
		function login() {

			// Please read rfc0821 (or if you're too lazy just sniff a conversation between your e-mail client
			// and one random smtp server)
			$this->chat("220", "EHLO ".$this->info["host"]."\r\n");
			$ehlo = $this->conn->read();

			// getting server supported auth methods (read rfc2554)
			// http://www.technoids.org/saslmech.html
			if ($this->info["user"] && preg_match_all("/\d{3}-AUTH\s(.*)/", $ehlo, $match) && isset($match[1][0])) {
				$methods = explode(" ", $match[1][0]);

				if (in_array("LOGIN", $methods)) {
					$this->conn->write("AUTH LOGIN\r\n");
					$this->chat("334", base64_encode($this->info["user"])."\r\n");
					$this->chat("334", base64_encode($this->info["pass"])."\r\n");
				} else {
					trigger_error("Unsupported SMTP AUTH scheme.", E_USER_ERROR);
				}

				if (!$this->chat("235", "", true))
					trigger_error("Incorrect SMTP Username or Password.", E_USER_ERROR);
			} else {
				$this->chat("220", "HELO ".$this->info["host"]."\r\n");
				$this->chat("250");
			}

			return true;
		}
  		// Sends an e-mail
		function send($to, $subject, $message, $content_type = "text/plain", $headers = null) {

			//
			$this->conn->write("MAIL FROM: <".$this->info["from-mail"].">\r\n");

			// can handle multiple recipients sepparated by commas
			$ato = explode(",", $to);
			foreach ($ato as $addr) {
				$this->chat("250", "RCPT TO: <".trim($addr).">\r\n");
			}

			// telling server that the following data is a message
			$this->chat("250", "DATA\r\n");
			$this->chat("354");

			// common headers
			$this->conn->write("Mime-Version: 1.0\r\n");
			$this->conn->write("Content-Type: $content_type\r\n");
			$this->conn->write("Subject: $subject\r\n");
			$this->conn->write("From: ".$this->info["from-name"]." <".$this->info["from-mail"].">\r\n");
			$this->conn->write("To: <".$to.">\r\n");
			$this->conn->write("Date: ".date("r")."\r\n");
			//$this->conn->write("X-Mailer: Gekko/".GEKKO_VERSION."\r\n");
			//$this->conn->write("X-Gekko-Tag: ".(isset($GLOBALS["USER"]["id"]) ? $GLOBALS["USER"]["id"] : "?")."@".USER_IP."\r\n");

			if (defined("GEKKO_SMTP_EXTRA_HEADERS"))
				$headers .= GEKKO_SMTP_EXTRA_HEADERS;

			if ($headers)
				$this->conn->write($headers);

			$this->conn->write("\r\n");

			// beginning message bdy
			$this->conn->write($message);

			// the final dot
			$this->conn->write("\r\n.\r\n");

			// expecting confirmation
			$this->chat("250");
		}

		/**
		* chat(expecting, answer, hide_errors);
		* Sends $answer to server after receiving the expected answer
		*/
		function chat($expecting, $answer = null, $hide_errors = false) {
			// reading what server is saying
			$data = $this->conn->read();
			// checking if this was an expected response code (first 3 numbers)
			if (substr(trim($data), 0, 3) == $expecting) {
				if ($answer)
					$this->conn->write($answer);
				return true;
			} else {
				if ($hide_errors)
					return false;
				die("Hubo un error al tratar de enviar tu suscripci&oacute;n, por favor inetenta de nuevo.");
				//trigger_error("S: \"".trim($data)."\", expecting: \"$expecting\"", E_USER_ERROR);
			}
		}
		// closing connection
		function bye() {
			$this->conn->write("QUIT\r\n");
			$this->conn->close();
		}
	}
	function sendmail($to, $subject, $message, $content_type = "text/plain", $headers = null) {

		// preventing possible spam attacks by crlf injection
		$to = trim(preg_replace("/[\r|\n](.*?)/", "", $to));
		$subject = trim(preg_replace("/[\r|\n](.*?)/", "", $subject));
		$message = trim(preg_replace("/[\r|\n]\.[\r|\n](.*?)/", "", $message));

		if (GEKKO_SMTP_MAIL) {

			$smtp = new smtpSession(GEKKO_SMTP_HOST, GEKKO_SMTP_PORT, GEKKO_SMTP_USER, GEKKO_SMTP_PASS);
			if ($smtp->conn->status())
				$smtp->send($to, $subject, $message, $content_type, $headers);

			$smtp->bye();

		} else {

			if (!$headers)
				$headers = "";

			$headers .= "Mime-Version: 1.0\r\n";
			$headers .= "Content-Type: $content_type\r\n";
			$headers .= "From: ".GEKKO_SMTP_FROM_NAME." <".GEKKO_SMTP_FROM_EMAIL.">\r\n";
			//$headers .= "X-Gekko-Tag: ".(isset($GLOBALS["USER"]["id"]) ? $GLOBALS["USER"]["id"] : "0")."@".getIP()."\r\n";
			$headers .= "Date: ".date("r")."\r\n";

			mail($to, $subject, $message, trim($headers));
		}

	}

?>
