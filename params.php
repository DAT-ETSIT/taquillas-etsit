<?php


class TaquillasParams{

	// Precio en Euros para compra por PayPal
	var $precioPaypalRenovacion = 6.5;
	var $precioPaypalCambio = 9;
	var $precioPaypalNueva = 18;

	var $paypalReceiverEmail;
	var $paypalUrl;

	var $precioRenovacion = 6.5;
	var $precioCambio = 9;
	var $precioNueva = 18;

	//No permitir varias acciones al mismo tiempo
	var $renovaciones = true;
	var $cancelaciones = false;
	var $cambios = true;
	var $nuevas = true;
	
	var $cursoActual = 2013;
	var $cursoAnterior = 2012;
	
	// Tienen permisos para pagar taquillas
	var $administradores = array("admin");
	
	
	var $sandbox;


	//Pago cuenta bancaria
	var $cco = "2038-1859-59-6003850935";


	var $instruccionesIngreso = "<p>No has finalizado el proceso,	únicamente has reservado tu taquilla</p>
	<p>Para finalizarlo deberás:</p>
	<ul>
	<li>Realizar un ingreso en cualquier sucursal de <b>Bankia</b>, a nombre de <b>Delegación de Alumnos UPM ETSI de Telecomunicación</b>, en la cuenta <b>2038-1859-59-6003850935</b></li>
	<li>Traer el comprobante (original) a DAT <b>del 2 al 10 de octubre</b> junto con este email impreso (El <b>comprobante del banco grapado detrás</b>)</li>
	<li><b>No se aceptan transferencias on-line</b>. Para eso usar la plataforma de pago online.</li>
	</ul>";



	function TaquillasParams($sandbox = false){
		$this->sandbox = false;
		if($this->sandbox){
			$this->paypalReceiverEmail = "receiver@paypal.com";
			$this->paypalUrl = "https://www.sandbox.paypal.com/cgi-bin/webscr";		
		}else{
			$this->paypalReceiverEmail = "receiver@paypal.com";
			$this->paypalUrl = "https://www.paypal.com/cgi-bin/webscr";
		}

	}

	function mensajeRenovada($taquilla){
		$mensaje = "<p>Hola, <br/> Muchas gracias por haber renovado la taquilla <b>$taquilla</b> mediante paypal.</p>";
		$mensaje .= "<p>Puedes imprimir este correo como justificante al igual que el de paypal (pero no es necesario que nos lo traigas).</p>";
		$mensaje .= "<p>Si tienes alguna duda, ponte en contacto con nosotros en taquillas@dat.etsit.upm.es</p><p>El equipo de taquillas</p>";
		return $mensaje;
	}

	function tienePermisos($admin){
		return in_array($admin,$this->administradores);
	}

}

?>
