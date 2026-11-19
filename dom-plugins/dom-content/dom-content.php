<?php
/**
 * Plugin Name: De Outfitman Content
 * Description: Eén beheerscherm voor alle teksten, foto's en video's van de winkel: homepage, categorieën, productpagina, winkelwagen, reviews en Gedragen door.
 * Version:     1.0.0
 * Author:      De Outfitman
 * Text Domain: dom-content
 *
 * Het thema blijft zonder deze plugin gewoon werken. Elk veld heeft in het
 * schema een standaardwaarde die gelijk is aan de tekst die eerder in de
 * sjablonen stond, dus zolang er niets is ingevuld verandert er niets op de
 * site. Wordt de plugin uitgezet, dan vallen de sjablonen terug op diezelfde
 * teksten die ze zelf meegeven.
 */

defined('ABSPATH') || exit;

define('DOM_CONTENT_VERSION', '1.0.0');
define('DOM_CONTENT_DIR', plugin_dir_path(__FILE__));
define('DOM_CONTENT_URL', plugin_dir_url(__FILE__));

/** De optie waarin alles staat. Eén rij in de database, genest per tabblad. */
define('DOM_CONTENT_OPTION', 'dom_content');

require_once DOM_CONTENT_DIR . 'inc/schema.php';
require_once DOM_CONTENT_DIR . 'inc/store.php';
require_once DOM_CONTENT_DIR . 'inc/fields.php';
require_once DOM_CONTENT_DIR . 'inc/admin.php';
require_once DOM_CONTENT_DIR . 'inc/categories.php';
require_once DOM_CONTENT_DIR . 'inc/black-friday.php';
require_once DOM_CONTENT_DIR . 'inc/black-friday-scherm.php';
require_once DOM_CONTENT_DIR . 'inc/frontend.php';
require_once DOM_CONTENT_DIR . 'inc/install.php';
