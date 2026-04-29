import TomSelect from 'tom-select';

/**
 * Keep native <select> (no Tom Select): short / status-only filters & pagination.
 * Long lists (Sanghas: monastery/exam, Scores: subject/sangha/exam, Mandatory scores, etc.) use Tom Select.
 */
function shouldSkipSelect(el) {
    const name = el.getAttribute('name') || '';
    if (name === 'per_page') {
        return true;
    }
    if (name === 'request_status' || name === 'moderation_status') {
        return true;
    }
    if (name === 'is_active' || name === 'is_published') {
        return true;
    }
    if (name === 'status' && el.id === 'mr_status') {
        return true;
    }

    return false;
}

function isInHiddenSubtree(el) {
    let n = el;
    while (n && n !== document.documentElement) {
        if (n instanceof HTMLElement && n.classList.contains('hidden')) {
            return true;
        }
        const cs = window.getComputedStyle(n);
        if (cs.display === 'none') {
            return true;
        }
        n = n.parentElement;
    }

    return false;
}

/**
 * Native <select> → Tom Select with searchable dropdown.
 * Opt out: data-no-select-search, or short filters (see shouldSkipSelect).
 */
export function initSearchableSelects(root = document) {
    if (!root?.querySelectorAll) {
        return;
    }

    root.querySelectorAll('select:not([data-no-select-search])').forEach((el) => {
        if (el.tomselect) {
            return;
        }
        if (shouldSkipSelect(el)) {
            return;
        }
        if (el.getAttribute('size') > 1) {
            return;
        }
        if (el.querySelectorAll('option').length === 0) {
            return;
        }
        if (isInHiddenSubtree(el)) {
            return;
        }

        try {
            new TomSelect(el, {
                plugins: ['dropdown_input'],
                allowEmptyOption: true,
                create: false,
                /* Escape overflow-x-auto / overflow-hidden on filter bars and cards */
                dropdownParent: 'body',
                onInitialize() {
                    if (this.control_input) {
                        this.control_input.setAttribute('placeholder', 'Search…');
                        this.control_input.setAttribute('aria-label', 'Search options');
                    }
                },
            });
        } catch {
            // Invalid DOM or already wrapped
        }
    });
}

if (typeof document !== 'undefined') {
    document.addEventListener(
        'click',
        (e) => {
            if (e.target?.closest?.('.mode-tab')) {
                queueMicrotask(() => initSearchableSelects(document));
            }
        },
        true
    );
}
