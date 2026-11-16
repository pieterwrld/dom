<?php
/**
 * Plugin Name: DOM Delivery Estimate
 * Description: Dynamic delivery date calculation. Respects weekends, cutoff time (17:00 CET), and Dutch holidays.
 * Version: 1.0.0
 * Author: De Outfitman
 * Requires Plugins: woocommerce
 * Text Domain: dom-delivery-estimate
 *
 * @package DomDeliveryEstimate
 */
defined('ABSPATH') || exit;

class DOM_Delivery_Estimate {

    private static $instance = null;
    private $cutoff_hour = 17;
    private $timezone = 'Europe/Amsterdam';

    public static function instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_filter('dom_delivery_estimate', [$this, 'calculate']);
        add_shortcode('dom_delivery', [$this, 'shortcode']);
    }

    public function calculate() {
        $tz = new DateTimeZone($this->timezone);
        $now = new DateTime('now', $tz);
        $delivery = clone $now;

        $is_before_cutoff = (int) $now->format('H') < $this->cutoff_hour;
        $is_weekday = !in_array($now->format('N'), ['6', '7']);

        if ($is_before_cutoff && $is_weekday) {
            $delivery->modify('+1 weekday');
        } else {
            $delivery->modify('+2 weekday');
        }

        // Skip Dutch holidays
        $holidays = $this->get_dutch_holidays((int) $delivery->format('Y'));
        $attempts = 0;
        while (in_array($delivery->format('Y-m-d'), $holidays) && $attempts < 10) {
            $delivery->modify('+1 weekday');
            $attempts++;
        }

        return [
            'date'      => $delivery,
            'formatted' => $this->format_dutch($delivery),
            'cutoff'    => $this->cutoff_hour,
        ];
    }

    private function format_dutch(DateTime $date) {
        $days = [1 => 'maandag', 2 => 'dinsdag', 3 => 'woensdag', 4 => 'donderdag', 5 => 'vrijdag', 6 => 'zaterdag', 7 => 'zondag'];
        $months = [1 => 'januari', 2 => 'februari', 3 => 'maart', 4 => 'april', 5 => 'mei', 6 => 'juni', 7 => 'juli', 8 => 'augustus', 9 => 'september', 10 => 'oktober', 11 => 'november', 12 => 'december'];

        return sprintf(
            '%s %s %s',
            $days[(int) $date->format('N')],
            $date->format('j'),
            $months[(int) $date->format('n')]
        );
    }

    private function get_dutch_holidays(int $year): array {
        $easter = new DateTime('@' . easter_date($year));
        $easter->setTimezone(new DateTimeZone($this->timezone));

        $holidays = [
            sprintf('%d-01-01', $year),
            sprintf('%d-04-27', $year), // Koningsdag
            sprintf('%d-05-05', $year), // Bevrijdingsdag
            sprintf('%d-12-25', $year), // Kerst 1
            sprintf('%d-12-26', $year), // Kerst 2
        ];

        // Easter-based
        $good_friday = (clone $easter)->modify('-2 days');
        $easter_monday = (clone $easter)->modify('+1 day');
        $ascension = (clone $easter)->modify('+39 days');
        $whit_monday = (clone $easter)->modify('+50 days');

        $holidays[] = $good_friday->format('Y-m-d');
        $holidays[] = $easter->format('Y-m-d');
        $holidays[] = $easter_monday->format('Y-m-d');
        $holidays[] = $ascension->format('Y-m-d');
        $holidays[] = $whit_monday->format('Y-m-d');

        return $holidays;
    }

    public function shortcode() {
        $est = $this->calculate();
        return sprintf(
            'Voor %d:00 besteld, <strong>%s</strong> in huis',
            $est['cutoff'],
            esc_html($est['formatted'])
        );
    }
}

DOM_Delivery_Estimate::instance();
