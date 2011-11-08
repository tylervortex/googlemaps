<?php echo $this->Html->css('tables', null, array('inline' => false)); ?>
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
	<h2>Quick Start</h2>
	<p>
		<b>ARO - Access Request Object</b><br />
		Things (most often users) that want to use stuff are called access request objects
	</p>
	<p>
		<b>ACO - Access Control Object</b><br />
		Things in the system that are wanted (most often actions or data) are called access control objects
	</p>
</div>