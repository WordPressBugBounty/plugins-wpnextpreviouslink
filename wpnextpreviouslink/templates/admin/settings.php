<?php
/**
 * Provide a dashboard view for the plugin
 *
 *
 * @link       https://codeboxr.com
 * @since      1.0.0
 *
 * @package    wpnextpreviouslink
 * @subpackage wpnextpreviouslink/templates/admin
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}
?>

<?php
$save_svg = wpnextpreviouslink_load_svg( 'icon_save' );
?>

<div class="wrap cbx-chota cbxchota-setting-common wpnextpreviouslink-page-wrapper wpnextpreviouslink-setting-wrapper" id="wpnextpreviouslink-setting">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2></h2>
	            <?php
	            //not needed as it's using page options-general.php
	            //settings_errors();
	            ?>
				<?php do_action( 'wpnextpreviouslink_wpheading_wrap_before', 'settings'); ?>
                <div class="wp-heading-wrap">
                    <div class="wp-heading-wrap-left pull-left">
	                    <?php do_action( 'wpnextpreviouslink_wpheading_wrap_left_before','settings'); ?>
                        <h1 class="wp-heading-inline wp-heading-inline-wpnextpreviouslink">
							<?php esc_html_e( 'Next Previous: Global Settings', 'wpnextpreviouslink' ); ?>
                        </h1>
	                    <?php do_action( 'wpnextpreviouslink_wpheading_wrap_left_after','settings' ); ?>
                    </div>
                    <div class="wp-heading-wrap-right  pull-right">
	                    <?php do_action( 'wpnextpreviouslink_wpheading_wrap_right_before','settings' ); ?>
                        <a href="<?php echo esc_url(admin_url( 'options-general.php?page=wpnextpreviouslink&doc=1' )); ?>" class="button outline primary"><?php esc_html_e( 'Support & Docs', 'wpnextpreviouslink' ); ?></a>
                        <a href="#" id="save_settings" class="button primary icon icon-right  mr-5"><?php esc_html_e( 'Save Settings', 'wpnextpreviouslink' ); ?>
                            <i class="cbx-icon">
		                        <?php
		                        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		                        echo $save_svg;
		                        ?>
                            </i>
                        </a>
	                    <?php do_action( 'wpnextpreviouslink_wpheading_wrap_right_after','settings' ); ?>
                    </div>
                </div>
				<?php do_action( 'wpnextpreviouslink_wpheading_wrap_after','settings' ); ?>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-12">
	            <?php do_action( 'wpnextpreviouslink_settings_form_before','settings' ); ?>
                <div class="postbox">
                    <div class="clear clearfix"></div>
                    <div class="inside setting-form-wrap">
                        <div class="clear clearfix"></div>
	                    <?php do_action( 'wpnextpreviouslink_settings_form_start','settings' ); ?>
						<?php
						$settings->show_navigation();
						$settings->show_forms();
						?>
	                    <?php do_action( 'wpnextpreviouslink_settings_form_end','settings' ); ?>
                        <div class="clear clearfix"></div>
                    </div>
                    <div class="clear clearfix"></div>
                </div>
	            <?php do_action( 'wpnextpreviouslink_settings_form_after','settings' ); ?>
            </div>
        </div>
    </div>
</div>