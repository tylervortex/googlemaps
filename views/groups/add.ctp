<?php echo $this->Html->css('tables', null, array('inline' => false)); ?>
<div class="module-title">Grupos</div>
<div class="groups form">
	<div class="module">
		<?php echo $this->element('groups_actions'); ?>
	</div>
	<?php echo $this->Session->flash('auth'); ?>
	<?php echo $this->Form->create('Group', array('encoding' => null)); ?>
	<fieldset>
 		<legend>Adicionar grupo</legend>
		<?php
		echo $this->Form->input('name', array('label' => 'Grupo'));
		echo $this->Form->input('description', array('label' => 'Descrição'));
		echo $this->Form->input('parent', array('label' => 'Grupo pai'));
		?>
	</fieldset>
	<?php echo $this->Form->end('Salvar'); ?>
</div>
