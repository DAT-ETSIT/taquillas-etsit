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
	<h1>Cambio de taquillas</h1>
</div><!-- /header -->

<?php
if(!defined("_TAQUILLAS"))
	die("No direct access allowed");
include("bbdd.php");

require_once("params.php");
$params = new TaquillasParams();

if($params->cambios == false) die("Esta acción no está disponible");

$paso = intval($_POST['paso']);

/**
/* Manejador de parámetros para ver en que estado de la renovación vamos 
 */
if($paso == 0)
	renovacion_paso1();
else if($paso == 2)
	renovacion_paso2($_POST['antigua']);
else if($paso == 4)
	renovacion_paso4($_POST['antigua'],$_POST['arrendatario']);
else if($paso == 5)
	comprobar_cambio($_POST['nueva'],$_POST['antigua'],$_POST['arrendatario']);	
else if($paso == 6)
	muestraPago($_POST['nueva'],$_POST['antigua'],$_POST['arrendatario']);
else if($paso == 7)
	renovacion_paso5($_POST['nueva'],$_POST['antigua'],$_POST['importe'],$_POST['arrendatario'],$_POST['formapago']);
else if($paso == 8)
	cambio_ingreso($_POST['nueva'],$_POST['antigua'],$_POST['arrendatario']);
else
	renovacion_paso1();
	
	
	
	
/**
/* Paso uno: Formulario para introducir número de taquilla a renovar
 */

function renovacion_paso1(){

echo '<div data-role="content">	
	<form action="index.php?pag=cambio" method="post">
		<div data-role="fieldcontain">
			<label for="antigua">Número de taquilla:</label>
			<input type="number" name="antigua" id="antigua" value="" placeholder="Número de taquilla vieja"  />
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
	else if(isTaquillaCambiada($numero,getCursoActual()))
		taquillaYaCambiada($numero);
	else
		renovacion_paso3($numero);
}

/**
/* Paso 3: Comprobar algún dato de arrendatario, formulario
/* @params $numero Numero de la taquilla a renovar
*/
function renovacion_paso3($antigua){
	echo '<div data-role="content">	
	<form action="index.php?pag=cambio" method="post">
		<div data-role="fieldcontain">
			<label for="arrendatario">Introducir email o DNI(solo numeros) de alguno de los arrendatarios</label>
			<input type="text" name="arrendatario" id="arrendatario" value="" placeholder="E-mail o DNI"  />
		</div>
		<input type="hidden" name="paso" id="paso" value="4" />
		<input type="hidden" name="antigua" id="antigua" value="'.$antigua.'" />
		<input type="submit" name="comprobar" id="comprobar" value="Comprobar arrendatarios" />
		
	</form>
</div><!-- /content -->';

}
/**
/* Paso 4: Comprobar algún dato de arrendatario, formulario
/* @params $numero Numero de la taquilla a renovar
/* @params $arrendatario DNI o email de alguno de los arrendatarios de la taquilla
*/
function renovacion_paso4($antigua,$arrendatario){
	if(isArrendatario($antigua,$arrendatario)){
		seleccionar_nueva_taquilla($antigua,$arrendatario);
	}else{
		echo "Datos de arrendatario no coinciden";
	}
}


function seleccionar_nueva_taquilla($antigua,$arrendatario){

	
	$zonas = getZonas();

	echo '<div data-role="content">';
	echo '<div class="ui-body ui-body-e">
				<p>A continuación se muestran las taquillas aún disponibles. 
				Una vez seleccionada dispondrá de 30 minutos para terminar la reserva durante el cual se le reservará el número de taquilla</p>
			</div>';
	echo '<form action="index.php?pag=cambio" method="post">';
	echo '<div data-role="fieldcontain">';
	echo '<label for="nueva">Número de taquilla:</label><select name="nueva" id="nueva">';
	echo '<option>-- SELECCIONAR TAQUILLA --</option>';
	foreach($zonas as $zona){
		$nombreZona = getNombreZona($zona);
		echo '<optgroup label="'.$nombreZona.'"></optgroup>';
		$taquillas = get_taquillas_alquilables(getCursoActual(),$zona);
		foreach($taquillas as $taquilla){
			echo '<option value="'.$taquilla['numero'].'">'.$taquilla['numero'].'</option>';
		}	
	}
	
	echo '</select>';
	echo '	</div>
			<input type="hidden" name="paso" id="paso" value="5" />
			<input type="hidden" name="antigua" id="antigua" value="'.$antigua.'" />
			<input type="hidden" name="arrendatario" id="arrendatario" value="'.$arrendatario.'" />
			<div><input type="submit" name="comprobar" id="comprobar" value="Continuar" /></div>
			
		</form>
	</div><!-- /content -->';
}

function comprobar_cambio($nueva,$antigua,$arrendatario){
	echo '<div data-role="content">';
	echo '<div class="ui-body ui-body-e">
				<p>¿Desea realmente cambiar su taquilla <b>'.$antigua.'</b> por la <b>'.$nueva.'</b>?</p>
			</div>';
	echo '<form action="index.php?pag=cambio" method="post">';
	
	echo '<input type="hidden" name="antigua" id="antigua" value="'.$antigua.'" />';
	echo '<input type="hidden" name="nueva" id="nueva" value="'.$nueva.'" />';
	echo '<input type="hidden" name="arrendatario" id="arrendatario" value="'.$arrendatario.'" />';
	
	echo '<div data-role="fieldcontain">';
	echo '	</div>
			<input type="hidden" name="paso" id="paso" value="6" />
			<div><input type="submit" name="comprobar" id="comprobar" value="Si" /></div>
			
		</form>
	</div><!-- /content -->';

}


/**
/* Paso 5: Comprobar algún dato de arrendatario, formulario
/* @params $numero Numero de la taquilla a renovar
/* @params $importe Importe de la renovación
/* @params $arrendatario DNI o email de alguno de los arrendatarios de la taquilla
*/
function renovacion_paso5($nueva,$antigua,$importe,$arrendatario,$online){
	require_once("params.php");
	
	$params = new TaquillasParams();
	
	if($online == "online")
		$online = true;
	else
		$online = false;
		
	if($online){
		echo '
		<div data-role="content">
		<form name="_xclick" action="'.$params->paypalUrl.'" 
    method="post">
    <input type="hidden" name="cmd" value="_xclick">
    <input type="hidden" name="business" value="'.$params->paypalReceiverEmail.'">
    <input type="hidden" name="currency_code" value="EUR">
    <input type="hidden" name="item_name" value="Cambio">
	<input type="hidden" name="item_number" value="'.$nueva.'">
    <input type="hidden" name="amount" value="'.$params->precioPaypalCambio.'">
    <input type="hidden" name="return" value="http://canival.dat.etsit.upm.es/~pmoncada/taquillas/paypal/ok.php">
    <input type="hidden" name="notify_url" value="http://canival.dat.etsit.upm.es/~pmoncada/taquillas/paypal/paypal.php?type=cambio&arrendatario='.$arrendatario.'&an='.$antigua.'">
    <input type="submit" value="Pagar ahora con PayPal"  border="0" name="submit" alt="Make payments with PayPal">
</form></div>';	
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
				<a data-role="button" href="confirmar_ingreso.php?i=cambio&nu='.$nueva.'&an='.$antigua.'&a='.$arrendatario.'" data-rel="dialog" data-transition="pop">Finalizar proceso</a>
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
function muestraPago($nueva,$antigua,$arrendatario){
	bloquearTaquilla($nueva);
	
	require_once("params.php");	
	$params = new TaquillasParams();
	
	$importe = $params->precioCambio;
	
	echo '<div data-role="content">	
	<h1>Importe: '.$importe.' EUR</h1>
	
	<form action="index.php?pag=cambio" method="post">
		<fieldset data-role="controlgroup">
	<legend>Seleccionar forma de pago:</legend>
     	<input type="radio" name="formapago" id="online" value="online" checked="checked" />
     	<label for="online">On-line</label>
		
     	<input type="radio" name="formapago" id="ingreso" value="ingreso"  />
     	<label for="ingreso">Ingreso bancario</label>    	
	</fieldset>
	<input type="hidden" name="paso" id="paso" value="7" />

	<input type="hidden" name="nueva" id="nueva" value="'.$nueva.'" />
	<input type="hidden" name="antigua" id="antigua" value="'.$antigua.'" />
	<input type="hidden" name="importe" id="importe" value="'.$importe.'" />
	<input type="hidden" name="arrendatario" id="arrendatario" value="'.$arrendatario.'" />
	<input type="submit" value="Cambiar a taquilla '.$nueva.'" name="finalizar" />
	</form>
</div><!-- /content -->';

}

function cambio_ingreso($nueva,$antigua,$arrendatario){
	$taquilla = intval($taquilla);
	require_once("params.php");
	$params = new TaquillasParams();
	
	if(cambioIngreso($nueva,$antigua,$arrendatario)){
		$operacion = getOperacion($nueva,getCursoActual());
		$mensaje = $params->instruccionesIngreso."<h2>Importe a ingresar: ".$params->precioCambio." EUR</h2>
		<h2>Número de operación: $operacion</h2><h2>Número de taquilla (indicar en el ingreso): $nueva</h2>";
		
		mandarCorreos($nueva,getCursoActual(),"[TAQUILLAS DAT] Reserva de renovación de taquilla $nueva",$mensaje);
		echo '<div data-role="content">
			<div data-role="header" data-theme="b" class="ui-header ui-bar-b" role="banner">
				<h1 class="ui-title" role="heading" aria-level="1">Reserva realizada</h1>
			</div>
			
			<div class="ui-body ui-body-d">
				<p>Tu reserva para la taquilla '.$nueva.' ha quedado realizada. La antigua taquilla número '.$antigua.' ha quedado libre.</p>
				<p>Para terminar el proceso de renovación realiza el ingreso de acuerdo con las instrucciones que recibirás por email</p>
			</div>

		</div>';
	
	}else{
		echo "Error, ponte en contacto con taquillas@dat.etsit.upm.es";
	}

}

/**
/* Mensaje de que la taquilla no se encuentra alquilada
*/
function taquillaNoAlquilada($numero){
	aviso("La taquilla $numero no es válida para cambio");
}

/**
/* Mensaje de que la taquilla no se encuentra alquilada
*/
function taquillaYaRenovada($numero){
	aviso("La taquilla $numero ya se ha renovado para este curso");
}

/**
/* Mensaje de que la taquilla ya esta cambiada
*/
function taquillaYaCambiada($numero){
	aviso("La taquilla $numero ya se ha cambiado para este curso");
}

?>
