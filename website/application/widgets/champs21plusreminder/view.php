<ul class="reminder_set">	
	<?php //echo "<pre>";print_r($data);?>
	<!--HOMEWORK-->
	<?php if(!empty($data['homework'])):?>	
		<li><div class="reminder_title">Homework</div></li>
		<?php foreach($data['homework'] as $d):?>
		<li>
			<div class="reminderbox <?php if($d['is_read']==="false"):?>unread<?php endif;?>">
			<?php echo $d['sender'];?>&nbsp;post&nbsp;<?php echo $d['subject'];?>
			</div>
		</li>
		<?php endforeach; ?>
	<?php endif; ?>
	
	<!--Event-->
	<?php if(!empty($data['event'])):?>	
		<li><div class="reminder_title">Event</div></li>
		<?php foreach($data['event'] as $d):?>
		<li>
			<div class="reminderbox <?php if($d['is_read']==="false"):?>unread<?php endif;?>">
			<?php echo $d['sender'];?>&nbsp;post&nbsp;<?php echo $d['subject'];?>
			</div>
		</li>
		<?php endforeach; ?>
	<?php endif; ?>
	
	<!--Fee-->
	<?php if(!empty($data['fee'])):?>	
		<li><div class="reminder_title">Fee</div></li>
		<?php foreach($data['fee'] as $d):?>
		<li>
			<div class="reminderbox <?php if($d['is_read']==="false"):?>unread<?php endif;?>">
			<?php echo $d['sender'];?>&nbsp;post&nbsp;<?php echo $d['subject'];?>
			</div>
		</li>
		<?php endforeach; ?>
	<?php endif; ?>
	
	<!--Result-->
	<?php if(!empty($data['result'])):?>	
		<li><div class="reminder_title">Result</div></li>
		<?php foreach($data['result'] as $d):?>
		<li>
			<div class="reminderbox <?php if($d['is_read']==="false"):?>unread<?php endif;?>">
			<?php echo $d['sender'];?>&nbsp;post&nbsp;<?php echo $d['subject'];?>
			</div>
		</li>
		<?php endforeach; ?>
	<?php endif; ?>
	
	<!--Exam-->
	<?php if(!empty($data['exam'])):?>	
		<li><div class="reminder_title">Exam</div></li>
		<?php foreach($data['exam'] as $d):?>
		<li>
			<div class="reminderbox <?php if($d['is_read']==="false"):?>unread<?php endif;?>">
			<?php echo $d['sender'];?>&nbsp;post&nbsp;<?php echo $d['subject'];?>
			</div>
		</li>
		<?php endforeach; ?>
	<?php endif; ?>
	
	<!--Others-->
	<?php if(!empty($data['others'])):?>	
		<li><div class="reminder_title">Others</div></li>
		<?php foreach($data['others'] as $d):?>
		<li>
			<div class="reminderbox <?php if($d['is_read']==="false"):?>unread<?php endif;?>">
			<?php echo $d['sender'];?>&nbsp;post&nbsp;<?php echo $d['subject'];?>
			</div>
		</li>
		<?php endforeach; ?>
	<?php endif; ?>
</ul>


<style>
	ul.reminder_set
	{
		margin:0px;
	}
	.reminder_set li
	{		
		padding:0px;
		margin:0px;
		border-bottom:1px solid #ccc;
		cursor:pointer;
	}	
	
	.reminder_set li .reminderbox
	{
		font-size:12px;
		padding:10px;
	}
	.reminder_set li .reminder_title
	{
		font-size:12px;
		padding:10px;
		font-weight:700;
	}
	.reminder_set li .reminderbox:hover
	{
		background:#F5F9FD;
	}
	.reminder_set li .unread
	{
		background:#DEECF9;
	}
</style>