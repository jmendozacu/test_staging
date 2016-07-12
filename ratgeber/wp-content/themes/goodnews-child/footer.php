	
					</div>
	
					<?php get_template_part('parts/post', 'nav'); ?>
				
	
					<!-- Footer -->
					<footer class="footer_wrapper hide-on-mobile-menu">
						
						<!-- Footer Widget Zone-->
						
				
						<?php 
							$footer_enabled = (bool)xt_option('footer-enabled');
							if( $footer_enabled) { 
								xt_show_custom_dynamic_sidebar('footer_widget', 'footer.php', false, null, 'footer footer__widgets'); 
							}
						?>
						
						<!-- End Footer Widget Zone-->	
						
						<?php 
						$subfooter_enabled = (bool)xt_option('sub-footer-enabled');
						if($subfooter_enabled): 
						?>
						<div class="copyright large-12 column">
							<div class="row">
								<div class="large-6 small-7 column">
									<p><?php echo xt_option('footer_copyright'); ?></p>
								</div>	
								<div class="large-6 small-5 column">
								
									<?php 
									$footer_back_to_top_enabled = xt_option('footer_back_to_top_enabled');
									$footer_back_to_top_text = xt_option('footer_back_to_top_text');
									?>
									<?php if($footer_back_to_top_enabled): ?>
									<a id="back-to-top" class="right" href="#" ><i class="fa fa-caret-up"></i><?php echo wp_kses_post($footer_back_to_top_text); ?></a>
									<?php endif; ?>
					
								</div>
							</div>
						</div>
						<?php endif; ?>
						
					</footer>
				
					<a class="exit-off-canvas"></a>
				
				</div>
				<!-- End Main Content and Sidebar -->
	
			</div>
		
		</div>
		<!-- Google Tag Manager -->
		<noscript><iframe src="//www.googletagmanager.com/ns.html?id=GTM-5B642B"
		height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
		<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
		new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
		j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
		'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
		})(window,document,'script','dataLayer','GTM-5B642B');</script>
		<!-- End Google Tag Manager -->		
		<?php wp_footer(); ?>
		
	</body>
</html>