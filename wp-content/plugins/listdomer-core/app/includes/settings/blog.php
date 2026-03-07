<?php

class LSDRC_Settings_Blog extends LSDRC_Settings
{
    /**
     * Sets the Blog section and fields in Redux Framework.
     * @return void
     */
    public function register()
    {
        Redux::set_section(
            $this->opt_name,
            [
                'title' => esc_html__('Blog', 'listdomer-core'),
                'id' => 'listdomer-blog',
                'desc' => esc_html__('Blog settings for various elements.', 'listdomer-core'),
                'customizer_width' => '400px',
                'icon' => 'el el-pencil',
                'fields' => $this->get_blog_fields(),
            ]
        );

        (new LSDRC_Settings_Blog_Archive())->register();
        (new LSDRC_Settings_Blog_Single())->register();
    }

    /**
     * Returns the fields for the Blog settings.
     * @return array Fields array.
     */
    private function get_blog_fields(): array
    {
        return [];
    }
}
