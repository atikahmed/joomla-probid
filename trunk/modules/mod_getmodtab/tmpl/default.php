<?php // no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
?>

<div id="hidetab<?php echo $uniqid ?>" class="hidetabouter">
	<ul class="hidetabs">
		<?php foreach ($list as $item_) : ?>
			<li class="hidetab"><a href="#" id="link_<?php echo $item_->id; ?>" class="linkopen" <?php echo $activator; ?>="hidetabshow('hidemodule_<?php echo $item_->id; ?>');return false"><?php echo $item_->title; ?></a></li>
		<?php endforeach; ?>
	</ul>
	
	<?php foreach ($list as $item_) : ?>
		<div tabindex="-1" class="hidetabcontent tabclosed" id="hidemodule_<?php echo $item_->id; ?>">
			<?php echo $item_->content; ?>
		</div>
	<?php endforeach; ?>
</div>