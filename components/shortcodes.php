<?php

/**
 * Custom shortcodes
 *
 * @package     PressbooksHfHPackage
 * @author      Matthias Nötzli
 * @copyright   2023 HfH
 * @license     GPL-2.0+
 */

namespace HfH\Pressbooks;

class Shortcodes
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
        add_action('init', array($this, 'init'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue'));
    }

    public function init()
    {
        add_shortcode('hfh_parts', array($this, 'parts_shortcode'));
        add_shortcode('hfh_chapters', array($this, 'chapters_shortcode'));
        add_shortcode('hfh_confetti', array($this, 'confetti_shortcode'));
    }

    public function enqueue()
    {
        wp_register_style('pressbooks_hfh_chapter_parts_shortcodes_style', HFH_PLUGIN_URL . 'components/css/chapter_parts_shortcodes.css', array(), HFH_PLUGIN_VERSION);
        wp_register_script('pressbooks_hfh_chapter_parts_shortcode_script', HFH_PLUGIN_URL . 'components/js/chapter_parts_shortcode.js', array("jquery"), HFH_PLUGIN_VERSION);
        wp_register_script('pressbooks_hfh_confetti_third_party', HFH_PLUGIN_URL . 'components/js/tsparticles.bundle.min.js', '2.11.0');
        wp_register_script('pressbooks_hfh_confetti_shortcode_script', HFH_PLUGIN_URL . 'components/js/confetti_shortcode.js', array("pressbooks_hfh_confetti_third_party"), HFH_PLUGIN_VERSION);
        wp_enqueue_script('pressbooks_hfh_confetti_shortcode_script');
    }

    public function parts_shortcode($atts)
    {
        wp_enqueue_script('pressbooks_hfh_chapter_parts_shortcode_script');
        wp_enqueue_style('pressbooks_hfh_chapter_parts_shortcodes_style');

        $defaults = array(
            'color' => null
        );
        $atts = shortcode_atts($defaults, $atts, 'hfh_chapter');
        //used in shortcode template
        $parts_color =  sanitize_hex_color($atts['color']);
        $book_structure = pb_get_book_structure();
        $parts = $book_structure['part'];
        $current_id = get_the_ID();
        $current_part = null;

        foreach ($parts as $index => $part) {
            if ($current_part == null) {
                foreach ($part["chapters"] as $index => $chapter) {
                    if ($current_id == $chapter["ID"]) {
                        $current_part = $part;
                        break;
                    }
                }
            }
        }

        ob_start();
        include('templates/parts_shortcode.php');
        return ob_get_clean();
    }

    public function chapters_shortcode($atts)
    {
        wp_enqueue_script('pressbooks_hfh_chapter_parts_shortcode_script');
        wp_enqueue_style('pressbooks_hfh_chapter_parts_shortcodes_style');

        $defaults = array(
            'color' => null
        );
        $atts = shortcode_atts($defaults, $atts, 'hfh_chapter');
        //used in shortcode template
        $chapters_color =  sanitize_hex_color($atts['color']);
        $book_structure = pb_get_book_structure();
        $parts = $book_structure['part'];
        $current_id = get_the_ID();
        $current_index = null;
        $current_part = null;

        foreach ($parts as $index => $part) {
            if ($current_part == null) {
                foreach ($part["chapters"] as $index => $chapter) {
                    if ($current_id == $chapter["ID"]) {
                        $current_index = $index;
                        $current_part = $part;
                        break;
                    }
                }
            }
        }

        $chapters = [$current_part["chapters"][$current_index]];
        if ($current_index + 1 < count($current_part["chapters"])) {
            $chapters[] = $current_part["chapters"][$current_index + 1];
        }
        if ($current_index + 2 < count($current_part["chapters"])) {
            $chapters[] = $current_part["chapters"][$current_index + 2];
        }

        ob_start();
        include('templates/chapters_shortcode.php');
        return ob_get_clean();
    }

    public function confetti_shortcode($atts)
    {
        $defaults = array(
            'colors' => '#be1925'
        );
        $attributes = shortcode_atts($defaults, $atts, 'hfh_chapter');
        $colors_no_whitespaces = preg_replace('/\s*,\s*/', ',', sanitize_text_field($attributes['colors']));
        return "<div class='pressbooks-hfh-confetti' data-colors='" . $colors_no_whitespaces . "'></div>";
    }
}
