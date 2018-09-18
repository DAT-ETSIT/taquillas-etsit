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
	<h1>Nuevas taquillas</h1>
</div><!-- /header -->

<?php

include("bbdd.php");
if(!defined("_TAQUILLAS"))
	die("No direct access allowed");

require_once("params.php");
$params = new TaquillasParams();

if($params->nuevas == false) die("Esta acción no está disponible");

$paso = intval($_POST['paso']);

/**
/* Manejador de parámetros para ver en que estado de la renovación vamos
 */
if($paso == 0)
	seleccionar_taquilla();
else if($paso == 2)
	formulario_arrendatarios($_POST['taquilla']);
else if($paso == 3)
	comprobar_datos($_POST);
else if($paso == 4)
	muestraPago($_POST['taquilla'], $_POST['personas']);
else if($paso == 5)
	nueva_pagar($_POST['taquilla'],$_POST['personasId'],$_POST['formapago']);
else if($paso == 6)
	nueva_ingreso($_POST['taquilla'],$_POST['personasId']);
else
	renovacion_paso1();




/**
/* Paso uno: Formulario para introducir número de taquilla a renovar
 */

function seleccionar_taquilla(){

	$taquillas = get_taquillas_alquilables(getCursoActual());

	$zonas = getZonas();

	echo '<div data-role="content">';
	echo '<div class="ui-body ui-body-e">
				<p>A continuación se muestran las taquillas aún disponibles.
				Una vez seleccionada dispondrá de 30 minutos para terminar la reserva durante el cual se le reservará el número de taquilla</p>
			</div>';
	echo '<form action="index.php?pag=nueva" method="post">';
	echo '<div data-role="fieldcontain">';
	echo '<label for="taquilla">Número de taquilla:</label><select name="taquilla" id="taquilla">';
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
			<input type="hidden" name="paso" id="paso" value="2" />
			<div><input type="submit" name="comprobar" id="comprobar" value="Continuar" /></div>

		</form>
	</div><!-- /content -->';
}

/**
/* Paso dos: Formulario para introducir datos de arrendatarios
 */
function formulario_arrendatarios($taquilla){
	$taquilla = intval($taquilla);
	if($taquilla == 0)
		die(aviso("Por favor, selecciona un número de taquilla"));
	bloquearTaquilla($taquilla);
	echo '<div data-role="content">';
	echo '<div class="ui-body ui-body-e">
				<p>Se pueden incluir hasta un máximo de 4 arrendatarios para cada taquilla. Cada uno de ellos tendrá autorización para poder pedir una llave pidiéndola en DAT</p>
				<p><strong>Obligatorio al menos uno</strong></p>
		  </div><br />';
	echo '<form action="index.php?pag=nueva" method="post">';
	for($i = 1; $i <= 4; $i++){
	echo '<h2 class="ui-title" role="heading" aria-level="1">Arrendatario '.$i.'</h2>';
	echo '<div class="ui-body ui-body-a">';
		echo '<div data-role="fieldcontain">';
		echo '<label for="nombre'.$i.'">Nombre</label><input type="text" name="nombre'.$i.'" id="nombre'.$i.'" /> ';
		echo '</div>';
		echo '<div data-role="fieldcontain">';
		echo '<label for="apellidos'.$i.'">Apellidos</label><input type="text" name="apellidos'.$i.'" id="apellidos'.$i.'" /> ';
		echo '</div>';
		echo '<div data-role="fieldcontain">';
		echo '<label for="dni'.$i.'">Dni (sólo números)</label><input type="number" name="dni'.$i.'" id="dni'.$i.'" /> ';
		echo '</div>';
		echo '<div data-role="fieldcontain">';
		echo '<label for="email'.$i.'">Email</label><input type="email" name="email'.$i.'" id="email'.$i.'" /> ';
		echo '</div>';
		echo '<div data-role="fieldcontain">';
		echo '<label for="telefono'.$i.'">Telefono</label><input type="number" name="telefono'.$i.'" id="telefono'.$i.'" /> ';
		echo '</div>';
	echo '</div>';

	}
	echo '<div class="ui-body ui-body-e">
				<p>Pulsa el <strong>botón continuar</strong> para llegar al siguiente paso</p>
		  </div>';
	echo '	<input type="hidden" name="paso" id="paso" value="3" />
			<input type="hidden" name="taquilla" id="taquilla" value="'.$taquilla.'" />
			<div><input type="submit" name="comprobar" id="comprobar" value="Continuar" /></div>

		</form>
	</div><!-- /content -->';
}

/**
/* Paso tres: Comprobar datos de arrendatarios
 */

function comprobar_datos($post){
	$personas = array();
	$errmsg = "";
	$taquilla = intval($post['taquilla']);
	for($i = 1; $i <= 4; $i++){
		//Comprobamos que todos los campos estan rellenos
		if(trim($post["nombre$i"]) != "" && trim($post["apellidos$i"]) != "" && intval($post["dni$i"]) != "" && trim($post["email$i"]) != "" && intval($post["telefono$i"]) != ""){
			$personas[$i-1]['nombre'] = $post["nombre$i"];
			$personas[$i-1]['apellidos'] = $post["apellidos$i"];
			$personas[$i-1]['dni'] = $post["dni$i"];
			$personas[$i-1]['email'] = $post["email$i"];
			$personas[$i-1]['telefono'] = $post["telefono$i"];
		}
	}
	if(count($personas[0]) == 0)
		$errmsg .= "Debes de introducir al menos los datos completos de 1 persona<br/>";

	if(trim($errmsg) == ""){
		echo '<h2>¿Son estos datos correctos?</h2>';
		echo '<ul data-role="listview" data-inset="true">';
		foreach ($personas as $persona){
			echo "<li><h3>".$persona['nombre']." ".$persona['apellidos']."</h3>";
			echo "<p><b>Email</b> ".$persona['email']."</p>";
			echo "<p><b>Teléfono</b> ".$persona['telefono']."</p>";
			echo "<p><b>Dni</b> ".$persona['dni']."</p>";
			echo "</li>";
		}
		echo '</ul>';

		$spersonas = base64_encode(json_encode($personas));
		echo '<form name="siguiente" action="index.php?pag=nueva" method="post">';
		echo '<input type="hidden" name="personas" value="'.$spersonas.'" id="personas" />';
		echo '<input type="hidden" name="taquilla" id="taquilla" value="'.$_POST['taquilla'].'" />';
		echo '<input type="hidden" name="paso" id="paso" value="4" />';
		echo '<div class="ui-body ui-body-b">
		<fieldset class="ui-grid-a">
				<div class="ui-block-a"><a href="index.php?pag=nueva"><button>Cancelar</button></a></div>
				<div class="ui-block-b"><input type="submit" value="Si, continuar al pago" /></div>
	    </fieldset>
		</div>';
		echo '</form>';

	}

}

/**
/* Muestra las formas de pago disponibles
/* @params $numero Numero de la taquilla a renovar
/* @params $importe Importe de la renovación
/* @params $arrendatario DNI o email de alguno de los arrendatarios de la taquilla
*/
function muestraPago($taquilla, $spersonas){
	require_once("params.php");
	$params = new TaquillasParams();

	$importe = $params->precioNueva;

	$personasId = insert_json_personas($spersonas,$taquilla);


	//$personas = json_decode(base64_decode($spersonas));


	echo '<div data-role="content">
	<h1>Importe: '.$importe.' EUR</h1>
	<h3>6.5 EUR de alquiler + 2.5 EUR cerradura + 9 EUR fianza*</h3>
	<p>*La fianza se devolverá al cancelar la taquilla el año que ya no quiera usarse</p>

	<form action="index.php?pag=nueva" method="post">
		<fieldset data-role="controlgroup">
	<legend>Seleccionar forma de pago:</legend>
     	<input type="radio" name="formapago" id="online" value="online" checked="checked" />
     	<label for="online">On-line</label>

     	<input type="radio" name="formapago" id="ingreso" value="ingreso"  />
     	<label for="ingreso">Ingreso bancario</label>
	</fieldset>
	<input type="hidden" name="paso" id="paso" value="5" />

	<input type="hidden" name="taquilla" id="taquilla" value="'.$taquilla.'" />
	<input type="hidden" name="importe" id="importe" value="'.$importe.'" />
	<input type="hidden" name="personasId" id="personasId" value="'.$personasId.'" />
	<input type="submit" value="Pagar taquilla'.$numero.'" name="finalizar" />
	</form>
</div><!-- /content -->';

}

/**
/* Paso 5: Comprobar algún dato de arrendatario, formulario
/* @params $numero Numero de la taquilla a renovar
/* @params $importe Importe de la renovación
/* @params $arrendatario DNI o email de alguno de los arrendatarios de la taquilla
*/

function nueva_pagar($numero,$personasId,$online){
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
    <input type="hidden" name="item_name" value="Nueva">
	<input type="hidden" name="item_number" value="'.$numero.'">
    <input type="hidden" name="amount" value="'.$params->precioPaypalNueva.'">
    <input type="hidden" name="return" value="https://dat.etsit.upm.es/taquillasapp/paypal/ok.php">
    <input type="hidden" name="notify_url" value="https://dat.etsit.upm.es/taquillasapp/paypal/paypal.php?type=nueva&personasId='.$personasId.'">
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
				<a data-role="button" href="confirmar_ingreso.php?i=nueva&t='.$numero.'&p='.$personasId.'" data-rel="dialog" data-transition="pop">Finalizar proceso</a>
			</div>

		</div>';


	}

}

function nueva_ingreso($taquilla,$personasId){
	$taquilla = intval($taquilla);
	$personasId = intval($personasId);
	require_once("params.php");
	$params = new TaquillasParams();

	if(nuevaIngreso($taquilla,$personasId)){
		$operacion = getOperacion($taquilla,getCursoActual());
		$mensaje = $params->instruccionesIngreso."<h2>Importe a ingresar: ".$params->precioNueva." EUR</h2>
		<h2>Número de operación: $operacion</h2><h2>Número de taquilla (indicar en el ingreso): $taquilla</h2>";

		mandarCorreos($taquilla,getCursoActual(),"[TAQUILLAS DAT] Reserva de alquiler de taquilla $taquilla",$mensaje);
		echo '<div data-role="content">
			<div data-role="header" data-theme="b" class="ui-header ui-bar-b" role="banner">
				<h1 class="ui-title" role="heading" aria-level="1">Reserva realizada</h1>
			</div>

			<div class="ui-body ui-body-d">
				<p>Tu reserva para la taquilla '.$taquilla.' ha quedado realizada</p>
				<p>Para terminar el proceso de alquiler realiza el ingreso de acuerdo con las instrucciones que recibirás por email</p>
			</div>

		</div>';

	}else{
		echo "Error, ponte en contacto con taquillas@dat.etsit.upm.es";
	}

}

?>
