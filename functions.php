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

// === REGISTER CUSTOM POST TYPES ===
add_action('init', function() {
    // Sessions CPT
    register_post_type('av_session', array(
        'labels' => array(
            'name' => 'Sessions',
            'singular_name' => 'Session',
            'add_new_item' => 'Add New Session',
            'edit_item' => 'Edit Session',
            'view_item' => 'View Session'
        ),
        'public' => true,
        'show_in_menu' => true,
        'menu_icon' => 'dashicons-welcome-learn-more',
        'supports' => array('title', 'custom-fields'),
        'has_archive' => false,
        'show_in_rest' => true
    ));

    // Resources CPT
    register_post_type('av_resource', array(
        'labels' => array(
            'name' => 'AI Resources',
            'singular_name' => 'AI Resource',
            'add_new_item' => 'Add New Resource',
            'edit_item' => 'Edit Resource',
            'view_item' => 'View Resource'
        ),
        'public' => true,
        'show_in_menu' => true,
        'menu_icon' => 'dashicons-lightbulb',
        'supports' => array('title', 'custom-fields'),
        'has_archive' => false,
        'show_in_rest' => true
    ));

    // Topics Taxonomy (for filtering)
    register_taxonomy('av_topic', array('av_session', 'av_resource'), array(
        'labels' => array(
            'name' => 'Topics',
            'singular_name' => 'Topic',
            'search_items' => 'Search Topics',
            'all_items' => 'All Topics',
            'edit_item' => 'Edit Topic',
            'update_item' => 'Update Topic',
            'add_new_item' => 'Add New Topic'
        ),
        'hierarchical' => false,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'topic'),
        'show_in_rest' => true
    ));
});

// === PRE-POPULATE TOPICS (Runs once) ===
add_action('init', function() {
    // Check if topics already exist to avoid duplicates
    $existing_topics = get_terms(array('taxonomy' => 'av_topic', 'hide_empty' => false));

    if (empty($existing_topics)) {
        $topics = array(
            'Sales',
            'Marketing',
            'Product Management',
            'Customer Experience',
            'AI',
            'Research with AI'
        );

        foreach ($topics as $topic) {
            wp_insert_term($topic, 'av_topic');
        }
    }
}, 20);

// === HYBRID DATA FUNCTIONS ===
function get_all_sessions() {
    $sessions = array();

    // Get from WordPress
    $wp_sessions = get_posts(array(
        'post_type' => 'av_session',
        'posts_per_page' => -1,
        'orderby' => 'menu_order',
        'order' => 'ASC'
    ));

    foreach ($wp_sessions as $post) {
        // Get topics for this post
        $topics = wp_get_post_terms($post->ID, 'av_topic', array('fields' => 'names'));

        $sessions[] = array(
            'title' => $post->post_title,
            'institution' => get_field('institution', $post->ID) ?: 'Guest Session',
            'program' => get_field('program', $post->ID) ?: '',
            'url' => get_field('session_url', $post->ID) ?: '#',
            'tag' => get_field('tag', $post->ID) ?: 'Session',
            'desc' => get_field('description', $post->ID) ?: null,
            'topics' => !is_wp_error($topics) ? $topics : array()
        );
    }

    // Merge with JSON data
    $json_data = get_lab_data();
    if (!empty($json_data['courses'])) {
        foreach ($json_data['courses'] as $course) {
            // Ensure topics field exists (use 'tags' from JSON if available)
            if (!isset($course['topics'])) {
                $course['topics'] = isset($course['tags']) ? $course['tags'] : array();
            }
            $sessions[] = $course;
        }
    }

    return $sessions;
}

function get_all_resources() {
    $resources = array();

    // Get from WordPress
    $wp_resources = get_posts(array(
        'post_type' => 'av_resource',
        'posts_per_page' => -1,
        'orderby' => 'menu_order',
        'order' => 'ASC'
    ));

    foreach ($wp_resources as $post) {
        // Get topics for this post
        $topics = wp_get_post_terms($post->ID, 'av_topic', array('fields' => 'names'));

        $resources[] = array(
            'title' => $post->post_title,
            'type' => get_field('resource_type', $post->ID) ?: 'Gemini Gem',
            'desc' => get_field('description', $post->ID) ?: '',
            'link' => get_field('resource_link', $post->ID) ?: '#',
            'prompt_text' => get_field('prompt_text', $post->ID) ?: '',
            'topics' => !is_wp_error($topics) ? $topics : array()
        );
    }

    // Merge with JSON data
    $json_data = get_lab_data();
    if (!empty($json_data['resources'])) {
        foreach ($json_data['resources'] as $resource) {
            // Ensure topics field exists (use 'tags' from JSON if available)
            if (!isset($resource['topics'])) {
                $resource['topics'] = isset($resource['tags']) ? $resource['tags'] : array();
            }
            $resources[] = $resource;
        }
    }

    return $resources;
}

// === SHORTCODE: [course_grid] ===
add_shortcode('course_grid', function() {
    $sessions = get_all_sessions(); // Hybrid: WordPress + JSON
    if (empty($sessions)) return '<p>No sessions found.</p>';

    $output = '<div class="course-grid">';
    foreach ($sessions as $course) {
        // Build data-tags attribute for filtering
        $tags_attr = '';
        if (!empty($course['topics']) && is_array($course['topics'])) {
            $tags_attr = 'data-tags="' . esc_attr(implode(',', $course['topics'])) . '"';
        }

        $output .= '
        <div class="course-card filterable-card" ' . $tags_attr . '>
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
    $resources = get_all_resources(); // Hybrid: WordPress + JSON
    if (empty($resources)) return '<p>No resources found.</p>';

    $output = '<div class="course-grid">';
    $prompt_storage = ''; // Hidden divs to store long prompt texts

    foreach ($resources as $index => $res) {

        $icon = '💎';
        if ($res['type'] === 'Gemini Gem') { $icon = '💎'; }
        elseif ($res['type'] === 'Custom GPT') { $icon = '🤖'; }
        elseif (strpos($res['type'], 'Prompt') !== false) { $icon = '⚡'; }

        // Build data attributes for modal
        $data_attrs = '';
        if (!empty($res['prompt_text'])) {
            // Resource with prompt - store in hidden div and reference by ID
            $prompt_id = 'prompt-' . $index;
            $prompt_storage .= '<div id="' . $prompt_id . '" class="prompt-storage" style="display:none;">' . esc_html($res['prompt_text']) . '</div>';
            $data_attrs = 'data-modal="prompt" data-prompt-id="' . $prompt_id . '" data-title="' . esc_attr($res['title']) . '"';
        } else {
            // Gem/GPT - modal will show link
            $data_attrs = 'data-modal="gem" data-gem-link="' . esc_url($res['link']) . '" data-title="' . esc_attr($res['title']) . '"';
        }

        // Build data-tags attribute for filtering
        $tags_attr = '';
        if (!empty($res['topics']) && is_array($res['topics'])) {
            $tags_attr = 'data-tags="' . esc_attr(implode(',', $res['topics'])) . '"';
        }

        $output .= '
        <div class="course-card filterable-card" ' . $data_attrs . ' ' . $tags_attr . '>
            <span class="course-tag">' . $icon . ' ' . esc_html($res['type']) . '</span>
            <div class="course-link">' . esc_html($res['title']) . '</div>
            
            <p class="course-desc">' . esc_html($res['desc']) . '</p>
            
            <div class="course-footer">View Details →</div>
        </div>';
    }
    $output .= '</div>';
    $output .= $prompt_storage; // Add hidden prompt storage divs
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

// === MIGRATION SCRIPT (TEMPORARY - Remove after use) ===
// Usage: Visit yoursite.com/?migrate_json_data=migrate123 (as admin)
add_action('init', function() {
    if (isset($_GET['migrate_json_data']) && $_GET['migrate_json_data'] === 'migrate123' && current_user_can('manage_options')) {

        $results = array('sessions' => array(), 'resources' => array(), 'errors' => array());
        $json_data = get_lab_data();

        // Migrate Sessions
        if (!empty($json_data['courses'])) {
            foreach ($json_data['courses'] as $course) {
                $post_id = wp_insert_post(array(
                    'post_type' => 'av_session',
                    'post_title' => $course['title'],
                    'post_status' => 'publish'
                ));

                if (!is_wp_error($post_id)) {
                    update_field('institution', $course['institution'], $post_id);
                    update_field('program', $course['program'], $post_id);
                    update_field('session_url', $course['url'], $post_id);
                    update_field('tag', $course['tag'] ?? 'Session', $post_id);
                    update_field('description', $course['desc'], $post_id);
                    $results['sessions'][] = $course['title'];
                } else {
                    $results['errors'][] = 'Failed to create session: ' . $course['title'];
                }
            }
        }

        // Migrate Resources
        if (!empty($json_data['resources'])) {
            foreach ($json_data['resources'] as $resource) {
                $post_id = wp_insert_post(array(
                    'post_type' => 'av_resource',
                    'post_title' => $resource['title'],
                    'post_status' => 'publish'
                ));

                if (!is_wp_error($post_id)) {
                    update_field('resource_type', $resource['type'], $post_id);
                    update_field('description', $resource['desc'], $post_id);
                    update_field('resource_link', $resource['link'], $post_id);
                    update_field('prompt_text', $resource['prompt_text'], $post_id);
                    $results['resources'][] = $resource['title'];
                } else {
                    $results['errors'][] = 'Failed to create resource: ' . $resource['title'];
                }
            }
        }

        // Display results
        wp_die('<h1>Migration Complete!</h1>
            <h2>Sessions Created (' . count($results['sessions']) . '):</h2>
            <ul><li>' . implode('</li><li>', $results['sessions']) . '</li></ul>
            <h2>Resources Created (' . count($results['resources']) . '):</h2>
            <ul><li>' . implode('</li><li>', $results['resources']) . '</li></ul>
            ' . (!empty($results['errors']) ? '<h2>Errors:</h2><ul><li>' . implode('</li><li>', $results['errors']) . '</li></ul>' : '') . '
            <p><a href="' . admin_url() . '">Go to Dashboard</a></p>');
    }
});