<?php
/**
 * Plugin Name: DOM Announcement Bar
 * Description: Marquee ticker with multiple messages, admin-editable via options page. Silver dot separators.
 * Version: 1.0.0
 * Author: De Outfitman
 * Text Domain: dom-announcement-bar
 *
 * @package DomAnnouncementBar
 */
defined('ABSPATH') || exit;

class DOM_Announcement_Bar {

    private static $instance = null;
    const OPTION_KEY = 'dom_announcements';

    public static function instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('admin_menu', [$this, 'add_admin_page']);
        add_action('admin_init', [$this, 'register_settings']);
        add_filter('dom_announcements', [$this, 'get_announcements']);
    }

    public function add_admin_page() {
        add_submenu_page(
            'themes.php',
            __('Announcement Bar', 'dom-announcement-bar'),
            __('Announcement Bar', 'dom-announcement-bar'),
            'manage_options',
            'dom-announcements',
            [$this, 'render_admin_page']
        );
    }

    public function register_settings() {
        register_setting('dom_announcements_group', self::OPTION_KEY, [
            'sanitize_callback' => [$this, 'sanitize_announcements'],
        ]);
    }

    public function sanitize_announcements($input) {
        if (!is_array($input)) return [];

        $clean = [];
        foreach ($input as $item) {
            $text = sanitize_text_field($item['text'] ?? '');
            if (!empty($text)) {
                $clean[] = [
                    'text'   => $text,
                    'link'   => esc_url_raw($item['link'] ?? ''),
                    'active' => !empty($item['active']),
                ];
            }
        }
        return $clean;
    }

    public function render_admin_page() {
        $announcements = get_option(self::OPTION_KEY, $this->get_defaults());
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Announcement Bar Berichten', 'dom-announcement-bar'); ?></h1>
            <form method="post" action="options.php">
                <?php settings_fields('dom_announcements_group'); ?>
                <table class="widefat fixed striped" id="dom-announcements-table">
                    <thead>
                        <tr>
                            <th style="width:50px">Actief</th>
                            <th>Bericht</th>
                            <th style="width:250px">Link (optioneel)</th>
                            <th style="width:50px"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($announcements as $i => $a) : ?>
                        <tr>
                            <td><input type="checkbox" name="<?php echo esc_attr(self::OPTION_KEY); ?>[<?php echo $i; ?>][active]" value="1" <?php checked($a['active'] ?? true); ?> /></td>
                            <td><input type="text" name="<?php echo esc_attr(self::OPTION_KEY); ?>[<?php echo $i; ?>][text]" value="<?php echo esc_attr($a['text']); ?>" class="regular-text" style="width:100%" /></td>
                            <td><input type="url" name="<?php echo esc_attr(self::OPTION_KEY); ?>[<?php echo $i; ?>][link]" value="<?php echo esc_attr($a['link'] ?? ''); ?>" class="regular-text" style="width:100%" /></td>
                            <td><button type="button" class="button" onclick="this.closest('tr').remove()">×</button></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <p>
                    <button type="button" class="button" id="dom-add-announcement">+ Bericht toevoegen</button>
                </p>
                <?php submit_button(__('Opslaan', 'dom-announcement-bar')); ?>
            </form>
        </div>
        <script>
        document.getElementById('dom-add-announcement').addEventListener('click', function() {
            const tbody = document.querySelector('#dom-announcements-table tbody');
            const idx = tbody.children.length;
            const key = '<?php echo esc_js(self::OPTION_KEY); ?>';
            tbody.insertAdjacentHTML('beforeend',
                '<tr>' +
                '<td><input type="checkbox" name="' + key + '[' + idx + '][active]" value="1" checked /></td>' +
                '<td><input type="text" name="' + key + '[' + idx + '][text]" value="" class="regular-text" style="width:100%" /></td>' +
                '<td><input type="url" name="' + key + '[' + idx + '][link]" value="" class="regular-text" style="width:100%" /></td>' +
                '<td><button type="button" class="button" onclick="this.closest(\'tr\').remove()">×</button></td>' +
                '</tr>'
            );
        });
        </script>
        <?php
    }

    private function get_defaults() {
        return [
            ['text' => 'BLACK FRIDAY — Tot 50% korting op alles', 'link' => '/shop/', 'active' => true],
            ['text' => 'Gratis verzending vanaf €220', 'link' => '', 'active' => true],
            ['text' => '14 dagen gratis retourneren', 'link' => '', 'active' => true],
            ['text' => '100% originele merkkleding', 'link' => '', 'active' => true],
            ['text' => 'Hulp nodig? WhatsApp ons!', 'link' => 'https://wa.me/31612345678', 'active' => true],
        ];
    }

    public function get_announcements($announcements = []) {
        $stored = get_option(self::OPTION_KEY, $this->get_defaults());
        return array_filter($stored, fn($a) => !empty($a['active']));
    }
}

DOM_Announcement_Bar::instance();
