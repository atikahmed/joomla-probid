<?php 
/**
 * RokNewsPager Module
 *
 * @package     RocketTheme
 * @subpackage  roknewspager.tmpl
 * @version   1.6 January 24, 2012
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access'); ?>

<div class="roknewspager-wrapper">
	<?php if($show_comment_count):?><div class="roknewspager-comments"><?php endif; ?>
	<ul class="roknewspager">
	<?php $count=0;?>
	<?php foreach ($list as $item) : $count ++; ?>
	    <li class="<?php if( $count==1 ) echo "first";?>">
			<?php 
				$db = JFactory::getDBO();
				
				$query_cat = 'SELECT cat.title, c.fulltext FROM #__content as c, #__categories as cat WHERE c.id = ' . $item->id . ' and c.catid = cat.id';
				$db->setQuery($query_cat);
					
				$cat = $db->loadObject();
				
				$query_con = 'SELECT jc.jr_city, jc.jr_state, jc.jr_zipcode FROM #__jreviews_content as jc WHERE jc.contentid = ' . $item->id;
				$db->setQuery($query_con);
					
				$con = $db->loadObject();
			?>
			<div class="roknewspager-cat">
				<?php echo $cat->title; ?>
				
			</div>
			
	        <div class="roknewspager-div">
	            <?php if($show_thumbnails && $item->thumb): ?>
	                <?php if($thumbnail_link):?><a href="<?php echo $item->link; ?>"> <?php endif;?>
                    <img src="<?php echo $item->thumb;?>" alt="<?php echo $item->title; ?>" />
                    <?php if($thumbnail_link):?></a> <?php endif;?>
	            <?php endif;?>
	            <?php if($show_title && $item->title):?><a href="<?php echo $item->link; ?>" class="roknewspager-title"><?php echo $item->title; ?></a><?php endif;?>
				
				<?php if($show_comment_count):?><div class="commentcount"><span><?php echo $item->comment_count; ?></span></div><?php endif;?>
				<?php if($show_author && $item->author):?><?php echo JText::_('by'); ?><span class="author"> <?php echo $item->author; ?>, </span><?php endif;?>
				<?php if($show_published_date && $item->published_date):?><span class="published-date"><?php echo JHTML::_('date', $item->published_date, JText::_('DATE_FORMAT_LC33')); ?></span><?php endif;?>
				
				<?php if($show_preview_text):?><div class="fulltext"><?php echo $cat->fulltext; ?></div><?php endif;?>
				
	            <?php if($show_ratings && $item->rating):?>
					<div class="article-rating">
						<div class="rating-bar">
							<div style="width:<?php echo $item->rating; ?>%"></div>
						</div>
					</div>
				<?php endif;?>
	            <?php if($show_readmore):?><a href="<?php echo $item->link; ?>" class="readon"><span><?php echo $readmore_text;?></span></a><?php endif;?>
				
				<div class="content-info">
					<?php echo $con->jr_city; ?>, <?php echo str_replace("*", "", $con->jr_state); ?>, <?php echo $con->jr_zipcode; ?>
				</div>
	        </div>
	    </li>
	<?php endforeach; ?>
	</ul>
	<?php if($show_comment_count):?></div><?php endif; ?>
</div>
<?php
	$disabled = ($pages == 1) ? " style='display: none;'" : '';
?>
<?php if($show_paging):?>
<div class="roknewspager-pages" <?php echo $disabled; ?>>
	<div class="roknewspager-spinner"></div>
    <div class="roknewspager-pages2">
        <div class="roknewspager-prev"></div>
        <div class="roknewspager-next"></div>
        <ul class="roknewspager-numbers">
            <?php for($x=1;$x<=$pages && $x < ($params->get('maxpages',8)+1);$x++):?>
            <li <?php if($x==$curpage):?>class="active"<?php endif; ?>><?php echo $x; ?></li>
            <?php endfor;?>
        </ul>
    </div>
</div>
<?php endif;?>
<?php
	$autoupdate = ($params->get('autoupdate', false)) ? 1 : 0;
	$autoupdate_delay = $params->get('autoupdate_delay', 5000);
	$moduleType = ($params->get('module_ident','name')=='name') ? "module=" . $module_name : "moduleid=" . $module_id;

	$url = JRoute::_( 'index.php?option=com_rokmodule&tmpl=component&type=raw&'.$moduleType.'&offset=_OFFSET_', true );
?>
<script type="text/javascript">
	RokNewsPagerStorage.push({
		'url': '<?php echo $url; ?>',
		'autoupdate': <?php echo $autoupdate; ?>, 
		'delay': <?php echo $autoupdate_delay; ?>
	});
</script>