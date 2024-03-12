<?php if ($chapters_color) : ?>
    <style>
        html:root {
            --c-chapters-color: <?= $chapters_color ?>;
        }
    </style>
<?php endif; ?>
<div class="timeline-wrapper">
    <div class="timeline">
        <div class="steps">
            <div>
                <?php if ($current_index > 0) :
                    $previous_chapter_ID = $current_part_chapters[$current_index - 1]["ID"]
                ?>
                    <div class="timeline__navigation timeline__navigation--previous">
                        <a href="<?= esc_url(get_permalink($previous_chapter_ID)) ?>"><svg class="hfh-icon hfh-icon--carret" width="13" height="20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="m1.005.999-.665.738.074.073c.04.039 2.1 1.895 4.576 4.123 2.477 2.229 4.501 4.06 4.499 4.07-.002.01-2.028 1.839-4.503 4.066-2.474 2.226-4.532 4.08-4.572 4.12l-.074.072.665.738.665.738 5.407-4.867 5.407-4.866-.094-.092c-.052-.05-2.485-2.242-5.407-4.871L1.67.261l-.665.738" fill-rule="evenodd" fill="currentColor" />
                            </svg></a>
                    </div>
                <?php endif; ?>
            </div>
            <?php
            for ($i = 0; $i < 3; $i++) :
                if ($i < count($chapters)) :
                    $chapter = $chapters[$i];
            ?>
                    <div class="step__label-wrapper" data-active="<?= $i == 0 ?>"><a href="<?= esc_url(get_permalink($chapter["ID"])) ?>" class="step__label"><?= $chapter["post_title"] ?></a>
                    </div>
                <?php else : ?>
                    <div></div>
                <?php endif; ?>
            <?php endfor; ?>
            <div>
                <?php if ($current_index < count($current_part_chapters) - 1) :
                    $next_chapter_ID = $current_part_chapters[$current_index + 1]["ID"]
                ?>
                    <div class="timeline__navigation timeline__navigation--next"><a href="<?= esc_url(get_permalink($next_chapter_ID)) ?>"><svg class="hfh-icon hfh-icon--carret" width="13" height="20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="m1.005.999-.665.738.074.073c.04.039 2.1 1.895 4.576 4.123 2.477 2.229 4.501 4.06 4.499 4.07-.002.01-2.028 1.839-4.503 4.066-2.474 2.226-4.532 4.08-4.572 4.12l-.074.072.665.738.665.738 5.407-4.867 5.407-4.866-.094-.092c-.052-.05-2.485-2.242-5.407-4.871L1.67.261l-.665.738" fill-rule="evenodd" fill="currentColor" />
                            </svg></a></div>
                <?php endif; ?>
            </div>

            <div></div>
            <?php for ($i = 0; $i < 3; $i++) :
                if ($i < count($chapters)) :
            ?>
                    <div class="connection-line" data-active="<?= $i == 0 ?>">
                    </div>
                <?php else : ?>
                    <div></div>
                <?php endif; ?>
            <?php endfor; ?>
            <div></div>

            <div></div>
            <?php for ($i = 0; $i < 3; $i++) :
                if ($i < count($chapters)) :
                    $is_first_chapter = $current_index + $i == 0;
                    $is_last_chapter = $current_index + $i == count($current_part_chapters) - 1;
            ?>
                    <div class="step__line" data-active="<?= $i == 0 ?>">
                        <?php if (!$is_first_chapter) : ?>
                            <div class="step__line__before"></div>
                        <?php else : ?>
                            <div></div>
                        <?php endif; ?>
                        <div class="circle"></div>
                        <?php if (!$is_last_chapter) : ?>
                            <div class="step__line__after"></div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endfor; ?>
            <div></div>
        </div>
    </div>
</div>
<div class="before-content-connection-line"></div>