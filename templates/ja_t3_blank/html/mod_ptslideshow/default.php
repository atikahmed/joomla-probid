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
		});
	</script> 
				
	<div class="container" style="width:484px; float: left;">
		<div class="wt-rotator">
			<div class="screen">
			</div>
			<div class="c-panel">
				<div class="thumbnails">
					<ul>
					<?php
						foreach($slideshow as $item):
					?>
						<?php if($item->url_):?>
						<li>
							<a href="<?php echo $item->url_; ?>" title="<?php echo $item->title_; ?>"></a>					
						</li>
						<?php endif;?>
					<?php
						endforeach;
					?>
					</ul>
				</div>
			</div>
		</div>
	</div>
	
	
	<script type="text/javascript">
	window.addEvent("domready", function() {
		var gallery22 = new slideGallery($$(".gallery22"), {
			steps: 1,
			mode: "line",
			autoplay: false,
			paging: true,
			pagingHolder: ".pagingSlide",
			onStart: function() {
				//this.gallery.getElement(".info").set("html", parseInt(this.current+1) + "-" + parseInt(this.visible+this.current) + " from " + this.items.length);
			},
			onPlay: function() { this.fireEvent("start"); }
		});
	});
</script>
	
	<div class="gallery gallery22">
		<div class="holder">
			<ul>
			<?php
				foreach($slideshow as $item):
			?>
				<li>
					<?php echo $item->description_; ?>				
				</li>
			<?php
				endforeach;
			?>
			</ul>
		</div>
		<div class="control">
			<a href="#" class="prev">prev</a>
			<a href="#" class="next">next</a>
			<span class="info"></span>
		</div>
		<div class="pagingSlide"></div>
	</div>

</div>
<?php endif; ?>

<script type="text/javascript">
	jQuery(document).ready(function($) {
		
		$('.pagingSlide ul li a').click(function() {
			var index = $(this).html() - 1;
			$('.c-panel .thumbnails ul li').get(index).click();
		});
		
	});
</script>