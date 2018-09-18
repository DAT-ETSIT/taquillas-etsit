<?php
// tell PHP to log errors to ipn_errors.log in this directory


/***********************************************************************************
/*
/* Sistema de taquillas ETSIT UPM
/* @author Pablo Moncada Isla pmoncadaisla@gmail.com
/* @version 09/2013
/*
/***********************************************************************************/


ini_set('log_errors', true);
ini_set('error_log', dirname(__FILE__).'/ipn_errors.log');



require_once("../bbdd.php");
require_once("../params.php");
require_once("../taquillas.functions.php");

$sandbox = false;


// intantiate the IPN listener
include('ipnlistener.php');
$listener = new IpnListener();

$params = new TaquillasParams($sandbox);

$type = $_GET['type'];


if($type == "renovacion"){
	$precio = $params->precioPaypalRenovacion;
}else if($type == "nueva"){
		$precio = $params->precioPaypalNueva;
		$personasId = intval($_GET['personasId']);
}else if($type == "cambio"){
		$precio = $params->precioPaypalCambio;
}else{
	error_log("ERROR. TIPO: $type");
	exit(0);
}
	

// tell the IPN listener to use the PayPal test sandbox
$listener->use_sandbox = $sandbox;

// try to process the IPN POST
try {
    $listener->requirePostMethod();
    $verified = $listener->processIpn();
} catch (Exception $e) {
    error_log($e->getMessage());
    exit(0);
}

if ($verified) {

    $errmsg = '';   // stores errors from fraud checks

    // 1. Make sure the payment status is "Completed"
    if ($_POST['payment_status'] != 'Completed') {
        // simply ignore any IPN that is not completed
		error_log("Pago no completado: ".$_POST['payment_status']. "Pending Reason: ".$_POST['pending_reason']);
        exit(0);
    }

    // 2. Make sure seller email matches your primary account email.
    if ($_POST['receiver_email'] != $params->paypalReceiverEmail) {
        $errmsg .= "'receiver_email' does not match: ";
        $errmsg .= $_POST['receiver_email']."\n";
		$errmsg .= "Should be:";
		$errmsg .= $params->paypalReveiverEmail."\n";
    }

    // 3. Make sure the amount(s) paid match
    if ($_POST['mc_gross'] != $precio) {
        $errmsg .= "'mc_gross' does not match: ";
        $errmsg .= $_POST['mc_gross']."\n";
    }

    // 4. Make sure the currency code matches
    if ($_POST['mc_currency'] != 'EUR') {
        $errmsg .= "'mc_currency' does not match: ";
        $errmsg .= $_POST['mc_currency']."\n";
    }
	
	

    // TODO: Check for duplicate txn_id

    if (!empty($errmsg)) {

        // manually investigate errors from the fraud checking
        $body = "IPN failed fraud checks: \n$errmsg\n\n";
        $body .= $listener->getTextReport();
        mail('taquillas@dat.etsit.upm.es', 'IPN Fraud Warning', $body);

    } else {
		//mail('taquillas@dat.etsit.upm.es', 'Valid IPN', $listener->getTextReport());
		
		if($type == "renovacion"){
			error_log("TIPO: $type");
			$arrendatario = $_GET['arrendatario'];
			$taquilla = $_POST['item_number'];
			$renovar = renovarPaypal($_POST['txn_id'],$taquilla,$arrendatario);			
		}
		else if($type == "cambio"){
			error_log("TIPO: $type");
			$arrendatario = $_GET['arrendatario'];
			$taquilla = $_POST['item_number'];			
			$antigua = intval($_GET['an']);
			$cambiar = cambiarPaypal($_POST['txn_id'],$taquilla,$antigua,$arrendatario);
		}else if($type == "nueva"){
			error_log("TIPO: $type");
			$personasId = intval($_GET['personasId']);
			$taquilla = $_POST['item_number'];
			$nueva = nuevaPaypal($_POST['txn_id'],$taquilla,$personasId);
		}else{
			error_log("Ningun tipo");
		}
		if($renovar){
			mail('taquillas@dat.etsit.upm.es', 'Valid IPN', $listener->getTextReport());
			mandarCorreos($taquilla,getCursoActual(),"Taquilla $taquilla renovada",$params->mensajeRenovada($taquilla));
		}else if($nueva){
			mail('taquillas@dat.etsit.upm.es', 'Valid IPN', $listener->getTextReport());
			mandarCorreos($taquilla,getCursoActual(),"Taquilla $taquilla alquilada","Tu taquilla $taquilla ha sido alquilada y pagada con PayPal");
		}
		else if($cambiar){
			mail('taquillas@dat.etsit.upm.es', 'Valid IPN', $listener->getTextReport());
			mandarCorreos($taquilla,getCursoActual(),"Taquilla $nueva cambiada","Tu antigua taquilla $antigua ha sido cambiada por la $taquilla y pagada con PayPal");
		}
		else{
			mail('taquillas@dat.etsit.upm.es', 'Problema al renovar', $listener->getTextReport());
		}
        // TODO: process order here
    }

} else {
    // manually investigate the invalid IPN
    mail('taquillas@dat.etsit.upm.es', 'Invalid IPN', $listener->getTextReport());
}



?>
