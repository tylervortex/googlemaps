<?php echo $this->Html->script('jquery.min', false); ?>
<?php echo $this->Html->scriptBlock('
	$(document).ready(function() {
		$("#UserUsername").focus();
	});
');
?>
<div style="margin:40px">
	<?php if (!$this->Session->check('Auth.User')) { ?>
	<?php echo $this->Form->create('User', array('action' => 'login', 'encoding' => null)); ?>
	<table class="login">
		<tr>
			<td style="text-align:right"><?php echo $this->Form->label('User.username', 'Usuário:'); ?></td>
			<td><?php echo $this->Form->input('username', array('label' => false, 'style' => 'width:180px')); ?></td>
		</tr>
		<tr>
			<td style="text-align:right"><?php echo $this->Form->label('User.password', 'Senha:'); ?></td>
			<td><?php echo $this->Form->input('password', array('label' => false, 'style' => 'width:180px')); ?></td>
		</tr>
		<tr>
			<td></td>
			<td><?php echo $this->Session->flash('auth'); ?></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td style="text-align:left"><?php echo $this->Form->submit('Login'); ?></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td style="text-align:left"><?php echo $this->Html->link('Esqueci minha senha', array('controller' => 'users', 'action' => 'forgot')); ?></td>
		</tr>
	</table>
	<?php echo $this->Form->end(); ?>
	<?php } ?>
</div>
