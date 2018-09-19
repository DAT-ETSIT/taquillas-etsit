<?php

/***********************************************************************************
/*
/* Sistema de taquillas ETSIT UPM
/* @author Pablo Moncada Isla pmoncadaisla@gmail.com
/* @version 09/2013
/*
/***********************************************************************************/

?>
<?php

require_once("params.php");
$params = new TaquillasParams();

?>
<div data-role="header">
	<h1>Taquillas DAT ETSIT</h1>
</div><!-- /header -->

<div data-role="content">

	<ul data-role="listview" data-theme="d" data-divider-theme="d" class="ui-listview">
		<li data-role="list-divider" role="heading" class="ui-li ui-li-divider ui-bar-d ui-li-has-count">Del 4 al 22 de septiembre<span class="ui-li-count ui-btn-up-c ui-btn-corner-all">1</span></li>
		<li data-corners="false" data-shadow="false" data-iconshadow="true" data-wrapperels="div" data-icon="arrow-r" data-iconpos="right" data-theme="d" class="ui-btn ui-btn-icon-right ui-li-has-arrow ui-li ui-btn-up-d"><div class="ui-btn-inner ui-li"><div class="ui-btn-text"><a href="index.php?pag=renovacion" class="ui-link-inherit"><p class="ui-li-aside ui-li-desc"><strong><?php echo ($params->renovaciones) ? "<b><font color=\"darkgreen\">ABIERTO</font></b>" : "<b><font color=\"red\">CERRADO</font></b>"; ?></strong></p>

				<h3 class="ui-li-heading">Renovación de taquillas</h3>
				<p class="ui-li-desc"><strong>Haz click aquí para renovar tu taquilla</strong></p>
				<p class="ui-li-desc">Únicamente para gente que ya tuviera taquilla del año pasado y quiera renovar exactamente la misma. Para renovar cambiándola por otra esperar hasta el día que toquen cambios.</p>
				<br/><p class="ui-li-desc"><strong>Precio: </strong> 6.5 EUR</p>


		</a></div><span class="ui-icon ui-icon-arrow-r ui-icon-shadow">&nbsp;</span></div></li>

		<li data-role="list-divider" role="heading" class="ui-li ui-li-divider ui-bar-d ui-li-has-count">Permanente<span class="ui-li-count ui-btn-up-c ui-btn-corner-all">1</span></li>
		<li data-corners="false" data-shadow="false" data-iconshadow="true" data-wrapperels="div" data-icon="arrow-r" data-iconpos="right" data-theme="d" class="ui-btn ui-btn-icon-right ui-li-has-arrow ui-li ui-btn-up-d"><div class="ui-btn-inner ui-li"><div class="ui-btn-text"><a href="index.php?pag=cancelacion" class="ui-link-inherit"><p class="ui-li-aside ui-li-desc"><strong><?php echo ($params->cancelaciones) ? "<b><font color=\"darkgreen\">ABIERTO</font></b>" : "<b><font color=\"red\">CERRADO</font></b>"; ?></strong></p>
				<h3 class="ui-li-heading">Cancelación de taquillas</h3>
				<p class="ui-li-desc"><strong>Haz click aquí para cancelar tu taquilla</strong></p>
				<p class="ui-li-desc">Se avisarán las fechas para recoger la fianza próximamente.</p>
				<p class="ui-li-desc"><b>NOTA Importante: </b> Haz clic si NO quieres tu taquilla el curso que viene y quieres recuperar la fianza</p>
		</a></div><span class="ui-icon ui-icon-arrow-r ui-icon-shadow">&nbsp;</span></div></li>
		
		<li data-role="list-divider" role="heading" class="ui-li ui-li-divider ui-bar-d ui-li-has-count">Del 30 de sep. al 6 de oct.<span class="ui-li-count ui-btn-up-c ui-btn-corner-all">1</span></li>
		<li data-corners="false" data-shadow="false" data-iconshadow="true" data-wrapperels="div" data-icon="arrow-r" data-iconpos="right" data-theme="d" class="ui-btn ui-btn-icon-right ui-li-has-arrow ui-li ui-btn-up-d"><div class="ui-btn-inner ui-li"><div class="ui-btn-text"><a href="index.php?pag=cambio" class="ui-link-inherit"><p class="ui-li-aside ui-li-desc"><strong><?php echo ($params->cambios) ? "<b><font color=\"darkgreen\">ABIERTO</font></b>" : "<b><font color=\"red\">CERRADO</font></b>"; ?></strong></p>
				<h3 class="ui-li-heading">Cambios de taquillas</h3>
				<p class="ui-li-desc"><strong>Haz click aquí para solicitar un cambio de taquilla</strong></p>
				<p class="ui-li-desc">Únicamente si ya tenías una taquilla el año pasado.</p>
				<br/><p class="ui-li-desc"><strong>Precio: </strong> 9 EUR (6.5 EUR alquiler + 2.5 EUR cerradura)</p>
		</a></div><span class="ui-icon ui-icon-arrow-r ui-icon-shadow">&nbsp;</span></div></li>
		
		<li data-role="list-divider" role="heading" class="ui-li ui-li-divider ui-bar-d ui-li-has-count">Del 7 al 20 de octubre<span class="ui-li-count ui-btn-up-c ui-btn-corner-all">1</span></li>
		<li data-corners="false" data-shadow="false" data-iconshadow="true" data-wrapperels="div" data-icon="arrow-r" data-iconpos="right" data-theme="d" class="ui-btn ui-btn-icon-right ui-li-has-arrow ui-li ui-btn-up-d"><div class="ui-btn-inner ui-li"><div class="ui-btn-text"><a href="index.php?pag=nueva" class="ui-link-inherit"><p class="ui-li-aside ui-li-desc"><strong><?php echo ($params->nuevas) ? "<b><font color=\"darkgreen\">ABIERTO</font></b>" : "<b><font color=\"red\">CERRADO</font></b>"; ?></strong></p>
				<h3 class="ui-li-heading">Nuevas taquillas</h3>
				<p class="ui-li-desc"><strong>Haz click aquí para solicitar una nueva taquilla</strong></p>
				<br/><p class="ui-li-desc"><strong>Precio: </strong> 18 EUR (6.5 EUR alquiler + 2.5 EUR cerradura + 9 EUR fianza*)
				<br/>El resto de años sólo pagarás 6.5 EUR de alquiler.</p>
				<p>*La fianza se devolverá cuando canceles la taquilla el año que ya no quieras usarla.</p>
		</a></div><span class="ui-icon ui-icon-arrow-r ui-icon-shadow">&nbsp;</span></div></li>



	</ul>
</div><!-- /content -->
