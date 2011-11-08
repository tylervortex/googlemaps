<?php echo $this->Html->css('tables', null, array('inline' => false)); ?>
<div class="module-title">Grupos</div>
<div class="groups view">
	<div class="module">
		<?php echo $this->element('groups_actions'); ?>
	</div>
	<?php echo $this->Session->flash('auth'); ?>
	<fieldset>
		<legend>Detalhes: <span class="item"><?php echo $group['Group']['name']; ?></span></legend>
		<dl><?php $i = 0; $odd = ' class="odd"'; $even = ' class="even"'; ?>
			<dt<?php if ($i % 2 == 0) echo $odd; else echo $even; ?>>Nome</dt>
			<dd<?php if ($i++ % 2 == 0) echo $odd; else echo $even; ?>><?php echo $group['Group']['name']; ?>&nbsp;</dd>
			<dt<?php if ($i % 2 == 0) echo $odd; else echo $even; ?>>Descrição</dt>
			<dd<?php if ($i++ % 2 == 0) echo $odd; else echo $even; ?>><?php echo $group['Group']['description']; ?>&nbsp;</dd>
			<dt<?php if ($i % 2 == 0) echo $odd; else echo $even; ?>>Criação</dt>
			<dd<?php if ($i++ % 2 == 0) echo $odd; else echo $even; ?>><?php echo $this->Formatacao->dataHora($group['Group']['created']); ?>&nbsp;</dd>
			<dt<?php if ($i % 2 == 0) echo $odd; else echo $even; ?>>Modificação</dt>
			<dd<?php if ($i++ % 2 == 0) echo $odd; else echo $even; ?>><?php echo $this->Formatacao->dataHora($group['Group']['modified']); ?>&nbsp;</dd>
		</dl>
	</fieldset>
</div>
