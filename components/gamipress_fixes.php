<?php

/**
 * Gamipress Notifications H5P fix
 *
 * @package     PressbooksHfHPackage
 * @author      Matthias Nötzli
 * @copyright   2023 HfH
 * @license     GPL-2.0+
 */

namespace HfH\Pressbooks;

use Hfh\Pressbooks\AdminMenu;

class GamipressFixes
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
        if (get_option(AdminMenu::GAMIPRESS_FIXES_ACTIVE_OPTION)) {
            add_action('init', array($this, 'init'));
        }
    }

    public function init()
    {
        add_action('wp_enqueue_scripts',  array($this, 'wp_enqueue_scripts'));
        add_action('wp_ajax_pressbooks_hfh_gamipress_restrict_content_check_access', array($this, 'check_access'));
    }

    public function wp_enqueue_scripts()
    {
        wp_enqueue_script(
            'hfh_gamipress_restrict_content_h5p_reload',
            HFH_PLUGIN_URL . 'components/js/gamipress_fixes.js',
            array('h5p-core-js-jquery'),
            HFH_PLUGIN_VERSION
        );
    }
}
