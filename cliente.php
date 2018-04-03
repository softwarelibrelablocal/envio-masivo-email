<?php

session_start();


if(!$_SESSION["sesion"]){
	die('No autorizado');
}

$_SESSION["asunto"] =  $_POST['asunto'];
$_SESSION["email_emisor"] = $_POST['email_emisor'];
$_SESSION["cuerpo_mensaje"] = $_POST['cuerpo_mensaje'];

if($_SESSION["asunto"] == '' || $_SESSION["email_emisor"] == '' || $_SESSION["cuerpo_mensaje"] == '' ){
	die('Faltan datos. Pulse volver del navegador');
}



?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Procesar Lista</title>
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

		//objtenemos los datos
		
		obtener_datos();
		
		//listado.forEach(function(elemento) {
			//console.log(elemento);
			//procesar_elemento(elemento);
		//});
		
		$( "#pausa" ).click(function() {
		  if(pausa == 0){
			  pausa = 1;
			  $('#pausa').html('Reanudar');
		  }else{
			  pausa = 0;
			  $('#pausa').html('Pausar');
			  procesar_elemento();
		  }
		});
		
		
		
	});
	
	function procesar_elemento(){
		//console.log('Vamos a procesar otro elemento');
		

		//console.log(obj_datos.0.nombre);
		var total_elementos = Object.keys(obj_datos).length;
		if(elemento_actual >= total_elementos ){
			alert('Proceso finalizado!!! Elemento actual = ' + elemento_actual);
		}
		
		if(pausa == 0){
			$("#contenedor_datos").scrollTo($("#fila_" + (parseInt(elemento_actual)-1)), 400);
			$('#fila_resultado_' + elemento_actual).html('Procesando...');			
			drawszlider(total_elementos, elemento_actual);
			$('#progreso_numeros').html('Procesados: <b>' + elemento_actual + '</b> de ' + total_elementos);
			ajax_api('ayto17',obj_datos[elemento_actual][1],obj_datos[elemento_actual][2],obj_datos[elemento_actual][0],obj_datos[elemento_actual][3],obj_datos[elemento_actual][4],obj_datos[elemento_actual][5]);
		}

		//eval("ajax_api('ayto17',obj_datos." + elemento_actual + ".nombre,obj_datos." + elemento_actual + ".apellido1 + ' ' + obj_datos." + elemento_actual + ".apellido2,obj_datos." + elemento_actual + ".rfc,obj_datos." + elemento_actual + ".curp,obj_datos." + elemento_actual + ".email,obj_datos." + elemento_actual + ".telefono);");
	}

	
	function ajax_api(password,nombre,apellidos,email,aux1,aux2,aux3){	
		estatus_ajax = 1;
		//alert(accion);
		var formURL = 'http://intranet/temp/enviomail/mandar_email.php';
		//var password = '';
		//var nombre = '';
		//var apellidos = '';
		//var rfc = '';
		//var curp = '';
		//var correo = '';
		//var telefono = '';
		
		$.ajax({
			url : formURL,
			type: "POST",
			dataType : "json",
			data: { password: password,comprobar:1,nombre: nombre,apellidos: apellidos,email: email, aux1: aux1, aux2: aux2, aux3: aux3},
			success:function(data, textStatus, jqXHR)
			{
				
				
				console.log('Elemento actual : ' + elemento_actual);
				
				$('#fila_resultado_' + elemento_actual).html(JSON.stringify(data));
				
				codigo = data[0].codigo;
				
				if(codigo == 1){
					$("#fila_" + elemento_actual).addClass('tr_active');
				}else{
					$("#fila_" + elemento_actual).addClass('tr_error');
				}
				
				//$("#fila_resultado_" + elemento_actual)val(JSON.stringify(data));
				
				elemento_actual = parseInt(elemento_actual) + 1;
				
				procesar_elemento();
				
			},
			error: function(jqXHR, textStatus, errorThrown)
			{
				//if fails 
				alert("No se pudo procesar el elemento " + elemento_actual);
				$('#fila_resultado_' + elemento_actual).html('[{"codigo":0,"mensaje":"Error de ajax"}]');
				procesar_elemento();
			}
		});
		
		//e.preventDefault(); //STOP default action
		//e.unbind(); //unbind. to stop multiple form submit.
		
	}

	
	function obtener_datos(){	
		var formURL = 'http://intranet/temp/enviomail/datos.php';
		$.ajax({
			url : formURL,
			type: "POST",
			dataType : "json",
			data: {},
			success:function(data, textStatus, jqXHR)
			{
				obj_datos = data;
				//alert(obj_datos[0].nombre);
				var numero_datos = Object.keys(obj_datos).length;
				console.log('Resultados obtenidos: ' + numero_datos);
				pintar_datos();
				alert('Numero de elementos: ' + numero_datos + '. Empezar proceso');
				procesar_elemento();
					
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
		for(i=0;i<Object.keys(obj_datos).length;i++){
						
			$('#tabla_datos > tbody:last-child').append('<tr id="fila_' + i + '"><td>' + obj_datos[i][1] + '</td><td>' + obj_datos[i][2] + '</td><td>' + obj_datos[i][0] + '</td><td>' + obj_datos[i][3] + '</td><td>' + obj_datos[i][4] + '</td><td>' + obj_datos[i][5] + '</td><td id="fila_resultado_' + i + '"></td></tr>');
			
		}
	}
	
	function drawszlider(ossz, meik){
		var szazalek=Math.round((meik*100)/ossz);
		document.getElementById("szliderbar").style.width=szazalek+'%';
		document.getElementById("szazalek").innerHTML=szazalek+'%';
	}

	
	</script>

<?php
	}
?>
  </head>
  <body>
    <!-- page content -->
<?php
	if(!$_SESSION["sesion"]){
		header("Location: index.php");
		die();
	}else{
?>
<div id="contenedor_global" style="margin: 50px;">
	<button id="pausa">Pausar</button>

	<div style="width:80%; text-align: center;" id="progreso_numeros">
	
	</div>
	<div id="szlider">
		<div id="szliderbar">
    </div>
	<div id="szazalek">
		</div>
	</div>

	<div id="contenedor_datos" style="width:80%; height: 500px; overflow-y:scroll; overflow-x:hide; border: 1px solid #ccc;">
		<table class="table table-striped" id="tabla_datos">
			<tr> 
				<th>Nombre</th>
				<th>Apellidos</th>
				<th>Email</th>
				<th>Aux1</th>
				<th>Aux2</th>
				<th>Aux3</th>
				<th>Resultado</th>
			</tr>
		</table>
	</div>

</div>

<?php 
	}
?>
	
  </body>
</html>