<?php

/***********************************************************************************
/*
/* Sistema de taquillas ETSIT UPM
/* @author Pablo Moncada Isla pmoncadaisla@gmail.com
/* @version 09/2013
/*
/***********************************************************************************/
?>

<?php define("_TAQUILLAS","true"); ?>
<?php header("Content-type: text/html; charset=utf-8"); ?>
<!DOCTYPE html>
<html>
	<head>
	<title>Taquillas DAT ETSIT</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link rel="stylesheet" href="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css" />
	<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
	<script src="https://code.jquery.com/jquery-migrate-3.0.1.min.js"></script>
	<script src="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>
</head>
<body>
<div data-role="page">
<?php
require("taquillas.functions.php");

/* Page ROUTER */
$page = $_GET['pag'];

if(is_file("pages/$page.html")) // Trata de cargar la pagina.html
	include("pages/$page.html");
else if(is_file("pages/$page.php")) // Si no existe html, pues .php
	include("pages/$page.php");
else
	include("pages/principal.php");
 ?>
<div data-role="footer" class="footer-docs" data-theme="c">
				<p><div style="float: left;">&copy; 2012 <span style="font-size: 0.8em">Pablo Moncada</span></div> <div style="font-size: 0.8em; text-align: right; margin-right: 30px;"> consultas, dudas y sugerencias en taquillas@dat.etsit.upm.es</div></p>
		</div>

</div><!-- /page -->

</body>
</html>
