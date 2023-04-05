<?php

/**
 * Course Progress Page
 *
 * @package     PressbooksHfHPackage
 * @author      Matthias Nötzli
 * @copyright   2021 HfH
 * @license     GPL-2.0+
 */

namespace HfH\Pressbooks;

class CourseProgress
{
    private static $instance = false;

    const PAGE_QUERY_VAR = 'hfh_page';
    const ALLOWED_POST_TYPES = array('front-matter', 'chapter', 'back-matter');
    const NONCE_ACTION = 'hfh_chapter_complete';
    const PROGRESS_PAGE_NAME = 'progress';
    const PROGRESS_USER_META_KEY = 'hfh-course-progress';
    const ACTIVE_OPTION = 'hfh_pressbooks_progress_active';

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
        if (!is_user_logged_in()) {
            return;
        }
        add_action('wp_enqueue_scripts',  array($this, 'wp_enqueue_scripts'));
        add_filter('query_vars', array($this, 'query_vars'));
        add_action('template_include', array($this, 'template_include'));
        add_filter('the_content', array($this, 'add_chapter_complete_button'), 14); //pressbooks adds footnotes etc. on priority 13
        add_action('wp_ajax_hfh_chapter_complete', array($this, 'hfh_chapter_complete'));
    }

    public function wp_enqueue_scripts()
    {
        wp_enqueue_style('hfh-course-progress',  HFH_PLUGIN_URL . 'components/css/course_progress.css');
        wp_enqueue_script(
            'hfh-course-progress-ajax',
            HFH_PLUGIN_URL . 'components/js/course_progress.js',
            array('jquery')
        );
        wp_localize_script(
            'hfh-course-progress-ajax',
            'phpVars',
            array(
                'progressURL' => add_query_arg(self::PAGE_QUERY_VAR, self::PROGRESS_PAGE_NAME, home_url()),
                'ajaxURL' => admin_url('admin-ajax.php'),
                'ajaxNonce'    => wp_create_nonce(self::NONCE_ACTION),
                'setCompleteText' => __('Mark Chapter as Complete', 'pressbooks-hfh'),
                'setIncompleteText' => __('Mark Chapter as Incomplete', 'pressbooks-hfh'),
                'progress' => $this->get_progress(),
                'progressLinkText' => __('Progress', 'pressbooks-hfh')
            )
        );
    }

    public function query_vars($query_vars)
    {
        $query_vars[] = self::PAGE_QUERY_VAR;
        return $query_vars;
    }

    public function template_include($template)
    {
        $hfh_page = get_query_var(self::PAGE_QUERY_VAR);
        if ($hfh_page === self::PROGRESS_PAGE_NAME) {
            return plugin_dir_path(__FILE__) . '/templates/course_progress.php';
        }
        return $template;
    }

    public function get_progress()
    {
        $structure = pb_get_book_structure();
        $front_matter = array('post_title' => 'Front Matter', 'chapters' => $structure['front-matter']);
        $back_matter = array('post_title' => 'Back Matter', 'chapters' => $structure['back-matter']);
        $parts = array_merge(array($front_matter), $structure['part'], array($back_matter));
        $parts = array_map(function ($part) {
            return array(
                'ID' => isset($part['ID']) ? $part['ID'] : '',
                'title' => $part['post_title'],
                'completion' => $this->get_completion($part['chapters'])
            );
        },  $parts);

        $progress = array_reduce($parts, function ($carry, $item) {
            $carry['complete'] += $item['completion']['complete'];
            $carry['total'] += $item['completion']['total'];
            return $carry;
        },  array('total' => 0, 'complete' => 0, 'parts' => $parts));

        return $progress;
    }

    private function get_completion($chapters)
    {
        $chaptersCompletion =  array_map(function ($chapter) {
            return  array(
                'ID' => $chapter['ID'],
                'title' => $chapter['post_title'],
                'complete' => $this->is_chapter_complete($chapter['ID'])
            );
        }, $chapters);
        $total = count($chapters);
        $complete = count(array_filter($chaptersCompletion, function ($chapter) {
            return $chapter['complete'];
        }));
        return array(
            'chapters' => $chaptersCompletion,
            'total' => $total,
            'complete' => $complete
        );
    }

    public function get_chapters_progress_template($chapters)
    {
        ob_start();
?>
        <table class="hfh-chapter">
            <thead class="hfh-visually-hidden">
                <tr>
                    <th class="hfh-chapter__name"><?= __('Chapter', 'pressbooks-hfh') ?></th>
                    <th class="hfh-chapter__status"><?= __('Status', 'pressbooks-hfh') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($chapters as $chapter) : ?>
                    <tr>
                        <td>
                            <a href="<?= get_the_permalink($chapter['ID']) ?>" target="_blank" rel="noopener noreferrer"><?= $chapter['title'] ?></a>
                        </td>
                        <td>
                            <?php if ($chapter['complete']) : ?>
                                <div class="hfh-visually-hidden"><?= __('Chapter Complete', 'pressbooks-hfh') ?></div>
                                <svg class="hfh-chapter__progress-indicator hfh-chapter__progress-indicator--complete" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            <?php else : ?>
                                <div class="hfh-visually-hidden"><?= __('Chapter Incomplete', 'pressbooks-hfh') ?></div>
                                <svg class="hfh-chapter__progress-indicator" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" stroke-width="1">
                                    <circle cx="10" cy="10" r="7.5" />
                                </svg>
                            <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php
        return ob_get_clean();
    }

    public function get_progress_bar_template($progress)
    {
        $percentage = $progress['total'] == 0 ? 100 : intdiv($progress['complete'] * 100, $progress['total']);
        ob_start();
    ?>
        <div class="hfh-progress-bar-wrapper">
            <div class="hfh-progress-bar">
                <div class="hfh-progress-bar__value" style="width:<?= $percentage ?>%"></div>
            </div>
            <div class="hfh-progress-bar__count"><span class="hfh-progress-bar__percentage"><?php printf(__('%s%% Complete', 'pressbooks-hfh'), $percentage) ?></span></div>
        </div>
<?php
        return ob_get_clean();
    }

    private function show_complete_button($post)
    {
        return (is_user_logged_in() && in_array($post->post_type, self::ALLOWED_POST_TYPES));
    }

    private function get_user_progress()
    {
        return get_user_meta(get_current_user_id(), self::PROGRESS_USER_META_KEY, true);
    }

    private function is_chapter_complete($chapter_id)
    {
        $post = get_post($chapter_id);
        $blog_id = get_current_blog_id();
        $progress = $this->get_user_progress();
        return ($progress && isset($progress[$blog_id]) && isset($progress[$blog_id][$post->ID]) && $progress[$blog_id][$post->ID] === true);
    }

    public function add_chapter_complete_button($content)
    {
        global $post;
        if ($this->show_complete_button($post)) {
            $isComplete = $this->is_chapter_complete($post->ID);
            $form = '<form id="hfh-course-progress-chapter-complete" method="POST"><input type="hidden" name="action" value="hfh_chapter_complete" /><input type="hidden" name="value" value="' . !$isComplete . '" /><input type="hidden" name="post" value="' . $post->ID . '" /><button type="submit">' . ($isComplete ? __('Mark Chapter as Incomplete', 'pressbooks-hfh') : __('Mark Chapter as Complete', 'pressbooks-hfh')) . '</button></form>';
            return $content . $form;
        }
        return $content;
    }

    public function hfh_chapter_complete()
    {
        if (check_ajax_referer('hfh_chapter_complete')) {
            if (isset($_POST['post']) && isset($_POST['value'])) {
                $post_id = intval($_POST['post']);
                $value = rest_sanitize_boolean($_POST['value']);
                $value = $this->set_chapter_complete($post_id, $value);
                wp_send_json_success(array('completed' => $value, 'progress' => $this->get_progress()));
            } else {
                wp_send_json_error();
            }
        }
    }

    private function set_chapter_complete($post_id, $value)
    {
        if ($post_id) {
            $progress = $this->get_user_progress();
            if (!$progress) {
                $progress = array();
            }
            global $blog_id;
            $progress[$blog_id][$post_id] = $value;
            update_user_meta(get_current_user_id(), self::PROGRESS_USER_META_KEY, $progress);
            return $value;
        }
    }
}
