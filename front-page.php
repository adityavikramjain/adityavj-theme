<?php
/**
 * Template Name: Electric Lab Homepage
 */
get_header(); ?>

<style>
    .terminal-header { font-family: 'Space Grotesk', 'Courier New', monospace; font-weight: 700; font-size: 1.1rem; color: #1F2937; margin-bottom: 20px; display: flex; align-items: center; opacity: 0.9; }
    .terminal-text { letter-spacing: -0.02em; }
    .cursor { display: inline-block; width: 10px; height: 1.2em; background-color: #FF4500; margin-left: 6px; animation: blinkCursor 1s infinite; }
    @keyframes blinkCursor { 0% { opacity: 1; } 50% { opacity: 0; } 100% { opacity: 1; } }
</style>

<div class="lab-container">

    <div class="terminal-header">
        <span class="terminal-text">&gt; adityavj.com</span><span class="cursor">_</span>
    </div>

    <div class="lab-hero">
        <div class="hero-text">
            <h1 class="hero-name">Aditya V Jain</h1>
            <div class="hero-tagline">
                Product Lead at Google. Prev at Snapdeal, Amazon, Shaadi.com. IIM-K Visiting Faculty.
                <strong>Arming Business Leaders with Practical AI Strategy.</strong>
            </div>
            <div class="btn-group">
                <a href="#book" class="btn btn-primary">Book Session</a>
                <a href="#resources" class="btn btn-outline">Explore AI Tools</a>
            </div>
            <div class="social-row">
                <a href="https://www.linkedin.com/in/aditya-jain-8895181/" target="_blank" class="social-link">LinkedIn</a>
                <a href="https://github.com/adityavikramjain" target="_blank" class="social-link">GitHub</a>
                <a href="mailto:aditya1384@gmail.com" class="social-link">Email</a>
            </div>
        </div>
        <div class="hero-visual">
            <div class="flux-blob"></div>
        </div>
    </div>

    <div class="lab-section-header">
        <h2 class="lab-title">Academic Sessions</h2>
    </div>
    <?php echo do_shortcode('[course_grid]'); ?>

    <div id="resources" class="lab-section-header">
        <h2 class="lab-title">The AI Lab</h2>
    </div>
    <?php echo do_shortcode('[resource_grid]'); ?>

    <div class="lab-section-header">
        <h2 class="lab-title">Build Notes & Writings</h2>
    </div>
    <?php echo do_shortcode('[writing_grid]'); ?>

    <div id="book" class="booking-bar">
        <h3>Ready to Accelerate?</h3>
        <p>Book a 1:1 mentorship session or schedule a custom workshop for your team. Let's transform your AI strategy into competitive advantage.</p>
        <a href="https://calendly.com/adityavj" target="_blank" class="btn btn-primary">Check Availability</a>
    </div>

</div>

<?php get_footer(); ?>
