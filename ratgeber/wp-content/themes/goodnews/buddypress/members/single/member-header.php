<?php

/**
 * BuddyPress - Users Header
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 */

?>

<?php do_action( 'bp_before_member_header' ); ?>

<div id="item-header-avatar">
	<a href="<?php bp_displayed_user_link(); ?>">

		<?php bp_displayed_user_avatar( 'type=full' ); ?>

	</a>
</div><!-- #item-header-avatar -->

<div id="item-header-content">

	<?php if ( bp_is_active( 'activity' ) && bp_activity_do_mentions() ) : ?>
		
			
		
		<?php
		ob_start();
		do_action( 'bp_member_header_actions' );
		$header_actions = ob_get_clean(); 
		if(!empty($header_actions)): 
		?>
		<div id="item-buttons">
			<h2 class="user-nicename">
				<a href="#" data-dropdown="items-buttons-dropdown" aria-controls="drop" aria-expanded="false" class="dropdown" data-options="is_hover:true">
					@<?php bp_displayed_user_mentionname(); ?> 
					<i class="fa fa-caret-down"></i>
				</a>
			</h2>
		
			<div id="items-buttons-dropdown" data-dropdown-content class="f-dropdown radius" role="menu" aria-hidden="false" tabindex="-1">
				<?php echo wp_kses_post($header_actions); ?>
			</div>	
		
		</div><!-- #item-buttons -->
		
		<?php else: ?>
		
		<h2 class="user-nicename">@<?php bp_displayed_user_mentionname(); ?></h2>

		<?php endif; ?>	



	<?php endif; ?>


	<span class="activity"><?php bp_last_activity( bp_displayed_user_id() ); ?></span>

	<?php do_action( 'bp_before_member_header_meta' ); ?>

	<div id="item-meta">

		<?php if ( bp_is_active( 'activity' ) ) : ?>

			<div id="latest-update">

				<?php bp_activity_latest_update( bp_displayed_user_id() ); ?>

			</div>

		<?php endif; ?>

		<?php
		/***
		 * If you'd like to show specific profile fields here use:
		 * bp_member_profile_data( 'field=About Me' ); -- Pass the name of the field
		 */
		 do_action( 'bp_profile_header_meta' );

		 ?>

	</div><!-- #item-meta -->

</div><!-- #item-header-content -->
	
<?php do_action( 'bp_after_member_header' ); ?>

<?php do_action( 'template_notices' ); ?>