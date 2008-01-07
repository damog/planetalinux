<?php require("header.php"); ?>
<?php
include_once('lib/header.inc');
include_once('lib/functions.inc');
$Conexion = DBConnect();
?>
	<div id='tabla' align='center'>
	<table class="tableamex" summary="Concurso Planeta Linux">
	<caption>Resultados Concurso Planeta Linux 2007</caption>
	<thead>
		<tr>
			<th scope="col">Correo de Denunciante</th>
			<th scope="col">Puntos Acumulados</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th scope="row"></a></th>
			<td colspan="2"></td>									
		</tr>
	</tfoot>
	<tbody>		
       <?php 
	      	$denunciantes=DBSelect("correo,puntos","denunciantes","","Order by puntos DESC");
		foreach($denunciantes as $key => $denuncia){
 			echo "<tr class='odd'><th scope='row' id=denunciantes>".$denuncia['correo']."</th>";
		        echo " <td>".$denuncia['puntos']."</td></tr>";
		}
       ?>
  	</tbody>
	</table>	
	</div>
<?php require("footer.php"); ?>
</body>
</html>

