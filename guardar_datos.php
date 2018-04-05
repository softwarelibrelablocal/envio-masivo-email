<?php

session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json;charset=utf-8");

$_SESSION["datos"] = '';

if(!$_SESSION["sesion"]){
	die('No autorizado');
}


$csv = $_POST['csv'];

//pasamos a array

$arrDatos = parse_csv (utf8_encode($csv), ";",true);

foreach($arrDatos as $key => $value){
	
	if (!array_key_exists('0', $arrDatos[$key])){
		$arrDatos[$key]['0'] = '';
	}
	
	if (!array_key_exists('1', $arrDatos[$key])){
		$arrDatos[$key]['1'] = '';
	}
	
	if (!array_key_exists('2', $arrDatos[$key])){
		$arrDatos[$key]['2'] = '';
	}
	
	if (!array_key_exists('3', $arrDatos[$key])){
		$arrDatos[$key]['3'] = '';
	}
	
	if (!array_key_exists('4', $arrDatos[$key])){
		$arrDatos[$key]['4'] = '';
	}
	
	if (!array_key_exists('5', $arrDatos[$key])){
		$arrDatos[$key]['5'] = '';
	}
}

//echo(json_encode($arrDatos));
$_SESSION["datos"] = json_encode($arrDatos);

//print_r($arrDatos);

echo($_SESSION["datos"]);

die();

function parse_csv ($csv_string, $delimiter = ",", $skip_empty_lines = true, $trim_fields = true)
{
    return array_map(
        function ($line) use ($delimiter, $trim_fields) {
            return array_map(
                function ($field) {
                    return str_replace('!!Q!!', '"', utf8_decode(urldecode($field)));
                },
                $trim_fields ? array_map('trim', explode($delimiter, $line)) : explode($delimiter, $line)
            );
        },
        preg_split(
            $skip_empty_lines ? ($trim_fields ? '/( *\R)+/s' : '/\R+/s') : '/\R/s',
            preg_replace_callback(
                '/"(.*?)"/s',
                function ($field) {
                    return urlencode(utf8_encode($field[1]));
                },
                $enc = preg_replace('/(?<!")""/', '!!Q!!', $csv_string)
            )
        )
    );
}

?>