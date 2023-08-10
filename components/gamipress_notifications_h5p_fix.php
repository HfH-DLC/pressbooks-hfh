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

class GamipressNotificationsH5PFix
{
    private static $instance = false;


    const ACTIVE_OPTION = 'hfh_pressbooks_gamipress_notifications_h5p_fix_active';

    public static function get_instance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        if (get_option(self::ACTIVE_OPTION)) {
            add_action('init', array($this, 'init'));
        }
    }

    public function init()
    {
        add_action('wp_enqueue_scripts',  array($this, 'wp_enqueue_scripts'));
    }

    public function wp_enqueue_scripts()
    {
        wp_enqueue_script(
            'hfh_gamipress_notifications_h5p_fix',
            HFH_PLUGIN_URL . 'components/js/gamipress_notifications_h5p_fix.js',
            array('h5p-core-js-jquery')
        );
    }
}
