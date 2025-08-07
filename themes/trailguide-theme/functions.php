
<?php
/**
 * Theme functions
 * @package Trailguide
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define('TRAILGUIDE_VER', '0.1.1');
define('TRAILGUIDE_DIR', get_stylesheet_directory());
define('TRAILGUIDE_URI', get_stylesheet_directory_uri());

// Theme supports
add_action('after_setup_theme', function() {
  add_theme_support('title-tag');
  add_theme_support('post-thumbnails');
  add_theme_support('editor-styles');
  add_theme_support('align-wide');
  add_theme_support('wp-block-styles');
  add_theme_support('responsive-embeds');
  register_nav_menus([
    'primary' => __('Primary Menu', 'trailguide'),
    'footer'  => __('Footer Menu', 'trailguide'),
  ]);
});

// Enqueue assets
add_action('wp_enqueue_scripts', function() {
  wp_enqueue_style('trailguide', trailguide_asset('assets/css/theme.css'), [], TRAILGUIDE_VER);
  // Leaflet for maps
  wp_enqueue_style('leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css', [], '1.9.4');
  wp_enqueue_script('leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', [], '1.9.4', true);
  wp_enqueue_script('trailguide', trailguide_asset('assets/js/main.js'), ['leaflet'], TRAILGUIDE_VER, true);
  wp_localize_script('trailguide', 'trailguideConfig', [
    'restUrl' => esc_url_raw( rest_url() ),
    'nonce'   => wp_create_nonce('wp_rest'),
  ]);
});

function trailguide_asset($path) {
  return trailguide_is_dev() ? add_query_arg('t', time(), trailguide_url($path)) : trailguide_url($path);
}

function trailguide_url($path) {
  return trailguide_is_child() ? get_stylesheet_directory_uri() . '/' . ltrim($path,'/') : get_template_directory_uri() . '/' . ltrim($path,'/');
}

function trailguide_is_child() {
  return get_stylesheet_directory() !== get_template_directory();
}

function trailguide_is_dev() {
  return defined('WP_DEBUG') && WP_DEBUG;
}

// Register Custom Post Type: Guide
add_action('init', function() {
  $labels = [
    'name' => __('Guides', 'trailguide'),
    'singular_name' => __('Guide', 'trailguide'),
  ];
  $args = [
    'label' => __('Guide', 'trailguide'),
    'labels' => $labels,
    'public' => true,
    'menu_icon' => 'dashicons-location-alt',
    'supports' => ['title','editor','excerpt','thumbnail','custom-fields','revisions'],
    'has_archive' => true,
    'rewrite' => ['slug' => 'guides'],
    'show_in_rest' => true,
  ];
  register_post_type('guide', $args);

  // Taxonomies
  register_taxonomy('country', ['guide'], [
    'label' => __('Country', 'trailguide'),
    'hierarchical' => true,
    'show_in_rest' => true,
    'rewrite' => ['slug' => 'country'],
  ]);
  register_taxonomy('city', ['guide'], [
    'label' => __('City', 'trailguide'),
    'hierarchical' => false,
    'show_in_rest' => true,
    'rewrite' => ['slug' => 'city'],
  ]);
  register_taxonomy('theme', ['guide'], [
    'label' => __('Theme', 'trailguide'),
    'hierarchical' => false,
    'show_in_rest' => true,
    'rewrite' => ['slug' => 'travel-theme'],
  ]);
});

// Shortcode: [tg_map lat="..." lng="..." zoom="12" height="400px"]
add_shortcode('tg_map', function($atts) {
  $a = shortcode_atts([
    'lat' => '0',
    'lng' => '0',
    'zoom'=> '12',
    'height' => '380px'
  ], $atts, 'tg_map');
  ob_start(); ?>
  <div class="tg-map" style="height:<?php echo esc_attr($a['height']); ?>"
       data-lat="<?php echo esc_attr($a['lat']); ?>"
       data-lng="<?php echo esc_attr($a['lng']); ?>"
       data-zoom="<?php echo esc_attr($a['zoom']); ?>"></div>
  <?php return ob_get_clean();
});

// JSON-LD for Guides
add_action('wp_head', function() {
  if (is_singular('guide')) {
    $data = [
      "@context" => "https://schema.org",
      "@type" => "TravelGuide",
      "name" => get_the_title(),
      "headline" => get_the_title(),
      "description" => wp_strip_all_tags(get_the_excerpt() ?: get_bloginfo('description')),
      "image" => get_the_post_thumbnail_url(get_the_ID(),'full'),
      "url" => get_permalink(),
      "datePublished" => get_the_date('c'),
      "dateModified" => get_the_modified_date('c'),
      "author" => [
        "@type" => "Person",
        "name" => get_the_author()
      ]
    ];
    echo '<script type="application/ld+json">'.wp_json_encode($data).'</script>';
  }
}, 5);
