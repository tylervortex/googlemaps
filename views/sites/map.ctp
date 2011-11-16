<?php echo $this->Html->css('tables', null, array('inline' => false)); ?>
<?php echo $this->Html->script('jquery.min', false); ?>
<?php echo $this->Html->script('http://maps.google.com/maps/api/js?sensor=false&amp;language=pt-BR', false); ?>
<?php $this->Html->scriptStart(array('inline' => false)); ?>
var sites = [
<?php
$count = count($sitesLocations);
for ($i = 0; $i < $count; $i++) {
	$site = $sitesLocations[$i];
	$img = -1;
	if ($site['Site']['segment_name'] == 'generation') {
		if ($site['Site']['generation_type_name'] == 'ute') {
			$img = 2;
		} else if ($site['Site']['generation_type_name'] == 'se') {
			$img = 3;
		} else if ($site['Site']['generation_type_name'] == 'sol') {
			$img = 4;
		} else {
			$img = 0;
		}
	} else if ($site['Site']['segment_name'] == 'consumption') {
		$img = 1;
	} else {
		/* Não é nem do segmento de geração, nem do segmento de consumo. 
		 * Verificar se é um ponto com hidrologia apenas. */
		if ($site['Site']['hydro'] && !$site['Site']['energy']) {
			$img = 5;
		}
	}
	$padrao1 = array(
		'before'=> '',
		'after' => '',
		'zero' => '0,0',
		'places' => 1,
		'thousands' => '.',
		'decimals' => ',',
		'negative' => '()',
		'escape' => true
	);
	$padrao3 = array(
		'before'=> '',
		'after' => '',
		'zero' => '0,000',
		'places' => 3,
		'thousands' => '.',
		'decimals' => ',',
		'negative' => '()',
		'escape' => true
	);
	$title = ($site['Site']['segment_name'] == 'generation' ? $site['GenerationType']['title'] : '') . ' ' . $site['Site']['short_name'];
	$status = 0; /* sem coleta */
	$txt = '';
	$txt .= '<div style="font-size:10pt">';
	$txt .= '<span style="font-weight:bold">' . $title . '<\/span>';
	$txt .= '<p style="margin-top:10px">';
	$txt .= '<span style="font-size:9pt">Última coleta com sucesso:</span><br />';
	foreach ($site['SiteMeter'] as $n => $meter) {
		if ($meter['fetch'] == 1) {
			$diff = time() - $meter['log_status_time_ts'];
			$status = 1;
			if ($meter['log_status_id'] != 1) {
				if ($diff > 6 * 60 * 60) {
					$status = 3;
				} else if ($diff > 3 * 60 * 60) {
					$status = 2;
				}
			}
		}
		$txt .= $this->Formatacao->dataHora($site['SiteMeter'][$n]['log_status_time'], false);
		$txt .= ' ';
		$txt .= $meterRoles[$site['SiteMeter'][$n]['meter_role_name']];
		$txt .= '<br />';
	}
	$txt .= '<\/p>';
	$txt .= '<p style="margin-top:10px">';
	$txt .= 'Tensão: ' . $this->Number->format($site['Site']['voltage_level'], $padrao1) . ' kV<br />';
	$txt .= 'Capacidade de geração: ' . $this->Number->format($site['Site']['generation_capacity'], $padrao3) . ' MW<br />';
	$txt .= 'Capacidade de consumo: ' . $this->Number->format($site['Site']['consumption_capacity'], $padrao3) . ' MW<br />';
	$txt .= '<\/p>';
	$txt .= '<\/div>';
?>
["<?php echo $title; ?>",<?php echo $site['Site']['latitude']; ?>,<?php echo $site['Site']['longitude']; ?>,<?php echo $img; ?>,<?php echo $status; ?>,'<?php echo $txt; ?>']<?php echo ($i != $count - 1 ? ',' : ''); ?>
<?php } ?>
];
var markers = new Array();
var map;
var infoWindow;
function initialize() {
	var mapOptions = {
		zoom: 5,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	};
	var img = [ 'dam', 'factory', 'powerplant', 'powersubstation', 'solarenergy', 'river' ];
	var images = new Array(img.length);
	for (var i = 0; i < img.length; i++) {
		images[i] = new Array(3);
		images[i][0] = new google.maps.MarkerImage('<?php echo $this->webroot; ?>img/' + img[i] + '0.png');
		images[i][1] = new google.maps.MarkerImage('<?php echo $this->webroot; ?>img/' + img[i] + '1.png');
		images[i][2] = new google.maps.MarkerImage('<?php echo $this->webroot; ?>img/' + img[i] + '2.png');
		images[i][3] = new google.maps.MarkerImage('<?php echo $this->webroot; ?>img/' + img[i] + '3.png');
	}
	map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
	infoWindow = new google.maps.InfoWindow({content:'...'});
	for (var i = 0; i < sites.length; i++) {
		var site = sites[i];
		var latlng = new google.maps.LatLng(site[1], site[2]);
		markers[i] = new google.maps.Marker({
			position: latlng,
			map: map,
			icon: images[site[3]][site[4]],
			title: site[0],
			html: site[5]
		});
		var marker = markers[i];
		google.maps.event.addListener(marker, 'click', function() {
			infoWindow.setContent(this.html);
			infoWindow.open(map, this);
		});
	}
	autoCenter();
}
function autoCenter() {
	var bounds = new google.maps.LatLngBounds();
	$.each(markers, function (index, marker) {
		bounds.extend(marker.position);
	});
	map.fitBounds(bounds);
}
$(document).ready(function () {
	initialize();
});
<?php $this->Html->scriptEnd(); ?>
<div class="module-title">Pontos de medição</div>
<div class="sites index">
	<div class="module">
		<?php echo $this->element('sites_actions'); ?>
	</div>
	<?php echo $this->Session->flash('auth'); ?>
	<fieldset style="clear:both">
		<legend>Mapa de Pontos de Medição</legend>
		<div <?php echo ($sitesLocations) ? 'id="map_canvas"' : ''; ?> style="float:left;margin-right:30px;width:700px;height:600px"><?php echo ($sitesLocations) ? '' : 'Nenhum ponto cadastrado.'; ?></div>
		<div style="float:left; font-size:10pt">
			<?php echo $this->Form->create('Site', array('encoding' => null)); ?>
			<p style="font-weight:bold">Legenda</p>
			<input name="data[UserMap][option]" id="UserMapOptionFct" value="fct" <?php echo (in_array('fct', $options) ? 'checked' : ''); ?> type="radio"> <?php echo $this->Html->image('factory1.png', array('style' => 'vertical-align:middle')); ?> Consumidor<br />
			<input name="data[UserMap][option]" id="UserMapOptionPch" value="pch" <?php echo (in_array('pch', $options) ? 'checked' : ''); ?> type="radio"> <?php echo $this->Html->image('dam1.png', array('style' => 'vertical-align:middle')); ?> Gerador hidrelétrico<br />
			<input name="data[UserMap][option]" id="UserMapOptionUte" value="ute" <?php echo (in_array('ute', $options) ? 'checked' : ''); ?> type="radio"> <?php echo $this->Html->image('powerplant1.png', array('style' => 'vertical-align:middle')); ?> Gerador térmico<br />
			<input name="data[UserMap][option]" id="UserMapOptionSol" value="sol" <?php echo (in_array('sol', $options) ? 'checked' : ''); ?> type="radio"> <?php echo $this->Html->image('solarenergy1.png', array('style' => 'vertical-align:middle')); ?> Gerador solar<br />
			<input name="data[UserMap][option]" id="UserMapOptionSe" value="se" <?php echo (in_array('se', $options) ? 'checked' : ''); ?> type="radio"> <?php echo $this->Html->image('powersubstation1.png', array('style' => 'vertical-align:middle')); ?> Subestação<br />
			<input name="data[UserMap][option]" id="UserMapOptionRiv" value="riv" <?php echo (in_array('riv', $options) ? 'checked' : ''); ?> type="radio"> <?php echo $this->Html->image('river1.png', array('style' => 'vertical-align:middle')); ?> Estação Hidrológica<br />
			<input name="data[UserMap][option]" id="UserMapOptionAll" value="all" <?php echo (empty($options) || in_array('all', $options) ? 'checked' : ''); ?> type="radio"> Todos</label><br />
			<p style="font-weight:bold">Cores</p>
			<table cellspacing="6" cellpadding="0">
			<tr><td style="width:70px"><input name="data[UserMap][color]" id="UserMapColorBlue" value="blue" <?php echo (in_array('blue', $colors) ? 'checked' : ''); ?> type="radio"> <span style="color:blue;font-weight:bold">azul</span></td><td>Coleta atualizada</td></tr>
			<tr><td style="width:70px"><input name="data[UserMap][color]" id="UserMapColorYellow" value="yellow" <?php echo (in_array('yellow', $colors) ? 'checked' : ''); ?> type="radio"> <span style="color:yellow;font-weight:bold">amarelo</span></td><td>Coleta atrasada há mais de 3 horas</td></tr>
			<tr><td style="width:100px"><input name="data[UserMap][color]" id="UserMapColorRed" value="red" <?php echo (in_array('red', $colors) ? 'checked' : ''); ?> type="radio"> <span style="color:red;font-weight:bold">vermelho</span></td><td>Coleta atrasada há mais de 6 horas</td></tr>
			<tr><td style="width:70px"><input name="data[UserMap][color]" id="UserMapColorGray" value="gray" <?php echo (in_array('gray', $colors) ? 'checked' : ''); ?> type="radio"> <span style="color:gray;font-weight:bold">cinza</span></td><td>Ponto sem coleta</td></tr>
			<tr><td style="width:70px"><input name="data[UserMap][color]" id="UserMapColorAll" value="all" <?php echo (empty($colors) || in_array('all', $colors) ? 'checked' : ''); ?> type="radio"></td><td>Todos</td></tr>
			</table>
			<?php echo $this->Form->end(array('label' => 'OK', 'div' => false)); ?>
		</div>
	</fieldset>
</div>
