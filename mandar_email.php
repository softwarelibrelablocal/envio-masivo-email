<?php

session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/Exception.php';
require 'phpmailer/PHPMailer.php';
require 'phpmailer/SMTP.php';


if(!$_SESSION["sesion"]){
	die('No autorizado');
}


if($_SESSION["asunto"] == '' || $_SESSION["email_emisor"] == '' || $_SESSION["cuerpo_mensaje"] == '' ){
	die('Faltan datos.');
}

//si no esta hacemos la llamada al Api




$nombre = $_POST['nombre'];
$apellidos = $_POST['apellidos'];
$email = $_POST['email'];
$email =  sanitize_email($email);
$aux1 = $_POST['aux1'];
$aux2 = $_POST['aux2'];
$aux3 = $_POST['aux3'];

$comprobar = $_POST['comprobar'];

$pausa = 1;//pausa en segundos

//descomentar si se quiere comprobar que se ha mandado antes

if($comprobar == 1){
	//comprobamos que no está dado de alta en la api
	//$filename = "registro.txt";
	//$fh = fopen($filename, 'r');
	//$texto = fread($fh, filesize($filename));
	//fclose($fh);
	
	$texto = $_SESSION["registro"];

	$texto_buscar = ';' . $aux1 . ';';

	if (strpos($texto, $texto_buscar) !== false) {
		$pausa = 0.2;
		sleep($pausa);
		echo('[{"codigo":-1,"mensaje":"Ya se ha enviado ese codigo anteriormente"}]');
		die();
	}
}


sleep($pausa);



//Mandamos el correo


$mail = new PHPMailer(true);                              // Passing `true` enables exceptions
try {
    //Server settings
   // $mail->SMTPDebug = 2;                                 // Enable verbose debug output
    $mail->isSMTP();                                      // Set mailer to use SMTP
    $mail->Host = '10.0.0.0';  // Specify main ip or domain SMTP servers
    $mail->SMTPAuth = false;                               // Enable SMTP authentication
    $mail->Username = $_SESSION["email_emisor"];                 // SMTP username
    $mail->Password = '';                           // SMTP password
    //$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
    $mail->Port = 25;                                    // TCP port to connect to

    //Recipients
    $mail->setFrom($_SESSION["email_emisor"], 'AYUNTAMIENTO DE RIVAS VACIAMADRID');
    $mail->addAddress($email);     // Add a recipient
    $mail->addReplyTo($_SESSION["email_emisor"], 'AYUNTAMIENTO DE RIVAS VACIAMADRID');
    //$mail->addCC('ricardoalfarovega@gmail.com');
    //$mail->addBCC('brobles@rivasciudad.es');

    //Attachments
    //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
    //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
	
	$mail->CharSet = 'UTF-8';

    //Content
    $mail->isHTML(true);                                  // Set email format to HTML
    
	$asunto = $_SESSION["asunto"];
	$mail->Subject = $asunto;
   
	//Mensaje
	
	$mensaje_html = $_SESSION["cuerpo_mensaje"];
	
	$mensaje_html = str_replace('{{email}}',$email, $mensaje_html);
	$mensaje_html = str_replace('{{nombre}}',$nombre, $mensaje_html);
	$mensaje_html = str_replace('{{apellidos}}',$apellidos, $mensaje_html);
	$mensaje_html = str_replace('{{aux1}}',$aux1, $mensaje_html);
	$mensaje_html = str_replace('{{aux2}}',$aux2, $mensaje_html);
	$mensaje_html = str_replace('{{aux3}}',$aux3, $mensaje_html);
	
	//$mensaje_html = str_replace("\r","<br>",$mensaje_html);
	//$mensaje_html = str_replace("+b+","<b>",$mensaje_html);
	//$mensaje_html = str_replace("+/b+","</b>",$mensaje_html);
	//$mensaje_html = str_replace("+u+","<u>",$mensaje_html);
	//$mensaje_html = str_replace("+/u+","</u>",$mensaje_html);
	//$mensaje_html = str_replace("+i+","<i>",$mensaje_html);
	//$mensaje_html = str_replace("+/i+","</i>",$mensaje_html);
	
	$mail->Body    = $mensaje_html;
	//texto plano
	//$mail->AltBody = 'Hola ' . $nombre . ' ' . $apellidos . ' <b>Esto es una prueba</b><br><br><a href="http://www.rivasciudad.es?aux1=' . $aux1 . '>www.rivasciudad.es?codigo=' . $aux2 . '</a>';

    $mail->send();
    //echo 'Message has been sent';
	
	//Insertado correctamenta.
	$text = ';' . $aux1 . ';';
	
	
	
	//$filename = "registro.txt";
	//$fh = fopen($filename, "a");
	//fwrite($fh, $text);
	//fclose($fh);
	
	$_SESSION["registro"] .= $text;
		
	echo('[{"codigo":1,"mensaje":"Email enviado a: ' . $email . '"}]');
	
} catch (Exception $e) {
    //echo 'Message could not be sent.';
    //echo 'Mailer Error: ' . $mail->ErrorInfo;
	
	echo('[{"codigo":0,"mensaje":"Error: ' . $mail->ErrorInfo . '"}]');
}



// Fin mandar correo.




//echo('[{"codigo":1,"mensaje":"Ok pruebas"}]');

die();

function sanitize_email($email){
	
	$email = strtolower($email);
	$email = str_replace(';','',$email);
	$email = str_replace(' ','',$email);
	
	return $email;
}

?>