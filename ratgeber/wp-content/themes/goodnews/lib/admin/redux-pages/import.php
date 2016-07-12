<?php
$themes = false;
$themes = wp_remote_retrieve_body(wp_remote_get(XT_IMPORT_URL.'/themes.php'));
$themes = unserialize($themes);
?>
<link rel="stylesheet" href="<?php echo XT_ADMIN_URL; ?>/assets/css/importer.css" type="text/css" media="screen, projection">

<script>

	jQuery(function($) {

		$('.xt-importer .theme-browser .demo-import').click(function(e) {
			e.preventDefault();

			var $this = $(this);
			var redux_sidebar = $('.redux-sidebar');


			if ( ! confirm("Attention: This will flush all posts, post metas, comments, links in your actual DB before importing. Are you sure you want to continue?") )
				return -1;

			
			var theme = $this.data('theme');
			var redux = $this.data('redux');
			var revslider = $this.data('revslider');
			var widgets = $this.data('widgets');

			var postData = { action: 'xt_import_default_data', theme: theme, redux: redux, revslider: revslider, widgets: widgets };
			var loader   = $('#xt-importer-loader');
			var status   = $('#xt-importer-status');


			loader.fadeIn();
			redux_sidebar.addClass('inactive');
			status.html('<p><strong>Flushing Current Data ... </strong></p>').fadeIn();
			
	
			 
			
			setTimeout(function() {
				status.append('<p><strong style="color:red">Please wait, this process can take up to 3 minutes... </strong></p>');
			}, 10000);	
			
			$.ajax({
				url      : ajaxurl+'<?php echo (defined('ICL_LANGUAGE_CODE') ? '?lang='.ICL_LANGUAGE_CODE : ''); ?>',
				data     : postData,
				type     : 'post',
				dataType : 'json',
				success  : function (data) {

					status.append(data.msg);
					status.get(0).scrollTop = status.get(0).scrollHeight;

					if ( data.error )
					{
						status.removeClass('success');
						status.addClass('error');
						loader.fadeOut();
						redux_sidebar.removeClass('inactive');

					} else {

						status.removeClass('error');
						status.addClass('success');

						status.append('<hr><p><strong>Assigning Pages To Template...</strong></p>');
						status.get(0).scrollTop = status.get(0).scrollHeight;

						postData = { action: 'xt_import_assign_templates' };

						$.ajax({
							url      : ajaxurl+'<?php echo (defined('ICL_LANGUAGE_CODE') ? '?lang='.ICL_LANGUAGE_CODE : ''); ?>',
							data     : postData,
							type     : 'POST',
							dataType : 'json',
							success  : function (data) {

								status.append(data.msg);
								status.get(0).scrollTop = status.get(0).scrollHeight;

								if ( data.error )
								{
									status.removeClass('success');
									status.addClass('error');
								}


								loader.fadeOut();
								redux_sidebar.removeClass('inactive');
								
								status.append('<br><a class="button" onclick="location.reload()">Click To Refresh Options</a>');
								status.get(0).scrollTop = status.get(0).scrollHeight;
								
								$.removeCookie('redux_current_tab', { path: '/' });
							}
						});

					}


				}
			});
		});
	});
</script>


<div class="xt-importer">

	
	<div id="xt-importer-loader" class="loader"></div>
	<div id="xt-importer-status" class="status"></div>	
	
		
	<?php if(!empty($themes)): ?>
	<input type="hidden" name="dummy" value="">
	<div class="theme-browser rendered">
		<div class="themes">
			
			<?php foreach($themes as $theme): ?>
			<div class="theme" tabindex="0" aria-describedby="<?php echo esc_attr($theme["id"]); ?>-action <?php echo esc_attr($theme["id"]); ?>-name">
				<div class="theme-screenshot">
					<img src="<?php echo esc_url($theme["screenshot"]); ?>" alt="<?php echo esc_attr($theme["name"]); ?>">
				</div>
				<h3 class="theme-name" id="<?php echo esc_attr($theme["id"]); ?>-name"><?php echo esc_attr($theme["name"]); ?></h3>
				<div class="theme-actions">
					<a class="button button-secondary demo-import" href="#" 
						data-theme="<?php echo esc_attr($theme["id"]); ?>" 
						data-redux="<?php echo (!empty($theme["redux"]) ? esc_url($theme["redux"]) : ''); ?>" 
						data-revslider="<?php echo (!empty($theme["revslider"]) ? esc_url($theme["revslider"]) : ''); ?>"
						data-widgets="<?php echo (!empty($theme["widgets"]) ? esc_url($theme["widgets"]) : ''); ?>"
					><?php echo __("Import Data", XT_TEXT_DOMAIN); ?></a>
					<a class="button button-secondary demo-preview" target="_blank" href="<?php echo esc_url($theme["url"]); ?>"><?php echo __("Live Preview", XT_TEXT_DOMAIN); ?></a>
				</div>
			</div>
			<?php endforeach;?>
			
		</div>
	</div>

	<br class="clear">
			
	<?php endif; ?>

</div>

