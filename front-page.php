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

    <!-- Sessions Filter -->
    <div class="filter-container">
        <div class="filter-bar">
            <button class="filter-button active" data-filter="all" data-target="sessions">All</button>
            <button class="filter-button" data-filter="Sales" data-target="sessions">Sales</button>
            <button class="filter-button" data-filter="Marketing" data-target="sessions">Marketing</button>
            <button class="filter-button" data-filter="Product Management" data-target="sessions">Product Management</button>
            <button class="filter-button" data-filter="Customer Experience" data-target="sessions">Customer Experience</button>
            <button class="filter-button" data-filter="AI" data-target="sessions">AI</button>
            <button class="filter-button" data-filter="Research with AI" data-target="sessions">Research with AI</button>
        </div>
        <div class="filter-count" id="sessions-count"></div>
    </div>

    <div class="grid-wrapper" id="sessions-wrapper">
        <?php echo do_shortcode('[course_grid]'); ?>
        <button class="show-more-button" data-target="sessions">
            <span class="show-more-text">Show More</span>
            <span class="show-less-text" style="display:none;">Show Less</span>
        </button>
    </div>

    <div id="resources" class="lab-section-header">
        <h2 class="lab-title">The AI Lab</h2>
    </div>

    <!-- Resources Filter -->
    <div class="filter-container">
        <div class="filter-bar">
            <button class="filter-button active" data-filter="all" data-target="resources">All</button>
            <button class="filter-button" data-filter="Sales" data-target="resources">Sales</button>
            <button class="filter-button" data-filter="Marketing" data-target="resources">Marketing</button>
            <button class="filter-button" data-filter="Product Management" data-target="resources">Product Management</button>
            <button class="filter-button" data-filter="Customer Experience" data-target="resources">Customer Experience</button>
            <button class="filter-button" data-filter="AI" data-target="resources">AI</button>
            <button class="filter-button" data-filter="Research with AI" data-target="resources">Research with AI</button>
        </div>
        <div class="filter-count" id="resources-count"></div>
    </div>

    <div class="grid-wrapper" id="resources-wrapper">
        <?php echo do_shortcode('[resource_grid]'); ?>
        <button class="show-more-button" data-target="resources">
            <span class="show-more-text">Show More</span>
            <span class="show-less-text" style="display:none;">Show Less</span>
        </button>
    </div>

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

    // === FILTERING SYSTEM ===
    const filterButtons = document.querySelectorAll('.filter-button');
    let activeFilters = { sessions: 'all', resources: 'all' };

    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const filter = this.getAttribute('data-filter');
            const target = this.getAttribute('data-target');

            // Update active state for this filter group
            document.querySelectorAll('.filter-button[data-target="' + target + '"]').forEach(btn => {
                btn.classList.remove('active');
            });
            this.classList.add('active');

            // Store active filter
            activeFilters[target] = filter;

            // Apply filter
            applyFilter(target, filter);
        });
    });

    function applyFilter(target, filter) {
        console.log('Applying filter:', target, '-', filter);

        // Determine which grid to filter
        const gridSelector = target === 'sessions' ?
            '.lab-section-header:has(.lab-title:contains("Academic Sessions")) + .filter-container + .course-grid' :
            '.lab-section-header:has(.lab-title:contains("The AI Lab")) + .filter-container + .course-grid';

        // Find the correct grid by position
        const allGrids = document.querySelectorAll('.course-grid');
        const grid = target === 'sessions' ? allGrids[0] : allGrids[1];

        if (!grid) {
            console.log('Filter: Grid not found for', target);
            return;
        }

        const cards = grid.querySelectorAll('.filterable-card');
        let visibleCount = 0;
        const totalCount = cards.length;

        cards.forEach(card => {
            if (filter === 'all') {
                card.classList.remove('hidden');
                visibleCount++;
            } else {
                const cardTags = card.getAttribute('data-tags');
                if (cardTags) {
                    // Split tags and trim whitespace
                    const tagArray = cardTags.split(',').map(tag => tag.trim());
                    if (tagArray.includes(filter)) {
                        card.classList.remove('hidden');
                        visibleCount++;
                    } else {
                        card.classList.add('hidden');
                    }
                } else {
                    // No tags, hide when filtering
                    card.classList.add('hidden');
                }
            }
        });

        // Update count display
        const countElement = document.getElementById(target + '-count');
        if (countElement) {
            countElement.textContent = 'Showing ' + visibleCount + ' of ' + totalCount;
        }

        console.log('Filter result:', target, '- Showing', visibleCount, 'of', totalCount, 'cards');

        // Update accordion after filtering
        if (typeof updateAccordionAfterFilter === 'function') {
            updateAccordionAfterFilter(target);
        }
    }

    // Initialize counts and accordion on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Small delay to ensure grids are fully rendered
        setTimeout(function() {
            applyFilter('sessions', 'all');
            applyFilter('resources', 'all');
            initializeAccordion('sessions');
            initializeAccordion('resources');
        }, 100);
    });

    // === ACCORDION SYSTEM ===
    const CARDS_PER_ROW = 3;
    const INITIAL_ROWS = 2;
    const INITIAL_CARDS = CARDS_PER_ROW * INITIAL_ROWS; // 6 cards
    let accordionState = { sessions: false, resources: false }; // false = collapsed, true = expanded

    function initializeAccordion(target) {
        const allGrids = document.querySelectorAll('.course-grid');
        const grid = target === 'sessions' ? allGrids[0] : allGrids[1];

        if (!grid) {
            console.log('Accordion init: Grid not found for', target);
            return;
        }

        const cards = grid.querySelectorAll('.filterable-card');
        const button = document.querySelector('.show-more-button[data-target="' + target + '"]');

        console.log('Accordion init:', target, '- Found', cards.length, 'cards, showing first', INITIAL_CARDS);

        if (!button) {
            console.log('Accordion init: Button not found for', target);
            return;
        }

        // Reset accordion state
        accordionState[target] = false;

        // Hide cards beyond initial display
        cards.forEach((card, index) => {
            if (index >= INITIAL_CARDS) {
                card.classList.add('accordion-hidden');
            } else {
                card.classList.remove('accordion-hidden');
            }
        });

        // Hide button if total cards <= INITIAL_CARDS
        if (cards.length <= INITIAL_CARDS) {
            button.classList.add('hidden');
        } else {
            button.classList.remove('hidden');
        }
    }

    function updateAccordionAfterFilter(target) {
        const allGrids = document.querySelectorAll('.course-grid');
        const grid = target === 'sessions' ? allGrids[0] : allGrids[1];

        if (!grid) return;

        const visibleCards = Array.from(grid.querySelectorAll('.filterable-card')).filter(card => !card.classList.contains('hidden'));
        const button = document.querySelector('.show-more-button[data-target="' + target + '"]');

        if (!button) return;

        // If expanded, show all visible cards
        if (accordionState[target]) {
            visibleCards.forEach(card => {
                card.classList.remove('accordion-hidden');
            });
        } else {
            // If collapsed, show only first INITIAL_CARDS visible cards
            visibleCards.forEach((card, index) => {
                if (index >= INITIAL_CARDS) {
                    card.classList.add('accordion-hidden');
                } else {
                    card.classList.remove('accordion-hidden');
                }
            });
        }

        // Hide button if visible cards <= INITIAL_CARDS
        if (visibleCards.length <= INITIAL_CARDS) {
            button.classList.add('hidden');
        } else {
            button.classList.remove('hidden');
        }
    }

    // Show More / Show Less button handlers
    const showMoreButtons = document.querySelectorAll('.show-more-button');
    showMoreButtons.forEach(button => {
        button.addEventListener('click', function() {
            const target = this.getAttribute('data-target');
            const allGrids = document.querySelectorAll('.course-grid');
            const grid = target === 'sessions' ? allGrids[0] : allGrids[1];

            if (!grid) return;

            const visibleCards = Array.from(grid.querySelectorAll('.filterable-card')).filter(card => !card.classList.contains('hidden'));
            const showMoreText = this.querySelector('.show-more-text');
            const showLessText = this.querySelector('.show-less-text');

            if (accordionState[target]) {
                // Currently expanded, collapse it
                visibleCards.forEach((card, index) => {
                    if (index >= INITIAL_CARDS) {
                        card.classList.add('accordion-hidden');
                    }
                });
                showMoreText.style.display = '';
                showLessText.style.display = 'none';
                accordionState[target] = false;

                // Scroll to section header
                const header = target === 'sessions'
                    ? document.querySelector('.lab-section-header:has(.lab-title)')
                    : document.querySelector('#resources');
                if (header) {
                    header.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            } else {
                // Currently collapsed, expand it
                visibleCards.forEach(card => {
                    card.classList.remove('accordion-hidden');
                });
                showMoreText.style.display = 'none';
                showLessText.style.display = '';
                accordionState[target] = true;
            }
        });
    });
})();
</script>

<?php get_footer(); ?>