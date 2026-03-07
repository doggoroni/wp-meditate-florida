<?php

use Elementor\Modules\Library\Documents\Library_Document;

class LSDRC_Documents_Header extends Library_Document
{
    public static function get_properties(): array
    {
        $properties = parent::get_properties();

        $properties['support_kit'] = true;
        $properties['show_in_finder'] = true;

        return $properties;
    }

    public static function get_type(): string
    {
        return 'lsdr-header';
    }

    public static function get_title(): string
    {
        return esc_html__('Listdomer Header', 'listdomer-core');
    }

    public static function get_plural_title(): string
    {
        return esc_html__('Listdomer Headers', 'listdomer-core');
    }
}
