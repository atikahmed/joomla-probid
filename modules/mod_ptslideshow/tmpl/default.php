<?php
	// no direct access
	defined('_JEXEC') or die;
?>
<?php if(count($slideshow) > 0): ?>
<div class="pt_slideshow<?php echo ' '.$moduleclass_sfx; ?>">
					
	<script language="javascript" type="text/javascript">
		jQuery(document).ready(	
			function($) {
				jQuery(".container").wtRotator({
					width: <?php echo $width; ?>,
					height: <?php echo $height; ?>,
					button_width: <?php echo $button_width; ?>,
					button_height: <?php echo $button_height; ?>,
					button_margin: <?php echo $button_margin; ?>,
					auto_start: <?php echo $auto_start; ?>,
					delay: <?php echo $delay; ?>,
					transition: "<?php echo $transition; ?>",
					transition_speed: <?php echo $transition_speed; ?>,
					auto_center: <?php echo $auto_center; ?>,
					cpanel_position: "<?php echo $cpanel_position; ?>",
					cpanel_align: "<?php echo $cpanel_align; ?>",
					timer_align: "<?php echo $timer_align; ?>",
					display_thumbs: <?php echo $display_thumbs; ?>,
					display_dbuttons: <?php echo $display_dbuttons; ?>,
					display_playbutton: <?php echo $display_playbutton; ?>,
					display_numbers: <?php echo $display_numbers; ?>,
					display_timer: <?php echo $display_timer; ?>,
					mouseover_pause: <?php echo $mouseover_pause; ?>,
					cpanel_mouseover: <?php echo $cpanel_mouseover; ?>,
					text_mouseover: <?php echo $text_mouseover; ?>,
					text_effect: "<?php echo $text_effect; ?>",
					text_sync: <?php echo $text_sync; ?>,
					tooltip_type: "<?php echo $tooltip_type; ?>",
					shuffle: <?php echo $shuffle; ?>,
					block_size: <?php echo $block_size; ?>,
					vert_size: <?php echo $vert_size; ?>,
					horz_size: <?php echo $horz_size; ?>,
					block_delay: <?php echo $block_delay; ?>,
					vstripe_delay: <?php echo $vstripe_delay; ?>,
					hstripe_delay: <?php echo $hstripe_delay; ?>			
				});
			}
		);
	</script> 
				
	<div class="container">
		<div class="wt-rotator">
			<div class="screen">
			</div>
			<div class="c-panel">
				<div class="buttons">
					<div class="prev-btn">prev</div>
					<div class="play-btn">play</div>
					<div class="next-btn">next</div>
				</div>
				<div class="thumbnails">
					<ul>
					<?php
						foreach($slideshow as $item):
					?>
						<?php if($item->url_):?>
						<li>
							<a href="<?php echo $item->url_; ?>" title="<?php echo $item->title_; ?>"></a>					
										
							<div style="top: 5px; left: 484px; width: 336px; height: auto; color: #fff; background-color: #000;">
								<h3><span class="title"><?php echo $item->title_; ?></span></h3>
								<?php echo $item->description_; ?>
							</div>
						</li>
						<?php endif;?>
					<?php
						endforeach
					?>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>
<?php endif; ?>