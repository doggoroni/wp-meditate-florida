<?php
if (!defined('ABSPATH')) {
    exit;
}

switch ($this->tab) {
    case 'dashboard':
        $this->get_template_part(__DIR__ . '/content/dashboard');
        break;
    case 'widgets':
        $this->get_template_part(__DIR__ . '/content/widgets');
        break;
    case '3rdpt-widgets':
        $this->get_template_part(__DIR__ . '/content/3rdpt-widgets');
        break;
    case 'extensions':
        $this->get_template_part(__DIR__ . '/content/extensions');
        break;
    case 'templates-kit':
        $this->get_template_part(__DIR__ . '/content/templates-kit');
        break;
    case 'settings':
        $this->get_template_part(__DIR__ . '/content/settings');
        break;
    case 'go-pro':
        $this->get_template_part(__DIR__ . '/content/go-pro');
        break;
    default:
        do_action('afeb/html/dashboard/get_tab_template', $this->tab);
        break;
}
