<?php

session_start();


$password = $_POST['password'];


$_SESSION["sesion"] = false;

$_SESSION["registro"] = '';

$_SESSION["datos"] = '';


$_SESSION["asunto"] = '';
$_SESSION["email_emisor"] = '';
$_SESSION["cuerpo_mensaje"] = '';



if($password == 'ayto17'){
	$_SESSION["sesion"] = true;
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>ENVIAR CORREO A LISTA</title>
    <link rel="stylesheet" href="css/bootstrap.css">
	<link rel="stylesheet" href="css/bootstrap-theme.css">
	
	<style>
		.tr_active {
			background-color: #f6ffee !important;
		}
		
		.tr_error {
			background-color: #ffeeee !important;
		}
		
		/* progress bar */
		
		#szlider{
			width:80%;
			height:30px;
			border:1px solid #999;
			overflow:hidden;
			margin: 20px 0;
		}
			
		
		#szliderbar{
			width:37%;
			height:30px;
			border-right: 1px solid #777777;
			background: #9ad46a; }
		
		#szazalek {
			color: #333;
			font-size: 15px;
			font-weight: bold;
			left: 25px;
			position: relative;
			top: -25px; }

	
	</style>
    <script src="js/jquery-1.11.1.min.js"></script>
	<script src="js/jquery.scrollTo.min.js"></script>
	<script src="js/bootstrap.js"></script>
<?php
	if($_SESSION["sesion"]){
?>
	<script>
	
	var listado = new Array();
	
	//listado[0] = ["", "Apellidos0", "","A123456789012","",""];
	//listado[1] = ["", "Apellidos1", "","B123456789012","",""];
	//listado[2] = ["", "Apellidos2", "","B123456789012","",""];
	
	var json_datos = '';
	var obj_datos = '';
	var numero_datos = 0;
	
	var elemento_actual = 0;
	
	var pausa = 0;
	
	
	
	//var obj_datos = JSON.parse(json_datos);
	//var numero_datos = Object.keys(obj_datos).length;
	
	
	
	
	
	
	
	$(document).ready(function () {
		$('#borrar_registro').click(function(){
			ajax_borrarRegistro();			
		});
		
		$('#subir_ficheroDatos').click(function(){
			ajax_guardarDatos();			
		});

			
	});

	
	
	
	
	function drawszlider(ossz, meik){
		var szazalek=Math.round((meik*100)/ossz);
		document.getElementById("szliderbar").style.width=szazalek+'%';
		document.getElementById("szazalek").innerHTML=szazalek+'%';
	}
	
	
	
	function ajax_borrarRegistro(){	
		var formURL = 'http://intranet/temp/enviomail/borrar_registro.php';
		$.ajax({
			url : formURL,
			type: "POST",
			dataType : "json",
			data: {},
			success:function(data, textStatus, jqXHR)
			{
				console.log(data);
				$('#resultado_borrarRegistro').show();
				$('#resultado_borrarRegistro').text(data[0]['mensaje']);
					
			},
			error: function(jqXHR, textStatus, errorThrown)
			{
				//if fails 
				alert("No se pudo borrar. Por favor contacta con soporte");
			}
		});
		
		//e.preventDefault(); //STOP default action
		//e.unbind(); //unbind. to stop multiple form submit.
		
	}
	
	function ajax_guardarDatos(){	
		var formURL = 'http://intranet/temp/enviomail/guardar_datos.php';
		$.ajax({
			url : formURL,
			type: "POST",
			dataType : "json",
			data: {'csv': $('#fichero_datos').val()},
			success:function(data, textStatus, jqXHR)
			{
				console.log(data);
				
				obj_datos = data;
				//alert(obj_datos[0].nombre);
				var numero_datos = Object.keys(obj_datos).length;
				console.log('Resultados obtenidos: ' + numero_datos);
				pintar_datos();
				//$('#resultado_borrarRegistro').show();
				//$('#resultado_borrarRegistro').text(data[0]['mensaje']);
					
			},
			error: function(jqXHR, textStatus, errorThrown)
			{
				//if fails 
				alert("No se pudo borrar. Por favor contacta con soporte");
			}
		});
		
		//e.preventDefault(); //STOP default action
		//e.unbind(); //unbind. to stop multiple form submit.
	}
	
	function pintar_datos(){
		
		//quitamos las filas
		$('.tr_dato').remove();
		
		$('#contenedor_datos').show();
		$('#contenedor_datosEnvio').show();
		
		
		for(i=0;i<Object.keys(obj_datos).length;i++){
						
			$('#tabla_datos > tbody:last-child').append('<tr id="fila_' + i + '" class="tr_dato"><td>' + obj_datos[i][0] + '</td><td>' + obj_datos[i][1] + '</td><td>' + obj_datos[i][2] + '</td><td>' + obj_datos[i][3] + '</td>' + '</td><td>' + obj_datos[i][4] + '</td>' + '</td><td>' + obj_datos[i][5] + '</td>' + '</tr>');
			
		}
	}

	</script>

<?php
	}
?>
  </head>
  <body>
    <!-- page content -->
<?php
	if(!$_SESSION['sesion']){
?>
<!-- Contenido sin validar -->
<div style="padding: 10px; width: 500px; margin:50px auto; border: 1px solid #ccc;">
<form method="post">
  <div class="form-group">
    <label for="password">Password</label>
    <input type="password" class="form-control" id="password" name="password" placeholder="Password">
  </div>
  <button type="submit" class="btn btn-default">Acceder</button>
</form>
</div>
<?php
	}else{
?>
<div style="padding: 10px; width: 900px; margin:50px auto; border: 1px solid #ccc;">
<form method="post" action="cliente.php">
  <div style="display: none;">
	  <h2>1.- Borrar fichero de registro</h2>
	  <button type="button" class="btn btn-success" id="borrar_registro">Borar fichero registro</button> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span id="resultado_borrarRegistro" style="padding: 8px; background-color: #5cb85c; color: #fff; display: none; font-size: 1.3em;"></span>
  </div>
  
  <h2 style="margin-top: 30px;">1.- Crear lista de emails.</h2>
  <p><span style="color: #aaa; font-size: 1em;">(formato csv ';' con campos email;nombre;apellidos;aux1;aux2;aux3)</span></p>
  
  <textarea class="form-control" rows="15" placeholder="Fichero csv separado por ;" id="fichero_datos" name="fichero_datos"></textarea>

  <div style="text-align: center; margin: 30px auto;"><button type="button" class="btn btn-primary" style="padding: 15px;" id="subir_ficheroDatos">Crear lista de emails</button></div>
 
  <div id="contenedor_datos" style="width:100%; margin-top: 50px; height: 300px; overflow-y:scroll; overflow-x:hide; border: 1px solid #ccc; display: none;">
		<p style="text-align: center; font-size: 1.3em;"><b>Lista de email cargada:</b></p>  
		<table class="table table-striped" id="tabla_datos">
			<tr> 
				<th>Email</th>
				<th>Nombre</th>
				<th>Apellidos</th>
				<th>Aux1</th>
				<th>Aux2</th>
				<th>Aux3</th>
			</tr>
		</table>
	</div>
</div>
<div id="contenedor_datosEnvio" style="padding: 10px; width: 900px; margin:50px auto; border: 1px solid #ccc; display: none;">
  

	  <h2 style="margin-top: 30px;">2.- Datos del envío.</h2>	  
	  <div class="form-group">
		<label for="email_emisor">Email emisor:</label>
		<input type="email_emisor" class="form-control" id="email_emisor" name="email_emisor" placeholder="prueba@prueba.es" required="required">
	  </div>
	  
	  <div class="form-group">
		<label for="asunto">Asunto:</label>
		<input type="asunto" class="form-control" id="asunto" name="asunto" placeholder="Asunto" required="required">
	  </div>
	  <div class="form-group">
		<div style="float: left; width: 90%;">
			<label for="cuerpo_mensaje">Cuerpo del mensaje: </label>
			
			<textarea class="form-control" rows="15" placeholder="Cuerpo del mensaje (acepta HTML)" id="cuerpo_mensaje" name="cuerpo_mensaje" required="required"></textarea>
		</div>
		<div style="float: left; width: 200; font-size: 12px; padding: 30px 10px;">Variables:<br><span style="color: #999;">{{email}}<br>{{nombre}}<br>{{apellidos}}<br>{{aux1}}<br>{{aux2}}<br>{{aux3}})</span></div>
		<div style="clear: both;"></div>
	  </div>
	  <div style="text-align: center; margin: 30px auto;"><button type="submit" class="btn btn-success" style="margin: auto; padding: 15px; font-size: 1.3em;">PROCEDER AL ENVÍO</button></div>
</div>
</form>


<?php 
	}
?>
	
  </body>
</html>