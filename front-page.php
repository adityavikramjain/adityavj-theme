<?php
/**
 * Template Name: Electric Lab Homepage
 */
get_header(); ?>

<div class="lab-container">

    <div class="terminal-header">
        <span class="terminal-text"><span class="terminal-prompt">&gt;</span> adityavj.com</span><span class="cursor"></span>
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

    <!-- Modal Overlay -->
    <div id="modal-overlay" class="modal-overlay">
        <div id="modal-container" class="modal-container">
            <div class="modal-header">
                <h3 id="modal-title"></h3>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body" id="modal-body"></div>
            <div class="modal-footer" id="modal-footer"></div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toast" class="toast"></div>

</div>

<script>
(function() {
    'use strict';

    const modalOverlay = document.getElementById('modal-overlay');
    const modalContainer = document.getElementById('modal-container');
    const modalTitle = document.getElementById('modal-title');
    const modalBody = document.getElementById('modal-body');
    const modalFooter = document.getElementById('modal-footer');
    const modalClose = document.querySelector('.modal-close');
    const toast = document.getElementById('toast');

    // Get all course cards with modal data attributes
    const resourceCards = document.querySelectorAll('.course-card[data-modal]');

    // Open modal on card click
    resourceCards.forEach(card => {
        card.addEventListener('click', function(e) {
            e.preventDefault();
            const modalType = this.getAttribute('data-modal');
            const title = this.getAttribute('data-title');

            modalTitle.textContent = title;

            if (modalType === 'prompt') {
                // Show prompt text with copy button - read from hidden div
                const promptId = this.getAttribute('data-prompt-id');
                const promptDiv = document.getElementById(promptId);
                if (!promptDiv) {
                    console.error('Prompt storage not found:', promptId);
                    return;
                }
                const promptText = promptDiv.textContent;
                modalBody.innerHTML = '<pre>' + escapeHtml(promptText) + '</pre>';

                // Store prompt text in a way that's safe for the copy button
                const copyBtn = document.createElement('button');
                copyBtn.className = 'copy-button';
                copyBtn.textContent = '📋 Copy Prompt';
                copyBtn.addEventListener('click', function() {
                    copyToClipboard(promptText);
                });
                modalFooter.innerHTML = '';
                modalFooter.appendChild(copyBtn);
            } else if (modalType === 'gem') {
                // Show gem description with link
                const gemLink = this.getAttribute('data-gem-link');
                modalBody.innerHTML = '<div class="gem-description">Click below to open this tool in a new tab and start using it right away.</div>';
                modalFooter.innerHTML = '<a href="' + gemLink + '" target="_blank" class="btn btn-primary">Open Tool →</a>';
            }

            // Show modal
            modalOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
    });

    // Close modal handlers
    function closeModal() {
        modalOverlay.classList.remove('active');
        document.body.style.overflow = '';
        setTimeout(() => {
            modalBody.innerHTML = '';
            modalFooter.innerHTML = '';
            modalTitle.textContent = '';
        }, 300);
    }

    modalClose.addEventListener('click', closeModal);

    modalOverlay.addEventListener('click', function(e) {
        if (e.target === modalOverlay) {
            closeModal();
        }
    });

    // ESC key to close
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modalOverlay.classList.contains('active')) {
            closeModal();
        }
    });

    // Stop propagation on modal container clicks
    modalContainer.addEventListener('click', function(e) {
        e.stopPropagation();
    });

    // Copy to clipboard function
    window.copyToClipboard = function(text) {
        navigator.clipboard.writeText(text).then(function() {
            showToast('Copied to clipboard! ✅');
        }).catch(function(err) {
            showToast('Failed to copy ❌');
            console.error('Copy failed:', err);
        });
    };

    // Show toast notification
    function showToast(message) {
        toast.textContent = message;
        toast.classList.add('show');
        setTimeout(() => {
            toast.classList.remove('show');
        }, 3000);
    }

    // HTML escape utility
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
})();
</script>

<?php get_footer(); ?>