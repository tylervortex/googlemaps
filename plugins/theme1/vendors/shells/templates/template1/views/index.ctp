<div class="<?php echo $pluralVar;?> index">
	<div class="module">
		<h2><?php echo "<?php __('{$pluralHumanName}');?>";?></h2>
		<div class="actions">
			<ul>
		        <li><?php echo "<?php echo \$this->Html->link('Add', array('action' => 'add'), array('class' => 'add')); ?>"; ?></li>
			<li><?php echo "<?php echo \$this->Session->flash('module'); ?>"; ?>&nbsp;</li>
			</ul>
		</div>
	</div>
	<table class="list" cellpadding="0" cellspacing="0">
		<tr>
		<?php foreach ($fields as $field): ?>
	<th><?php echo "<?php echo \$this->Paginator->sort('{$field}');?>";?></th>
		<?php endforeach; ?>
	<th class="actions">Actions</th>
		</tr>
		<?php
		echo "<?php
		\$i = 0;
		foreach (\${$pluralVar} as \${$singularVar}):
			\$class = null;
			if (\$i++ % 2 == 0) {
				\$class = ' class=\"odd\"';
			} else {
				\$class = ' class=\"even\"';
			}
		?>\n";
	echo "\t\t<tr<?php echo \$class;?>>\n";
		foreach ($fields as $field) {
			$isKey = false;
			if (!empty($associations['belongsTo'])) {
				foreach ($associations['belongsTo'] as $alias => $details) {
					if ($field === $details['foreignKey']) {
						$isKey = true;
						echo "\t\t\t<td><?php echo \$this->Html->link(\${$singularVar}['{$alias}']['{$details['displayField']}'], array('controller' => '{$details['controller']}', 'action' => 'view', \${$singularVar}['{$alias}']['{$details['primaryKey']}'])); ?></td>\n";
						break;
					}
				}
			}
			if ($isKey !== true) {
				echo "\t\t\t<td><?php echo \${$singularVar}['{$modelClass}']['{$field}']; ?></td>\n";
			}
		}
		echo "\t\t\t<td class=\"actions\">\n";
		echo "\t\t\t\t<?php echo \$this->Html->link(\$this->Html->image('view.icon.png', array('title' => 'View', 'alt' => 'View', 'border' => 0)), array('action' => 'view', \${$singularVar}['{$modelClass}']['{$primaryKey}']), array('escape' => false), null, false); ?>\n";
		echo "\t\t\t\t<?php echo \$this->Html->link(\$this->Html->image('edit.icon.png', array('title' => 'Edit', 'alt' => 'Edit', 'border' => 0)), array('action' => 'edit', \${$singularVar}['{$modelClass}']['{$primaryKey}']), array('escape' => false), null, false); ?>\n";
		echo "\t\t\t\t<?php echo \$this->Html->link(\$this->Html->image('delete.icon.png', array('title' => 'Delete', 'alt' => 'Delete', 'border' => 0)), array('action' => 'delete', \${$singularVar}['{$modelClass}']['{$primaryKey}']), array('escape' => false), sprintf('Delete %s?', \${$singularVar}['{$modelClass}']['{$primaryKey}']), false); ?>\n";
		echo "\t\t\t</td>\n";
	echo "\t\t</tr>\n";
	echo "\t\t<?php endforeach; ?>\n";
?>
	</table>
	<?php echo "<?php echo \$this->element('paging'); ?>\n"; ?>
</div>
