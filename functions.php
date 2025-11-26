<?php
// === CRITICAL: REMOVE ASTRA HEADER COMPLETELY ===
add_action( 'wp', function() {
    if ( is_front_page() ) {
        // Disable all Astra header components
        add_filter( 'astra_header_display', '__return_false' );
        add_filter( 'astra_main_header_display', '__return_false' );
        remove_action( 'astra_header', 'astra_header_markup' );
        
        // Remove title from appearing
        add_filter( 'astra_the_title_enabled', '__return_false' );
    }
}, 1 );

// Force hide header with CSS backup
add_action( 'wp_head', function() {
    if ( is_front_page() ) {
        echo '<style>.site-header, header, #masthead { display: none !important; }</style>';
    }
});

// === ENQUEUE STYLES & FONTS ===
add_action( 'wp_enqueue_scripts', function() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array('parent-style'), '2.1.0' );
    wp_enqueue_style( 'google-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Space+Grotesk:wght@500;700;900&display=swap', false );
} );

// === HELPER: GET DATA FROM JSON ===
function get_lab_data() {
    $json_file = get_stylesheet_directory() . '/data.json';
    if (!file_exists($json_file)) return [];
    return json_decode(file_get_contents($json_file), true);
}

// === SHORTCODE: [course_grid] ===
add_shortcode('course_grid', function() {
    $data = get_lab_data();
    if (empty($data['courses'])) return '<p>No courses found.</p>';

    $output = '<div class="course-grid">';
    foreach ($data['courses'] as $course) {
        $output .= '
        <div class="course-card">
            <span class="course-tag">SESSION</span>
            <a href="' . esc_url($course['url']) . '" target="_blank" class="course-link">' . esc_html($course['title']) . '</a>
            <p class="course-meta">' . esc_html($course['program']) . ' • ' . esc_html($course['institution']) . '</p>
            <div class="course-footer">Open Deck →</div>
        </div>';
    }
    $output .= '</div>';
    return $output;
});

// === SHORTCODE: [resource_grid] ===
add_shortcode('resource_grid', function() {
    $data = get_lab_data();
    if (empty($data['resources'])) return '<p>No resources found.</p>';

    $output = '<div class="course-grid">';
    foreach ($data['resources'] as $res) {

        $icon = '💎';
        if ($res['type'] === 'Gemini Gem') { $icon = '💎'; }
        elseif ($res['type'] === 'Custom GPT') { $icon = '🤖'; }
        elseif (strpos($res['type'], 'Prompt') !== false) { $icon = '⚡'; }

        // Build data attributes for modal
        $data_attrs = '';
        if (!empty($res['prompt_text'])) {
            // Resource with prompt - modal will show prompt text
            $escaped_prompt = htmlspecialchars($res['prompt_text'], ENT_QUOTES, 'UTF-8');
            $data_attrs = 'data-modal="prompt" data-prompt-text="' . $escaped_prompt . '" data-title="' . esc_attr($res['title']) . '"';
        } else {
            // Gem/GPT - modal will show link
            $data_attrs = 'data-modal="gem" data-gem-link="' . esc_url($res['link']) . '" data-title="' . esc_attr($res['title']) . '"';
        }

        $output .= '
        <div class="course-card" ' . $data_attrs . '>
            <span class="course-tag">' . $icon . ' ' . esc_html($res['type']) . '</span>
            <div class="course-link">' . esc_html($res['title']) . '</div>
            <div class="course-footer">View Details →</div>
        </div>';
    }
    $output .= '</div>';
    return $output;
});

// === SHORTCODE: [writing_grid] ===
add_shortcode('writing_grid', function() {
    $args = array('post_type' => 'post', 'posts_per_page' => 3);
    $query = new WP_Query($args);
    if (!$query->have_posts()) return '<p>No posts found.</p>';
    
    $output = '<div class="course-grid">';
    while ($query->have_posts()) {
        $query->the_post();
        $output .= '
        <div class="course-card">
            <span class="course-tag">' . get_the_date() . '</span>
            <a href="' . get_permalink() . '" class="course-link">' . get_the_title() . '</a>
            <p class="course-desc">' . get_the_excerpt() . '</p>
            <div class="course-footer">Read Article →</div>
        </div>';
    }
    $output .= '</div>';
    wp_reset_postdata();
    return $output;
});