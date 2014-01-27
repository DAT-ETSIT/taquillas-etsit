<?php

/***********************************************************************************
/*
/* Sistema de taquillas ETSIT UPM
/* @author Pablo Moncada Isla pmoncadaisla@gmail.com
/* @version 09/2013
/*
/***********************************************************************************/

/* Required library */
require_once("params.php");
require_once("taquillas.functions.php");

switch ($_GET['i']) {
    case "renovacion":
		confirmar_renovacion($_GET['t'],$_GET['a']);
        break;
	case "nueva":
		confirmar_nueva($_GET['t'],$_GET['p']);
        break;
	case "cambio":
		confirmar_cambio($_GET['nu'],$_GET['an'],$_GET['a']);
   default:
		die("error");
}

function confirmar_renovacion($taquilla,$arrendatario){
	echo '<div data-role="dialog">	
		<div data-role="header" data-theme="d">
			<h1>¿Realizar renovación?</h1>
		</div>

		<div data-role="content" data-theme="c">
			
			<p><b>Recuerda:</b> Realizar el ingreso y traer el comprobante a DAT</p>
			<p>Recibirás un email de confirmación y las instrucciones por email</p>
			<form name="confirmar" method="post" action="index.php?pag=renovacion">
				<input type="hidden" name="taquilla" id="taquilla" value="'.$taquilla.'" />
				<input type="hidden" name="arrendatario" id="arrendatario" value="'.$arrendatario.'" />
				<input type="hidden" name="paso" id="paso" value="6" />
				<input type="submit" data-theme="b" value="Confirmar reserva" />
			</form>
			<a href="index.php" data-role="button" data-rel="back" data-theme="c">Cancel</a>    
		</div>
	</div>';

}

function confirmar_nueva($taquilla,$personasId){
	echo '<div data-role="dialog">	
		<div data-role="header" data-theme="d">
			<h1>¿Finalizar proceso de alquiler?</h1>
		</div>

		<div data-role="content" data-theme="c">
			
			<p><b>Recuerda:</b> Realizar el ingreso y traer el comprobante a DAT</p>
			<p>Recibirás un email de confirmación y las instrucciones por email</p>
			<form name="confirmar" method="post" action="index.php?pag=nueva">
				<input type="hidden" name="taquilla" id="taquilla" value="'.$taquilla.'" />
				<input type="hidden" name="personasId" id="personasId" value="'.$personasId.'" />
				<input type="hidden" name="paso" id="paso" value="6" />
				<input type="submit" data-theme="b" value="Confirmar reserva" />
			</form>
			<a href="index.php" data-role="button" data-rel="back" data-theme="c">Cancel</a>    
		</div>
	</div>';

}

function confirmar_cambio($nueva,$antigua,$arrendatario){
	echo '<div data-role="dialog">	
		<div data-role="header" data-theme="d">
			<h1>¿Finalizar proceso de alquiler?</h1>
		</div>

		<div data-role="content" data-theme="c">
			
			<p><b>Recuerda:</b> Realizar el ingreso y traer el comprobante a DAT</p>
			<p>Recibirás un email de confirmación y las instrucciones por email</p>
			<form name="confirmar" method="post" action="index.php?pag=cambio">
				<input type="hidden" name="nueva" id="nueva" value="'.$nueva.'" />
				<input type="hidden" name="antigua" id="antigua" value="'.$antigua.'" />
				<input type="hidden" name="arrendatario" id="arrendatario" value="'.$arrendatario.'" />
				<input type="hidden" name="paso" id="paso" value="8" />
				<input type="submit" data-theme="b" value="Confirmar reserva" />
			</form>
			<a href="index.php" data-role="button" data-rel="back" data-theme="c">Cancel</a>    
		</div>
	</div>';

}

?>