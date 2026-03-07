<?php

namespace AFEB\Extensions;

use AFEB\Assets;
use AFEB\Extensions;
use AFEB\Helper;
use Elementor\Core\Kits\Documents\Kit;
use Elementor\Icons_Manager;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" GoingUp Extension Class
 * 
 * @class GoingUp
 * @version 1.0.4
 */
class GoingUp extends Extensions
{
    /**
     * @var Assets
     */
    private $assets;

    /**
     * GoingUp Constructor
     * 
     * @since 1.0.4
     */
    public function __construct()
    {
        $this->assets = new Assets();
        $this->actions();
    }

    /**
     * GoingUp Class Actions
     * 
     * @since 1.0.4
     */
    public function actions()
    {
        add_action('wp_enqueue_scripts', function () {
            $this->assets->going_up_style();
            $this->assets->going_up_script();
        });
        add_action('elementor/kit/register_tabs', [$this, 'register_site_settings'], 1, 40);
        add_action('wp_footer', [$this, 'render_output']);
    }

    /**
     * Register GoingUp extension site_settings
     *
     * @since 1.0.4
     * 
     * @param object $kit
     */
    public function register_site_settings(Kit $kit)
    {
        $kit->register_tab('afeb_going_up_kit_settings', GoingUpKit::class);
    }

    /**
     * Render GoingUp extension output on the frontend
     *
     * @since 1.0.4
     */
    public function render_output()
    {
        $settings = [];
        $settings['afeb_gup'] = Helper::get_elementor_settings('afeb_gup');
        $settings['afeb_gup_ic'] = Helper::get_elementor_settings('afeb_gup_ic');
        $settings['afeb_gup_ttl_sh'] = Helper::get_elementor_settings('afeb_gup_ttl_sh');
        $settings['afeb_gup_ttl'] = Helper::get_elementor_settings('afeb_gup_ttl');
        if ($settings['afeb_gup'] || Helper::is_edit_mode()) :
            $classes = $settings['afeb_gup_ttl_sh'] == 'yes' && trim($settings['afeb_gup_ttl']) ? 'show-title' : '';
?>
            <div class="afeb-gotop">
                <button class="afeb-gotop-btn <?php echo esc_attr($classes); ?>">
                    <span class="afeb-gotop-ic">
                        <?php Icons_Manager::render_icon($settings['afeb_gup_ic']); ?>
                    </span>
                    <?php if ($classes) : ?>
                        <span class="afeb-gotop-title"><?php echo esc_html($settings['afeb_gup_ttl']); ?></span>
                    <?php endif; ?>
                </button>
            </div>
<?php
        endif;
    }
}
