<ul class="reminder_set">	
	<?php //echo "<pre>";print_r($data);?>
	<!--HOMEWORK-->
	<?php if(!empty($data['homework'])):?>	
		<li><div class="reminder_title f2 homework-icon">Homework</div></li>
		<?php foreach($data['homework'] as $d):?>
		<li>
			<div class="reminderbox <?php if($d['is_read']==="false"):?>unread<?php endif;?>">
                            <?php $datediff = get_post_time($d['created_at']); ?>
                            <a href="<?php echo base_url().'schools';?>"><?php echo $d['sender'];?>&nbsp;post&nbsp;<?php echo $d['subject'];?><span style="font-size:10px;"><br /><?php echo $datediff." ago";?></span></a>
			</div>
		</li>
		<?php endforeach; ?>
	<?php endif; ?>
	
	<!--Event-->
	<?php if(!empty($data['event'])):?>	
		<li><div class="reminder_title f2 event-icon">Event</div></li>
		<?php foreach($data['event'] as $d):?>
		<li>
			<?php $datediff = get_post_time($d['created_at']); ?>
                        <div class="reminderbox <?php if($d['is_read']==="false"):?>unread<?php endif;?>">
			<a href="<?php echo base_url().'schools';?>"><?php echo $d['sender'];?>&nbsp;post&nbsp;<?php echo $d['subject'];?><span style="font-size:10px;"><br /><?php echo $datediff." ago";?></a>
			</div>
		</li>
		<?php endforeach; ?>
	<?php endif; ?>
	
	<!--Fee-->
	<?php if(!empty($data['fee'])):?>	
		<li><div class="reminder_title f2 fee-icon">Fee</div></li>
		<?php foreach($data['fee'] as $d):?>
		<li>
			<?php $datediff = get_post_time($d['created_at']); ?>
                        <div class="reminderbox <?php if($d['is_read']==="false"):?>unread<?php endif;?>">
			<a href="<?php echo base_url().'schools';?>"><?php echo $d['sender'];?>&nbsp;post&nbsp;<?php echo $d['subject'];?><span style="font-size:10px;"><br /><?php echo $datediff." ago";?></a>
			</div>
		</li>
		<?php endforeach; ?>
	<?php endif; ?>
	
	<!--Result-->
	<?php if(!empty($data['result'])):?>	
		<li><div class="reminder_title f2 result-icon">Result</div></li>
		<?php foreach($data['result'] as $d):?>
		<li>
			<?php $datediff = get_post_time($d['created_at']); ?>
                        <div class="reminderbox <?php if($d['is_read']==="false"):?>unread<?php endif;?>">
			<a href="<?php echo base_url().'schools';?>"><?php echo $d['sender'];?>&nbsp;post&nbsp;<?php echo $d['subject'];?><span style="font-size:10px;"><br /><?php echo $datediff." ago";?></a>
			</div>
		</li>
		<?php endforeach; ?>
	<?php endif; ?>
	
	<!--Exam-->
	<?php if(!empty($data['exam'])):?>	
		<li><div class="reminder_title f2 exam-icon">Exam</div></li>
		<?php foreach($data['exam'] as $d):?>
		<li>
			<?php $datediff = get_post_time($d['created_at']); ?>
                        <div class="reminderbox <?php if($d['is_read']==="false"):?>unread<?php endif;?>">
			<a href="<?php echo base_url().'schools';?>"><?php echo $d['sender'];?>&nbsp;post&nbsp;<?php echo $d['subject'];?><span style="font-size:10px;"><br /><?php echo $datediff." ago";?></a>
			</div>
		</li>
		<?php endforeach; ?>
	<?php endif; ?>
	
	<!--Others-->
	<?php if(!empty($data['others'])):?>	
		<li><div class="reminder_title f2 other-icon">Others</div></li>
		<?php foreach($data['others'] as $d):?>
		<li>
			<?php $datediff = get_post_time($d['created_at']); ?>
                        <div class="reminderbox <?php if($d['is_read']==="false"):?>unread<?php endif;?>">
			<a href="<?php echo base_url().'schools';?>"><?php echo $d['sender'];?>&nbsp;post&nbsp;<?php echo $d['subject'];?><span style="font-size:10px;"><br /><?php echo $datediff." ago";?></a>
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
                width: 100%;
	}
	.reminder_set li .reminder_title
	{
		font-size:16px;
		padding:10px 0 10px 50px;
		font-weight:700;
	}
	.reminder_set li .reminderbox:hover
	{
		background:#414952;
	}
        .reminder_set li .reminderbox:hover a
        {
            color:#fff;
        }
	.reminder_set li .unread
	{
		background:#F7F7F7;
	}
        .reminderbox a
        {
            color: #666666;
        }
        .homework-icon
        {
            background: url('/styles/layouts/tdsfront/images/icons/homework-icon.png') no-repeat;
            background-position: 10px;
        }
        .event-icon
        {
            background: url('/styles/layouts/tdsfront/images/icons/event-icon.png') no-repeat;
            background-position: 10px;
        }
        .fee-icon
        {
            background: url('/styles/layouts/tdsfront/images/icons/fee-icon.png') no-repeat;
            background-position: 10px;
        }
        .result-icon
        {
            background: url('/styles/layouts/tdsfront/images/icons/result-icon.png') no-repeat;
            background-position: 10px;
        }
        .exam-icon
        {
            background: url('/styles/layouts/tdsfront/images/icons/exam-icon.png') no-repeat;
            background-position: 10px;
        }
        .other-icon
        {
            background: url('/styles/layouts/tdsfront/images/icons/other-icon.png') no-repeat;
            background-position: 10px;
        }
</style>