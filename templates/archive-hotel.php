<?php
#TODO: remove GDLR
?>
<?php get_header(); ?>
<div class="gdlr-content">

    <?php
        global $gdlr_sidebar, $theme_option;
        $gdlr_sidebar = array(
            'type'=>$theme_option['archive-sidebar-template'],
            'left-sidebar'=>$theme_option['archive-sidebar-left'],
            'right-sidebar'=>$theme_option['archive-sidebar-right']
        );
        $gdlr_sidebar = gdlr_get_sidebar_class($gdlr_sidebar);
    ?>
    <div class="with-sidebar-wrapper">
        <div class="with-sidebar-container container">
            <div class="with-sidebar-left <?php echo esc_attr($gdlr_sidebar['outer']); ?> columns">
                <div class="with-sidebar-content <?php echo esc_attr($gdlr_sidebar['center']); ?> gdlr-item-start-content columns">
                    <?php include( plugin_dir_path( __FILE__ ) . '/loop-hotel.php' ) ?>
                </div>
                <?php get_sidebar('left'); ?>
                <div class="clear"></div>
            </div>
            <?php get_sidebar('right'); ?>
            <div class="clear"></div>
        </div>
    </div>

</div><!-- gdlr-content -->
<?php get_footer(); ?>
