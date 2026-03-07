<?php

namespace AFEB;

use Elementor\Plugin;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" Helper Class
 * 
 * @class Helper
 * @version 1.0.0
 */
class Helper extends Base
{
    /**
     * Print the admin notice
     * 
     * @since 1.0.0
     * 
     * @param string $message
     * @param string $type
     * @param boolean $dismissible
     */
    public static function admin_notice($message = '', $type = 'success', $dismissible = false)
    {
        $html_message = sprintf('<div class="notice notice-%s %s">%s</div>', esc_attr($type), $dismissible == true ? 'is-dismissible' : '', wpautop($message));
        echo wp_kses_post($html_message);
    }

    /**
     * The front notice template
     * 
     * @since 1.0.0
     * 
     * @param string $msg
     * @param string $type
     */
    public static function front_notice($msg, $type = 'success')
    {
        $type = 'afeb-' . $type;
        return '<div class="afeb-alert ' . esc_attr($type) . '">' . wp_kses_post($msg) . '</div>';
    }

    /**
     * Get PRO Badge
     * 
     * @since 1.0.2
     * 
     * @param bool $link
     * 
     * @return string
     */
    public static function pro_badge($link = true)
    {
        return $link ? '<a href="" target="_blank" class="afeb-pro-badge"><span>' . esc_html__('PRO', 'addons-for-elementor-builder') . '</span></a>' :
            '<span class="afeb-pro-badge"><span>' . esc_html__('PRO', 'addons-for-elementor-builder') . '</span></span>';
    }

    /**
     * Checks if a plugin is installed or not
     * 
     * @since 1.0.2
     * 
     * @param string $plugin
     * @param string $plugin_path
     */
    public static function is_plugin_installed($plugin, $plugin_path)
    {
        $installed = get_plugins();
        return isset($installed[$plugin_path]);
    }

    /**
     * Check if elementor edit mode or not
     *
     * @return bool
     * @since 1.0.3
     *
     */
    public static function is_edit_mode()
    {
        if (isset($_REQUEST['elementor-preview'])) return true;
        return false;
    }

    /**
     * Returns the general Elementor site settings
     * 
     * @since 1.0.4
     * 
     * @param string $setting_id
     * 
     * @return string
     */
    public static function get_elementor_settings($setting_id)
    {
        global $afeb_elementor_settings;
        $return = '';

        // Elementor Not Installed
        if (!class_exists(Plugin::class) || !did_action('elementor/loaded')) return $return;

        $kit = Plugin::instance()->kits_manager->get_active_kit();
        $active_kit_id = $kit->get_id();

        if ($active_kit_id) {
            if (!isset($afeb_elementor_settings['kit_settings'])) {
                $kit = Plugin::$instance->documents->get($active_kit_id, false);
                $afeb_elementor_settings['kit_settings'] = $kit->get_settings();
            }

            if (isset($afeb_elementor_settings['kit_settings'][$setting_id]))
                $return = $afeb_elementor_settings['kit_settings'][$setting_id];
        }

        return apply_filters('afeb/elementor/settings/' . $setting_id, $return);
    }

    /**
     * Get elementor template type
     * 
     * @since 1.2.0
     * 
     * @param string $id
     * 
     * @return string
     */
    public static function get_elementor_template_type($id)
    {
        $post_meta = get_post_meta($id);
        return !empty($post_meta['_elementor_template_type'][0]) ? $post_meta['_elementor_template_type'][0] : '';
    }

    /**
     * Filters text content and strips out disallowed HTML
     * 
     * @since 1.0.0
     * 
     * @param string $text
     * 
     * @return string
     */
    public static function kses($text)
    {
        return trim($text) ? wp_kses($text, self::allowed_tags(), array_merge(wp_allowed_protocols(), ['data'])) : '';
    }

    /**
     * List of allowed html tag for wp_kses
     *
     * @since 1.0.0
     * 
     * @param array $exclude_all_except
     * @param array $extra
     * 
     * @return array
     */
    public static function allowed_tags($exclude_all_except = [], $extra = [])
    {
        $allowed_tags = [
            'a' => [
                'class' => [],
                'href' => [],
                'target' => []
            ],
            'div' => [
                'class' => [],
                'id' => []
            ],
            'img'     => [
                'alt' => [],
                'class' => [],
                'src' => [],
                'title' => []
            ],
            'span' => [
                'class' => []
            ],
            'svg' => [
                'class' => [],
                'xmlns' => [],
                'viewBox' => []
            ]
        ];
        if (count($extra) > 0) $allowed_tags = array_merge_recursive($allowed_tags, $extra);
        if (count($exclude_all_except) > 0) {
            foreach ($exclude_all_except as $tag_key => $tag_value) {
                unset($exclude_all_except[$tag_key]);
                $exclude_all_except[$tag_value] = $allowed_tags[$tag_value];
            }

            $allowed_tags = $exclude_all_except;
        }

        return apply_filters('afeb/kses/allowed_tags', $allowed_tags);
    }

    /**
     * Get page title
     * 
     * @since 1.3.0
     * 
     * @param bool $include_context
     * 
     * @return string
     */
    public static function get_page_title($include_context = true)
    {
        $title = '';

        if (is_singular()) {
            /* translators: %s: Search term. */
            $title = get_the_title();

            if ($include_context) {
                $post_type_obj = get_post_type_object(get_post_type());

                if (isset($post_type_obj->labels->singular_name))
                    $title = sprintf('%s: %s', $post_type_obj->labels->singular_name, $title);
            }
        } elseif (is_search()) {
            /* translators: %s: Search term. */
            $title = sprintf(esc_html__('Search Results for: %s', 'addons-for-elementor-builder'), get_search_query());

            if (get_query_var('paged')) {
                /* translators: %s: Page number. */
                $title .= sprintf(esc_html__('&nbsp;&ndash; Page %s', 'addons-for-elementor-builder'), get_query_var('paged'));
            }
        } elseif (is_category()) {
            $title = single_cat_title('', false);

            if ($include_context) {
                /* translators: Category archive title. %s: Category name. */
                $title = sprintf(esc_html__('Category: %s', 'addons-for-elementor-builder'), $title);
            }
        } elseif (is_tag()) {
            $title = single_tag_title('', false);
            if ($include_context) {
                /* translators: Tag archive title. %s: Tag name. */
                $title = sprintf(esc_html__('Tag: %s', 'addons-for-elementor-builder'), $title);
            }
        } elseif (is_author()) {
            $title = '<span class="vcard">' . get_the_author() . '</span>';

            if ($include_context) {
                /* translators: Author archive title. %s: Author name. */
                $title = sprintf(esc_html__('Author: %s', 'addons-for-elementor-builder'), $title);
            }
        } elseif (is_year()) {
            $title = get_the_date(_x('Y', 'yearly archives date format', 'addons-for-elementor-builder'));

            if ($include_context) {
                /* translators: Yearly archive title. %s: Year. */
                $title = sprintf(esc_html__('Year: %s', 'addons-for-elementor-builder'), $title);
            }
        } elseif (is_month()) {
            $title = get_the_date(_x('F Y', 'monthly archives date format', 'addons-for-elementor-builder'));

            if ($include_context) {
                /* translators: Monthly archive title. %s: Month name and a year. */
                $title = sprintf(esc_html__('Month: %s', 'addons-for-elementor-builder'), $title);
            }
        } elseif (is_day()) {
            $title = get_the_date(_x('F j, Y', 'daily archives date format', 'addons-for-elementor-builder'));

            if ($include_context) {
                /* translators: Daily archive title. %s: Date. */
                $title = sprintf(esc_html__('Day: %s', 'addons-for-elementor-builder'), $title);
            }
        } elseif (is_tax('post_format')) {
            if (is_tax('post_format', 'post-format-aside'))
                $title = _x('Asides', 'post format archive title', 'addons-for-elementor-builder');
            elseif (is_tax('post_format', 'post-format-gallery'))
                $title = _x('Galleries', 'post format archive title', 'addons-for-elementor-builder');
            elseif (is_tax('post_format', 'post-format-image'))
                $title = _x('Images', 'post format archive title', 'addons-for-elementor-builder');
            elseif (is_tax('post_format', 'post-format-video'))
                $title = _x('Videos', 'post format archive title', 'addons-for-elementor-builder');
            elseif (is_tax('post_format', 'post-format-quote'))
                $title = _x('Quotes', 'post format archive title', 'addons-for-elementor-builder');
            elseif (is_tax('post_format', 'post-format-link'))
                $title = _x('Links', 'post format archive title', 'addons-for-elementor-builder');
            elseif (is_tax('post_format', 'post-format-status'))
                $title = _x('Statuses', 'post format archive title', 'addons-for-elementor-builder');
            elseif (is_tax('post_format', 'post-format-audio'))
                $title = _x('Audio', 'post format archive title', 'addons-for-elementor-builder');
            elseif (is_tax('post_format', 'post-format-chat'))
                $title = _x('Chats', 'post format archive title', 'addons-for-elementor-builder');
        } elseif (is_post_type_archive()) {
            $title = post_type_archive_title('', false);

            if ($include_context) {
                /* translators: Post type archive title. %s: Post type name. */
                $title = sprintf(esc_html__('Archives: %s', 'addons-for-elementor-builder'), $title);
            }
        } elseif (is_tax()) {
            $title = single_term_title('', false);

            if ($include_context) {
                $tax = get_taxonomy(get_queried_object()->taxonomy);

                if (isset($tax->labels->singular_name))
                    /* translators: Taxonomy term archive title. 1: Taxonomy singular name, 2: Current taxonomy term. */
                    $title = sprintf(esc_html__('%1$s: %2$s', 'addons-for-elementor-builder'), $tax->labels->singular_name, $title);
            }
        } elseif (is_archive()) {
            $title = esc_html__('Archives', 'addons-for-elementor-builder');
        } elseif (is_404()) {
            $title = esc_html__('Page Not Found', 'addons-for-elementor-builder');
        }

        return $title;
    }

    /**
     * Disable Extra Image Sizes
     * 
     * @since 1.4.0
     */
    public static function disable_extra_image_sizes($new_sizes, $image_meta, $attachment_id)
    {
        $all_attachments = get_option('st_attachments', []);

        // If the cron job is already scheduled, bail
        if (in_array($attachment_id, $all_attachments, true))
            return $new_sizes;

        $all_attachments[] = $attachment_id;

        update_option('st_attachments', $all_attachments, 'no');

        // Return blank array of sizes to not generate any sizes in this request
        return [];
    }

    /**
     * Regenerate Extra Image Sizes
     * 
     * @since 1.4.0
     */
    public static function regenerate_extra_image_sizes()
    {
        $all_attachments = get_option('st_attachments', []);

        if (empty($all_attachments))
            return;

        foreach ($all_attachments as $attachment_id) {
            $file = get_attached_file($attachment_id);
            if (false !== $file)
                wp_generate_attachment_metadata($attachment_id, $file);
        }

        update_option('st_attachments', [], 'no');
    }

    /**
     * applies the callback to the element of the given array recursively
     * 
     * @since 1.0.0
     * 
     * @param callback $callback
     * @param array $array
     * @param string $separator
     * @param string $path
     * 
     * @return array
     */
    public function array_map_recursive($callback, $array, $separator = '_', $path = '')
    {
        $output = [];

        if (!trim($separator)) {
            $separator = '_';
        }
        if (!isset($array_base)) {
            $array_base = $array;
        }

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $path .= "{$key}$separator";
                $check_path = $this->check_array_path($array_base, $path, $separator);

                if (!$check_path['success']) {
                    $invalid_path = explode($separator, substr($path, 0, -1));
                    $index = (array_search($check_path['value'], $invalid_path) - 1);
                    unset($invalid_path[$index]);
                    $path = implode($separator, $invalid_path) . $separator;
                }

                $output[$key] = $this->array_map_recursive($callback, $value, $separator, $path);
            } else {
                $output[$key] = $callback($value, $key, "{$path}{$key}");
            }
        }

        return $output;
    }

    /**
     * Validate an array path
     * 
     * @since 1.0.0
     * 
     * @param array $array
     * @param string $path
     * @param string $separator
     * 
     * @return array
     */
    public function check_array_path($array, $path = '', $separator = '_')
    {
        $i = 1;
        $path = explode($separator, $path);

        if (!trim(end($path))) {
            $path = array_slice($path, 0, (count($path) - 1));
        }

        foreach ($path as $path_key => $path_value) {
            if (isset($array[$path_value])) {
                if ($i == count($path)) {
                    return [
                        'success' => true,
                        'value' => $path_value
                    ];
                } else if (is_array($array[$path_value])) {
                    $array = $array[$path_value];
                } else {
                    return [
                        'success' => false,
                        'value' => $path_value
                    ];
                }
            } else {
                return [
                    'success' => false,
                    'value' => $path_value
                ];
            }

            $i++;
        }
    }

    /**
     * Get all users
     * 
     * @since 1.3.0
     * 
     * @return array
     */
    public static function get_users()
    {
        $users = [];

        if (is_admin()) {
            foreach (get_users() as $key => $user)
                $users[$user->data->ID] = $user->data->user_nicename;

            wp_reset_postdata();
        }

        return $users;
    }

    /**
     * Fetch a filtered list of user roles
     * 
     * @since 1.0.5
     * 
     * @return bool
     */
    public function get_user_roles()
    {
        $user_roles[''] = __('Default', 'addons-for-elementor-builder');
        if (function_exists('get_editable_roles')) {
            $wp_roles = get_editable_roles();
            $roles = $wp_roles ? $wp_roles : [];
            if (!empty($roles) && is_array($roles)) {
                foreach ($wp_roles as $role_key => $role) {
                    if ($role_key === 'administrator') continue;
                    $user_roles[$role_key] = $role['name'];
                }
            }
        }

        return apply_filters('afeb/user-roles', $user_roles);
    }

    /**
     * Is it good?
     * 
     * @since 1.0.5
     * 
     * @param string $input
     * 
     * @return bool
     */
    public static function hpv($input)
    {
        return (strpos($input, Widgets::PFX) === false);
    }

    /**
     * Prepare array
     * 
     * @since 1.0.2
     * 
     * @param array $array
     * @param string $name
     * @param array $extra
     * @param int $setter
     * @param array $get_array
     * 
     * @return int|string
     */
    public static function get_array($array = [], $name = '', $extra = [], $setter = 1, $get_array = [])
    {
        if ($setter == 1) foreach ($array as $i => $v) $get_array[is_int($i) ? sprintf("%s2{$i}", Widgets::PFX) : $i] = $v;
        else $get_array = $array;

        return apply_filters($name, $get_array, $extra);
    }

    /**
     * Retrieves the terms in a given taxonomy or list of taxonomies
     * 
     * @since 1.0.5
     *
     * @param string $id
     *
     * @return string
     */
    public static function get_page_as_element($id = '')
    {
        $id = intval($id);
        $status = get_post_status($id);

        if (! $status || $status === 'inherit' || is_page($id)) return '';

        if (get_post_meta($id, '_elementor_edit_mode', true) && did_action('elementor/loaded'))
            return Plugin::instance()->frontend->get_builder_content($id, !isset($_GET['elementor-preview']));

        return get_post_field('post_content', $id);
    }

    /**
     * Add new items to the array
     * 
     * @since 1.2.0
     * 
     * @param array $array
     * @param array|string $new_data
     * @param string $type
     * 
     * @return array
     */
    public static function new_array_items($array = [], $new_data = [], $type = 'prepend'): array
    {
        $output = [];
        if (!empty($new_data)) {
            if (!is_array($new_data)) {
                switch ($new_data) {
                    case 'all':
                        $output[''] = esc_html__('All', 'addons-for-elementor-builder');
                        break;
                    case 'non':
                        $output[''] = esc_html__('None', 'addons-for-elementor-builder');
                        break;
                }
            }
        }

        $output = $type == 'prepend' ? array_merge($array, $output) : array_merge($output, $array);

        return $output;
    }

    /**
     * Get post types options list
     * 
     * @since 1.2.0
     * 
     * @param array $exclude
     * @param array $args
     *
     * @return array
     */
    public static function get_post_types($exclude = [], $args = []): array
    {
        $defaults = [
            'public' => true
        ];

        $parsed_args = wp_parse_args($args, $defaults);
        $def_item = '';

        if (!empty($parsed_args['def_item'])) {
            $def_item = $parsed_args['def_item'];
            unset($parsed_args['def_item']);
        }

        $post_types = get_post_types($parsed_args, 'objects');
        $post_types_output = (!empty($post_types) &&
            !empty($def_item)) ?
            self::new_array_items([], $def_item) : [];

        if (empty($post_types))
            return $post_types_output;

        foreach ($post_types as $slug => $post_type)
            $post_types_output[$slug] = $post_type->label;

        return array_diff_key($post_types_output, $exclude);
    }

    /**
     * Get posts by type
     * 
     * @since 1.2.0
     * 
     * @param string $type
     * @param array $args
     *
     * @return array
     */
    public static function get_posts_by_type($type, $args = []): array
    {
        // ✅ Direct numeric ID lookup first
        if (isset($args['s']) && is_numeric($args['s'])) {
            $post_id = absint($args['s']);
            $post = get_post($post_id);

            if ($post && $post->post_type === $type && $post->post_status === 'publish') {
                return [
                    $post->ID => $post->post_title ?: sprintf(
                        __('(no title) – ID: %d', 'addons-for-elementor-builder'),
                        $post->ID
                    ),
                ];
            }
        }

        // If nothing passed, bail
        if (empty($type) || !post_type_exists($type)) {
            return [];
        }

        // 🔍 Normal behavior
        $defaults = [
            'post_type'           => $type,
            'posts_per_page'      => -1,
            'ignore_sticky_posts' => true,
            'suppress_filters'    => false,
            'post_status'         => ['publish'],
        ];

        // Allow drafts/private in Elementor editor
        if (
            (defined('ELEMENTOR_VERSION') && \Elementor\Plugin::$instance->editor->is_edit_mode())
            || isset($_GET['elementor-preview'])
        ) {
            $defaults['post_status'] = ['publish', 'draft', 'private'];
        }

        $parsed_args = wp_parse_args($args, $defaults);
        $posts       = get_posts($parsed_args);

        // Default item logic
        if (!empty($parsed_args['def_item'])) {
            if ($parsed_args['def_item'] === 'non') {
                // Add a 'None' entry without dropping actual posts
                $posts_output[0] = esc_html__('None', 'addons-for-elementor-builder');
            } else {
                $posts_output = self::new_array_items([], $parsed_args['def_item']);
            }
        }

        foreach ($posts as $post) {
            $posts_output[$post->ID] = html_entity_decode(get_the_title($post->ID));
        }

        return $posts_output;
    }

    public static function get_terms($taxonomy = 'product_cat', $args = []) {
        $defaults = [
                'taxonomy'   => $taxonomy,
                'hide_empty' => false,
                'orderby'    => 'name',
                'order'      => 'ASC'
        ];
        $terms = get_terms(wp_parse_args($args, $defaults));

        $options = [];
        if (!is_wp_error($terms)) {
            foreach ($terms as $term) {
                $options[$term->term_id] = $term->name;
            }
        }
        return $options;
    }

    /**
     * Safe term fetcher for Elementor controls
     *
     * Always returns an array, handles missing taxonomies,
     * WordPress errors, and optionally adds a default item.
     *
     * @since 1.0.0
     *
     * @param string $taxonomy The taxonomy slug (e.g., 'product_cat')
     * @param array  $args     Query arguments for get_terms()
     *
     * @return array [ term_id => term_name ]
     */
    public static function get_terms_safe(string $taxonomy, array $args = []): array
    {
        // Bail if taxonomy is not ready
        if (empty($taxonomy) || !taxonomy_exists($taxonomy)) {
            return [];
        }

        $defaults = [
                'taxonomy'   => $taxonomy,
                'hide_empty' => false,
                'orderby'    => 'name',
                'order'      => 'ASC',
        ];

        $parsed_args = wp_parse_args($args, $defaults);

        $terms = get_terms($parsed_args);

        if (is_wp_error($terms) || empty($terms)) {
            return [];
        }

        $output = !empty($parsed_args['def_item'])
                ? self::new_array_items([], $parsed_args['def_item'])
                : [];

        foreach ($terms as $term) {
            $output[$term->term_id] = $term->name;
        }

        return $output;
    }


    /**
     * Get a single post by numeric ID if type matches
     *
     * @since 1.2.0
     *
     * @param int    $id
     * @param string $type
     * @return array
     */
    public static function get_post_by_id_if_type(int $id, string $type): array
    {
        $post = get_post($id);

        if ($post && $post->post_type === $type && $post->post_status === 'publish') {
            return [
                    $post->ID => $post->post_title ?: sprintf(__('(no title) – ID: %d', 'addons-for-elementor-builder'), $post->ID),
            ];
        }

        return [];
    }

    /**
     * Retrieves the terms by Tax
     * 
     * @since 1.2.0
     *
     * @param string $tax
     * @param array $args
     * 
     * @return array
     */
    public static function get_terms_by_tax($tax, $args = []): array
    {

        $defaults = [
            'taxonomy' => $tax,
            'hide_empty' => false
        ];

        $parsed_args = wp_parse_args($args, $defaults);
        $terms = get_terms($parsed_args);
        $terms_output = (!empty($terms) &&
            !empty($parsed_args['def_item'])) ?
            self::new_array_items([], $parsed_args['def_item']) : [];

        foreach ($terms as $term)
            $terms_output[$term->term_id] = $term->name;

        return $terms_output;
    }

    /**
     * Check if string contains specific values
     * 
     * @since 1.0.0
     * 
     * @param string $haystack
     * @param array $search
     * 
     * @return int|null
     */
    public static function contains($haystack = '', $search = [])
    {
        if ($haystack) {
            foreach ((array) $search as $item) {
                if ($item && strpos((string) $haystack, (string) $item) !== false) return 1;
            }
        }

        return null;
    }

    /**
     * Limit words of string
     * 
     * @since 1.0.7
     * 
     * @param string $string
     * @param int $length
     * @param string $read_more
     * 
     * @return string
     */
    public static function limit_words($string = '', $length = 12, $read_more = null)
    {
        $read_more_link = self::get_string_between($string, '<a', '</a>', true);
        if (isset($read_more_link[0])):
            $read_more_link = $read_more_link[0];
            $string = str_replace($read_more_link, '', $string);
        endif;

        $count = count((array) preg_split('~[^\p{L}\p{N}\']+~u', $string)) - 1;
        $length--;

        if ($count > $length):
            $string = wp_strip_all_tags($string);
            $string = preg_replace('/((\w+\W*){' . $length . '}(\w+))(.*)/u', '${1}', $string) . ' ...';
        endif;

        if ($read_more) $string .= $read_more_link;
        return str_replace(['... ', 'Array'], '', $string);
    }

    /**
     * Get string between two string
     * 
     * @since 1.0.7
     * 
     * @param string $string
     * @param string $start
     * @param string $end
     * @param bool $match
     * 
     * @return string
     */
    public static function get_string_between($string = '', $start = '', $end = '', $match = false)
    {
        if ($string):
            if ($match):
                preg_match_all('~' . preg_quote($start, '~') . '(.*?)' . preg_quote($end, '~') . '~s', $string, $matches);
                return $matches[0];
            endif;

            $array = explode($start, $string);

            if (isset($array[1])):
                $array = explode($end, $array[1]);
                return $array[0];
            endif;
        endif;
    }

    /**
     * Returns email content wrapped in email template
     * 
     * @since 1.0.0
     * 
     * @param string $email_content
     */
    public static function email_content($email_content)
    {
        ob_start();
?>
        <div class="afeb-email-body" style="padding: 100px 0; background-color: #ebebeb;">
            <table class="afeb-email" border="0" cellpadding="0" cellspacing="0" style="width: 40%; margin: 0 auto; background: #fff; padding: 30px 30px 26px; border: 0.4px solid #d3d3d3; border-radius: 11px; font-family: 'Segoe UI', sans-serif; ">
                <tbody>
                    <tr>
                        <td colspan="2" style="text-align: left;">
                            <?php echo wp_kses_post($email_content); ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
<?php
        return wp_kses_post(ob_get_clean());
    }

    /**
     * Sending an email
     *
     * @since 1.0.0
     * 
     * @param string $email
     * @param string $subject
     * @param string $message
     * @param mixed  $header
     * @param array  $attachment
     */
    public static function send_email($email, $subject, $message, $header, $attachment)
    {
        $message = self::email_content($message);
        wp_mail($email, $subject, $message, $header, $attachment);
    }

    /**
     * Add a filter hook
     * 
     * @since 1.0.2
     * 
     * @param array $items
     * @param string $hook
     * @param object|array $args
     * 
     * @return array
     */
    public static function fhook($items = [], $hook = '', $args = [])
    {
        $output = [];
        $chars = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
        $num = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25];
        $rand_prx_key = [$chars[wp_rand($num[sqrt(225)], 15)], $chars[wp_rand($num[sqrt(441)], 21)]];
        $rand_sfx_key = [$chars[wp_rand($num[sqrt(225)], 15)], $chars[wp_rand($num[sqrt(289)], 17)], $chars[wp_rand($num[sqrt(196)], 14)]];
        $items = apply_filters($hook, $items, $args);
        $count = 0;
        foreach ($items as $key => $value) {
            if (strpos($key, sprintf('%s_', strtolower(implode($rand_prx_key)))) === false) $output[$key] = $value;
            else $output[$key] = $value . sprintf(' (%s) ', implode($rand_sfx_key));
            $count++;
        }
        return $output;
    }

    /**
     * Returns all navigation menu list
     * 
     * @since 1.3.0
     * 
     * @return array
     */
    public function get_nav_menus()
    {
        $menus = wp_get_nav_menus();
        $items = ['default' => esc_html__('Select Menu', 'addons-for-elementor-builder')];
        foreach ($menus as $menu) $items[$menu->slug] = $menu->name;

        return $items;
    }
    public function get_product($product_id) {
        if (function_exists('wc_get_product')) {
            return wc_get_product($product_id);
        }
        return null;
    }

    public function get_alignment_options(): array
    {
        $options = [
            'left' => ['title' => esc_html__('Left', 'addons-for-elementor-builder'), 'icon' => 'eicon-text-align-left'],
            'center' => ['title' => esc_html__('Center', 'addons-for-elementor-builder'), 'icon' => 'eicon-text-align-center'],
            'right' => ['title' => esc_html__('Right', 'addons-for-elementor-builder'), 'icon' => 'eicon-text-align-right']
        ];

        return is_rtl() ? array_reverse($options) : $options;
    }

}
