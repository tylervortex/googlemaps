<div class="<?php echo $pluralVar;?> view">
	<div class="module">
		<h2><?php echo "<?php __('{$pluralHumanName}');?>";?></h2>
		<div class="actions">
			<ul>
		        <li><?php echo "<?php echo \$this->Html->link('Add', array('action' => 'add'), array('class' => 'add')); ?>"; ?></li>
		        <li><?php echo "<?php echo \$this->Html->link('Edit', array('action' => 'edit', \${$singularVar}['{$modelClass}']['id']), array('class' => 'edit')); ?>"; ?></li>
		        <li><?php echo "<?php echo \$this->Html->link('Delete', array('action' => 'delete', \${$singularVar}['{$modelClass}']['id']), array('class' => 'delete'), sprintf('Delete %s?', \${$singularVar}['{$modelClass}']['{$primaryKey}']), false); ?>"; ?></li>
		        <li><?php echo "<?php echo \$this->Html->link('List', array('action' => 'index'), array('class' => 'list')); ?>"; ?></li>
			<li><?php echo "<?php echo \$this->Session->flash('module'); ?>"; ?>&nbsp;</li>
			</ul>
		</div>
	</div>
	<fieldset>
 	<legend><?php echo "<?php printf(__('" . Inflector::humanize($action) . " %s', true), __('{$singularHumanName}', true)); ?>";?></legend>
	<dl><?php echo "<?php \$i = 0; \$odd = ' class=\"odd\"'; \$even = ' class=\"even\"'; ?>\n";?>
	<?php
	foreach ($fields as $field) {
		$isKey = false;
		if (!empty($associations['belongsTo'])) {
			foreach ($associations['belongsTo'] as $alias => $details) {
				if ($field === $details['foreignKey']) {
					$isKey = true;
					echo "\t\t<dt<?php if (\$i % 2 == 0) echo \$odd; else echo \$even; ?>><?php __('" . Inflector::humanize(Inflector::underscore($alias)) . "'); ?></dt>\n";
					echo "\t\t<dd<?php if (\$i++ % 2 == 0) echo \$odd; else echo \$even; ?>><?php echo \$this->Html->link(\${$singularVar}['{$alias}']['{$details['displayField']}'], array('controller' => '{$details['controller']}', 'action' => 'view', \${$singularVar}['{$alias}']['{$details['primaryKey']}'])); ?>&nbsp;</dd>\n";
					break;
				}
			}
		}
		if ($isKey !== true) {
			echo "\t\t<dt<?php if (\$i % 2 == 0) echo \$odd; else echo \$even; ?>><?php __('" . Inflector::humanize($field) . "'); ?></dt>\n";
			echo "\t\t<dd<?php if (\$i++ % 2 == 0) echo \$odd; else echo \$even; ?>><?php echo \${$singularVar}['{$modelClass}']['{$field}']; ?>&nbsp;</dd>\n";
		}
	}
	?>
	</dl>
	</fieldset>
</div>
