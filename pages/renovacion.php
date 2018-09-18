<?php

/***********************************************************************************
/*
/* Sistema de taquillas ETSIT UPM
/* @author Pablo Moncada Isla pmoncadaisla@gmail.com
/* @version 09/2013
/*
/***********************************************************************************/

?>
<div data-role="header">
	<h1>Renovacion de taquillas</h1>
</div><!-- /header -->

<?php
if(!defined("_TAQUILLAS"))
	die("No direct access allowed");
include("bbdd.php");

require_once("params.php");
$params = new TaquillasParams();

if($params->renovaciones == false) die("Esta acción no está disponible");

$paso = intval($_POST['paso']);

/**
/* Manejador de parámetros para ver en que estado de la renovación vamos
 */
if($paso == 0)
	renovacion_paso1();
else if($paso == 2)
	renovacion_paso2($_POST['numero']);
else if($paso == 4)
	renovacion_paso4($_POST['numero'],$_POST['arrendatario']);
else if($paso == 5)
	renovacion_paso5($_POST['numero'],$_POST['importe'],$_POST['arrendatario'],$_POST['formapago']);
else if($paso == 6)
	renovacion_ingreso($_POST['taquilla'],$_POST['arrendatario']);
else
	renovacion_paso1();




/**
/* Paso uno: Formulario para introducir número de taquilla a renovar
 */

function renovacion_paso1(){

echo '<div data-role="content">
	<form action="index.php?pag=renovacion" method="post">
		<div data-role="fieldcontain">
			<label for="numero">Número de taquilla:</label>
			<input type="number" name="numero" id="numero" value="" placeholder="Número de taquilla a renovar"  />
		</div>
		<input type="hidden" name="paso" id="paso" value="2" />
		<input type="submit" name="comprobar" id="comprobar" value="Comprobar taquilla" />

	</form>
</div><!-- /content -->';

}

/**
/* Paso dos: Comprueba que la taquilla esté alquilada actualmente
/* if TRUE: Pasa a pedir datos de arrendatarios (email o DNI) para comprobar que es suya
/* else: Avisa de que la taquilla no se encuentra alquilada
/* @params $numero Numero de la taquilla a renovar
*/

function renovacion_paso2($numero){
	$numero = intval($numero);
	if(!isTaquillaAlquilada($numero))
		taquillaNoAlquilada($numero);
	else if(isTaquillaRenovada($numero))
		taquillaYaRenovada($numero);
	else
		renovacion_paso3($numero);
}

/**
/* Paso 3: Comprobar algún dato de arrendatario, formulario
/* @params $numero Numero de la taquilla a renovar
*/
function renovacion_paso3($numero){
	echo '<div data-role="content">
	<form action="index.php?pag=renovacion" method="post">
		<div data-role="fieldcontain">
			<label for="numero">Introducir email o DNI(solo numeros) de alguno de los arrendatarios</label>
			<input type="text" name="arrendatario" id="arrendatario" value="" placeholder="E-mail o DNI"  />
		</div>
		<input type="hidden" name="paso" id="paso" value="4" />
		<input type="hidden" name="numero" id="numero" value="'.$numero.'" />
		<input type="submit" name="comprobar" id="comprobar" value="Comprobar arrendatarios" />

	</form>
</div><!-- /content -->';

}
/**
/* Paso 3: Comprobar algún dato de arrendatario, formulario
/* @params $numero Numero de la taquilla a renovar
/* @params $arrendatario DNI o email de alguno de los arrendatarios de la taquilla
*/
function renovacion_paso4($numero,$arrendatario){
	if(isArrendatario($numero,$arrendatario)){
		muestraPago($numero,9,$arrendatario);
	}else{
		echo "Datos de arrendatario no coinciden";
	}
}
/**
/* Paso 5: Comprobar algún dato de arrendatario, formulario
/* @params $numero Numero de la taquilla a renovar
/* @params $importe Importe de la renovación
/* @params $arrendatario DNI o email de alguno de los arrendatarios de la taquilla
*/

function renovacion_paso5($numero,$importe,$arrendatario,$online){
	require_once("params.php");

	$params = new TaquillasParams();

	if($online == "online")
		$online = true;
	else
		$online = false;

	if($online){
		require_once("logos.php");
		aviso("Ahora vas a visitar el sitio de Paypal, donde puedes pagar con tu <b>cuenta de paypal</b> o con tu <b>tarjeta de crédito</b>. <br/> No es necesario que tengas cuenta de paypal, simpemente pulsa en \"¿No dispone de una cuenta paypal?\".");
		paypal_logo();
		echo '
		<div data-role="content">
		<form name="_xclick" action="'.$params->paypalUrl.'"
    method="post">
    <input type="hidden" name="cmd" value="_xclick">
    <input type="hidden" name="business" value="'.$params->paypalReceiverEmail.'">
    <input type="hidden" name="currency_code" value="EUR">
    <input type="hidden" name="item_name" value="Renovacion">
	<input type="hidden" name="item_number" value="'.$numero.'">
    <input type="hidden" name="amount" value="'.$params->precioPaypalRenovacion.'">
    <input type="hidden" name="return" value="https://dat.etsit.upm.es/taquillasapp//paypal/ok.php">
    <input type="hidden" name="notify_url" value="https://dat.etsit.upm.es/taquillasapp/paypal/paypal.php?type=renovacion&arrendatario='.$arrendatario.'">
    <input type="submit" value="Pagar ahora con PayPal"  border="0" name="submit" alt="Make payments with PayPal">
</form></div>';
	aviso("Imagen de muestra del sitio de Paypal");
	echo '<center><img src="images/paypal.png" /></center>';
	}else{
		echo '<div data-role="content">
			<div data-role="header" data-theme="e" class="ui-header ui-bar-e" role="banner">
				<h1 class="ui-title" role="heading" aria-level="1">Indicaciones para pago con ingreso en cuenta bancaria</h1>
			</div>

			<div class="ui-body ui-body-e">
				<ul>
					<li>Se ha de hacer un ingreso en la cuenta '.$params->cco.'</li>
					<li>Indicar en el asunto el número de la taquilla</li>
					<li> Se deberá traer el comprobante original (no fotocopia) a DAT en el plazo acordado</li>
					<li>Su taquilla quedará reservada, pero si no trae el resguardo a tiempo perderá la reserva</li>
				</ul>
				<a data-role="button" href="confirmar_ingreso.php?i=renovacion&t='.$numero.'&a='.$arrendatario.'" data-rel="dialog" data-transition="pop">Finalizar proceso</a>
			</div>

		</div>';


	}

}
/**
/* Muestra las formas de pago disponibles
/* @params $numero Numero de la taquilla a renovar
/* @params $importe Importe de la renovación
/* @params $arrendatario DNI o email de alguno de los arrendatarios de la taquilla
*/
function muestraPago($numero,$importe,$arrendatario){

	require_once("params.php");
	$params = new TaquillasParams();
	$importe = $params->precioRenovacion;

	echo '<div data-role="content">
	<h1>Importe: '.$importe.' EUR</h1>

	<form action="index.php?pag=renovacion" method="post">
		<fieldset data-role="controlgroup">
	<legend>Seleccionar forma de pago:</legend>
     	<input type="radio" name="formapago" id="online" value="online" checked="checked" />
     	<label for="online">On-line</label>

     	<input type="radio" name="formapago" id="ingreso" value="ingreso"  />
     	<label for="ingreso">Ingreso bancario</label>
	</fieldset>
	<input type="hidden" name="paso" id="paso" value="5" />

	<input type="hidden" name="numero" id="numero" value="'.$numero.'" />
	<input type="hidden" name="importe" id="importe" value="'.$importe.'" />
	<input type="hidden" name="arrendatario" id="arrendatario" value="'.$arrendatario.'" />
	<input type="submit" value="Renovar taquilla '.$numero.'" name="finalizar" />
	</form>
</div><!-- /content -->';

}

function renovacion_ingreso($taquilla,$arrendatario){
	$taquilla = intval($taquilla);
	require_once("params.php");
	$params = new TaquillasParams();

	if(renovarIngreso($taquilla,$arrendatario)){
		$operacion = getOperacion($taquilla,getCursoActual());
		$mensaje = $params->instruccionesIngreso."<h2>Importe a ingresar: ".$params->precioRenovacion." EUR</h2>
		<h2>Número de operación: $operacion</h2><h2>Número de taquilla (indicar en el ingreso): $taquilla</h2>";

		mandarCorreos($taquilla,getCursoActual(),"[TAQUILLAS DAT] Reserva de renovación de taquilla $taquilla",$mensaje);
		echo '<div data-role="content">
			<div data-role="header" data-theme="b" class="ui-header ui-bar-b" role="banner">
				<h1 class="ui-title" role="heading" aria-level="1">Reserva realizada</h1>
			</div>

			<div class="ui-body ui-body-d">
				<p>Tu reserva para la taquilla '.$taquilla.' ha quedado realizada</p>
				<p>Para terminar el proceso de renovación realiza el ingreso de acuerdo con las instrucciones que recibirás por email</p>
			</div>

		</div>';

	}else{
		aviso("Error, ponte en contacto con taquillas@dat.etsit.upm.es");
	}

}

/**
/* Mensaje de que la taquilla no se encuentra alquilada
*/
function taquillaNoAlquilada($numero){
	aviso("La taquilla no se encuentra alquilada");
}

/**
/* Mensaje de que la taquilla no se encuentra alquilada
*/
function taquillaYaRenovada($numero){
	aviso("La taquilla <b>$numero</b> ya se ha renovado para este curso");
}

?>
