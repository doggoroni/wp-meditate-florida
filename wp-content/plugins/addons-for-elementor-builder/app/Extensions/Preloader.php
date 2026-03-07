<?php

namespace AFEB\Extensions;

use AFEB\Assets;
use AFEB\Extensions;
use AFEB\Helper;
use Elementor\Core\Kits\Documents\Kit;
use Elementor\Plugin;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" Preloader Extension Class
 * 
 * @class Preloader
 * @version 1.5.0
 */
class Preloader extends Extensions
{
    /**
     * @var Assets
     */
    private $assets;

    /**
     * Preloader Constructor
     * 
     * @since 1.5.0
     */
    public function __construct()
    {
        $this->assets = new Assets();
        $this->actions();
    }

    /**
     * Preloader Class Actions
     * 
     * @since 1.5.0
     */
    public function actions()
    {
        add_action('wp_enqueue_scripts', function () {
            $this->assets->preloader_style();
            $this->assets->preloader_script();
        });
        add_action('elementor/kit/register_tabs', [$this, 'register_site_settings'], 1, 40);
        add_action('wp_body_open', [$this, 'render_output'], 0);
    }

    /**
     * Register Preloader extension site_settings
     *
     * @since 1.5.0
     * 
     * @param object $kit
     */
    public function register_site_settings(Kit $kit)
    {
        $kit->register_tab('afeb_preloader_kit_settings', PreloaderKit::class);
    }

    /**
     * Render Preloader extension output on the frontend
     *
     * @since 1.5.0
     */
    public function render_output()
    {
        $settings = [];
        $settings['loader'] = Helper::get_elementor_settings('afeb_preloader_loader');

        if (!empty($settings['loader']) && $settings['loader'] != 'none' && !Helper::is_edit_mode()) {
            $settings['entrance_animation'] = Helper::get_elementor_settings('afeb_preloader_entrance_animation');
            $settings['exit_animation'] = Helper::get_elementor_settings('afeb_preloader_exit_animation');
            $settings['animation_duration'] = Helper::get_elementor_settings('afeb_preloader_animation_duration');

            $entrance_animation = isset($settings['entrance_animation']) ? esc_attr($settings['entrance_animation']) : 'none';
            $exit_animation = isset($settings['exit_animation']) ? esc_attr($settings['exit_animation']) : 'none';
            $animation_duration = isset($settings['animation_duration']['size']) ? floatval($settings['animation_duration']['size']) : 300;

            $styles = [];
            $kit_settings = [];
            $kit_id = get_option('elementor_active_kit', -1);
            $default_kit = Plugin::instance()->documents->get_doc_for_frontend($kit_id);

            if ($default_kit && is_object($default_kit)) {
                $kit_settings = $default_kit->get_settings();
            }

            if (!empty($kit_settings)) {

                if (!empty($kit_settings['afeb_preloader_background_color'])) {
                    $styles[] = [
                        'selectors' => ['.afeb-preloader',],
                        'rules' => ['background-color' => $kit_settings['afeb_preloader_background_color'],],
                    ];
                }

                if (!empty($kit_settings['afeb_preloader_loader_animation_background_color'])) {
                    $styles[] = [
                        'selectors' => [
                            '.afeb-modern-loader .afeb-bar',
                            '.afeb-worm-crawl-loader .afeb-circle:before',

                        ],
                        'rules' => ['background-color' => $kit_settings['afeb_preloader_loader_animation_background_color'],],
                    ];
                }

                if (!empty($kit_settings['afeb_preloader_loader_animation_color'])) {
                    $styles[] = [
                        'selectors' => [
                            '.afeb-whirlwind-loader:after',
                            '.afeb-whirlwind-loader:before',
                            '.afeb-whirlwind-loader',
                            '.afeb-speedster-loader .afeb-bar::before',

                        ],
                        'rules' => ['border-top-color' => $kit_settings['afeb_preloader_loader_animation_color'],],
                    ];
                }

                if (!empty($kit_settings['afeb_preloader_animation_scale']['size'])) {
                    $styles[] = [
                        'selectors' => ['.afeb-preloader .afeb-loader',],
                        'rules' => ['transform' => 'scale(' . $kit_settings['afeb_preloader_animation_scale']['size'] . ')',],
                    ];
                }
            }

            if (!empty($styles)) {
                echo '<style id="afeb-preloader-styles">';
                foreach ($styles as $i => $value) {
                    if (!empty($value['selectors'])) {
                        $selector = implode(',', $value['selectors']);
                        if (trim($selector)) {
                            echo $selector . '{';
                            foreach ($value['rules'] as $rule_key => $rule_value) {
                                if (trim($rule_value)) {
                                    echo $rule_key . ':' . $rule_value . ';';
                                }
                            }
                            echo '}';
                        }
                    }
                }
                echo '</style>';
            }
?>
            <div class="afeb-preloader"
                data-settings="<?php echo wp_json_encode([
                                    'entrance_animation' => $entrance_animation,
                                    'exit_animation' => $exit_animation,
                                    'animation_duration' => $animation_duration,
                                ]) ?>">
                <?php
                if ($settings['loader'] == 'animation') {
                    $settings['loader_animation'] = Helper::get_elementor_settings('afeb_preloader_loader_animation');
                    $loader_animation = isset($settings['loader_animation']) ? esc_attr($settings['loader_animation']) : 'modern';
                    $loader_animation_content = '<div class="afeb-preloader">';

                    switch ($loader_animation) {
                        case 'modern':
                            $loader_animation_content .= '<div class="afeb-modern-loader afeb-loader">';
                            for ($i = 0; $i < 5; $i++)
                                $loader_animation_content .= '<div class="afeb-bar"></div>';

                            $loader_animation_content .= '</div>';
                            break;
                        case 'whirlwind':
                            $loader_animation_content .= '<div class="afeb-loader"><div class="afeb-whirlwind-loader"></div></div>';
                            break;
                        case 'speedster':
                            $loader_animation_content .= '<div class="afeb-speedster-loader afeb-loader">';
                            for ($i = 0; $i < 4; $i++)
                                $loader_animation_content .= '<div class="afeb-bar"></div>';

                            $loader_animation_content .= '</div>';
                            break;
                        case 'worm-crawl':
                            $loader_animation_content .= '<div class="afeb-worm-crawl-loader afeb-loader">';
                            for ($i = 0; $i < 12; $i++)
                                $loader_animation_content .= '<div class="afeb-circle"></div>';

                            $loader_animation_content .= '</div>';
                            break;
                    }

                    $loader_animation_content .= '</div>';
                    echo $loader_animation_content;
                }
                ?>
            </div>
            <script>
                jQuery('.afeb-preloader').css({
                    'opacity': 1,
                    'z-index': 9999999
                });

                jQuery(window).on('beforeunload', function() {
                    let entrance_animation = '<?php echo $entrance_animation; ?>';
                    switch (entrance_animation) {
                        case 'fade-in':
                            jQuery('.afeb-preloader').fadeIn(<?php echo $animation_duration; ?>);
                            break;
                        case 'slide-down':
                            jQuery('.afeb-preloader').slideDown(<?php echo $animation_duration; ?>);
                            break;
                        default:
                            jQuery('.afeb-preloader').show();
                            break;
                    }
                });

                let fade_out_preloader = function() {
                    let exit_animation = '<?php echo $exit_animation; ?>';
                    switch (exit_animation) {
                        case 'fade-out':
                            jQuery('.afeb-preloader').fadeOut(<?php echo $animation_duration; ?>);
                            break;
                        case 'slide-up':
                            jQuery('.afeb-preloader').slideUp(<?php echo $animation_duration; ?>);
                            break;
                        default:
                            jQuery('.afeb-preloader').hide();
                            break;
                    }
                }

                let fall_back_timeout = setTimeout(fade_out_preloader, 10000);

                jQuery(window).on('pageshow', function(e) {
                    // It is executed when the action is back.
                    if (e.originalEvent.persisted) {
                        fade_out_preloader();
                    }
                });

                jQuery(window).on('load', function() {
                    clearTimeout(fall_back_timeout);
                    fade_out_preloader();
                });
            </script>
<?php
        }
    }
}
