<?php

	if (!defined("SAFE_INCLUDE")) die("Get a life!");

	if (DEBUG)
		error_reporting(E_ALL);

	// safe include
	define("IN-GEKKO", true);

	// here you can configure if you want to use a external SMTP server to send mail, if
	// you set GEKKO_SMTP_MAIL to false then it will use the simple mail() function.
	define("GEKKO_SMTP_MAIL", false);
		define ("GEKKO_SMTP_HOST", "smtp.mail.yahoo.com.mx");
		define ("GEKKO_SMTP_PORT", 25);
		define ("GEKKO_SMTP_USER", "xiamkong");
		define ("GEKKO_SMTP_PASS", "foobarbaz");
		// aparecera como direccion de origen
		define ("GEKKO_SMTP_FROM_EMAIL", "planetalinux@damog.net");
		// nombre de origen
		define ("GEKKO_SMTP_FROM_NAME", "Planeta Linux");
		// algunos headers extra
		define ("GEKKO_SMTP_EXTRA_HEADERS", "X-User-IP: {$_SERVER["REMOTE_ADDR"]}\r\n");

	define("MESSAGE_PREFIX", "Nueva solicitud de inscripcion al planeta:\r\n\r\n");
	define("SUBJECT_PREFIX", "Solicitud: ");
	define("MAIL_RECIPIENT", "planetalinux@damog.net");

	// extensiones aceptadas de hackergotchi
	define("HG_ACCEPT_EXTENSION", "png");

	// max. tamaño en bytes del hackergotchi
	define("HG_MAX_SIZE", 100*1024);

	$asuntos = Array (
		"Suscripci&oacute;n",
		"Actualizaci&oacute;n"
	);

	$paises = Array (
		"mx" => "M&eacute;xico",
		"pe" => "Per&uacute;",
		"ve" => "Venezuela"
	);
?>