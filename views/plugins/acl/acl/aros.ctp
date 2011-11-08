<?php echo $this->Html->css('tables', null, array('inline' => false)); ?>
<?php print $this->element('acl_scripts'); ?>
<div class="module-title">Privilégios</div>
<div class="users index">
	<div class="module">
		<div class="actions">
			<ul>
				<li><?php echo $this->Html->link('Aros', array('plugin' => 'acl', 'controller' => 'acl', 'action' => 'aros'), array('class' => 'aros')); ?></li>
				<li><?php echo $this->Html->link('Acos', array('plugin' => 'acl', 'controller' => 'acl', 'action' => 'acos'), array('class' => 'acos')); ?></li>
				<li><?php echo $this->Html->link('Permissões', array('plugin' => 'acl', 'controller' => 'acl', 'action' => 'permissions'), array('class' => 'permissions')); ?></li>
				<li><?php echo $this->Session->flash('module'); ?>&nbsp;</li>
			</ul>
		</div>
	</div>
	<div style="font-size:80%">
		<p>
			<b>ARO - Access Request Object</b><br />
			Things (most often users) that want to use stuff are called access request objects
		</p>
	</div>
	<table>
		<tr>
			<td>
				<select id="aro_editor_parentId" class="acl_select" size="10">
					<option>Empty</option>
				</select>
				<p><input id="aro_editor_edit" type="button" value="Edit"></p>
			</td>
			<td style="padding-left:50px;vertical-align:top">
				<table>
					<tr>
						<td>Alias</td>
							  <td><input id="aro_editor_alias" type="text"></td>
						</tr>
						<tr>
							<td>Model</td>
							<td><input id="aro_editor_model" type="text"></td>
						</tr>
						<tr>
							<td>Key</td>
							<td><input id="aro_editor_foreignKey" type="text"></td>
						</tr>
						<tr>
							<td></td>
							<td>
								<input id="aro_editor_create" type="button" value="Create">
								<input id="aro_editor_cancel" type="button" value="Cancel" style="display:none">
								<input id="aro_editor_update" type="button" value="Update" style="display:none">
								<input id="aro_editor_delete" type="button" value="Delete" style="display:none">
							</td>
						</tr>
				</table>
				<input id="aro_editor_id" type="hidden">
				<input id="aro_editor_originalParentId" type="hidden">
			</td>
		</tr>
	</table>
	<?php echo $this->Html->script('jquery.min', false); ?>
	<script type="text/javascript">
		$(document).ready(function() {
			acl_aro_setup();
		});
	</script>