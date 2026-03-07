<?php
$description = get_the_archive_description();

$is_limit_enabled = LSDR_Settings::get('listdomer_page_desc_archive_limit_enable', true);
$desc_data = [];

if ($is_limit_enabled)
{
    $limit = LSDR_Settings::get('listdomer_page_desc_archive_limit', 300);
    $desc_data = LSDR_Base::lsd_get_trimmed_description($description, $limit);
}
else
{
    $desc_data['short'] = $description;
    $desc_data['full'] = $description;
    $desc_data['is_show_more'] = false;
}
?>
<div
    class="lsd-description-content"
    data-short="<?php echo esc_attr($desc_data['short'] ?? ''); ?>"
    data-full="<?php echo esc_attr($desc_data['full'] ?? ''); ?>"
>
    <p><?php echo wp_kses_post($desc_data['short'] ?? ''); ?></p>
</div>

<?php if (!empty($desc_data['is_show_more'])): ?>
    <button
        class="lsd-description-toggle"
        type="button"
        data-state="less"
        data-show-more-label="<?php esc_attr_e('Show More', 'listdomer'); ?>"
        data-show-less-label="<?php esc_attr_e('Show Less', 'listdomer'); ?>"
    >
        <?php echo esc_html__('Show More', 'listdomer'); ?>
    </button>
<?php endif; ?>
