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
	<h1>Cancelar taquilla</h1>
</div><!-- /header -->

<?php
if(!defined("_TAQUILLAS"))
	die("No direct access allowed");
include("bbdd.php");

require_once("params.php");
$params = new TaquillasParams();

if($params->cancelaciones == false) die(aviso("Esta acción no está disponible"));

$paso = intval($_POST['paso']);

/**
/* Manejador de parámetros para ver en que estado de la renovación vamos
 */
if($paso == 0)
	renovacion_paso1();
else if($paso == 2)
	renovacion_paso2($_POST['numero']);
else if($paso == 4)
	comprobar_arrendatarios($_POST['numero'],$_POST['arrendatario']);
else if($paso == 5)
	terminar_cancelacion($_POST['numero'],$_POST['arrendatario']);
else
	renovacion_paso1();




/**
/* Paso uno: Formulario para introducir número de taquilla a renovar
 */

function renovacion_paso1(){

echo '<div data-role="content">
	<form action="index.php?pag=cancelacion" method="post">
		<div data-role="fieldcontain">
			<label for="numero">Número de taquilla:</label>
			<input type="number" name="numero" id="numero" value="" placeholder="Número de taquilla a cancelar"  />
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
	<form action="index.php?pag=cancelacion" method="post">
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

function confirmar_cancelacion($numero,$arrendatario){
	echo '<div data-role="content">
	<form action="index.php?pag=cancelacion" method="post">';
	echo '<div class="ui-body ui-body-e">
		  <p>¿Está seguro de que desea pedir cancelación de la taquilla <b>'.$numero.'</b>?</p>
		  <p>No obstante esto es únicamente una solicitud de querer cancelarla, si cambias de opinión no tienes más que ir a la sección de renovaciones</p>
		  </div><br />';
	echo '<input type="hidden" name="paso" id="paso" value="5" />
		<input type="hidden" name="numero" id="numero" value="'.$numero.'" />
		<input type="hidden" name="arrendatario" id="arrendatario" value="'.$arrendatario.'" />
		<input type="submit" name="comprobar" id="comprobar" value="Cancelar taquilla" />
	</form>
</div><!-- /content -->';

}

function comprobar_arrendatarios($numero,$arrendatario){
	if(isArrendatario($numero,$arrendatario)){
		confirmar_cancelacion($numero,$arrendatario);
	}else{
		echo "Datos de arrendatario no coinciden";
	}
}

function terminar_cancelacion($numero,$arrendatario){
	$cancelar = cancelarTaquilla($numero,$arrendatario);

	if($cancelar){

		$mensaje = "<p>Gracias por haber confirmado que no vas a seguir ocupando la taquilla <b>$numero</b> durante este curso<p>";
		$mensaje .= "<p>Te agradeceríamos que dejaras limpia lo antes posible la taquilla, de lo contrario la vaciaremos nosotros y todas tus pertenencias las guardaremos temporalmente en DAT</p>";
		$mensaje .= "<p>Puedes pasar a recoger la fianza de tu taquilla a partir del jueves 18 de septiembre hasta el lunes 29 de septiembre de 13h a 14h. Si fuera a venir alguien que no es arrendatario de la taquilla, por favor,
		haznos llegar una autorización indicando nombre, apellidos y número de identificación (DNI o pasaporte)impresa que traer la persona autorizada</p>";
		$mensaje .= "<br/><p>Muchas gracias, <br/> El equipo de taquillas</p>";

		mandarCorreos($numero,getCursoAnterior(),"[TAQUILLAS DAT] Cancelación de taquilla $numero",$mensaje);

		echo '<div data-role="content">';
		echo '<div class="ui-body ui-body-c">
			<p>Se ha enviado la solicitud de cancelación de la taquilla '.$numero.'</p>
			  </div><br />';
		echo '</div><!-- /content -->';
	}else{
		echo '<div data-role="content">';
		echo '<div class="ui-body ui-body-c">
				<p>Ha habido un error, ponte en contato con taquillas@dat.etsit.upm.es</p>
			  </div><br />';
		echo '</div><!-- /content -->';
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
