<?php

	/*
	*	Formulario de ingreso para planetalinux.org
	*	------------------------------------------------------------------------
	*	Por: J. Carlos Nieto <xiam@users.sourceforge.net>
	*	This program is Free Software.
	*
	*	@license	http://www.gnu.org/copyleft/gpl.html GNU/GPL License 2.0
	*	@link		http://xiam.menteslibres.org
	*/

	define("SAFE_INCLUDE", true);

	define("DEBUG", true);

	if (!is_writable("data"))
		die("./data/ debe ser escribible.");

	include "conf.php";

	function message_die($text) {
		include "../header.php";
		echo "<h1>Suscripci&oacute;n</h1>"
		."$text"
		."<br /><a href=\"javascript:history.back(1)\">[ &laquo; volver ]</a> | <a href=\"http://www.planetalinux.org\">[ www.planetalinux.org ]</a>";
		include "../footer.php";
		exit;
	}

	if (isset($_POST['input'])) {

		//header("content-type: text/plain");

		// preventing d.o.s. and spam
		include "lib/antiflood.hack.php";

		// custom smtp mail and http connections
		include "lib/network.lib.php";

		$input = $_POST["input"];

		if (isset($_FILES["hg_file"]) && $_FILES["hg_file"]["size"]) {

			if (!$_FILES["hg_file"]["error"] && $_FILES["hg_file"]["size"] <= HG_MAX_SIZE) {

				// verificando extensión (no confio en mime-type)
				if (preg_match("/^.*\.(".HG_ACCEPT_EXTENSION.")$/i", $_FILES["hg_file"]["name"])) {

					// tamaño maximo
					list($imgw, $imgh) = @getimagesize($_FILES["hg_file"]["tmp_name"]);

					if (!isset($imgw) || ($imgw > 95 || $imgh > 95))
						die("La imagen contiene errores de formato o es demasiado grande (95px es el lado mayor permitido).");

					// evitando colisiones de nombre de face
					$p = "";
					while (file_exists($input["hackergotchi"] = "data/$p".$_FILES["hg_file"]["name"]))
						$p .= "_";

					move_uploaded_file($_FILES["hg_file"]["tmp_name"], $input["hackergotchi"]);


					// actualizando url o sobreescribiendo si ya existia
					$input["hackergotchi"] = "http://{$_SERVER["SERVER_NAME"]}".dirname($_SERVER["PHP_SELF"])."/{$input["hackergotchi"]}";
				} else {
					die("No me agrada la extensi&oacute;n de tu hackergotchi...");
				}
			} else {
				@unlink($_FILES["hg_file"]["tmp_name"]);
				die("No pude subir tu hackergotchi. &iquest;Tiene un tama&ntilde;o demasiado grande?");
			}
		}

		if (isset($input["feed"]) && isset($input["nombre"]) && isset($input["pais"])) {

			if (!preg_match("/http:\/\//", $input["feed"]))
				message_die("Feed invalido. Falt&oacute; el prefijo http://?");

			$http = new httpSession("feedvalidator.org", "80");

			if ($http->conn->status()) {

				$buff = $http->get("/check.cgi?url=".urlencode($input["feed"]));

				$match = array();

				preg_match("/<title>([^<]*)<\/title>.*<h2>([^<]*)<\/h2>[^<]*<p>(.*?)<\/p>/s", $buff, $match);

				if (isset($match[3])) {
					unset($match[0]);
					// removing html tags from validation message
					$match[3] = preg_replace("/<[^>]*>\W/", "", $match[3]);
					$vresults = implode("\r\n", $match);
				}

				$http->close();
			}
			unset($http);

			// verificando si se pudo contactar a feedvalidator.org
			if (!isset($vresults))
				$vresults = "No se pudo contactar a feedvalidator.org\r\n";

			$vresults .= "\r\nhttp://www.feedvalidator.org/check.cgi?url=".urlencode($input["feed"]);

			$message = MESSAGE_PREFIX;

			foreach ($input as $field => $value)
				$message .= "$field:\r\n\t$value\r\n\r\n";

			$message .= "\r\n\r\nLa opinion de feedvalidator.org:\r\n---\r\n$vresults";

			sendmail (
				MAIL_RECIPIENT,
				SUBJECT_PREFIX."{$input["subject"]}",
				$message,
				"text/plain"
			);

			if (isset($_POST["subscribe"]) && $_POST["subscribe"]) {

				// suscribiendo a la lista
				$http = new httpSession("damog.net", "80");
				$postVars = Array (
					"email"		=> $input["email"],
					"fullname"	=> $input["nombre"]
				);
				$http->post("/mailman/subscribe/planetalinux_damog.net", $postVars);
				$http->close();
 			}

			// incrementando contador de acceso para evitar flood
			antiflood_countaccess();

			message_die("Gracias, tu petici&oacute;n ya ha sido enviada, ser&aacute; revisada y le avisaremos del resultado.");

		} else {

			message_die("Verifica que tu nombre, feed y hackergotchi esten completos.");

		}
	}
?>
<?php
	include "../header.php";
?>
	<h2>Suscripci&oacute;n a *.planetalinux.org</h2>

	<h3>Requisitos</h3>
	<p>Por favor, para conocer los requisitos para suscribirte a <a href="http://www.planetalinux.org">planetalinux.org</a>
	aseg&uacute;rese de haber le&iacute;do la secci&oacute;n de <a href="../faq.php">Preguntas de Uso Frecuente</a> y
	los <a href="../lineamientos.php">Lineamientos</a>.</p>

	<h3>Solicitud de inscripci&oacute;n</h3>
	<form action="index.php" method="post" enctype="multipart/form-data">
		<table>
		<tr>
			<td>Nombre:<br />
				<small>Tu nombre completo como deseas que aparezca</small>
			</td>
			<td><input class="text" type="text" name="input[nombre]" /></td>
		</tr>
		<tr>
			<td>Correo Electr&oacute;nico:<br />
				<small>Tu e-mail para contactarte en casos especiales</small>
			</td>
			<td><input class="text" type="text" name="input[email]" /></td>
		</tr>
		<tr>
			<td>Asunto:</td>
			<td>
				<select name="input[subject]">
				<?php foreach ($asuntos as $asunto) { ?>
					<option value="<?=$asunto?>"><?=$asunto?></option>
				<?php } ?>
				</select>
			</td>
		</tr>
		<tr>
			<td>Pa&iacute;s:</td>
			<td>
				<select name="input[pais]">
				<?php foreach ($paises as $id => $pais) { ?>
					<option value="<?=$id?>"><?=$pais?></option>
				<?php } ?>
				</select>
			</td>
		</tr>
		<tr>
			<td>RSS Feed URL:<br />
				<small>El Feed debe ser RSS 2.0 V&aacute;lido</small>
			</td>
			<td><input class="text" type="text" name="input[feed]" /></td>
		</tr>
		<tr>
			<td>Mensaje:<br />
				<small>Unas palabras para la nueva comunidad</small>
			</td>
			<td><textarea name="input[mensaje]" cols="30" rows="6"></textarea></td>
		</tr>
		<tr>
			<td>Hackergotchi:<br />
				<small>Una imagen para identificarte f&aacute;cilmente. <a href="http://en.wikipedia.org/wiki/Hackergotchi">[?]</a></small>
			</td>
			<td>
			Archivo:<br />
			<input type="file" name="hg_file" /><br />
			URL:<br />
			<input class="text" type="text" name="hackergotchi" />
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<input type="checkbox" name="subscribe" value="1" /> Deseo suscribirme tambien a la lista de correo.
			</td>
		</tr>
		</table>
		<button type="submit">Enviar</button>
	</form>
<?php
	include "../footer.php";
?>
