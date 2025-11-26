<?php
// 1. Enqueue Styles
add_action( 'wp_enqueue_scripts', function() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array('parent-style') );
} );

// 2. Helper to Get Data from JSON
function get_lab_data() {
    $json_file = get_stylesheet_directory() . '/data.json';
    if (!file_exists($json_file)) return [];
    return json_decode(file_get_contents($json_file), true);
}

// 3. Shortcode: [course_grid]
add_shortcode('course_grid', function() {
    $data = get_lab_data();
    if (empty($data['courses'])) return '<p>No courses found.</p>';

    $output = '<div class="course-grid">';
    foreach ($data['courses'] as $course) {
        $output .= '
        <div class="course-card">
            <span class="course-tag">' . esc_html($course['tag']) . '</span>
            <a href="' . esc_url($course['url']) . '" target="_blank" class="course-link">' . esc_html($course['title']) . '</a>
            <p class="course-desc">' . esc_html($course['desc']) . '</p>
            <div class="course-footer">View Slides →</div>
        </div>';
    }
    $output .= '</div>';
    return $output;
});

// 4. Shortcode: [resource_grid]
add_shortcode('resource_grid', function() {
    $data = get_lab_data();
    if (empty($data['resources'])) return '<p>No resources found.</p>';

    $output = '<div class="course-grid">';
    foreach ($data['resources'] as $res) {
        $output .= '
        <div class="course-card">
            <span class="course-tag">' . esc_html($res['type']) . '</span>
            <a href="' . esc_url($res['link']) . '" target="_blank" class="course-link">' . esc_html($res['title']) . '</a>
            <p class="course-desc">' . esc_html($res['desc']) . '</p>
            <div class="course-footer">Try Now →</div>
        </div>';
    }
    $output .= '</div>';
    return $output;
});

// 5. Shortcode: [writing_grid] - Pulls actual WP Posts
add_shortcode('writing_grid', function() {
    $args = array('post_type' => 'post', 'posts_per_page' => 3);
    $query = new WP_Query($args);
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