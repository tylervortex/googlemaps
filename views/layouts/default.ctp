<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php echo $this->Html->charset(); ?>
	<title><?php echo $title_for_layout; ?></title>
	<?php echo $this->Html->meta('icon'); ?>
	<?php echo $this->Html->css('garland'); ?>
	<?php echo $this->Html->css('navigation'); ?>
	<?php echo $this->Html->css('modules'); ?>
	<?php echo $scripts_for_layout; ?>

<script type="text/javascript">
var _gaq = _gaq || [];
_gaq.push(['_setAccount', 'UA-21396501-2']);
_gaq.push(['_setDomainName', '.com.br']);
_gaq.push(['_trackPageview']);

(function() {
	var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();
</script>

</head>
<body class="sidebars">
	<div id="header-region" class="clear-block">
		<?php if (!$this->Session->check('Auth.User')) { ?>
		<?php echo $this->Html->link('Login', '/users/login'); ?>
		<?php } else { ?>
		<?php echo $this->Session->read('Auth.User.name'); ?> |
		<?php echo $this->Html->link('Preferências', '/preferences'); ?> |
		<?php echo $this->Html->link('Sair', '/users/logout'); ?>
		<?php } ?>
	</div>
	<div id="wrapper">
		<div id="container" class="clear-block">
			<div id="header">
				<div id="logo-floater">
					<?php echo $this->Html->link($this->Html->image('header.logo.png', array('title' => 'GoogleMaps', 'alt' => 'GoogleMaps', 'border' => 0)), '/', array('escape' => false), null, false); ?>
					<div style="float:right">
					<ul id="menu" class="navigation">
						<!--<?php if (isset($perm) && $perm['reports']) { ?><li><?php echo $this->Html->link('Relatórios', array('plugin' => null, 'controller' => 'reports', 'action' => 'index')); ?>&nbsp;</li><?php } ?>-->
						<?php if (isset($perm) && $perm['tools']) { ?><li><?php echo $this->Html->link('Ferramentas', array('plugin' => null, 'controller' => 'tools', 'action' => 'index')); ?>&nbsp;</li><?php } ?>
						<?php if (isset($perm) && $perm['sites']) { ?><li><?php echo $this->Html->link('Pontos de medição', array('plugin' => null, 'controller' => 'sites', 'action' => 'index')); ?>&nbsp;</li><?php } ?>
						<?php if (isset($perm) && $perm['meters']) { ?><li><?php echo $this->Html->link('Pontos de medição', array('plugin' => null, 'controller' => 'sites', 'action' => 'meters')); ?>&nbsp;</li><?php } ?>
						<?php if (isset($perm) && $perm['registers']) { ?><li><?php echo $this->Html->link('Cadastros', array('plugin' => null, 'controller' => 'registers', 'action' => 'index')); ?>&nbsp;</li><?php } ?>
					</ul>
					</div>
				</div>
			</div>
			<div id="center">
				<div id="squeeze">
					<div class="right-corner">
						<div class="left-corner">
							<div class="content clear-block">
								<?php echo $content_for_layout; ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="footer">
			<p>2011 © <?php echo $this->Html->link($this->Html->image('empresa.png', array('title' => 'Empresa', 'border' => 0)), 'empresa', array('escape' => false), null, false); ?></p>
		</div>
	</div>
</body>
</html>
