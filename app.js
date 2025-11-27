(function() {
    'use strict';

    // Load data and initialize
    fetch('data.json')
        .then(response => response.json())
        .then(data => {
            renderSessions(data.courses || []);
            renderResources(data.resources || []);
            initializeModals();
            initializeFiltering();
            initializeAccordion();
        })
        .catch(error => console.error('Error loading data:', error));

    // Render Academic Sessions
    function renderSessions(sessions) {
        const grid = document.getElementById('sessions-grid');
        if (!grid) return;

        sessions.forEach(session => {
            const tags = session.tags || session.topics || [];
            const tagsAttr = tags.length > 0 ? tags.join(',') : '';

            const card = document.createElement('div');
            card.className = 'course-card filterable-card';
            if (tagsAttr) card.setAttribute('data-tags', tagsAttr);

            card.innerHTML = `
                <span class="course-tag">SESSION</span>
                <a href="${escapeHtml(session.url)}" target="_blank" class="course-link">${escapeHtml(session.title)}</a>
                <p class="course-meta">${escapeHtml(session.program)} • ${escapeHtml(session.institution)}</p>
                <div class="course-footer">Open Deck →</div>
            `;

            grid.appendChild(card);
        });
    }

    // Render AI Lab Resources
    function renderResources(resources) {
        const grid = document.getElementById('resources-grid');
        const promptStorage = document.getElementById('prompt-storage');
        if (!grid) return;

        resources.forEach((resource, index) => {
            const tags = resource.tags || resource.topics || [];
            const tagsAttr = tags.length > 0 ? tags.join(',') : '';

            // Handle workflows differently
            if (resource.type === 'Workflow' && resource.steps) {
                renderWorkflow(grid, resource, tagsAttr);
            } else {
                renderRegularResource(grid, promptStorage, resource, index, tagsAttr);
            }
        });
    }

    // Render Workflow Card
    function renderWorkflow(grid, workflow, tagsAttr) {
        const card = document.createElement('div');
        card.className = 'course-card workflow-card filterable-card';
        if (tagsAttr) card.setAttribute('data-tags', tagsAttr);

        let stepsHTML = '';
        workflow.steps.forEach((step, stepIndex) => {
            const stepClass = stepIndex > 0 ? 'workflow-step-right' : 'workflow-step-left';
            stepsHTML += `
                <a href="${escapeHtml(step.link)}" target="_blank" class="workflow-step ${stepClass}">
                    <div class="step-number">${step.step_number}</div>
                    <div class="step-title">${escapeHtml(step.title)}</div>
                    <div class="step-desc">${escapeHtml(step.desc)}</div>
                </a>
            `;
            if (stepIndex < workflow.steps.length - 1) {
                stepsHTML += '<div class="workflow-arrow">→</div>';
            }
        });

        card.innerHTML = `
            <span class="course-tag">🔗 WORKFLOW</span>
            <div class="workflow-title">${escapeHtml(workflow.workflow_title)}</div>
            <p class="workflow-desc">${escapeHtml(workflow.workflow_desc)}</p>
            <div class="workflow-steps">${stepsHTML}</div>
        `;

        grid.appendChild(card);
    }

    // Render Regular Resource Card
    function renderRegularResource(grid, promptStorage, resource, index, tagsAttr) {
        const icons = {
            'Gemini Gem': '💎',
            'Custom GPT': '🤖',
            'Prompt': '⚡'
        };
        const icon = icons[resource.type] || (resource.type.includes('Prompt') ? '⚡' : '💎');

        const card = document.createElement('div');
        card.className = 'course-card filterable-card';
        if (tagsAttr) card.setAttribute('data-tags', tagsAttr);

        // Handle prompt text or gem link
        if (resource.prompt_text) {
            const promptId = 'prompt-' + index;
            card.setAttribute('data-modal', 'prompt');
            card.setAttribute('data-prompt-id', promptId);
            card.setAttribute('data-title', resource.title);

            // Store prompt in hidden div
            const promptDiv = document.createElement('div');
            promptDiv.id = promptId;
            promptDiv.className = 'prompt-storage';
            promptDiv.style.display = 'none';
            promptDiv.textContent = resource.prompt_text;
            promptStorage.appendChild(promptDiv);
        } else {
            card.setAttribute('data-modal', 'gem');
            card.setAttribute('data-gem-link', resource.link);
            card.setAttribute('data-title', resource.title);
        }

        card.innerHTML = `
            <span class="course-tag">${icon} ${escapeHtml(resource.type)}</span>
            <div class="course-link">${escapeHtml(resource.title)}</div>
            <p class="course-desc">${escapeHtml(resource.desc || '')}</p>
            <div class="course-footer">View Details →</div>
        `;

        grid.appendChild(card);
    }

    // Initialize Modal System
    function initializeModals() {
        const modalOverlay = document.getElementById('modal-overlay');
        const modalContainer = document.getElementById('modal-container');
        const modalTitle = document.getElementById('modal-title');
        const modalBody = document.getElementById('modal-body');
        const modalFooter = document.getElementById('modal-footer');
        const modalClose = document.querySelector('.modal-close');
        const toast = document.getElementById('toast');

        // Open modal on card click
        document.addEventListener('click', function(e) {
            const card = e.target.closest('.course-card[data-modal]');
            if (!card) return;

            e.preventDefault();
            const modalType = card.getAttribute('data-modal');
            const title = card.getAttribute('data-title');

            modalTitle.textContent = title;

            if (modalType === 'prompt') {
                // Show prompt text with copy button
                const promptId = card.getAttribute('data-prompt-id');
                const promptDiv = document.getElementById(promptId);
                if (!promptDiv) {
                    console.error('Prompt storage not found:', promptId);
                    return;
                }
                const promptText = promptDiv.textContent;
                modalBody.innerHTML = '<pre>' + escapeHtml(promptText) + '</pre>';

                const copyBtn = document.createElement('button');
                copyBtn.className = 'copy-button';
                copyBtn.textContent = '📋 Copy Prompt';
                copyBtn.addEventListener('click', function() {
                    copyToClipboard(promptText, toast);
                });
                modalFooter.innerHTML = '';
                modalFooter.appendChild(copyBtn);
            } else if (modalType === 'gem') {
                // Show gem description with link
                const gemLink = card.getAttribute('data-gem-link');
                modalBody.innerHTML = '<div class="gem-description">Click below to open this tool in a new tab and start using it right away.</div>';
                modalFooter.innerHTML = '<a href="' + escapeHtml(gemLink) + '" target="_blank" class="btn btn-primary">Open Tool →</a>';
            }

            // Show modal
            modalOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';
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
            if (e.target === modalOverlay) closeModal();
        });
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && modalOverlay.classList.contains('active')) {
                closeModal();
            }
        });
        modalContainer.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }

    // Copy to clipboard
    function copyToClipboard(text, toast) {
        navigator.clipboard.writeText(text).then(function() {
            showToast('Copied to clipboard! ✅', toast);
        }).catch(function(err) {
            showToast('Failed to copy ❌', toast);
            console.error('Copy failed:', err);
        });
    }

    // Show toast notification
    function showToast(message, toast) {
        toast.textContent = message;
        toast.classList.add('show');
        setTimeout(() => {
            toast.classList.remove('show');
        }, 3000);
    }

    // Initialize Filtering System
    function initializeFiltering() {
        const filterButtons = document.querySelectorAll('.filter-button');
        let activeFilters = { sessions: 'all', resources: 'all' };

        filterButtons.forEach(button => {
            button.addEventListener('click', function() {
                const filter = this.getAttribute('data-filter');
                const target = this.getAttribute('data-target');

                // Update active state
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

        // Apply filter function
        function applyFilter(target, filter) {
            console.log('Applying filter:', target, '-', filter);

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
                        const tagArray = cardTags.split(',').map(tag => tag.trim());
                        if (tagArray.includes(filter)) {
                            card.classList.remove('hidden');
                            visibleCount++;
                        } else {
                            card.classList.add('hidden');
                        }
                    } else {
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
            updateAccordionAfterFilter(target);
        }

        // Initialize counts on page load
        setTimeout(function() {
            applyFilter('sessions', 'all');
            applyFilter('resources', 'all');
        }, 100);

        // Make applyFilter available globally
        window.applyFilter = applyFilter;
    }

    // Initialize Accordion System
    function initializeAccordion() {
        const CARDS_PER_ROW = 3;
        const INITIAL_ROWS = 2;
        const INITIAL_CARDS = CARDS_PER_ROW * INITIAL_ROWS; // 6 cards
        let accordionState = { sessions: false, resources: false };

        setTimeout(function() {
            initAccordion('sessions');
            initAccordion('resources');
        }, 150);

        function initAccordion(target) {
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

        window.updateAccordionAfterFilter = function(target) {
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
        };

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
    }

    // HTML escape utility
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

})();
