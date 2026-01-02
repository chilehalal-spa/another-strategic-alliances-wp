<?php
/**
 * Plantilla: Vista Grid (Cuadrícula)
 * Variables disponibles:
 * @var string $display_title
 * @var string $color
 * @var array  $items
 * @var string $extra_class
 * @var bool   $show_names
 * @var string $img_inline_style
 * @var int    $grid_cols (NUEVO)
 */
?>
<section class="another-grid-section">
    <div class="another-container">
        <?php if ($display_title): ?>
            <h3 class="another-section-title" style="color: <?php echo esc_attr($color); ?>;">
                <?php echo esc_html($display_title); ?>
            </h3>
        <?php endif; ?>
        
        <div class="another-grid-container" style="--grid-cols: <?php echo intval($grid_cols); ?>;">
            <?php foreach ($items as $item): 
                $name = esc_attr($item['title'] ?? '');
                $logo = esc_url($item['icon'] ?? '');
                $link = esc_url($item['url'] ?? '');
            ?>
                <div class="another-grid-item <?php echo $extra_class; ?>">
                    <?php if ($link): ?>
                        <a href="<?php echo $link; ?>" target="_blank" rel="noopener">
                            <img src="<?php echo $logo; ?>" alt="<?php echo $name; ?>" title="<?php echo $name; ?>" style="<?php echo $img_inline_style; ?>">
                        </a>
                    <?php else: ?>
                        <img src="<?php echo $logo; ?>" alt="<?php echo $name; ?>" title="<?php echo $name; ?>" style="<?php echo $img_inline_style; ?>">
                    <?php endif; ?>

                    <?php if ($show_names && $name): ?>
                        <span class="another-item-name"><?php echo $name; ?></span>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>