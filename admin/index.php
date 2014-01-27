<?php $debug = false; ?>
<?php if($debug){ error_reporting(E_ALL); ini_set('display_errors', '1');} ?>
<?php require("../taquillas.functions.php"); ?>
<?php require("../params.php"); $params = new TaquillasParams(); ?>
<?php define("_TAQUILLAS","true"); ?>
<!DOCTYPE html> 
<html> 
	<head> 
	<title>Admin Taquillas</title> 
	<meta name="viewport" content="width=device-width, initial-scale=1"> 
	<link rel="stylesheet" href="http://code.jquery.com/mobile/1.1.1/jquery.mobile-1.1.1.min.css" />
	<link rel="stylesheet" href="http://jquerymobile.com/demos/1.1.1/docs/_assets/css/jqm-docs.css" />
	<link rel="stylesheet" media="print" href="../css/print.css" />
	<link rel="stylesheet" media="screen" href="../css/screen.css" />
	
	<meta name="mobile-web-app-capable" content="yes">
	<link rel="apple-touch-icon" href="../images/upm.gif">
	<link rel="shorcut icon" type="image/gif" href="../images/upm.gif">
	
	<script src="http://code.jquery.com/jquery-1.7.1.min.js"></script>
	<script src="http://code.jquery.com/mobile/1.1.1/jquery.mobile-1.1.1.min.js"></script>
	<script src="http://jquerymobile.com/demos/1.1.1/docs/_assets/js/jqm-docs.js"></script>

	<style>
	.containing-element .ui-slider-switch { width: 10em }
	</style>
</head> 
<body>
<div data-role="page" class="type-interior">
	<?php admin_header(); ?>
	<?php admin_content(); ?>
	<?php if(!isset($_GET['print'])) admin_footer(); ?>
</div><!-- /page -->

</body>
</html>

<?php


function admin_info_taquilla(){
	if(isset($_GET['taquilla'])) $taquilla = intval($_GET['taquilla']); else $taquilla = false;
	if(isset($_POST['submitcomentario'])) insertarComentario($taquilla,intval($_GET['curso']),$_POST['comentario'],$_POST['persistente']);
	if($taquilla){
		$curso = intval($_GET['curso']);
		$datos = get_info_taquilla($taquilla,$curso);
		$alquilada =  $datos['operacion'] ? "Si" : "No";
		$pagada =  $datos['pagada'] ? "Si" : "No"; 
		
		$tipoHeader = ($datos['tipo'] != "") ? "(".$datos['tipo'].")" : "";
		admin_content_header("Taquilla $taquilla $tipoHeader");
		
		echo "<p><strong>Taquilla Alquilada: </strong>$alquilada</p>";
		echo "<p><strong>Taquilla pagada: </strong> $pagada</p>";
		echo "<p><strong>Zona: </strong>".getNombreZona($datos['zona'])."</p>";
		echo ($datos['tipo'] == "cambio") ? "<p><strong>Taquilla antigua: </strong> ".getCambioOrigen($datos['operacion'])."</p>" : "";
		
		// Solo muestra arrendatarios si se encuentra alquilada
		if($datos['operacion']):
		
		
		
		admin_content_header("Arrendatarios");
		$personas = getArrendatarios($taquilla,$curso);
		
		echo '<br /><ul data-role="listview" data-inset="true">';
		foreach ($personas as $persona){
			echo "<li><h3>".get_utf8($persona['nombre'])." ".get_utf8($persona['apellidos'])."</h3>";
			echo "<p><b>Email</b> ".$persona['email']."</p>";
			echo "<p><b>Teléfono</b> ".$persona['telefono']."</p>";

			//echo "<p><b>DNI</b> ".$persona['dni']."</p>";
			echo "</li>";
		}
		echo '</ul>';
		echo '<form name="mandarcorreo" method="post" action="index.php?pag=mandaremail&taquilla='.$taquilla.'&curso='.$curso.'"><input type="submit" name="realizar" value="Escribir un email" /></form>';
		
		endif;
		
		admin_content_header("Operaciones realizadas");
		
		$operaciones = getOperaciones($taquilla);
		
		
		echo '<br /><ul data-role="listview" data-inset="true">';
		foreach ($operaciones as $operacion){
			echo "<li><a href=\"index.php?pag=infoop&operacion=".$operacion['id']."\"><h3>Numero: ".$operacion['id']."</h3>";
			echo "<p><b>Tipo</b> ".$operacion['tipo']."</p>";
			echo "<p><b>Timestamp</b> ".$operacion['timestamp']."</p>";
			echo "</a></li>";
		}
		echo '</ul>';
		
		
		
		admin_content_header("Comentarios");
		echo '<form method="post" name="comentarios" action="index.php?pag=infotaquilla&taquilla='.$taquilla.'&curso='.$curso.'">';
		echo '<textarea name="comentario" id="comentario" placeholder="Escribe un comentario" /></textarea>';
		echo '<div class="containing-element" ><label for="persistente"></label><select name="persistente" id="persistente" data-role="slider" >
						<option value="0">No persistente</option>
						<option value="1">Si persistente</option>
					</select></div>
			<input data-icon="check" type="submit" name="submitcomentario" value="Insertar" />';
		echo '	';
		echo '</form>';
		
		$comentarios = getComentarios($taquilla,$curso);
		
		if($comentarios){
		
			echo '<br /><ul data-role="listview" data-inset="true">';
			foreach ($comentarios as $comentario){
				echo "<li>";
				echo '<p><strong>'.$comentario['admin'].'</strong> dice: '.$comentario['comentario']. '<br />'.$comentario['timestamp'];
				echo "</li>";
			}
			echo '</ul>';
		}
		
		
		if($debug) var_dump($datos);
	
	}else{
		$cursos = get_cursos();
		echo '<form name="vertaquilla" action="index.php?pag=infotaquila" method="get">
			<div data-role="fieldcontain">
				<label for="taquilla">Número de taquilla</label>
				<input type="number" name="taquilla" id="taquillas" placeholder="Número de taquilla"/>
			</div>
			<div data-role="fieldcontain">
				<label for="curso">Curso académico</label>
				<select name="curso">';				
				foreach($cursos as $curso):
					echo '<option>'.$curso.'</option>';
				endforeach;
			echo '</select></div>
			<input type="submit" name="comprobar" id="comprobar" value="Ver taquilla" />
			</form>';
	}

}

function admin_info_operacion(){
	if(isset($_GET['operacion'])) $operacion = intval($_GET['operacion']); else $operacion = false;
	if($operacion){	
		
		if(isset($_POST['realizar']))
			$setTarea = setTareaRealizada($operacion, 1);		
		
		if(isset($_POST['pendiente']))		
			$setTarea = setTareaRealizada($operacion, 0);

		$datos = get_info_operacion($operacion);		
		$personas = getArrendatarios($datos['taquilla'],$datos['curso']);
		
		if($setTarea){
			if(isset($_POST['realizar'])){
				$asunto = ($datos['tipo'] == "cambio") ? "Tu cambio de taquilla se ha realizado" : "Tu nueva taquilla está lista";
				$mensaje = ($datos['tipo'] == "cambio") ? "<p>Hola,</p><p>Ya te hemos cambiado la cerradura a la taquilla ".$datos['taquilla']."</p><p>El equipo de taquillas</p>" : "<p>Hola,</p><p>Ya te hemos dejado preparada la taquilla ".$datos['taquilla'].".<br/>Puedes bajar a DAT a por tu llave</p><p>El equipo de taquillas</p>";
				mandarCorreos($datos['taquilla'],$datos['curso'],$asunto,$mensaje);
			}
			else if(isset($_POST['pendiente'])){
				$asunto = ($datos['tipo'] == "cambio") ? "Error con tu cambio" : "Error con tu nueva taquilla";
				$mensaje = "<p>Hola,</p><p>Si has recibido un email diciendo que tu taquilla ya estaba lista nos hemos equivocado.<br/>Te ruego que nos disculpes, te volverá a llegar un correo cuando lo esté.</p><p>El equipo de taquillas</p>";
				mandarCorreos($datos['taquilla'],$datos['curso'],$asunto,$mensaje);
			}
		}
		
		$alquilada =  $datos['operacion'] ? "Si" : "No";
		$pagada =  $datos['pagado'] ? "Si" : "No"; 
		
		admin_content_header("Operación $operacion");
		
		echo "<p><strong>Operación pagada: </strong> $pagada</p>";
		echo "<p><strong>Curso: </strong> ".$datos['curso']."</p>";
		echo "<p><strong>Taquilla: </strong> <a href=\"index.php?pag=infotaquila&taquilla=".$datos['taquilla']."&curso=".$datos['curso']."\">".$datos['taquilla']."</a></p>";
		echo "<p><strong>Timestamp: </strong> ".$datos['timestamp']."</p>";
		echo "<p><strong>txn_id: </strong> ".$datos['txn_id']."</p>";
		echo "<p><strong>Tipo: </strong> ".$datos['tipo']."</p>";
		
		admin_content_header("Arrendatarios");
		
		echo '<br /><ul data-role="listview" data-inset="true">';
		foreach ($personas as $persona){
			echo "<li><h3>".get_utf8($persona['nombre'])." ".get_utf8($persona['apellidos'])."</h3>";
			echo "<p><b>Email</b> ".$persona['email']."</p>";
			echo "<p><b>Teléfono</b> ".$persona['telefono']."</p>";
			//echo "<p><b>DNI</b> ".$persona['dni']."</p>";
			echo "</li>";
		}
		echo '</ul>';
		echo '<form name="mandarcorreo" method="post" action="index.php?pag=mandaremail&taquilla='.$datos['taquilla'].'&curso='.$datos['curso'].'"><input type="submit" name="realizar" value="Escribir un email" /></form>';
		
		
		if( ($datos['tipo'] == "cambio" || $datos['tipo'] == "nueva")):
		
		admin_content_header("Tarea a realizar");
		
		if ($datos['tipo'] == "cambio"){
				aviso('<ol><li> Vaciar y quitar la cerradura de la taquilla <b>'.getCambioOrigen($datos['id']).'</b>, la antigua.</li>
				<li>Poner esa cerradura en la taquilla <b>'.$datos['taquilla'].'</b>, la nueva,  y vaciarla si fuera necesario.</li>
				<li>Cambiar la llave de sitio en el manojo de llaves.</li></ol>');
			}else if($datos['tipo'] == "nueva"){
			aviso('<ol><li> Vaciar y quitar la cerradura de la taquilla <b>'.$datos['taquilla'].'</b></li>
				<li>Poner una cerradura nueva</li>
				<li>Poner la llave de la cerradura en el manojo en su sitio correspondiente</li></ol>');
			}
		if($datos['tarea_realizada'] == 0){
			echo '<form name="realizar" method="post" action="index.php?pag=infoop&operacion='.$datos['id'].'"><input type="submit" name="realizar" value="Marcar tarea hecha" /></form>';
			echo '<p><b>Nota</b>: Se enviará un correo a los arrendatarios avisando de que ya se ha realizado la tarea</p>';
		}else{
			echo "<br />";
			aviso("La tarea ya ha sido realizada");
			echo '<form name="realizar" method="post" action="index.php?pag=infoop&operacion='.$datos['id'].'"><input type="submit" name="pendiente" value="Volver a tarea pendiente" /></form>';
		}
			
		
		endif;
		
		if($debug) var_dump($datos);
	
	}else{
		
		echo '<form name="vertaquilla" action="index.php?pag=infoop" method="get">
			<div data-role="fieldcontain">
				<label for="operacion">Número de operación</label>
				<input type="number" name="operacion" id="operacion" placeholder="Número de operación"/>
			</div>
			
			<input type="submit" name="comprobar" id="comprobar" value="Ver operación" />
			</form>';
	}
}

function admin_info_taquillas_alquiladas(){
	
	
	
	if(isset($_GET['curso'])) $curso = intval($_GET['curso']); else $curso = false;
	if($curso){
	
		$zona = (isset($_GET['zona'])) ? intval($_GET['zona']) : false;
		$taquillas = get_taquillas_alquiladas($curso, $zona);
		
		admin_content_header("Alquiladas $curso", count($taquillas));
				
		
		echo '<br /><ul data-role="listview" data-filter="true" data-inset="true">';
		foreach ($taquillas as $taquilla){
			$personas = getArrendatarios($taquilla['taquilla'],$curso);
			$estado = ($taquilla['pagado'] == 1) ? "<font color=\"darkgreen\">Pagada</font>" : "<font color=\"red\">Sin pagar</font>";
			echo "<li><a href=\"index.php?pag=infotaquila&taquilla=".$taquilla['taquilla']."&curso=$curso\"><h3>Número ".$taquilla['taquilla']."</h3>";
			echo "<p><b>Tipo</b> ".$taquilla['tipo']."</p>";
			echo "<p><b>Estado</b> ".$estado."</p>";
			echo "<p><strong>Zona: </strong>".getNombreZona($taquilla['zona'])."</p>";
			//echo "<p><b>Pagada</b> ".$taquilla['pagado']."</p>";
			
			echo "<p><b>Arrendatarios:</b> ";
			foreach ($personas as $persona){
				echo get_utf8($persona['nombre'])." ".get_utf8($persona['apellidos']).", ";
			}
			echo "</p>";
			echo "</a></li>";
		}
		echo '</ul>';
		

		
		
	}else{
		$cursos = get_cursos();

		echo '<form name="vertaquilla" action="index.php?pag=infoalquiladas" method="get">
			<div data-role="fieldcontain">
				<label for="curso">Curso académico</label>
				<select name="curso">';				
				foreach($cursos as $curso):
					echo '<option>'.$curso.'</option>';
				endforeach;
			echo '</select></div>
			<input type="submit" name="comprobar" id="comprobar" value="Ver taquillas" />
			</form>';
	}

	
}

function admin_info_taquillas_norenovadas(){
	
	
	
	if(isset($_GET['curso'])) $curso = intval($_GET['curso']); else $curso = false;
	if($curso){
	
		$zona = (isset($_GET['zona'])) ? intval($_GET['zona']) : false;
		$taquillas = get_taquillas_no_renovadas($curso, $zona);
		
				
		admin_content_header("No renovadas $curso",count($taquillas));				
		echo "<p>Son las taquillas que hay que vaciar</p>";
		echo '<br /><ul data-role="listview" data-filter="true" data-inset="true">';
		foreach ($taquillas as $taquilla){
			$personas = getArrendatarios($taquilla['taquilla'],$curso);
			$estado = ($taquilla['pagado'] == 1) ? "<font color=\"darkgreen\">Pagada</font>" : "<font color=\"red\">Sin pagar</font>";
			echo "<li><a href=\"index.php?pag=infotaquila&taquilla=".$taquilla['taquilla']."&curso=$curso\"><h3>Número ".$taquilla['taquilla']."</h3>";
			echo "<p><b>Razón</b> ".getRazonNoRenovada($taquilla['numero'],$curso)."</p>";
			echo "<p><strong>Zona: </strong>".getNombreZona($taquilla['zona'])."</p>";
			
			echo "</a></li>";
		}
		echo '</ul>';
		

		
		
	}else{
		$cursos = get_cursos();

		echo '<form name="vertaquilla" action="index.php?pag=infonorenovadas" method="get">
			<div data-role="fieldcontain">
				<label for="curso">Curso académico</label>
				<select name="curso">';				
				foreach($cursos as $curso):
					echo '<option>'.$curso.'</option>';
				endforeach;
			echo '</select></div>
			<input type="submit" name="comprobar" id="comprobar" value="Ver no renovadas" />
			</form>';
	}

	
}

function admin_info_tareas(){
		admin_content_header("Nuevas y cambios");
		$tareas = getTareasPendientes();		
		echo '<br /><ul data-role="listview" data-filter="true" data-inset="true">';
		foreach ($tareas as $tarea){
			$estado = ($tarea['pagado'] == 1) ? "<font color=\"darkgreen\"><b>Pagada</b></font>" : "<font color=\"red\">Sin pagar</font>";
			echo '<li><a href="index.php?pag=infoop&operacion='.$tarea['id'].'">';
			echo '<p><strong>Número de tarea:</strong> '.$tarea['id'] .'</p>';
			echo '<p><strong>Tipo de tarea:</strong> '.ucfirst($tarea['tipo']) .' taquilla<br/>';
			$personas = getArrendatarios($tarea['taquilla'],$tarea['curso']);
			echo "<b>Arrendatarios: </b>";
			foreach ($personas as $persona){
				echo get_utf8($persona['nombre'])." ".get_utf8($persona['apellidos']).", ";			
			}
			echo "<br/>";
			echo "<b>Estado</b> ".$estado."<br/>";
			if($tarea['tipo'] == "cambio")
				echo '<b>Info: </b>Desde la taquilla '.getCambioOrigen($tarea['id']).' a la taquilla '.$tarea['taquilla'].'</p>';
			else if($tarea['tipo'] == "nueva")
				echo '<b>Info: </b>Poner la taquilla '.$tarea['taquilla'].'</p>';

			echo '<p>La taquilla destino está ';
			echo (isTaquillaVacia($tarea['taquilla'],getCursoAnterior())) ? '<b><font color="darkgreen">libre</font></b>' : 'posiblemente <b><font color="red"> ocupada</font></b>';
			echo '</p>';
			
			echo '</a></li>';
		}
		echo '</ul>';

}


function admin_info_cancelaciones(){
		admin_content_header("Cancelaciones");
		$cancelaciones = getCancelaciones();
		echo "<br />";
		echo '<ul data-role="listview" data-filter="true" data-inset="true">';
		foreach ($cancelaciones as $cancelacion){
			
			$personas = getArrendatarios($cancelacion['taquilla'],$cancelacion['curso']-1);
			
			echo '<li><a href="index.php?pag=infocancelacion&cancelacion='.$cancelacion['id'].'" >';
			echo '<h3>Taquilla '.$cancelacion['taquilla'] .'</h3>';

			echo "<p><b>Autorizados: </b>";
			foreach ($personas as $persona){
				echo get_utf8($persona['nombre'])." ".get_utf8($persona['apellidos']).", ";			
			}
			echo "</p>";
			echo "<p><b>Fianza devuelta: </b>";
			echo ($cancelacion['fianza_devuelta'] == 1) ? "Si" : "No";
			
			echo '</p></a></li>';
		}
		echo '</ul>';

}

function admin_info_cancelacion(){
	global $params;
	if(isset($_GET['cancelacion'])) $cancelacion = intval($_GET['cancelacion']); else $cancelacion = false;
	if($cancelacion){	
		
		if(isset($_POST['devolver'])){
			if(trim($_POST['quien']) == "")
				aviso("El campo de quién recoge la fianza no puede estar vacío");
			else{
				if($params->tienePermisos($_SERVER['PHP_AUTH_USER']))
					$setTarea = setFianzaDevuelta($cancelacion, $_POST['quien']);
			}
		}
		


		$datos = getCancelacion($cancelacion);		
		$personas = getArrendatarios($datos['taquilla'],$datos['curso']-1);
		
		if($setTarea){
			$asunto = "Devolución de la fianza";
			$mensaje = "<p>Hola,</p><p>Te comunicamos que se acaba de devolver a <b>".$_POST['quien']."</b> la fianza de tu taquilla ".$datos['taquilla'].".<br/> Si se trata de un error ponte en contacto con nosotros inmediatamente.</p><p>El equipo de taquilla</p>";
			mandarCorreos($datos['taquilla'],$datos['curso']-1,$asunto,$mensaje);
		}
	
		admin_content_header("Cancelación de taquilla ".$datos['taquilla']);
		
		aviso("La fianza de <b>9 Euros</b> será devuelta en persona, siempre y cuando la taquilla se encuentre en buen estado y tendrá que <b>devolver al menos 1 llave</b>");
		
		echo '<div class="noprint">';
		admin_content_header("Instrucciones para devolver fianza");
		aviso("<ol>
		<li>Comprobar que la persona que recoge la fianza es arrendatario o tiene autorización</li>
		<li>Escribir su nombre en el recuadro de abajo y pulsar devolver fianza</li>
		<li>Imprimir esta página, que tiene que firmar la persona que recoge</li>
		<li>Finalmente, devovler el dinero</li></ol>");
		
		echo '</div>';
		
		admin_content_header("Autorizados");
		
		echo '<p>Esta personas están autorizadas a recoger la fianza</p>';
		echo '<ul data-role="listview" data-inset="true">';
		foreach ($personas as $persona){
			echo "<li><h3>".get_utf8($persona['nombre'])." ".get_utf8($persona['apellidos'])."</h3>";
			echo "<p><b>Email</b> ".$persona['email']."</p>";
			echo "<p><b>Teléfono</b> ".$persona['telefono']."</p>";
			//echo "<p><b>DNI</b> ".$persona['dni']."</p>";
			echo "</li>";
		}
		echo '</ul>';
		
		if($params->tienePermisos($_SERVER['PHP_AUTH_USER'])){
			admin_content_header("Devolución de fianza");
		
		
			if($datos['fianza_devuelta'] == 0){
				echo '<form name="realizar" method="post" action="index.php?pag=infocancelacion&cancelacion='.$datos['id'].'">';
				echo '<div data-role="fieldcontain">
					<label for="quien">Quien recoge:</label>
					<input type="text" name="quien" id="quien" placeholder="Nombre y apellidos"/>
				</div>';
				echo '<input type="submit" name="devolver" value="Devolver fianza" /></form>';
				echo '<p><b>Nota</b>: Se enviará un correo a los arrendatarios avisando de que ya se ha realizado la tarea</p>';
			}else{
				echo "<br />";
				setlocale(LC_ALL,"es_ES");
				$fecha = date('l \d\í\a j \d\e F \d\e Y \a \l\a\s G:i',$datos['hora_recogida']);
				aviso("La fianza de 9 Euros la recogió <b>".$datos['quien_recoge']."</b> el $fecha");
				
				echo '<div class="noscreen">';
				aviso("Firmado: <br/><br/>");
				echo '</div>';
			}
		}
			
		
		
		if($debug) var_dump($datos);
	
	}else{
		
		echo '<form name="vertaquilla" action="index.php?pag=infoop" method="get">
			<div data-role="fieldcontain">
				<label for="operacion">Número de operación</label>
				<input type="number" name="operacion" id="operacion" placeholder="Número de operación"/>
			</div>
			
			<input type="submit" name="comprobar" id="comprobar" value="Ver operación" />
			</form>';
	}
}

function admin_mandar_email(){


	if($_POST['comprobar'] && isset($_POST['taquilla']) ){
		$curso = intval($_POST['curso']);
		$asunto = $_POST['asunto'];
		$taquilla = intval($_POST['taquilla']);	
		$mensaje .= $_POST['mensaje'];	
	
		$estado = mandarCorreos($taquilla,$curso,$asunto,$mensaje,true);
	
		echo ($estado) ? "<p>Correo enviado correctamente</p>" : "<p>Fallo al mandar correo</p>";		
		
	
	}else{
		if(isset($_GET['taquilla'])) $taquilla = intval($_GET['taquilla']);	
		if($taquilla != 0) $value = 'value="'.$taquilla.'"';
		if(isset($_GET['curso'])) $cur = intval($_GET['curso']);	
		$cursos = get_cursos();
		echo '<form name="vertaquilla" action="index.php?pag=mandaremail" method="post">
			<div data-role="fieldcontain">
				<label for="taquilla">Número de taquilla</label>
				<input type="number" name="taquilla" id="taquillas" '.$value.' placeholder="Número de taquilla"/>
			</div>
			<div data-role="fieldcontain">
				<label for="curso">Curso académico</label>
				<select name="curso">';				
				foreach($cursos as $curso):
					if($curso == $cur) $selected = 'selected="selected"'; else $selected = "";
					echo '<option '.$selected.'>'.$curso.'</option>';
				endforeach;
			echo '</select></div>
			<div data-role="fieldcontain">
				<label for="asunto">Asunto:</label>
				<input type="text" name="asunto" id="asunto" />
			</div>
			<div data-role="fieldcontain">
				<label for="mensaje">Mensaje:</label>
				<textarea name="mensaje" id="mensaje"></textarea>
			</div>
			<input type="submit" name="comprobar" id="comprobar" value="Mandar email" />
			</form>';
	}

}

function admin_email_masivo(){

if(isset($_POST['tipo'])) $tipo = $_POST['tipo']; else $taquilla = false;
	if($tipo){
	$curso = intval($_POST['curso']);
	$asunto = $_POST['asunto'];
	
	$mensaje .= $_POST['mensaje'];

	
	$estado = emailMasivo($tipo,$curso,$asunto,$mensaje);
	
	echo ($estado) ? "<p>Correo enviado correctamente</p>" : "<p>Fallo al mandar correo</p>";		
		
	
	}else{
		$cursos = get_cursos();
		if(!in_array(getCursoActual(),$cursos))
			$cursos[] = getCursoActual();
			
		echo '<form name="vertaquilla" action="index.php?pag=emailmasivo" method="post">
			<div data-role="fieldcontain">
				<label for="tipo">Tipo de taquillas</label>
				<select name="tipo">';				
				echo '<option value="renovadas">Alquiladas</option>';
				echo '<option value="norenovadas">No renovadas</option>';
				echo '<option value="canceladas">Canceladas</option>';
				echo '<option value="cambios">Cambios</option>';
			echo '</select></div>
			<div data-role="fieldcontain">
				<label for="curso">Curso académico</label>
				<select name="curso">';				
				foreach($cursos as $curso):
					echo '<option>'.$curso.'</option>';
				endforeach;
			echo '</select></div>
			<div data-role="fieldcontain">
				<label for="asunto">Asunto:</label>
				<input type="text" name="asunto" id="asunto" />
			</div>
			<div data-role="fieldcontain">
				<label for="mensaje">Mensaje:</label>
				<textarea name="mensaje" id="mensaje"></textarea>
			</div>
			<input type="submit" name="comprobar" id="comprobar" value="Mandar email" />
			</form>';
	}


}

function admin_pagar_operacion(){
	global $params;
	if(!$params->tienePermisos($_SERVER['PHP_AUTH_USER']))
		die('no tienes permisos');
	if(isset($_GET['taquilla'])) $taquilla = intval($_GET['taquilla']); else $taquilla = false;
	if($taquilla){
		$curso = getCursoActual();
		$operacion = getOperacion($taquilla,$curso);
		
		if(!isOperacionPagada($operacion)){
			$pagar = pagarOperacion($operacion);
			$asunto = "Hemos recibido tu ingreso";
			$mensaje = "<p>Hola,</p><p>Hemos recibido y comprobado el justificante de tu ingreso.<br/>Con esto el proceso de renovación está completo.</p><p>Muchas gracias</p>";
			$estado = mandarCorreos($taquilla,$curso,$asunto,$mensaje);
			aviso("La operación <b>$operacion</b> correspondiente a la taquilla <b>$taquilla</b> del curso <b>$curso</b> ha sido pagada");
		}else{
			aviso("<font color=\"red\"><b>Error: </b></font>La operación <b>$operacion</b> ya estaba pagada</p>");
		}
	
	}else{
		
		echo '<form name="vertaquilla" action="index.php?pag=pagaroperacion" method="get">
			<p>Introducir número de taquilla para establecer como pagada este curso</p>
			<div data-role="fieldcontain">
				<label for="operacion">Número de taquilla</label>
				<input type="number" name="taquilla" id="taquilla" placeholder="Número de taquilla"/>
			</div>
			
			<input type="submit" name="comprobar" id="comprobar" value="Pagar taquilla" />
			</form>';
	}
}

function admin_content_header($titulo, $count = 0){
	
	$bubble = ($count > 0) ? '<span class="ui-li-count ui-btn-up-c ui-btn-corner-all">'.$count.'</span>' : "";
	echo '<div data-role="header" data-theme="b" class="ui-header ui-bar-b" role="banner" >
				<h1 class="ui-title printcontrast" role="heading" aria-level="1">'.$bubble." ".$titulo.'</h1>
			</div>';
}


function admin_header(){
	echo '<div class="noprint" data-role="header" data-theme="f" >
		<h1>Administrador de taquillas</h1>
		<a href="index.php" data-icon="home" data-iconpos="notext" data-direction="reverse">Home</a>
		<a href="../nav.html" data-icon="search" data-iconpos="notext" data-rel="dialog" data-transition="fade">Search</a>
	</div><!-- /header -->';
}

function admin_content(){
	echo '<div data-role="content">';
	admin_content_primary();
	if(!isset($_GET['print']))
		admin_content_secondary();
	echo '</div><!-- /content -->';
}

function admin_content_primary(){
	echo '<div class="content-primary">';
	
	switch($_GET['pag']){
		case "infotaquilla":
			admin_info_taquilla();
			break;
		case "infoop":
			admin_info_operacion();
			break;
		case "infoalquiladas":
			admin_info_taquillas_alquiladas();
			break;
		case "infotareas":
			admin_info_tareas();
			break;
		case "mandaremail":
			admin_mandar_email();
			break;
		case "emailmasivo":
			admin_email_masivo();
			break;
		case "pagaroperacion":
			admin_pagar_operacion();
			break;
		case "infonorenovadas":
			admin_info_taquillas_norenovadas();
			break;
		case "infocancelaciones":
			admin_info_cancelaciones();
			break;
		case "infocancelacion":
			admin_info_cancelacion();
			break;
		default:
			admin_info_taquilla();
	}
	echo '</div><!--/content-primary -->';

}

function admin_content_secondary(){
	global $params;

	echo '<div class="content-secondary noprint">

				<div data-role="collapsible" data-collapsed="true" data-theme="b" data-content-theme="d">

						<h3>Menú taquillas</h3>

						<ul data-role="listview" data-theme="c" data-dividertheme="d">

							<li data-role="list-divider">Ver información</li>
							<li><a href="index.php?pag=infotaquilla">Información de una taquilla</a></li>
							<li><a href="index.php?pag=infoop">Información de una operación</a></li>
							<li><a href="index.php?pag=infoalquiladas">Ver todas alquiladas</a></li>
							<li><a href="index.php?pag=infonorenovadas">Ver no renovadas</a></li>
							
							<li data-role="list-divider">Tareas</li>
							<li><a href="index.php?pag=infotareas">Nuevas y cambios<span class="ui-li-count ui-btn-up-c ui-btn-corner-all">'.countTareas().'</span></a></li>
							<li><a href="index.php?pag=infocancelaciones">Cancelaciones<span class="ui-li-count ui-btn-up-c ui-btn-corner-all">'.countCancelaciones().'</span></a></li>
							
							<li data-role="list-divider">Operativa</li>';
								if($params->tienePermisos($_SERVER['PHP_AUTH_USER']))
									echo '<li><a href="index.php?pag=pagaroperacion">Pagar una taquilla</a></li>';
							echo '<li><a href="index.php?pag=mandaremail">Mandar email</a></li>
							<li><a href="index.php?pag=emailmasivo">Emails masivos</a></li>

						</ul>
				</div>
			</div>';

}

function admin_footer(){
	echo '<div data-role="footer" class="footer-docs noprint" data-theme="c">
				<p>&copy; 2012 Pablo Moncada</p>
		</div>';
}




?>
