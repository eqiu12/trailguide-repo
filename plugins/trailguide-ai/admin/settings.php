
<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_action('admin_menu', function(){
  add_options_page(
    'Trailguide AI',
    'Trailguide AI',
    'manage_options',
    'trailguide-ai',
    'trailguide_ai_settings_page'
  );
});

add_action('admin_init', function(){
  register_setting('trailguide_ai', 'trailguide_ai_openai_api_key');
});

function trailguide_ai_settings_page(){
  ?>
  <div class="wrap">
    <h1>Trailguide AI Settings</h1>
    <form method="post" action="options.php">
      <?php settings_fields('trailguide_ai'); do_settings_sections('trailguide_ai'); ?>
      <table class="form-table">
        <tr>
          <th scope="row"><label for="trailguide_ai_openai_api_key">OpenAI API Key</label></th>
          <td><input type="password" id="trailguide_ai_openai_api_key" name="trailguide_ai_openai_api_key" value="<?php echo esc_attr(get_option('trailguide_ai_openai_api_key','')); ?>" class="regular-text" /></td>
        </tr>
      </table>
      <?php submit_button(); ?>
    </form>
    <p>Store your API key securely. The plugin will use server-side requests.</p>
  </div>
  <?php
}
