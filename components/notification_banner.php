<?php

/**
 * Network notification banner
 *
 * @package     PressbooksHfHPackage
 * @author      Matthias Nötzli
 * @copyright   2023 HfH
 * @license     GPL-2.0+
 */

namespace HfH\Pressbooks;


class NetworkNotificationBanner
{
    const ACTIVE_OPTION = 'hfh_pressbooks_network_notification_banner_active';
    const CONTENT_OPTION = 'hfh_pressbooks_network_notification_banner_content';

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
        if (get_site_option(self::ACTIVE_OPTION)) {
            add_action('init', array($this, 'init'));
        }
    }

    public function init()
    {
        add_action('wp_enqueue_scripts',  array($this, 'wp_enqueue_scripts'));
        add_filter('wp_head', array($this, 'display_notification'));
    }

    public function wp_enqueue_scripts()
    {
        wp_enqueue_style(
            'hfh-network-notification-banner',
            HFH_PLUGIN_URL . 'components/css/notification_banner.css',
            array(),
            HFH_PLUGIN_VERSION
        );
    }

    public function display_notification()
    {
        $notificationContent = get_site_option(self::CONTENT_OPTION);
        echo '<div id="hfh-network-notification-banner">' . wp_kses_post($notificationContent) . '</div>';
    }
}
