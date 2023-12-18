<?php
/**
 * Plugin Name: Admin Notice Example
 * Description: Demonstrates various types of admin notices in the WordPress admin area.
 * Text Domain: admin-notice-example
 * 
 * @package AdminNoticeExample
 */

namespace AdminNoticeExample;

// Hooking functions to WordPress actions.
// add_action reference: https://developer.wordpress.org/reference/functions/add_action/
add_action( 'admin_menu', __NAMESPACE__ . '\add_plugin_settings_page' );
add_action( 'admin_notices', __NAMESPACE__ . '\display_general_info_notice' );
add_action( 'admin_notices', __NAMESPACE__ . '\display_dismissible_success_notice' );
add_action( 'admin_notices', __NAMESPACE__ . '\display_transient_reset_warning' );
add_action( 'admin_init', __NAMESPACE__ . '\handle_dismissal_of_success_notice' );
add_action( 'admin_init', __NAMESPACE__ . '\process_transient_reset_action' );

/**
 * Adds a settings page for the plugin in the WordPress admin area.
 * add_options_page reference: https://developer.wordpress.org/reference/functions/add_options_page/
 */
function add_plugin_settings_page(): void {
    add_options_page(
        __( 'Admin Notice Example', 'admin-notice-example' ), // Page title
        __( 'Admin Notice Example', 'admin-notice-example' ), // Menu title
        'manage_options',                                     // Capability required
        'admin-notice-example-settings',                      // Menu slug
        __NAMESPACE__ . '\render_settings_page'               // Callback function
    );
}

/**
 * Renders the content of the settings page.
 */
function render_settings_page(): void {
?>
    <div class="wrap">
        <h2><?php esc_html_e( 'Admin Notice Example Settings', 'admin-notice-example' ); ?></h2>
    </div>
<?php
}

/**
 * Displays a custom dismissible success notice.
 * get_transient reference: https://developer.wordpress.org/reference/functions/get_transient/
 * Demonstrates using transients for temporary data storage.
 */
function display_dismissible_success_notice(): void {
    if ( false === get_transient( 'admin_notice_example_dismissed' ) ) {
        $dismiss_url = esc_url( add_query_arg( 'dismiss_admin_notice_example', '1' ) );
?>
        <div class="notice notice-success is-dismissible">
            <p><em><?php esc_html_e( 'Admin Notice Example:', 'admin-notice-example' ); ?></em> 
            <?php esc_html_e( 'This is a dismissible success notice.', 'admin-notice-example' ); ?>
            <a href="<?php echo $dismiss_url; ?>"><?php esc_html_e( 'Dismiss', 'admin-notice-example' ); ?></a></p>
        </div>
<?php
    }
}

/**
 * Handles the dismissal of the success notice.
 * set_transient reference: https://developer.wordpress.org/reference/functions/set_transient/
 * This function sets a transient to remember the dismissal status of the notice.
 */
function handle_dismissal_of_success_notice(): void {
    if ( isset( $_GET['dismiss_admin_notice_example'] ) && '1' === $_GET['dismiss_admin_notice_example'] ) {
        set_transient( 'admin_notice_example_dismissed', true, DAY_IN_SECONDS * 24 );
    }
}

/**
 * Displays a general informational notice in the admin area.
 * admin_url reference: https://developer.wordpress.org/reference/functions/admin_url/
 * This function creates a link to the plugin's settings page.
 */
function display_general_info_notice(): void {
    $settings_page_url = get_plugin_settings_page_url();
?>
    <div class="notice notice-info">
        <p><em><?php esc_html_e( 'Admin Notice Example:', 'admin-notice-example' ); ?></em></p>
        <p><?php esc_html_e( 'This plugin showcases various admin notices.', 'admin-notice-example' ); ?></p>
        <p><a href="<?php echo esc_url( $settings_page_url ); ?>"><?php esc_html_e( 'Settings page', 'admin-notice-example' ); ?></a></p>
    </div>
<?php
}

/**
 * Displays a warning notice with an option to reset the transient.
 * add_query_arg reference: https://developer.wordpress.org/reference/functions/add_query_arg/
 * This function provides a link to reset the notice display status.
 */
function display_transient_reset_warning(): void {
    if ( false !== get_transient( 'admin_notice_example_dismissed' ) ) {
        $reset_url = esc_url( add_query_arg( 'reset_admin_notice_example', '1', get_plugin_settings_page_url() ) );
?>
        <div class="notice notice-warning">
            <p><em><?php esc_html_e( 'Admin Notice Example:', 'admin-notice-example' ); ?></em> 
            <?php esc_html_e( 'The success notice was dismissed.', 'admin-notice-example' ); ?>
            <a href="<?php echo $reset_url; ?>"><?php esc_html_e( 'Reset and display again.', 'admin-notice-example' ); ?></a></p>
        </div>
<?php
    }
}

/**
 * Processes the action to reset the transient.
 * wp_redirect reference: https://developer.wordpress.org/reference/functions/wp_redirect/
 * remove_query_arg reference: https://developer.wordpress.org/reference/functions/remove_query_arg/
 * This function redirects the user after resetting the transient, ensuring the URL is clean.
 */
function process_transient_reset_action(): void {
    if ( isset( $_GET['reset_admin_notice_example'] ) && '1' === $_GET['reset_admin_notice_example'] ) {
        delete_transient( 'admin_notice_example_dismissed' );

        $redirect_url = remove_query_arg( 'reset_admin_notice_example', get_plugin_settings_page_url() );
        
        wp_redirect( $redirect_url );
        exit;
    }
}

/**
 * Get the settings page URL for the plugin.
 */
function get_plugin_settings_page_url(): string {
    $slug = 'admin-notice-example-settings';
    return admin_url( "admin.php?page=$slug" );
}

/**
 * Checks if the current admin page is the plugin's settings page or the WordPress plugins page.
 * 
 * @return bool
 */
function is_plugin_pages(): bool {
    $screen = get_current_screen();

    // Check if the current screen is the plugin's settings page or the WordPress plugins page.
    if ($screen && ($screen->id === "settings_page_admin-notice-example-settings" || $screen->id === "plugins")) {
        return true;
    }

    return false;
}
