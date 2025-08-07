
<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_action('rest_api_init', function(){
  register_rest_route('trailguide/v1', '/ai', [
    'methods'  => 'POST',
    'permission_callback' => function(){ return current_user_can('edit_posts') || wp_doing_ajax(); },
    'callback' => 'trailguide_ai_rest_handler',
    'args' => [
      'prompt' => ['required' => true],
      'context' => ['required' => false]
    ]
  ]);
});

function trailguide_ai_rest_handler( WP_REST_Request $req ){
  $prompt  = sanitize_text_field( $req->get_param('prompt') );
  $context = wp_kses_post( $req->get_param('context') );

  $api_key = get_option('trailguide_ai_openai_api_key');
  if ( empty($api_key) ) {
    return new WP_Error('no_key', 'Missing OpenAI API key in settings', [ 'status' => 400 ]);
  }

  // Compose a simple system prompt tailored for travel Q&A.
  $messages = [
    ['role' => 'system', 'content' => 'You are a helpful travel assistant. Be concise, practical, and avoid hallucinations.'],
    ['role' => 'user', 'content' => "Context:\n" . wp_strip_all_tags($context) . "\n\nQuestion:\n" . $prompt]
  ];

  $body = [
    'model' => 'gpt-4o-mini',
    'messages' => $messages,
    'temperature' => 0.2
  ];

  $res = wp_remote_post('https://api.openai.com/v1/chat/completions', [
    'headers' => [
      'Authorization' => 'Bearer ' . $api_key,
      'Content-Type'  => 'application/json'
    ],
    'body' => wp_json_encode($body),
    'timeout' => 30
  ]);

  if ( is_wp_error($res) ) {
    return new WP_Error('openai_error', $res->get_error_message(), [ 'status' => 500 ]);
  }

  $code = wp_remote_retrieve_response_code($res);
  $json = json_decode( wp_remote_retrieve_body($res), true );

  if ( $code >= 400 ) {
    return new WP_Error('openai_http', 'OpenAI error', [ 'status' => $code, 'body' => $json ]);
  }

  $answer = $json['choices'][0]['message']['content'] ?? '';
  return [ 'answer' => $answer ];
}
