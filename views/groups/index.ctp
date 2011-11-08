<?php echo $this->Html->css('tables', null, array('inline' => false)); ?>
<div class="module-title">Grupos</div>
<div class="groups index">
	<div class="module">
		<?php echo $this->element('groups_actions'); ?>
	</div>
	<?php echo $this->Session->flash('auth'); ?>
	<?php if (!$groups) { ?>
	Nenhum grupo cadastrado.
	<?php } else { ?>
	<table class="list" cellpadding="0" cellspacing="0">
		<tr>
			<th><?php echo $this->Paginator->sort('Nome','name');?></th>
			<th>Grupo pai</th>
			<th>Descrição</th>
			<th class="actions">Ações</th>
		</tr>
		<?php
		$i = 0;
		foreach ($groups as $group):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = 'class="odd"';
			} else {
				$class = 'class="even"';
			}
		?>
		<tr <?php echo $class; ?>>
			<td style="text-align:left"><?php echo $group['Group']['name']; ?></td>
			<td style="text-align:left"><?php echo ($group['Group']['parent_id'] ? $groupList[$group['Group']['parent_id']] : ''); ?></td>
			<td style="text-align:left"><?php echo $group['Group']['description']; ?></td>
			<td class="actions">
				<?php if ($permgroups['view']) { echo $this->Html->link('', array('controller' => 'groups', 'action' => 'view', $group['Group']['id']), array('escape' => false, 'class' => 'actions view', 'title' => 'Visualizar detalhes'), null, false); } ?>
				<?php if ($permgroups['edit']) { echo $this->Html->link('', array('action' => 'edit', $group['Group']['id']), array('escape' => false, 'class' => 'actions edit', 'title' => 'Editar'), null, false); } ?>
				<?php if ($permgroups['delete']) { echo $this->Html->link('', array('action' => 'delete', $group['Group']['id']), array('escape' => false, 'class' => 'actions delete', 'title' => 'Remover grupo'), 'Remover o grupo ' . $group['Group']['name'] . '?\n\nOs usuários deste grupo ficarão sem grupo.'); } ?>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
	<?php echo $this->element('paging'); ?>
	<?php } ?>
</div>
