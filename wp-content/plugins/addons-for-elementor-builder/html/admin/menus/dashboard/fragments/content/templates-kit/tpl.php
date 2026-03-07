<?php

use AFEB\Base;
use AFEB\TemplatesKit;

if (!defined('ABSPATH')) {
    exit;
}
?>
<!-- Start Search Elements Section -->
<div class="afeb-space afeb-mr-20"></div>
<div class="afeb-row">
    <div class="afeb-col-lg-4"></div>
    <div class="afeb-col-lg-4">
        <div class="afeb-elements-heading">
            <h2 class=""><?php esc_html_e('What type of website are you building?', 'addons-for-elementor-builder'); ?></h2>
        </div>
        <!-- <div class="afeb-elements-search-section">
            <span class="afeb-elements-search-input-icon fa fa-search"></span>
            <input class="afeb-elements-search-input" type="text" placeholder="<?php esc_html_e('Search the demos', 'addons-for-elementor-builder'); ?>">
        </div> -->
    </div>
    <div class="afeb-col-lg-4"></div>
</div>
<div class="afeb-space afeb-mr-20"></div>
<!-- End Search Elements Section -->

<!-- Start Elements Section -->
<div class="afeb-row afeb-template-kit-section-row">
    <?php
    $count = 0;
    $kits = TemplatesKit::kits();
    $sorted_kits = [];

    foreach ($kits as $slug => $kit) {
        foreach ($kit as $version => $data)
            $sorted_kits[$slug . '-' . $version] = $data;
    }

    // Sort by Priority
    uasort($sorted_kits, function ($item1, $item2) {
        if ($item1['priority'] == $item2['priority']) return 0;
        return $item1['priority'] < $item2['priority'] ? -1 : 1;
    });

    foreach ($sorted_kits as $kit_id => $data):

        $image_url = 'https://cdn.webilia.com/u/c/vertex/' . $kit_id . '/';
        $preview_text = ' ' . esc_html__('Preview', 'addons-for-elementor-builder') . ' ';
        $preview_url = esc_url(Base::AFEB_DEMO_URL . '/' . $kit_id);
        $back_text = ' ' . esc_html__('Back', 'addons-for-elementor-builder') . ' ';

        $meta_data = [
            'kit_id' => "data-kit-id='" . esc_attr($kit_id) . "'",
            'plugins' => !empty($data['plugins']) ? "data-plugins='" . esc_attr(wp_json_encode($data['plugins'])) . "'" : '',
            'pages' => !empty($data['pages_title']) ? "data-pages='" . esc_attr(implode(',', array_keys($data['pages_title']))) . "'" : '',
            'pages_title' => !empty($data['pages_title']) ? "data-pages-title='" . esc_attr(wp_json_encode($data['pages_title'])) . "'" : '',
            'pages_meta' => !empty($data['pages_meta']) ? "data-pages-meta='" . esc_attr(wp_json_encode($data['pages_meta'])) . "'" : '',
            'image_url' => "data-image-url='" . $image_url . "'",
            'preview_text' => "data-preview-text='" . $preview_text . "'",
            'preview_url' => "data-preview-url='" . $preview_url . "'",
            'back_text' => "data-back-text='" . $back_text . "'",
        ];
    ?>
        <!-- Start Element Section -->
        <div class="afeb-element-search-section afeb-col-lg-4 <?php echo $count > 2 ? 'afeb-mr-top-20' : ''; ?>">
            <div class="afeb-box-section afeb-element-section afeb-template-kit-section afeb-template-kit-section-<?php echo esc_attr($kit_id) ?>"
                <?php echo implode(' ', $meta_data) ?>>
                <div class="afeb-box-header">
                    <span class="afeb-preview fa fa-plus"></span>
                    <span class="afeb-loader"><i class="fa fa-spin fa-spinner"></i></span>
                    <img src="<?php echo $image_url . 'home.webp'; ?>" alt="">
                </div>
                <div class="afeb-box-body">
                    <h3 class="afeb-element-search-text"><?php echo esc_html($data['name']); ?></h3>
                    <h5><?php echo esc_html($data['description']); ?></h5>
                    <div class="afeb-template-kit-btns">

                        <a class="afeb-template-kit-preview-btn" href="<?php echo $preview_url; ?>" target="_blank">
                            <?php echo $preview_text; ?>
                            <span class="fa fa-eye"></span>
                        </a>

                        <button class="afeb-template-kit-import-btn">
                            <?php esc_html_e('Import', 'addons-for-elementor-builder'); ?>
                            <span class="fa fa-download"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Element Section -->
    <?php
        $count++;
    endforeach;
    ?>
</div>
<!-- End Elements Section -->

<!-- Start Elements Section -->
<div class="afeb-row afeb-template-preview-kit-section"></div>
<!-- End Elements Section -->

<?php $this->get_template_part(__DIR__ . '/dialogs/import'); ?>
