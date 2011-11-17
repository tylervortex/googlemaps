<?php echo $this->Html->css('tables', null, array('inline' => false)); ?>
<?php echo $this->Html->script('jquery.min', false); ?>
<?php echo $this->Html->script('http://maps.google.com/maps/api/js?sensor=false&amp;language=pt-BR', false); ?>
<?php $this->Html->scriptStart(array('inline' => false)); ?>
var sites = [
<?php
$count = count($sitesLocations);
$count = 0;
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
var images;
var infoWindow;
function initialize() {
	var mapOptions = {
		zoom: 5,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	};
	var img = [ 'dam', 'factory', 'powerplant', 'powersubstation', 'solarenergy', 'river' ];
	images = new Array(img.length);
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
	$(".legend_type").click(function() {
		var a = $(this).attr('id').split('_');
		$.ajax({
			url: "<?php echo $this->Html->url(array('controller' => 'sites', 'action' => 'ajax_map')); ?>",
			data: { "data[type]" : a[1] },
			type: "POST",
			success: function(data, statusText) {
				console.log(data);
				var o = $.parseJSON(data);
				var bounds = new google.maps.LatLngBounds();
				for (var i in o) {
					site = o[i];
					console.log(site);
					var latlng = new google.maps.LatLng(site.lat, site.lng);
					marker = new google.maps.Marker({
						position: latlng,
						map: map,
						icon: images[site.i][site.s],
						title: site.t,
						html: site.t
					});
					bounds.extend(marker.position);
					google.maps.event.addListener(marker, 'click', function() {
						infoWindow.setContent(this.html);
						infoWindow.open(map, this);
					});
				}
				map.fitBounds(bounds);
			},
			error: function(x, statusText) {
				if (x.status == 403) {
					alert("Sua sessão expirou.\n\nPor favor, faça o login novamente.");
					window.location.href = "/";
				}
			}
		});

	});
});
<?php $this->Html->scriptEnd(); ?>
<div class="module-title">Pontos de medição</div>
<div class="sites index">
	<div class="module">
		<?php echo $this->element('sites_actions'); ?>
	</div>
	<?php echo $this->Session->flash('auth'); ?>
	<?php if (!$sitesLocations) { ?>
	Nenhum ponto cadastrado.
	<?php } else { ?>
	<fieldset style="clear:both">
		<legend>Mapa de Pontos de Medição</legend>
		<div id="map_canvas" style="float:left;margin-right:30px;width:700px;height:600px"></div>
		<div style="font-size:10pt">
			<p style="font-weight:bold">Legenda</p>
			<?php echo $this->Html->image('factory1.png', array('style' => 'vertical-align:middle', 'class' => 'legend_type', 'id' => 'type_1')); ?> Consumidor<br />
			<?php echo $this->Html->image('dam1.png', array('style' => 'vertical-align:middle', 'class' => 'legend_type', 'id' => 'type_0')); ?> Gerador hidrelétrico<br />
			<?php echo $this->Html->image('powerplant1.png', array('style' => 'vertical-align:middle', 'class' => 'legend_type', 'id' => 'type_2')); ?> Gerador térmico<br />
			<?php echo $this->Html->image('solarenergy1.png', array('style' => 'vertical-align:middle', 'class' => 'legend_type', 'id' => 'type_4')); ?> Gerador solar<br />
			<?php echo $this->Html->image('powersubstation1.png', array('style' => 'vertical-align:middle', 'class' => 'legend_type', 'id' => 'type_3')); ?> Subestação<br />
			<?php echo $this->Html->image('river1.png', array('style' => 'vertical-align:middle', 'class' => 'legend_type', 'id' => 'type_5')); ?> Estação hidrológica<br />
			<p style="font-weight:bold">Cores</p>
			<table cellspacing="6" cellpadding="0">
			<tr><td style="width:70px"><span style="color:blue;font-weight:bold">azul</span></td><td>Coleta atualizada</td></tr>
			<tr><td style="width:70px"><span style="color:yellow;font-weight:bold">amarelo</span></td><td>Coleta atrasada há mais de 3 horas</td></tr>
			<tr><td style="width:70px"><span style="color:red;font-weight:bold">vermelho</span></td><td>Coleta atrasada há mais de 6 horas</td></tr>
			<tr><td style="width:70px"><span style="color:gray;font-weight:bold">cinza</span></td><td>Ponto sem coleta</td></tr>
			</table>
		</div>
	</fieldset>
	<?php } ?>
</div>
