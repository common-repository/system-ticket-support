<?php
$number_page = $limit;
$data = range(1, $numbPage);
$num_results = $total;
$page = $current_page;
?>
<div class="sts-paginator" id="<?php echo isset($purchase_page) ? 'sts-paginator-purchase' : 'sts-paginator' ?>"
     data-sts-target="<?php echo esc_attr($target) ?>">
    <?php
    $tmp = [];
    for ($p = 1, $i = 0; $i < $num_results; $p++, $i += $number_page):
        if ($page == $p) :
            $tmp[] = '<a class="sts-paginator__item sts-paginator__item-numb active" href="#"
			   data-page="' . esc_attr($p) . '"
			>' . esc_html($p) . '</a>';
        else:
            $tmp[] = '<a class="sts-paginator__item sts-paginator__item-numb" href="#"
			   data-page="' . esc_attr($p) . '"
			>' . esc_html($p) . '</a>';;
        endif;
    endfor;

    for ($i = count($tmp) - 3; $i > 1; $i--) {
        if (abs($page - $i - 1) >= 2) {
            unset($tmp[$i]);
        }
    }
    if (count($tmp) > 1) :
        if ($page > 1) :
            ?>
            <a href="#" id="sts-paginator__item--prev"
               class="sts-paginator__item sts-paginator__item--prev"><?php esc_html_e('Previous', 'sts') ?> </a>
        <?php
        else:
            ?>
            <a href="#" id="sts-paginator__item--prev"
               class="sts-paginator__item sts-paginator__item--prev close"><?php esc_html_e('Previous', 'sts') ?></a>
        <?php
        endif;
        $lastlink = 0;
        foreach ($tmp as $i => $link) :
            if ($i > $lastlink + 1) :
                ?>
                <span>....</span>
            <?php
            endif;
            echo wp_kses_post($link);
            $lastlink = $i;
        endforeach;
        if ($page <= $lastlink) :
            ?>
            <a href="#" id="sts-paginator__item--next"
               class="sts-paginator__item sts-paginator__item--next"><?php esc_html_e('Next', 'sts') ?></a>
        <?php
        endif;
    endif;
    ?>
</div>
