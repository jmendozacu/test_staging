<?php 
	global $hide_page_title, $full_width_page, $current_page_title; 
	
	$titlebar_enabled = (bool)xt_option('titlebar', null, 1);
	$breadcrumbs = false;	
	$breadcrumbs_enabled = (bool)xt_option('titlebar-breadcrumbs', null, 1);
	
	if($titlebar_enabled && $breadcrumbs_enabled)
		$breadcrumbs = xt_breadcrumbs();
			
?>
<?php  if($titlebar_enabled && empty($hide_page_title) && !empty($current_page_title)) : ?>
<div class="row full-width page-header-row hide-on-mobile-menu">
	<div class="medium-12 column">
		<div class="page-header <?php echo (!empty($breadcrumbs) ? 'has-breadcrumbs' : '');?>">
			<div class="row in-container">
				<div class="medium-<?php echo (!empty($breadcrumbs) ? '8' : '12');?> column">
					<h1 class="hide-for-small-up"><?php echo $current_page_title; ?></h1>
					<div class="page-title" itemprop="name headline">
						<?php echo $current_page_title; ?>					
					</div>
				</div>
				<?php if(!empty($breadcrumbs)): ?>
				<div class="medium-4 column">
					<?php echo $breadcrumbs; ?>
				</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
<?php endif; ?> 
