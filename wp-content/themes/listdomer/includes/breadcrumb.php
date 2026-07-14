<?php

class LSDR_Breadcrumb extends LSDR_Base
{
    /**
     * Hook breadcrumb rendering into the theme.
     */
    public function init()
    {
        add_action('lsdr_breadcrumb', [$this, 'breadcrumb']);
    }

    /**
     * Render the breadcrumb HTML.
     */

    public function breadcrumb(): void
    {
        if (is_front_page()) return;

        $items = $this->get_items();
        $delimiter = '<li class="delimiter"><i class="fas fa-chevron-right"></i></li>';

        echo '<div class="lsdr-breadcrumb" aria-label="' . esc_html__('Breadcrumb', 'listdomer') . '">';
        echo '<ul>';

        foreach ($items as $index => $item)
        {
            echo '<li>';
            echo !empty($item['url'])
                ? '<a href="' . esc_url($item['url']) . '">' . esc_html($item['label']) . '</a>'
                : '<span>' . esc_html($item['label']) . '</span>';
            echo '</li>';

            if ($index < count($items) - 1) echo $delimiter;
        }

        echo '</ul>';
        echo '</div>';
    }


    /**
     * Build an array of breadcrumb items based on the current page context.
     *
     * @return array
     */
    protected function get_items(): array
    {
        if (is_front_page()) return [];

        $items = [$this->make_item('Home', home_url('/'))];

        switch (true)
        {
            case (is_home() && !is_front_page()):
                return array_merge($items, $this->get_blog_home());

            case (is_single() && get_post_type() === 'post'):
                return array_merge($items, $this->get_single_post());

            case is_category():
                return array_merge($items, $this->get_term_hierarchy('category'));

            case is_tag():
                return array_merge($items, $this->get_term_hierarchy('post_tag'));

            case is_tax():
                $term = get_queried_object();
                if ($term instanceof WP_Term)
                {
                    return array_merge($items, $this->get_term_hierarchy($term->taxonomy, $term));
                }
                break;
            case is_page():
                return array_merge($items, $this->get_page_hierarchy());

            case is_search():
                $items[] = $this->make_item('Search results for "' . get_search_query() . '"');
                break;

            case is_author():
                $author = get_queried_object();
                $items[] = $this->make_item('' . $author->display_name);
                break;

            case is_404():
                $items[] = $this->make_item('404 Not Found');
                break;
        }

        return $items;
    }

    /**
     * Get breadcrumb for the blog home (posts page).
     *
     * @return array
     */
    protected function get_blog_home(): array
    {
        $page_id = get_option('page_for_posts');
        return [$this->make_item(get_the_title($page_id))];
    }

    /**
     * Get breadcrumb items for a single blog post.
     *
     * @return array
     */
    protected function get_single_post(): array
    {
        $items = [];
        $blog_page_id = get_option('page_for_posts');

        if ($blog_page_id)
        {
            $items[] = $this->make_item(get_the_title($blog_page_id), get_permalink($blog_page_id));
        }

        $categories = get_the_category();
        if (!empty($categories))
        {
            $main_category = $categories[0];
            $items = array_merge($items, $this->get_term_hierarchy('category', $main_category, true));
        }

        $items[] = $this->make_item(get_the_title());
        return $items;
    }

    /**
     * Get breadcrumb hierarchy for a taxonomy term (e.g. categories).
     *
     * @param string $taxonomy
     * @param null $term
     * @param bool $link_current
     * @return array
     */
    protected function get_term_hierarchy(string $taxonomy, $term = null, bool $link_current = false): array
    {
        $items = [];
        $term = $term ?: get_queried_object();

        if ($term instanceof WP_Term)
        {
            $ancestors = array_reverse(get_ancestors($term->term_id, $taxonomy));

            foreach ($ancestors as $ancestor_id)
            {
                $ancestor = get_term($ancestor_id, $taxonomy);
                if ($ancestor && !is_wp_error($ancestor))
                {
                    $items[] = $this->make_item($ancestor->name, get_term_link($ancestor));
                }
            }

            // Link current term only if specified
            $items[] = $link_current
                ? $this->make_item($term->name, get_term_link($term))
                : $this->make_item($term->name);
        }

        return $items;
    }

    /**
     * Get breadcrumb hierarchy for parent pages.
     *
     * @return array
     */
    protected function get_page_hierarchy(): array
    {
        global $post;
        $parents = [];

        $parent_id = $post->post_parent;
        while ($parent_id)
        {
            $page = get_post($parent_id);
            if ($page)
            {
                $parents[] = $this->make_item(get_the_title($page), get_permalink($page));
                $parent_id = $page->post_parent;
            }
            else
            {
                break;
            }
        }

        $items = array_reverse($parents);
        $items[] = $this->make_item(get_the_title());
        return $items;
    }

    /**
     * Create a breadcrumb item array.
     *
     * @param string $label
     * @param string $url (optional)
     * @return array
     */
    protected function make_item(string $label, string $url = ''): array
    {
        return !empty($url)
            ? ['label' => $label, 'url' => $url]
            : ['label' => $label];
    }
}
