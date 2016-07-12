<?php 
$social_networks = xt_option('social_icons');
?>
<?php if(!empty($social_networks) && !empty($social_networks[0]["name"])): ?>

	<!-- social-networks -->
	<ul class="social-networks">
	
		<?php foreach($social_networks as $network): ?>
		<?php 
			if(empty($network["name"])) 
				continue;
		?>
		<li>
			<a style="color:<?php echo esc_attr($network["color"]); ?>" target="_blank" href="<?php echo esc_url($network["url"]); ?>">
				<?php if(!empty($network["thumb"])): ?>
				<img src="<?php echo esc_url($network["thumb"]); ?>" alt="<?php echo esc_attr($network["name"]); ?>" style="max-height:50px;">
				<?php else: ?>
				<i class="fa fa-<?php echo esc_attr($network["icon"]); ?>" title="<?php echo esc_attr($network["name"]); ?>"></i>
				<?php endif; ?>
			</a>
		</li>

		<?php endforeach; ?>	
		
	</ul>
	
<?php endif; ?>				

