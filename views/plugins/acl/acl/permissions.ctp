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
			<b>Navigating The Tree</b><br />
			Try double-clicking on each aro/aco to find out if it has any children. If it does,
			the children will load in the select box. You can move back one level by double-clicking
			the two dots. If you single click an aro/aco, its already assigned permissions appear
			in the chart below.
		</p>
		<p>
			<b>Granting Permissions</b><br />
			Navigate to an aro on the left side and an aco on the right side. When you are ready
			to grant permission, click 'Grant', and you will see the newly assigned permission appear
			below.
		</p>
		<p>
			<b>Revoking Permissions</b><br />
			You can easily revoke a permission by first browsing an aro/aco. When you click on one,
			the granted permissions appear below. You can revoke a permission at any time by clicking
			revoke.
		</p>
	</div>
	<table style="width:100%">
		<thead>
			<tr>
				<th style="font-size:80%;min-width:40%;text-align:center">ARO - Access Request Objects</th>
				<th>&nbsp;</th>
				<th style="font-size:80%;min-width:40%;text-align:center">ACO - Access Control Objects</th>
			</tr>
		</thead>
		<tr>
			<td style="text-align:center">
				<select id="aro_editor_parentId" class="acl_select" size="10">
					<option>Empty</option>
				</select>
			</td>
			<td style="text-align:center;vertical-align:top;padding-top:30px">
				<div id="acl_link_button" class="actions" style="cursor:pointer;font-size:80%;font-weight:bold">
					<span class="grant">Grant</span>
				</div>
			</td>
			<td style="text-align:center">
				<select id="aco_editor_parentId" class="acl_select" size="10">
					<option>Empty</option>
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="3">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="3">
				<div id="aro_permissions"></div>
			</td>
		</tr>
		<tr>
			<td colspan="3">
				<div id="aco_permissions"></div>
			</td>
		</tr>
	</table>
	<?php echo $this->Html->script('jquery.min', false); ?>
	<script type="text/javascript">
		$(document).ready(function() {
			acl_permission_setup();
		});
	</script>