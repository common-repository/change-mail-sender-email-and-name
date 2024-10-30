<?php 
/**
 * Plugin Name: Change Mail Sender Email and Name
 * Description: Change the sender name and email address easily from the default WordPress settings.
 * Requires at least: 6.1
 * Requires PHP: 7.4
 * Author: Ataurr
 * Version: 1.0.3
 * Author URI: https://wpmet.com/
 * License: GPL-3.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 *
 * Text Domain: change-mail-sender-email-and-name
 * Domain Path: /languages
 */

// Don't call the file directly
if ( !defined( 'ABSPATH' ) ) exit;

class Mail_Sender_Modifier {

    public function __construct() {
        add_action('init', [$this, 'load_textdomain']);
        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_menu', [$this, 'add_menu']);
        add_filter('wp_mail_from', [$this, 'modify_mail_from']);
        add_filter('wp_mail_from_name', [$this, 'modify_mail_from_name']);
    }

    /**
     * Load plugin textdomain.
     *
     * @since 1.0.0
     */
    public function load_textdomain() {
        load_plugin_textdomain('change-mail-sender-email-and-name', false, basename(dirname(__FILE__)) . '/languages');
    }

    /**
     * Register settings and fields.
     */
    public function register_settings() {
        add_settings_section(
            'mail_sender_modifier_section',
            esc_html__('Mail Sender Modifier', 'change-mail-sender-email-and-name'),
            [$this, 'settings_section_text'],
            'mail_sender_modifier_sender'
        );

        add_settings_field(
            'mail_sender_modifier_id',
            esc_html__('Mail Sender Name', 'change-mail-sender-email-and-name'),
            [$this, 'sender_name_field'],
            'mail_sender_modifier_sender',
            'mail_sender_modifier_section'
        );

        register_setting('mail_sender_modifier_section', 'mail_sender_modifier_id');

        add_settings_field(
            'mail_sender_modifier_email_id',
            esc_html__('Mail Sender Email', 'change-mail-sender-email-and-name'),
            [$this, 'sender_email_field'],
            'mail_sender_modifier_sender',
            'mail_sender_modifier_section'
        );

        register_setting('mail_sender_modifier_section', 'mail_sender_modifier_email_id');
    }

    /**
     * Display text in the settings section.
     */
    public function settings_section_text() {
        echo '<p>' . esc_html__('You may change your WordPress default mail sender name and email.', 'change-mail-sender-email-and-name') . '</p>';
    }

    /**
     * Display the sender name field.
     */
    public function sender_name_field() {
        printf(
            '<input name="mail_sender_modifier_id" type="text" class="regular-text" value="%s" placeholder="'.esc_html__('Mail Name','change-mail-sender-email-and-name' ).'"/>',
            esc_attr(get_option('mail_sender_modifier_id'))
        );
    }

    /**
     * Display the sender email field.
     */
    public function sender_email_field() {
        printf(
            '<input name="mail_sender_modifier_email_id" type="email" class="regular-text" value="%s" placeholder="'.esc_html__('Add your email here','change-mail-sender-email-and-name' ).'"/>',
            esc_attr(get_option('mail_sender_modifier_email_id'))
        );
    }

    /**
     * Add submenu under settings.
     */
    public function add_menu() {
        add_submenu_page(
            'options-general.php',
            'Mail Sender Modifier',
            'Mail Sender Modifier',
            'manage_options',
            'mail_sender_modifier_sender',
            [$this, 'settings_page_output']
        );
    }

    /**
     * Output the settings form.
     */
    public function settings_page_output() {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Mail Sender Modifier', 'change-mail-sender-email-and-name'); ?></h1>
            <form action="options.php" method="POST">
                <?php
                    settings_errors();
                    do_settings_sections('mail_sender_modifier_sender');
                    settings_fields('mail_sender_modifier_section');
                    submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Change the default email address in outgoing mail.
     */
    public function modify_mail_from($old_email) {
        return get_option('mail_sender_modifier_email_id') ?: $old_email;
    }

    /**
     * Change the default name in outgoing mail.
     */
    public function modify_mail_from_name($old_name) {
        return get_option('mail_sender_modifier_id') ?: $old_name;
    }
}

// Initialize the plugin class.
new Mail_Sender_Modifier();
