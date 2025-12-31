<?php
/**
 * Plantilla: Vista Slider (Carrusel)
 * * Variables disponibles (heredadas del include):
 * @var string $display_title
 * @var string $color
 * @var array  $items
 * @var string $extra_class
 * @var bool   $show_names
 */
?>
<section class="another-slider-section">
    <div class="another-container">
        <?php if ($display_title): ?>
            <h3 class="another-section-title" style="color: <?php echo esc_attr($color); ?>;">
                <?php echo esc_html($display_title); ?>
            </h3>
        <?php endif; ?>
        
        <div class="swiper another-slider-container">
            <div class="swiper-wrapper">
                <?php foreach ($items as $item): 
                    $name = esc_attr($item['title'] ?? '');
                    $logo = esc_url($item['icon'] ?? '');
                    $link = esc_url($item['url'] ?? '');
                ?>
                    <div class="swiper-slide another-slider-item <?php echo $extra_class; ?>">
                        <?php if ($link): ?>
                            <a href="<?php echo $link; ?>" target="_blank" rel="noopener">
                                <img src="<?php echo $logo; ?>" alt="<?php echo $name; ?>" title="<?php echo $name; ?>">
                            </a>
                        <?php else: ?>
                            <img src="<?php echo $logo; ?>" alt="<?php echo $name; ?>" title="<?php echo $name; ?>">
                        <?php endif; ?>

                        <?php if ($show_names && $name): ?>
                            <span class="another-item-name"><?php echo $name; ?></span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>