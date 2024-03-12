<?php if ($parts_color) : ?>
    <style>
        html:root {
            --c-parts-color: <?= $parts_color ?>;
        }
    </style>
<?php endif; ?>
<div class="phase-bar">
    <ul>
        <?php
        if ($atts['include-front-matter'] == 'true') :

            $first_chapter_id = null;
            if (count($front_matters) > 0) {
                $first_chapter_id = $front_matters[0]["ID"];
            }
        ?>
            <li>
                <?php if ($first_chapter_id) : ?>
                    <a href="<?= get_permalink($first_chapter_id) ?>">
                    <?php endif; ?>
                    <div class="phase <?php if ($current_part["ID"] == 'front-matter') :  ?>phase--current<?php endif; ?>">
                        <?php if ($index == 0) : ?>
                            <div><svg class="arrow arrow--white arrow--before" viewbox="0 0 6 10" preserveAspectRatio="none">
                                    <polygon points="0,0 5,5 0,10" />
                                </svg>
                            </div>
                        <?php endif; ?>
                        <div class="phase__text"><?= __('Front Matter', 'pressbooks') ?></div>
                        <div><svg class="arrow arrow--after" viewbox="0 0 6 10" preserveAspectRatio="none">
                                <polygon points="0,0 5,5 0,10">
                            </svg>
                        </div>
                        <div><svg class="arrow arrow--white arrow--after" viewbox="0 0 6 10" preserveAspectRatio="none">
                                <polygon points="0,0 5,5 0,10" />
                            </svg>
                        </div>
                    </div>
                    <?php if ($first_chapter_id) : ?>
                    </a>
                <?php endif; ?>
            </li>
        <?php endif; ?>

        <?php
        foreach ($parts as $index => $part) :
            $first_chapter_id = null;
            if (count($part["chapters"]) > 0) {
                $first_chapter_id = $part["chapters"][0]["ID"];
            }
        ?>
            <li>
                <?php if ($first_chapter_id) : ?>
                    <a href="<?= get_permalink($first_chapter_id) ?>">
                    <?php endif; ?>
                    <div class="phase <?php if ($current_part["ID"] == $part["ID"]) :  ?>phase--current<?php endif; ?>">
                        <?php if ($index == 0) : ?>
                            <div><svg class="arrow arrow--white arrow--before" viewbox="0 0 6 10" preserveAspectRatio="none">
                                    <polygon points="0,0 5,5 0,10" />
                                </svg>
                            </div>
                        <?php endif; ?>
                        <div class="phase__text"><?= $part["post_title"] ?></div>
                        <div><svg class="arrow arrow--after" viewbox="0 0 6 10" preserveAspectRatio="none">
                                <polygon points="0,0 5,5 0,10">
                            </svg>
                        </div>
                        <div><svg class="arrow arrow--white arrow--after" viewbox="0 0 6 10" preserveAspectRatio="none">
                                <polygon points="0,0 5,5 0,10" />
                            </svg>
                        </div>
                    </div>
                    <?php if ($first_chapter_id) : ?>
                    </a>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>

        <?php
        if ($atts['include-back-matter'] == 'true') :

            $first_chapter_id = null;
            if (count($back_matters) > 0) {
                $first_chapter_id = $back_matters[0]["ID"];
            }
        ?>
            <li>
                <?php if ($first_chapter_id) : ?>
                    <a href="<?= get_permalink($first_chapter_id) ?>">
                    <?php endif; ?>
                    <div class="phase <?php if ($current_part["ID"] == 'back-matter') :  ?>phase--current<?php endif; ?>">
                        <?php if ($index == 0) : ?>
                            <div><svg class="arrow arrow--white arrow--before" viewbox="0 0 6 10" preserveAspectRatio="none">
                                    <polygon points="0,0 5,5 0,10" />
                                </svg>
                            </div>
                        <?php endif; ?>
                        <div class="phase__text"><?= __('Back Matter', 'pressbooks') ?></div>
                        <div><svg class="arrow arrow--after" viewbox="0 0 6 10" preserveAspectRatio="none">
                                <polygon points="0,0 5,5 0,10">
                            </svg>
                        </div>
                        <div><svg class="arrow arrow--white arrow--after" viewbox="0 0 6 10" preserveAspectRatio="none">
                                <polygon points="0,0 5,5 0,10" />
                            </svg>
                        </div>
                    </div>
                    <?php if ($first_chapter_id) : ?>
                    </a>
                <?php endif; ?>
            </li>
        <?php endif; ?>
    </ul>
</div>