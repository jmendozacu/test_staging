<?php
/**
 * Single Product tabs
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Filter tabs and allow third parties to add their own
 *
 * Each tab is an array containing title, callback and priority.
 * @see woocommerce_default_product_tabs()
 */
$tabs = apply_filters( 'woocommerce_product_tabs', array() );

if ( ! empty( $tabs ) ) : ?>


<div class="row clear padding-top no-col-padding">
	<div class="small-12 medium-8 column">
		<div class="r-padding">
			<div class="woocommerce-tabs">
				<dl class="tabs" data-tab>
					<?php $i = 0; foreach ( $tabs as $key => $tab ) : ?>
						<dt></dt>
						<dd class="<?php echo esc_attr($key); ?>_tab <?php echo ($i == 0 ? 'active' : ''); ?>">
							<a href="#tab-<?php echo esc_attr($key); ?>"><?php echo apply_filters( 'woocommerce_product_' . $key . '_tab_title', $tab['title'], $key ) ?></a>
						</dd>
		
					<?php $i++; endforeach; ?>
				</dl>
				<div class="tabs-content">
				<?php $i = 0; foreach ( $tabs as $key => $tab ) : ?>
		
					<div class="content <?php echo ($i == 0 ? 'active' : ''); ?>" id="tab-<?php echo esc_attr($key); ?>">
						<?php call_user_func( $tab['callback'], $key, $tab ) ?>
					</div>
					
				<?php $i++; endforeach; ?>
				</div>
			</div>
		</div>

	</div>
	
	<div class="small-12 medium-4 column">		
		
		<?php if ( get_option( 'woocommerce_review_rating_verification_required' ) === 'no' || wc_customer_bought_product( '', get_current_user_id(), $product->id ) ) : ?>
	
			<div id="review_form_wrapper">
				<div id="review_form">
					<?php
						$commenter = wp_get_current_commenter();
	
						$comment_form = array(
							'title_reply'          => have_comments() ? __( 'Leave Your Review', 'woocommerce' ) : __( 'Be the first to review', 'woocommerce' ) . ' &ldquo;' . get_the_title() . '&rdquo;',
							'title_reply_to'       => __( 'Leave a Reply to %s', 'woocommerce' ),
							'comment_notes_before' => '',
							'comment_notes_after'  => '',
							'fields'               => array(
								'author' => '<p class="comment-form-author">' . '<label for="author">' . __( 'Name', 'woocommerce' ) . ' *</label> ' .
								            '<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30" aria-required="true" /></p>',
								'email'  => '<p class="comment-form-email"><label for="email">' . __( 'Email', 'woocommerce' ) . ' *</label> ' .
								            '<input id="email" name="email" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30" aria-required="true" /></p>',
							),
							'label_submit'  => __( 'Submit Review', 'woocommerce' ),
							'logged_in_as'  => '',
							'comment_field' => ''
						);
	
						if ( get_option( 'woocommerce_enable_review_rating' ) === 'yes' ) {
							$comment_form['comment_field'] = '<p class="comment-form-rating"><label>' . __( 'Rate this product', 'woocommerce' ) .'</label><select name="rating" id="rating">
								<option value="">' . __( 'Rate&hellip;', 'woocommerce' ) . '</option>
								<option value="5">' . __( 'Perfect', 'woocommerce' ) . '</option>
								<option value="4">' . __( 'Good', 'woocommerce' ) . '</option>
								<option value="3">' . __( 'Average', 'woocommerce' ) . '</option>
								<option value="2">' . __( 'Not that bad', 'woocommerce' ) . '</option>
								<option value="1">' . __( 'Very Poor', 'woocommerce' ) . '</option>
							</select></p>';
						}
	
						$comment_form['comment_field'] .= '<p class="comment-form-comment"><label for="comment">' . __( 'Your Review', 'woocommerce' ) . '</label><textarea id="comment" name="comment" cols="45" rows="4" aria-required="true"></textarea></p>';
	
						comment_form( apply_filters( 'woocommerce_product_review_comment_form_args', $comment_form ) );
					?>
				</div>
			</div>
	
		<?php else : ?>
	
			<div class="alert-box warning"><?php _e( 'Only logged in customers who have purchased this product may leave a review.', 'woocommerce' ); ?></div>
	
		<?php endif; ?>
	
		<div class="clear"></div>
	</div>
</div>	


<?php endif; ?>