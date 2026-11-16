<?php
/**
 * Plugin Name: DOM Reviews
 * Description: WhatsApp screenshot reviews with admin upload interface, star ratings, and grid display.
 * Version: 1.0.0
 * Author: De Outfitman
 * Text Domain: dom-reviews
 *
 * @package DomReviews
 */
defined('ABSPATH') || exit;

class DOM_Reviews {

    private static $instance = null;
    const POST_TYPE = 'dom_review';

    public static function instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('init', [$this, 'register_post_type']);
        add_action('add_meta_boxes', [$this, 'add_meta_boxes']);
        add_action('save_post_' . self::POST_TYPE, [$this, 'save_meta']);
        add_filter('dom_reviews_get', [$this, 'get_reviews'], 10, 2);
        add_shortcode('dom_reviews', [$this, 'shortcode']);
    }

    public function register_post_type() {
        register_post_type(self::POST_TYPE, [
            'labels' => [
                'name'          => __('Reviews', 'dom-reviews'),
                'singular_name' => __('Review', 'dom-reviews'),
                'add_new'       => __('Review toevoegen', 'dom-reviews'),
                'add_new_item'  => __('Nieuwe review toevoegen', 'dom-reviews'),
                'edit_item'     => __('Review bewerken', 'dom-reviews'),
            ],
            'public'       => false,
            'show_ui'      => true,
            'show_in_menu' => true,
            'menu_icon'    => 'dashicons-star-filled',
            'supports'     => ['title', 'thumbnail'],
            'menu_position' => 28,
        ]);
    }

    public function add_meta_boxes() {
        add_meta_box(
            'dom_review_details',
            __('Review Details', 'dom-reviews'),
            [$this, 'render_meta_box'],
            self::POST_TYPE,
            'normal',
            'high'
        );
    }

    public function render_meta_box($post) {
        wp_nonce_field('dom_review_save', 'dom_review_nonce');
        $rating = get_post_meta($post->ID, '_dom_review_rating', true) ?: 5;
        $name = get_post_meta($post->ID, '_dom_review_name', true);
        $text = get_post_meta($post->ID, '_dom_review_text', true);
        $product_id = get_post_meta($post->ID, '_dom_review_product', true);
        $featured = get_post_meta($post->ID, '_dom_review_featured', true);
        ?>
        <table class="form-table">
            <tr>
                <th><label for="dom_review_name">Naam klant</label></th>
                <td><input type="text" id="dom_review_name" name="dom_review_name" value="<?php echo esc_attr($name); ?>" class="regular-text" /></td>
            </tr>
            <tr>
                <th><label for="dom_review_rating">Sterren (1-5)</label></th>
                <td><input type="number" id="dom_review_rating" name="dom_review_rating" value="<?php echo esc_attr($rating); ?>" min="1" max="5" step="1" /></td>
            </tr>
            <tr>
                <th><label for="dom_review_text">Review tekst</label></th>
                <td><textarea id="dom_review_text" name="dom_review_text" rows="3" class="large-text"><?php echo esc_textarea($text); ?></textarea></td>
            </tr>
            <tr>
                <th><label for="dom_review_product">Product (optioneel)</label></th>
                <td>
                    <select id="dom_review_product" name="dom_review_product">
                        <option value="">— Geen product —</option>
                        <?php
                        $products = wc_get_products(['limit' => 100, 'status' => 'publish', 'orderby' => 'title', 'order' => 'ASC']);
                        foreach ($products as $product) {
                            printf(
                                '<option value="%d" %s>%s</option>',
                                $product->get_id(),
                                selected($product_id, $product->get_id(), false),
                                esc_html($product->get_name())
                            );
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="dom_review_featured">Uitgelicht op homepage</label></th>
                <td><input type="checkbox" id="dom_review_featured" name="dom_review_featured" value="1" <?php checked($featured, '1'); ?> /></td>
            </tr>
        </table>
        <p class="description">Upload de WhatsApp screenshot als uitgelichte afbeelding (Featured Image) hierboven.</p>
        <?php
    }

    public function save_meta($post_id) {
        if (!isset($_POST['dom_review_nonce']) || !wp_verify_nonce($_POST['dom_review_nonce'], 'dom_review_save')) return;
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (!current_user_can('edit_post', $post_id)) return;

        $fields = [
            'dom_review_name'    => '_dom_review_name',
            'dom_review_rating'  => '_dom_review_rating',
            'dom_review_text'    => '_dom_review_text',
            'dom_review_product' => '_dom_review_product',
        ];

        foreach ($fields as $input => $meta_key) {
            if (isset($_POST[$input])) {
                update_post_meta($post_id, $meta_key, sanitize_text_field($_POST[$input]));
            }
        }

        $featured = isset($_POST['dom_review_featured']) ? '1' : '0';
        update_post_meta($post_id, '_dom_review_featured', $featured);
    }

    public function get_reviews($reviews = [], $args = []) {
        $defaults = [
            'limit'      => 9,
            'featured'   => false,
            'product_id' => 0,
        ];
        $args = wp_parse_args($args, $defaults);

        $query_args = [
            'post_type'      => self::POST_TYPE,
            'posts_per_page' => $args['limit'],
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
            'meta_query'     => [],
        ];

        if ($args['featured']) {
            $query_args['meta_query'][] = [
                'key'   => '_dom_review_featured',
                'value' => '1',
            ];
        }

        if ($args['product_id']) {
            $query_args['meta_query'][] = [
                'key'   => '_dom_review_product',
                'value' => $args['product_id'],
            ];
        }

        $posts = get_posts($query_args);
        $result = [];

        foreach ($posts as $post) {
            $result[] = [
                'id'       => $post->ID,
                'name'     => get_post_meta($post->ID, '_dom_review_name', true),
                'rating'   => (int) get_post_meta($post->ID, '_dom_review_rating', true),
                'text'     => get_post_meta($post->ID, '_dom_review_text', true),
                'image'    => get_the_post_thumbnail_url($post->ID, 'medium_large'),
                'product'  => get_post_meta($post->ID, '_dom_review_product', true),
                'date'     => $post->post_date,
            ];
        }

        return $result;
    }

    public function shortcode($atts) {
        $atts = shortcode_atts([
            'limit'    => 6,
            'featured' => 'true',
            'columns'  => 3,
        ], $atts);

        $reviews = apply_filters('dom_reviews_get', [], [
            'limit'    => (int) $atts['limit'],
            'featured' => $atts['featured'] === 'true',
        ]);

        if (empty($reviews)) return '';

        ob_start();
        ?>
        <div class="grid grid-cols-2 md:grid-cols-<?php echo esc_attr($atts['columns']); ?> gap-4">
            <?php foreach ($reviews as $review) : ?>
            <div class="bg-surface rounded-md overflow-hidden">
                <?php if ($review['image']) : ?>
                <img src="<?php echo esc_url($review['image']); ?>" alt="Review van <?php echo esc_attr($review['name']); ?>" class="w-full aspect-[3/4] object-cover" loading="lazy" />
                <?php endif; ?>
                <div class="p-3">
                    <div class="flex gap-0.5 text-yellow-400 mb-1">
                        <?php for ($i = 0; $i < $review['rating']; $i++) : ?>
                        <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                        <?php endfor; ?>
                    </div>
                    <p class="text-xs font-medium"><?php echo esc_html($review['name']); ?></p>
                    <?php if ($review['text']) : ?>
                    <p class="text-2xs text-muted mt-0.5 line-clamp-2"><?php echo esc_html($review['text']); ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php
        return ob_get_clean();
    }
}

DOM_Reviews::instance();
