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
</head>
<body class="sidebars">
	<div id="header-region" class="clear-block">
		<?php if (!$this->Session->check('Auth.User')) { ?>
		<?php echo $this->Html->link('Login', '/users/login'); ?>
		<?php } else { ?>
		<?php echo $this->Session->read('Auth.User.name'); ?> |
		<?php echo $this->Html->link('Preferências', '/users/preferences'); ?> |
		<?php echo $this->Html->link('Sair', '/users/logout'); ?>
		<?php } ?>
	</div>
	<div id="wrapper">
		<div id="container" class="clear-block">
			<div id="header">
				<div id="logo-floater">
					<?php echo $this->Html->link($this->Html->image('header.logo.png', array('title' => 'GoogleMaps', 'alt' => 'GoogleMaps', 'border' => 0)), '/', array('escape' => false), null, false); ?>
					<div style="float:right">
					<?php if (isset($menu) && !empty($menu)) { ?>
					<ul id="menu" class="navigation">
						<?php foreach ($menu as $m) { ?>
						<li><?php echo $this->Html->link($m['title'], $m['url']); ?></li>
						<?php } ?>
					</ul>
					<?php } ?>
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
			2011 © GoogleMaps
		</div>
	</div>
</body>
</html>
