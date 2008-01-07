<?php require("header.php"); ?>
<?php
include_once('lib/header.inc');
include_once('lib/functions.inc');
$Conexion = DBConnect();

$error = 0;

if($_POST['submit']){

	// Aqui se tiene que hacer la validacion de que haya sido exitosa la insercion

        $urlfeed=StringSet($_POST['urlfeed']);
        $urlblog=StringSet($_POST['urlblog']);
        $nameblogger=$_POST['nameblogger'];
        $instancias=StringSet($_POST['instancias']);
        $denunciante=StringSet($_POST['denunciante']);
        $comentario=$_POST['comentario'];
	if(!$urlfeed) {
		print "<p class='error'>No especificaste la URL del feed.</p>";
		$error = 1;
	}

	if(!$urlblog) {
		print "<p class='error'>No especificaste la URL del blog.</p>";
		$error = 1;
	}

        if (!$instancias){
		print "<p class='error'>No especificaste una instancia valida.</p>";
		$error = 1;
        }

        if (!IsValidMail($denunciante)){
		print "<p class='error'>No especificaste un correo valido.</p>";
		$error = 1;
        }

	if(!$nameblogger) {
		print "<p class='error'>No especificaste un nombre del blogger.</p>";
		$error = 1;
	}

	if($error == 0) {
		// Haz algo que sigue

		$feedvalido=DBSelect("id","propuestas","feed='$urlfeed'");
		if(!$feedvalido){
			$iddenunciante=DBSelect("id","denunciantes","correo='$denunciante'");
			if(!$iddenunciante){
		 		$insertadenunciante['correo']=DBStringSet($denunciante);
				$insertadenunciante['puntos']= 1;
				DBInsert2(denunciantes,$insertadenunciante);
				$iddenunciante=DBSelect("id","denunciantes","correo='$denunciante'");	
			}
			else{
				$puntos=DBSelect("puntos","denunciantes","correo='$denunciante'");
				$contador=$puntos['0']['puntos'];
				$puntosarr['puntos']=$contador + 1;
				DBUpdate(denunciantes,$puntosarr,"correo='$denunciante'");
			}
			$altas['feed']=DBStringSet($urlfeed);
			$altas['url']=DBStringSet($urlblog);
			$altas['blogger']=DBStringSet($nameblogger);
			$altas['idinstancia']=DBStringSet($instancias);
			$altas['iddenunciante']=DBStringSet($iddenunciante['0']['id']);
			$altas['comentario']=DBStringSet($comentario);
			DBInsert2(propuestas,$altas);
			include_once('gracias.php');
			exit();
			}
		else{
			print "<p class='error'>Ya existe el feed: ".$urlfeed."</p>";
		}
	} else {
		print "<br />";
	}
}


?>

  <div id='table' align='center'>
  <form method="post" action="concurso.php"> 
	<table class="tableamex" summary="Concurso Planeta Linux">
	<caption>Concurso Planeta Linux 2007</caption>
	<thead>
		<tr>
			<th scope="col"></th>
			<th scope="col"></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th scope="row"></a></th>
			<td colspan="2"></td>									
		</tr>
	</tfoot>
	<tbody>		
    <tr class="odd"> 
        <th scope="row" id=URLFeed>URL del feed</th>
        <td><font size=-1>http://</font> <input type='text' name='urlfeed' class='textarea' size='35' value="<?php print $urlfeed; ?>" style='margin-left:0px'></td>
    </tr>	
	<tr class="odd"> 
        <th scope="row" id=URLBlog>URL del blog</th>
        <td><font size=-1>http://</font> <input type='text' name='urlblog' class='textarea' size='35' value="<?php print $urlblog; ?>" style='margin-left:0px'></td>
    </tr>
	<tr class="odd"> 
        <th scope="row" id=bloggername>Nombre completo del blogger</th>
        <td><input type='text' name='nameblogger' class='textarea' size='40' value="<?php print $nameblogger; ?>" style='margin-left:0px'></td>
    </tr>
	<tr class="odd"> 
        <th scope="row" id=instancia>Instancia</th>
		<td>
		<select name=instancias>
                      <?php 
		      	$grupo1=DBSelect("id,pais","instancias","existe=1");
		      	$grupo2=DBSelect("id,pais","instancias","existe=0 Order by pais");
				print "<option label='--------------' value=''>------------</option>";
			foreach($grupo1 as $key => $instancia){
				
 				print "<option label=&nbsp;".$instancia['pais']." value=".$instancia['id']; if($instancia['id'] == $_POST[instancias]) { print " SELECTED"; } print ">&nbsp;".$instancia['pais']."</option>";
			}
				print "<option label='--------------' value=''>------------</option>";

			foreach($grupo2 as $key => $instancia){
				print "<option label=&nbsp;".$instancia['pais']." value=".$instancia['id']; if($instancia['id'] == $_POST[instancias]) { print " SELECTED"; } print ">&nbsp;".$instancia['pais']."</option>";
			}
		      ?>
		</select>
		</td>
    </tr>
	<tr class="odd"> 
        <th scope="row" id=denunciante>Mail del denunciante</th>
        <td><input type='text' name='denunciante' class='textarea' size='40' value="<?php print $denunciante; ?>" style='margin-left:0px'></td>
    </tr>
    <tr class="odd">
    	<th scope="row" id=comentarios>Comentarios *</th>
        <td>
	  <TEXTAREA name="comentario" rows="10" cols="40"><?php print $comentario; ?></TEXTAREA>
	</td>  
    </tr>	
	<tr>  	  	
  		<td><input type='submit' name='submit' class='button' value='Enviar'></td>
		<td><font size="-2">* campo opcional</font></td>
  	</tr>
	</table>	
	</div>
<?php require("footer.php"); ?>
</body>
</html>

