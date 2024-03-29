<?php

/**
 * Form to set password for all book posts
 *
 * @package     PressbooksHfHPackage
 * @author      Matthias Nötzli
 * @copyright   2021 HfH
 * @license     GPL-2.0+
 */

namespace HfH\Pressbooks;

class BookPostsPassword
{

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
        add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('admin_post_set_book_password', array($this, 'set_book_password'));
    }

    public function add_settings_page()
    {
        add_submenu_page(
            'pb_organize',
            __('Chapter Password', 'pressbooks-hfh'),
            __('Chapter Password', 'pressbooks-hfh'),
            'edit_posts',
            'hfh-book-password',
            array($this, 'settings_page_html'),
            20
        );
    }

    public function settings_page_html()
    {
        $password_set = get_transient('pressbooks-hfh-book-password-set');
        delete_transient('pressbooks-hfh-book-password-set');
?>
        <div class="wrap">
            <?php if ($password_set) : ?>
                <div class="notice notice-success is-dismissible">
                    <p><?= __('Password set successfully.', 'pressbooks-hfh') ?></p>
                </div>
            <?php endif; ?>
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <p>
                <?= __('This form will set the chapter password for all chapters in this book. Submitting the form without a password will remove all chapter passwords.', 'pressbooks-hfh') ?>
            </p>
            <form action="<?= admin_url('admin-post.php') ?>" method="post">
                <?php wp_nonce_field('pressbooks-hfh_set_book_password', 'pressbooks-hfh_book-password-nonce'); ?>
                <input type="hidden" name="action" value="set_book_password">
                <label for="book_password"><?php _e('Password', 'pressbooks-hfh'); ?></label>
                <div>
                    <input type="text" name="book_password" id="book_password" style="text-align:left" value="" maxlength="255" />
                </div>
                <?php
                submit_button(__('Save', 'pressbooks-hfh'));
                ?>
            </form>
        </div>
<?php
    }

    public function set_book_password()
    {
        check_admin_referer('pressbooks-hfh_set_book_password', 'pressbooks-hfh_book-password-nonce');
        if (isset($_POST['book_password'])) {
            $book_password = $_POST['book_password'];
            $posts = get_posts([
                'post_type' => array('part', 'chapter', 'front-matter', 'back-matter'),
                'post_status' => 'any',
                'posts_per_page' => -1,
                'no_found_rows' => true,
                'fields' => 'ids'
            ]);
            foreach ($posts as $post_id) {
                wp_update_post(
                    [
                        'ID' => $post_id,
                        'post_password' => $book_password,
                    ]
                );
            }
        }
        set_transient('pressbooks-hfh-book-password-set', true, MINUTE_IN_SECONDS);
        $url = $_POST['_wp_http_referer'];
        wp_safe_redirect($url);
        exit();
    }
}
