
<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_action('init', function(){
  register_block_type( __DIR__, [
    'render_callback' => 'trailguide_ai_qa_render'
  ]);
});

function trailguide_ai_qa_render($attrs, $content){
  if (!is_singular()) return '';
  global $post;
  $ctx = wp_strip_all_tags( get_the_excerpt($post) . ' ' . wp_trim_words( wp_strip_all_tags( get_the_content(null, false, $post) ), 300 ) );
  ob_start(); ?>
  <div class="tg-ai-qa">
    <form class="tg-ai-form" onsubmit="return window.trailguideAsk(this)">
      <input type="text" name="q" placeholder="<?php echo esc_attr__('Ask a question about this destination...', 'trailguide'); ?>" required />
      <button type="submit"><?php echo esc_html__('Ask', 'trailguide'); ?></button>
    </form>
    <div class="tg-ai-answer" aria-live="polite"></div>
    <script>
      window.trailguideAsk = async function(form){
        const q = form.q.value.trim();
        if(!q) return false;
        const wrap = form.closest('.tg-ai-qa');
        const out = wrap.querySelector('.tg-ai-answer');
        out.textContent = 'Thinking…';
        try {
          const res = await fetch('<?php echo esc_url( rest_url('trailguide/v1/ai') ); ?>', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-WP-Nonce': '<?php echo esc_js( wp_create_nonce('wp_rest') ); ?>'
            },
            body: JSON.stringify({ prompt: q, context: <?php echo wp_json_encode($ctx); ?> })
          });
          const data = await res.json();
          if(!res.ok) throw new Error((data && data.message) || 'Error');
          out.textContent = data.answer || 'No answer.';
        } catch(e){
          out.textContent = 'Error: ' + e.message;
        }
        return false;
      }
    </script>
  </div>
  <?php
  return ob_get_clean();
}
