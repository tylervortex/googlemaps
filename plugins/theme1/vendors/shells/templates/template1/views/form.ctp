<div class="<?php echo $pluralVar;?> form">
	<div class="module">
		<h2><?php echo "<?php __('{$pluralHumanName}');?>";?></h2>
		<div class="actions">
			<ul>
			<?php if (strpos($action, 'add') === false) { ?>
		        <li><?php echo "<?php echo \$this->Html->link('Add', array('action' => 'add'), array('class' => 'add')); ?>"; ?></li>
		        <li><?php echo "<?php echo \$this->Html->link('View', array('action' => 'view', \${$singularVar}['{$modelClass}']['id']), array('class' => 'view')); ?>"; ?></li>
		        <li><?php echo "<?php echo \$this->Html->link('Delete', array('action' => 'delete', \${$singularVar}['{$modelClass}']['id']), array('class' => 'delete'), sprintf('Delete %s?', \${$singularVar}['{$modelClass}']['{$primaryKey}']), false); ?>"; ?></li>
			<?php } ?>
		        <li><?php echo "<?php echo \$this->Html->link('List', array('action' => 'index'), array('class' => 'list')); ?>"; ?></li>
			<li><?php echo "<?php echo \$this->Session->flash('module'); ?>"; ?>&nbsp;</li>
			</ul>
		</div>
	</div>
	<?php echo "<?php echo \$this->Form->create('{$modelClass}');?>\n";?>
	<fieldset>
 		<legend><?php echo "<?php printf(__('" . Inflector::humanize($action) . " %s', true), __('{$singularHumanName}', true)); ?>";?></legend>
		<?php
		echo "\t<?php\n";
		foreach ($fields as $field) {
			if (strpos($action, 'add') !== false && $field == $primaryKey) {
				continue;
			} elseif (!in_array($field, array('created', 'modified', 'updated'))) {
				echo "\t\techo \$this->Form->input('{$field}');\n";
			}
		}
		if (!empty($associations['hasAndBelongsToMany'])) {
			foreach ($associations['hasAndBelongsToMany'] as $assocName => $assocData) {
				echo "\t\techo \$this->Form->input('{$assocName}');\n";
			}
		}
		echo "\t?>\n";
?>
	</fieldset>
<?php
	echo "<?php echo \$this->Form->end(__('Submit', true));?>\n";
?>
</div>
