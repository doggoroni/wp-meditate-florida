<?php

class LSDRC_Settings_Listdom extends LSDRC_Settings
{
    /**
     * Set listdom settings section for Redux.
     * @return void
     */
    public function register()
    {
        Redux::set_section(
            $this->opt_name,
            [
                'title' => esc_html__('Listdom Settings', 'listdomer-core'),
                'id' => 'listdomer-listdom-settings',
                'customizer_width' => '400px',
                'icon' => get_template_directory_uri() . '/assets/img/listdom-icon.svg',
                'desc' => sprintf(
                    '<div class="lsd-alert-no-my"><div class="lsd-alert lsd-info" role="alert">
                        <strong>%s</strong> %s <a href="%s">%s</a>
                    </div></div>',
                    esc_html__('Attention!', 'listdomer-core'),
                    esc_html__('If you want to change the Listdom settings', 'listdomer-core'),
                    esc_url(admin_url('admin.php?page=listdom-settings&tab=customizer')),
                    esc_html__('click here to edit.', 'listdomer-core')
                ),
                'class' => 'lsdrc-hide-redux-title',
            ]
        );
    }
}
