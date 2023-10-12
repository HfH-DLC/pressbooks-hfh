<?php

namespace Hfh\Pressbooks;

use HfH\Pressbooks\CourseProgress;

class AdminMenu
{
    const NONCE = '_hfh_pressbooks_wpnonce';
    const SLUG = 'hfh-pressbooks';
    const ACTION = 'hfh_pressbooks_save_settings';
    const SETTINGS_SLUG = self::SLUG . '-settings';

    const GAMIPRESS_FIXES_ACTIVE_OPTION  = 'hfh_pressbooks_gamipress_notifications_h5p_fix_active';

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
        add_menu_page("HfH Pressbooks", 'HfH Pressbooks', 'manage_options', self::SLUG);
        add_submenu_page(self::SLUG, 'Übersicht', 'Übersicht', 'manage_options', self::SLUG, array($this, 'display_overview'));
        add_submenu_page(self::SLUG, 'Einstellungen', 'Einstellungen', 'manage_options', self::SETTINGS_SLUG, array($this, 'display_settings'));
        add_option(CourseProgress::ACTIVE_OPTION, false);
        add_option(self::GAMIPRESS_FIXES_ACTIVE_OPTION, false);
    }

    function display_overview()
    {
?>
        <div class="wrap">
            <h2>Übersicht</h2>
            <h3>Buchpasswort</h3>
            <p>Unter <a href="<?= admin_url('admin.php?page=hfh-book-password') ?>">Organisieren > Buchpasswort</a> kann ein Passwort für das komplette Buch gesetzt werden.</p>

            <h3>Lesefortschritt</h3>
            <p>
                Unter <a href="<?= admin_url('admin.php?page=hfh-settings') ?>">HfH Pressbooks > Einstellungen</a> kann der Lesefortschritt eingestellt werden. Am Ende jedes Kapitels wird ein Button angezeigt, mit dem der Benutzer das Kapitel als bearbeitet markieren kann. Im Inhaltsverzeichnis wird angezeigt, welche Kapitel bereits bearbeitet wurden. Im Menü wird die Seite "Fortschritt" verfügbar.
            </p>
            <h3>Kapitelkategorien</h3>
            <p>
                Unter <a href="<?= admin_url('edit-tags.php?taxonomy=category&post_type=chapter') ?>">Organisieren > Kapitelkategorien</a> ist die Kategorie-Seite für Kapitel verfügbar, die in Pressbooks standardmässig versteckt ist.
            </p>
            <h3>Shibboleth Zugriff</h3>
            <p>
                Unter <a href="<?= admin_url('options-general.php?page=pressbooks_sharingandprivacy_options') ?>">Einstellungen > Freigabe & Datenschutz</a>
                kann eingestellt werden, Angehörige welcher Organisation ein privates Buch lesen dürfen. Benutzer*innen, die über diese Option Zugriff auf ein Buch erhalten, erhalten die Rolle "Abonnent".
                Sie verlieren die Zugriffsrechte nicht mehr, auch wenn sich diese Einstellung oder ihre Organisationsangehörigkeit ändern.
            </p>
            <h3>Shortcodes</h3>
            <h4>[hfh_parts] und [hfh_chapters]</h4>
            <p>Diese beiden Shortcodes zeigen die Teile resp. Kapitel des Buches an. Mit dem Attribut "color" kann die Hauptfarbe gesetzt werden, z.B. [hfh_chapters color="#E31826"].</p>
            <h4>[hfh_confetti]</h4>
            <p>Dieser Shortcode zeigt beim Laden der Seite Konfetti an. Die Farben der Konfetti können mit dem Attribute "colors" definiert werden, z.B. [hfh_confetti colors="#BE1925,#E31826,#D8757C,#F2D1D3,#F8E8E9,#FBF3F4,#767676,#14776c,#F08100,#FFCD00"].
            </p>
            <h3>Gamipress Fixes</h3>
            <p>
                Unter <a href="<?= admin_url('admin.php?page=hfh-settings') ?>">HfH Pressbooks > Einstellungen</a> können diverse Fixes für Gamipress aktiviert werden:
            <ul>
                <li>Das Gamipress Notifications Plugin erkennt nicht, wenn ein H5P beantwortet wird, bis die Seite neu geladen wird.
                <li>
                <li>Das Gamipress Restrict Content Plugin lädt die Seite nicht neu, wenn eine Benutzer:in erfolgreich ein H5P beantwortet, und dadurch Zugang erhalten sollte.
                <li>
            </ul>
            </p>
        </div>
    <?php
    }

    public function display_settings()
    {
    ?>
        <div class="wrap">
            <h2>Einstellungen</h2>
            <form method="post" action="edit.php?action=<?= self::ACTION ?>">
                <?php wp_nonce_field(self::ACTION, self::NONCE); ?>
                <h3>Lesefortschritt</h3>
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="progress_active">Lesefortschritt anzeigen</label></th>
                        <td>
                            <input type="checkbox" name="<?= CourseProgress::ACTIVE_OPTION ?>" id="progress_active" value="true" <?= get_option(CourseProgress::ACTIVE_OPTION) ? 'checked' : '' ?>>
                        </td>
                    </tr>
                </table>
                <h3>Gamipress</h3>
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="<?= self::GAMIPRESS_FIXES_ACTIVE_OPTION  ?>">Gamipress Fixes aktivieren</label></th>
                        <td>
                            <input type="checkbox" name="<?= self::GAMIPRESS_FIXES_ACTIVE_OPTION   ?>" id="<?= self::GAMIPRESS_FIXES_ACTIVE_OPTION   ?>" value="true" <?= get_option(self::GAMIPRESS_FIXES_ACTIVE_OPTION) ? 'checked' : '' ?>>
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
        update_option(self::GAMIPRESS_FIXES_ACTIVE_OPTION, isset($_POST[self::GAMIPRESS_FIXES_ACTIVE_OPTION]));
        $url = add_query_arg(
            array(
                'page' => self::SETTINGS_SLUG,
                'updated' => true,
            ),
            admin_url('admin.php')
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
