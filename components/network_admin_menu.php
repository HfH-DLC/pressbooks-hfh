<?php

namespace Hfh\Pressbooks;

use HfH\Pressbooks\NetworkNotificationBanner;

class NetworkAdminMenu
{
    const NONCE = '_hfh_pressbooks_wpnonce';
    const SLUG = 'hfh-pressbooks';
    const ACTION = 'hfh_pressbooks_save_network_settings';
    const SETTINGS_SLUG = self::SLUG . '-settings';

    private static $instance = false;

    public static function get_instance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }


    private function __construct()
    {
        add_action('network_admin_menu', array($this, 'add_menu'));
        add_action('admin_action_' . self::ACTION, array($this, 'save_settings'));
        add_action('admin_notices', array($this, 'display_notice'));
    }

    public function add_menu()
    {
        add_menu_page("HfH Pressbooks", 'HfH Pressbooks', 'manage_options', self::SLUG);
        add_submenu_page(self::SLUG, 'Übersicht', 'Übersicht', 'manage_options', self::SLUG, array($this, 'display_overview'));
        add_submenu_page(self::SLUG, 'Einstellungen', 'Einstellungen', 'manage_options', self::SETTINGS_SLUG, array($this, 'display_settings'));
        add_option(NetworkNotificationBanner::ACTIVE_OPTION, false);
        add_option(NetworkNotificationBanner::CONTENT_OPTION);
    }

    function display_overview()
    {
?>
        <div class="wrap">
            <h2>Übersicht</h2>
            <h3>Benachrichtigungsbanner</h3>
            <p>
                Unter <a href="<?= network_admin_url('/admin.php?page=' . self::SETTINGS_SLUG) ?>">HfH Pressbooks > Einstellungen</a> kann ein Benachrichtigungsbanner für das ganze Netzwerk aktiviert werden.
            </p>
        </div>
    <?php
    }

    public function display_settings()
    {
        $editorSettings = array(
            'textarea_name' => NetworkNotificationBanner::CONTENT_OPTION,
            'textarea_rows' => 2,
            'quicktags'     => false,
            'tinymce'       => array(
                'toolbar1' => 'italic,underline,link, | undo,redo',
                'toolbar2' => ''
            ),
            'media_buttons' => false
        );

        $editorContent = get_site_option(NetworkNotificationBanner::CONTENT_OPTION);

    ?>
        <div class="wrap">
            <h2>Einstellungen</h2>
            <form method="post" action="edit.php?action=<?= self::ACTION ?>">
                <?php wp_nonce_field(self::ACTION, self::NONCE); ?>
                <h3>Benachrichtigungsbanner</h3>
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="network_notification_banner_active">Benachrichtigungsbanner anzeigen</label></th>
                        <td>
                            <input type="checkbox" name="<?= NetworkNotificationBanner::ACTIVE_OPTION ?>" id="network_notification_banner_active" value="true" <?= get_site_option(NetworkNotificationBanner::ACTIVE_OPTION) ? 'checked' : '' ?>>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="network_notification_banner_content">Benachrichtigungsinhalt</label></th>
                        <td>
                            <?php wp_editor($editorContent, 'network_notification_banner_content_editor', $editorSettings); ?>
                        </td>
                    </tr>
                </table>
                <?php
                submit_button();
                ?>
            </form>
        </div>
<?php
    }

    function save_settings()
    {
        check_admin_referer(self::ACTION, self::NONCE);
        update_site_option(NetworkNotificationBanner::ACTIVE_OPTION, isset($_POST[NetworkNotificationBanner::ACTIVE_OPTION]));
        if (isset($_POST[NetworkNotificationBanner::CONTENT_OPTION])) {
            $network_notification_banner_content = wp_kses_post($_POST[NetworkNotificationBanner::CONTENT_OPTION]);
            update_site_option(NetworkNotificationBanner::CONTENT_OPTION, $network_notification_banner_content);
        }
        $url = add_query_arg(
            array(
                'page' => self::SETTINGS_SLUG,
                'updated' => true,
            ),
            network_admin_url('admin.php')
        );
        wp_redirect($url);

        exit;
    }

    function display_notice()
    {
        if (isset($_GET['page']) && $_GET['page'] == self::SETTINGS_SLUG && isset($_GET['updated'])) {
            echo '<div id="message" class="updated notice is-dismissible"><p>Settings updated.</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
        }
    }
}
