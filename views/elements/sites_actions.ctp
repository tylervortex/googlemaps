<div class="actions">
	<ul style="float:left">
		<?php if ($permsites['add'] && $this->action != 'add') {; ?><li><?php echo $this->Html->link('Adicionar', array('controller' => 'sites', 'action' => 'add'), array('class' => 'add')); ?></li><?php } ?>
		<?php if ($permsites['view'] && $this->action != 'view' && isset($site)) {; ?><li><?php echo $this->Html->link('Dados cadastrais', array('controller' => 'sites', 'action' => 'view', $site['Site']['id']), array('class' => 'view')); ?></li><?php } ?>
		<?php if ($permsites['access'] && $this->action != 'access' && isset($site)) { ?><li><?php echo $this->Html->link('Permissões', array('controller' => 'users', 'action' => 'access', $site['Site']['id']), array('class' => 'access')); ?></li><?php } ?>
		<?php if ($permsites['table'] && $this->action != 'table' && isset($site)) {; ?><li><?php echo $this->Html->link('Coleta de dados', array('controller' => 'sites', 'action' => 'table', $site['Site']['id']), array('class' => 'table')); ?></li><?php } ?>
		<?php if ($permsites['hydro'] && $this->action != 'hydro' && isset($site)) {; ?><li><?php echo $this->Html->link('Curva-chave', array('controller' => 'sites', 'action' => 'hydro', $site['Site']['id']), array('class' => 'keycurve')); ?></li><?php } ?>
		<?php if ($permsites['gallery'] && $this->action != 'gallery' && isset($site)) {; ?><li><?php echo $this->Html->link('Fotos', array('controller' => 'sites', 'action' => 'gallery', $site['Site']['id']), array('class' => 'gallery')); ?></li><?php } ?>
		<?php if ($permsites['edit'] && $this->action != 'edit' && isset($site)) {; ?><li><?php echo $this->Html->link('Editar', array('controller' => 'sites', 'action' => 'edit', $site['Site']['id']), array('class' => 'edit')); ?></li><?php } ?>
		<?php if ($permsites['delete'] && $this->action != 'delete' && isset($site)) {; ?><li><?php echo $this->Html->link('Remover', array('controller' => 'sites', 'action' => 'delete', $site['Site']['id']), array('class' => 'delete'), 'Remover o ponto de medição ' . $site['Site']['short_name'] . '?'); ?></li><?php } ?>
		<?php if ($permsites['index'] && $this->action != 'index') {; ?><li><?php echo $this->Html->link('Pontos de medição', array('controller' => 'sites', 'action' => 'index'), array('class' => 'list')); ?></li><?php } ?>
		<?php if ($permsites['meters'] && $this->action != 'meters') {; ?><li><?php echo $this->Html->link('Listar', array('controller' => 'sites', 'action' => 'meters'), array('class' => 'list')); ?></li><?php } ?>
		<?php if ($permsites['map'] && $this->action != 'map') {; ?><li><?php echo $this->Html->link('Mapa', array('controller' => 'sites', 'action' => 'map'), array('class' => 'map')); ?></li><?php } ?>
		<li><?php echo $this->Session->flash('module'); ?>&nbsp;</li>
	</ul>
	<?php if (isset($sites) && count($sites) > 1) { ?>
	<div class="sites-select">
	Selecionar ponto de medição<br />
	<?php echo $this->Form->input('sites', array(
		'id' => 'combobox',
		'label' => false,
		'div' => false,
		'type' => 'select',
		'options' => $sites
	)); ?>
	</div>
	<?php } else { ?>
	<br />
	<?php } ?>
</div>
