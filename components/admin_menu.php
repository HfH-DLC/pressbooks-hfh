<?php

namespace Hfh\Pressbooks;

use HfH\Pressbooks\CourseProgress;

class AdminMenu
{
    const NONCE = '_hfh_pressbooks_wpnonce';
    const SLUG = 'hfh-pressbooks';
    const ACTION = 'hfh_pressbooks_save_settings';

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
        add_action('admin_menu', array($this, 'add_menu'));
        add_action('admin_action_' . self::ACTION, array($this, 'save_settings'));
        add_action('admin_notices', array($this, 'display_notice'));
    }

    public function add_menu()
    {
        if (!menu_page_url('hfh', false)) {
            add_menu_page("HfH", 'HfH', 'manage_options', 'hfh', array($this, 'display_menu'), 'dashicons-admin-generic', 26);
        }
        add_submenu_page('hfh', 'Pressbooks', 'Pressbooks', 'manage_options', self::SLUG, array($this, 'display_pressbooks_menu'));
        add_option(CourseProgress::ACTIVE_OPTION, false);
    }

    public function display_menu()
    {
    }

    public function display_pressbooks_menu()
    {
?>

        <div class="wrap">
            <h2>Pressbooks</h2>

            <form method="post" action="edit.php?action=<?= self::ACTION ?>">
                <?php wp_nonce_field(self::ACTION, self::NONCE); ?>
                <h2>Learning Progress</h2>
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="progress_active">Show learning progress</label></th>
                        <td>
                            <input type="checkbox" name="<?= CourseProgress::ACTIVE_OPTION ?>" id="progress_active" value="true" <?= get_option(CourseProgress::ACTIVE_OPTION) ? 'checked' : '' ?>>
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
        update_option(CourseProgress::ACTIVE_OPTION, isset($_POST[CourseProgress::ACTIVE_OPTION]));
        $url = add_query_arg(
            array(
                'page' => self::SLUG,
                'updated' => true,
            ),
            admin_url('admin.php')
        );
        wp_redirect($url);

        exit;
    }

    function display_notice()
    {

        if (isset($_GET['page']) && $_GET['page'] == self::SLUG && isset($_GET['updated'])) {
            echo '<div id="message" class="updated notice is-dismissible"><p>Settings updated.</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
        }
    }
}
