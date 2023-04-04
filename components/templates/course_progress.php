<?php

namespace HfH\Pressbooks;

get_header();

$progress = CourseProgress::get_instance()->get_progress();

?>
<div class="hfh-course-progress">
    <h2>Course Progress</h2>
    <?= CourseProgress::get_instance()->get_progress_bar_template($progress) ?>
    <ul class="hfh-course-progress-list">
        <?php foreach ($progress['parts'] as $part) : ?>
            <li>
                <div class="hfh-part">
                    <h3 class="hfh-part__title"><?= $part['title'] ?></h3>
                    <?= CourseProgress::get_instance()->get_progress_bar_template($part['completion']) ?>
                    <?= CourseProgress::get_instance()->get_chapters_progress_template($part['completion']['chapters']) ?>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
<?php
get_footer();
