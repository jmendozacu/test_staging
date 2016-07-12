<?php
global $XT_Theme;
?>
<!-- CSS -->
<link rel="stylesheet" href="<?php echo XT_ADMIN_URL; ?>/assets/css/blueprint-css/screen.css" type="text/css" media="screen, projection">
<!--[if lt IE 8]><link rel="stylesheet" href="<?php echo XT_ADMIN_URL; ?>/assets/blueprint-css/ie.css" type="text/css" media="screen, projection"><![endif]-->
<link rel="stylesheet" href="<?php echo XT_ADMIN_URL; ?>/assets/css/blueprint-css/plugins/fancy-type/screen.css" type="text/css" media="screen, projection">
<link rel="stylesheet" href="<?php echo XT_ADMIN_URL; ?>/assets/css/docs.css" type="text/css" media="screen, projection">
<link href="//fonts.googleapis.com/css?family=Roboto:400,700" rel="stylesheet" type="text/css">

<script>
jQuery(document).ready(function(e) {
	jQuery('.toggle').on('click', function(e) {
		e.preventDefault();
		$toggle = jQuery(this);
		$target = jQuery($toggle.attr('href'));
		
		$target.toggle('fade', function() {
			
			if(jQuery(this).is(':hidden')) {
				$toggle.text('+');
			}else{
				$toggle.text('-');
			}
		});
	});	
	
	jQuery('#changelog-toggle').click();
});	
</script>

<div class="xt-docs">
	<div class="container">
		
		<img src="<?php echo XT_ADMIN_URL.'/assets/img/banner.png'; ?>">
		<br>
		
		<h1><?php echo $XT_Theme->fullname; ?></h1>
		
		<div class="borderTop">
			<div class="span-8 colborder info prepend-1">
				<p class="prepend-top">
					<strong>
						Version: <?php echo $XT_Theme->version; ?><br>
						Created: <?php echo $XT_Theme->launch_date; ?><br>
						By: <a href="<?php echo $XT_Theme->author_uri; ?>"><?php echo $XT_Theme->author; ?></a><br>
					</strong>
				</p>
			</div>

			<div class="span-10 last">
				<p class="prepend-top append-0"><strong class="thankyou">Thank you for purchasing our theme.</strong><br>Don't hesitate to <a target="_blank" href="http://themeforest.net/user/xplodedthemes">contact us</a> if you have any questions.</p>
			</div>
		</div>

		<hr>
		<h2><span class="el-icon-book"></span> Documentation</h2>
		
		<p>
			<a target="_blank" href="<?php echo esc_attr(XT_DOCS_URL);?>">Visit our online documentation</a>
		</p>

		<hr>
		<h2><span class="el-icon-group"></span> Support</h2>
		
		<p>
			<a target="_blank" href="http://xt.ticksy.com/">Visit our online Help Desk</a>
		</p>
		
		<hr>
		<h2><span class="el-icon-star"></span> Rate Me</h2>
		
		<p>
			If you do like our support, please help us grow and always provide a better service by <a href="http://themeforest.net/downloads?utf8=%E2%9C%93&amp;order=desc&amp;sort_by=Date+Purchased&amp;sort_by=Date+Purchased&amp;filter_by=themeforest.net&amp;page=1#item-9552804" target="_blank" rel="nofollow"><b>rating</b></a> our theme on ThemeForest:
			
		</p>
				
		
		<?php if(!empty($XT_Theme->updates)): ?>
		<hr>
		<h2><span class="el-icon-paper-clip"></span> Change-Log <a href="#changelog" id="changelog-toggle" class="toggle">+</a></h2>
		<div id="changelog" class="changelog">
		<?php foreach($XT_Theme->updates as $update): ?>
		
			<h4>V.<?php echo $update["version"]; ?> - <em><?php echo $update["date"]; ?></em></h4>
			<ul>
			<?php foreach($update["changes"] as $key => $update): ?>
			
				<?php if(is_array($update)): ?>
				
					<?php foreach($update as $item): ?>
					
						<li><span class="<?php echo esc_attr($key); ?>"><?php echo ucfirst($key); ?></span> <?php echo $item; ?></li>
						
					<?php endforeach; ?>
					
				<?php else: ?>
						
					<li><span class="<?php echo esc_attr($key); ?>"><?php echo ucfirst($key); ?></span> <?php echo $update; ?></li>
						
				<?php endif; ?>
				
			<?php endforeach; ?>
			</ul>
			
		<?php endforeach; ?>
		</div>
		<?php endif; ?>
		
		<br>

		<div class="panel">
			Thank you for being a great customer, Cheers!
			<br>
			<div style="margin-top:5px;"><strong style="font-size:18px;">XplodedThemes.com</strong></div>
		</div>


	</div>
</div>