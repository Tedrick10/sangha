{{-- Monastery portal tiles + bottom nav: inline so CSS updates apply on refresh without Vite HMR/build. --}}
<style id="monastery-portal-tiles-styles">
/* -------------------------------------------------------------------------
   Monastery portal — programme home (grid + tiles + icons; plain CSS)
   ------------------------------------------------------------------------- */
.monastery-portal-home-section {
    margin-top: 0.25rem;
}

.monastery-portal-home-wrap {
    width: 100%;
    margin-left: auto;
    margin-right: auto;
    max-width: 100%;
}

@media (min-width: 640px) {
    .monastery-portal-home-wrap {
        max-width: 46rem;
    }
}

@media (min-width: 1024px) {
    .monastery-portal-home-wrap {
        max-width: 52rem;
    }
}

.monastery-portal-home-grid {
    display: grid;
    width: 100%;
    gap: 0.5rem;
    grid-template-columns: repeat(2, minmax(0, 11.25rem));
    align-items: center;
    justify-items: stretch;
    justify-content: center;
}

@media (min-width: 480px) {
    .monastery-portal-home-grid {
        gap: 0.625rem;
        grid-template-columns: repeat(3, minmax(0, 12.5rem));
    }
}

@media (min-width: 768px) {
    .monastery-portal-home-grid {
        gap: 0.75rem;
    }
}

.monastery-portal-home-tile {
    touch-action: manipulation;
    -webkit-tap-highlight-color: transparent;
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 0.4rem;
    min-height: 0;
    aspect-ratio: 1;
    width: 100%;
    max-width: min(100%, 11.25rem);
    margin-left: auto;
    margin-right: auto;
    padding: 0.55rem 0.4rem 0.5rem;
    text-align: center;
    text-decoration: none;
    color: inherit;
    isolation: isolate;
    border-radius: 1.35rem;
    border: 1px solid rgb(226 232 240 / 0.95);
    background:
        linear-gradient(175deg, rgb(255 255 255 / 0.98) 0%, rgb(248 250 252) 45%, rgb(241 245 249 / 0.98) 100%);
    box-shadow:
        0 1px 0 rgb(255 255 255 / 1) inset,
        0 0 0 1px rgb(255 255 255 / 0.5) inset,
        0 2px 6px rgb(15 23 42 / 0.05),
        0 14px 36px rgb(15 23 42 / 0.08);
    transition:
        border-color 0.28s ease,
        box-shadow 0.28s ease,
        background 0.28s ease,
        transform 0.28s cubic-bezier(0.34, 1.3, 0.64, 1),
        filter 0.28s ease;
}

@media (prefers-reduced-motion: no-preference) {
    @keyframes mp-home-tile-enter {
        from {
            opacity: 0;
            transform: translateY(0.85rem) scale(0.94);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    .monastery-portal-home-tile {
        animation: mp-home-tile-enter 0.55s cubic-bezier(0.22, 1, 0.36, 1) backwards;
    }

    .monastery-portal-home-tile:nth-child(1) {
        animation-delay: 0.04s;
    }

    .monastery-portal-home-tile:nth-child(2) {
        animation-delay: 0.09s;
    }

    .monastery-portal-home-tile:nth-child(3) {
        animation-delay: 0.14s;
    }

    .monastery-portal-home-tile:nth-child(4) {
        animation-delay: 0.19s;
    }

    .monastery-portal-home-tile:nth-child(5) {
        animation-delay: 0.24s;
    }

    .monastery-portal-home-tile:nth-child(6) {
        animation-delay: 0.29s;
    }
}

.monastery-portal-home-tile::before {
    content: '';
    position: absolute;
    inset: 0;
    z-index: 0;
    border-radius: inherit;
    background: radial-gradient(100% 70% at 50% 0%, rgb(251 191 36 / 0.14), transparent 62%);
    opacity: 0;
    transition: opacity 0.25s ease;
    pointer-events: none;
}

.monastery-portal-home-tile::after {
    content: '';
    position: absolute;
    left: 50%;
    bottom: 0.55rem;
    z-index: 0;
    width: 42%;
    max-width: 3.5rem;
    height: 2px;
    border-radius: 2px;
    transform: translateX(-50%) scaleX(0);
    background: linear-gradient(90deg, transparent, rgb(245 158 11 / 0.75), transparent);
    opacity: 0;
    transition:
        transform 0.25s cubic-bezier(0.22, 1, 0.36, 1),
        opacity 0.2s ease;
    pointer-events: none;
}

html.dark .monastery-portal-home-tile {
    border-color: rgb(148 163 184 / 0.2);
    background: linear-gradient(
        162deg,
        rgb(51 65 85 / 0.45) 0%,
        rgb(30 41 59 / 0.78) 48%,
        rgb(15 23 42 / 0.95) 100%
    );
    box-shadow:
        0 1px 0 rgb(255 255 255 / 0.09) inset,
        0 0 0 1px rgb(255 255 255 / 0.06) inset,
        0 10px 40px rgb(0 0 0 / 0.42),
        0 0 0 1px rgb(15 23 42 / 0.45),
        0 0 36px rgb(59 130 246 / 0.04);
    backdrop-filter: blur(16px) saturate(1.45);
}

html.dark .monastery-portal-home-tile::before {
    background: radial-gradient(100% 75% at 50% 0%, rgb(251 191 36 / 0.18), transparent 58%);
}

.monastery-portal-home-tile__iconWrap {
    position: relative;
    z-index: 1;
    display: flex;
    flex-shrink: 0;
    align-items: center;
    justify-content: center;
    width: 4rem;
    height: 4rem;
    border-radius: 1.2rem;
    box-shadow:
        0 1px 0 rgb(255 255 255 / 0.45) inset,
        0 3px 10px rgb(15 23 42 / 0.07);
    transition:
        transform 0.28s cubic-bezier(0.34, 1.3, 0.64, 1),
        background-color 0.25s ease,
        color 0.25s ease,
        box-shadow 0.28s ease,
        filter 0.28s ease;
}

.monastery-portal-home-tile__svg {
    width: 2.125rem;
    height: 2.125rem;
}

@media (min-width: 640px) {
    .monastery-portal-home-tile {
        max-width: min(100%, 12.5rem);
        gap: 0.5rem;
        padding: 0.7rem 0.5rem 0.65rem;
        border-radius: 1.5rem;
    }

    .monastery-portal-home-tile::after {
        bottom: 0.75rem;
    }

    .monastery-portal-home-tile__iconWrap {
        width: 4.625rem;
        height: 4.625rem;
        border-radius: 1.3rem;
    }

    .monastery-portal-home-tile__svg {
        width: 2.375rem;
        height: 2.375rem;
    }
}

html.dark .monastery-portal-home-tile__iconWrap {
    background: linear-gradient(155deg, rgb(255 255 255 / 0.1), rgb(255 255 255 / 0.03)) !important;
    border: 1px solid rgb(255 255 255 / 0.1);
    box-shadow:
        inset 0 1px 0 rgb(255 255 255 / 0.12),
        0 4px 14px rgb(0 0 0 / 0.25);
}

/* Icon wells — cohesive saturation (light) */
.monastery-portal-home-tile[data-mp-tile='total'] .monastery-portal-home-tile__iconWrap {
    background: linear-gradient(145deg, rgb(239 246 255), rgb(219 234 254));
    color: rgb(29 78 216);
}

.monastery-portal-home-tile[data-mp-tile='primary'] .monastery-portal-home-tile__iconWrap {
    background: linear-gradient(145deg, rgb(254 252 232), rgb(254 243 199));
    color: rgb(161 98 7);
}

.monastery-portal-home-tile[data-mp-tile='intermediate'] .monastery-portal-home-tile__iconWrap {
    background: linear-gradient(145deg, rgb(245 243 255), rgb(237 233 254));
    color: rgb(91 33 182);
}

.monastery-portal-home-tile[data-mp-tile='level-1'] .monastery-portal-home-tile__iconWrap {
    background: linear-gradient(145deg, rgb(236 253 245), rgb(209 250 229));
    color: rgb(4 120 87);
}

.monastery-portal-home-tile[data-mp-tile='level-2'] .monastery-portal-home-tile__iconWrap {
    background: linear-gradient(145deg, rgb(240 249 255), rgb(224 242 254));
    color: rgb(3 105 161);
}

.monastery-portal-home-tile[data-mp-tile='level-3'] .monastery-portal-home-tile__iconWrap {
    background: linear-gradient(145deg, rgb(255 247 237), rgb(254 235 200));
    color: rgb(180 83 9);
}

/* Icon wells (dark) — same frosted chip; accent via glow + icon color */
html.dark .monastery-portal-home-tile[data-mp-tile='total'] .monastery-portal-home-tile__iconWrap {
    color: rgb(147 197 253);
    box-shadow:
        inset 0 1px 0 rgb(255 255 255 / 0.12),
        0 4px 14px rgb(0 0 0 / 0.25),
        0 0 0 1px rgb(96 165 250 / 0.3),
        0 0 40px rgb(59 130 246 / 0.32);
}

html.dark .monastery-portal-home-tile[data-mp-tile='primary'] .monastery-portal-home-tile__iconWrap {
    color: rgb(253 224 71);
    box-shadow:
        inset 0 1px 0 rgb(255 255 255 / 0.12),
        0 4px 14px rgb(0 0 0 / 0.25),
        0 0 0 1px rgb(251 191 36 / 0.32),
        0 0 36px rgb(245 158 11 / 0.3);
}

html.dark .monastery-portal-home-tile[data-mp-tile='intermediate'] .monastery-portal-home-tile__iconWrap {
    color: rgb(196 181 253);
    box-shadow:
        inset 0 1px 0 rgb(255 255 255 / 0.12),
        0 4px 14px rgb(0 0 0 / 0.25),
        0 0 0 1px rgb(167 139 250 / 0.32),
        0 0 36px rgb(139 92 246 / 0.26);
}

html.dark .monastery-portal-home-tile[data-mp-tile='level-1'] .monastery-portal-home-tile__iconWrap {
    color: rgb(110 231 183);
    box-shadow:
        inset 0 1px 0 rgb(255 255 255 / 0.12),
        0 4px 14px rgb(0 0 0 / 0.25),
        0 0 0 1px rgb(52 211 153 / 0.3),
        0 0 36px rgb(16 185 129 / 0.24);
}

html.dark .monastery-portal-home-tile[data-mp-tile='level-2'] .monastery-portal-home-tile__iconWrap {
    color: rgb(125 211 252);
    box-shadow:
        inset 0 1px 0 rgb(255 255 255 / 0.12),
        0 4px 14px rgb(0 0 0 / 0.25),
        0 0 0 1px rgb(56 189 248 / 0.3),
        0 0 36px rgb(14 165 233 / 0.26);
}

html.dark .monastery-portal-home-tile[data-mp-tile='level-3'] .monastery-portal-home-tile__iconWrap {
    color: rgb(253 186 116);
    box-shadow:
        inset 0 1px 0 rgb(255 255 255 / 0.12),
        0 4px 14px rgb(0 0 0 / 0.25),
        0 0 0 1px rgb(251 146 60 / 0.32),
        0 0 36px rgb(249 115 22 / 0.26);
}

.monastery-portal-home-tile__label {
    position: relative;
    z-index: 1;
    max-width: 100%;
    padding: 0 0.2rem;
    font-size: clamp(0.8rem, 2.6vw + 0.18rem, 0.95rem);
    font-weight: 600;
    line-height: 1.22;
    letter-spacing: 0.01em;
    color: rgb(30 41 59);
    text-wrap: balance;
}

@media (min-width: 640px) {
    .monastery-portal-home-tile__label {
        font-size: clamp(0.875rem, 1vw + 0.65rem, 1rem);
        letter-spacing: 0.02em;
    }
}

html.dark .monastery-portal-home-tile__label {
    color: rgb(248 250 252);
    text-shadow: 0 1px 2px rgb(0 0 0 / 0.25);
}

@media (hover: hover) and (pointer: fine) {
    .monastery-portal-home-tile:hover {
        border-color: rgb(251 191 36 / 0.5);
        transform: translateY(-5px) scale(1.02);
        box-shadow:
            0 1px 0 rgb(255 255 255 / 0.95) inset,
            0 0 0 1px rgb(251 191 36 / 0.18) inset,
            0 8px 20px rgb(15 23 42 / 0.08),
            0 22px 48px rgb(245 158 11 / 0.18);
    }

    .monastery-portal-home-tile:hover::before {
        opacity: 1;
    }

    .monastery-portal-home-tile:hover::after {
        opacity: 1;
        transform: translateX(-50%) scaleX(1);
    }

    html:not(.dark) .monastery-portal-home-tile:hover .monastery-portal-home-tile__iconWrap {
        transform: scale(1.1) translateY(-2px);
        box-shadow:
            0 1px 0 rgb(255 255 255 / 0.55) inset,
            0 10px 24px rgb(15 23 42 / 0.12);
    }

    html.dark .monastery-portal-home-tile:hover .monastery-portal-home-tile__iconWrap {
        transform: scale(1.1) translateY(-2px);
        filter: brightness(1.12) saturate(1.08);
    }

    html.dark .monastery-portal-home-tile:hover {
        border-color: rgb(253 224 71 / 0.38);
        background: linear-gradient(
            168deg,
            rgb(71 85 105 / 0.5) 0%,
            rgb(51 65 85 / 0.9) 40%,
            rgb(30 41 59 / 0.98) 100%
        );
        box-shadow:
            0 1px 0 rgb(255 255 255 / 0.12) inset,
            0 0 0 1px rgb(251 191 36 / 0.16) inset,
            0 18px 48px rgb(0 0 0 / 0.55),
            0 0 56px rgb(245 158 11 / 0.12);
    }

}

@media (prefers-reduced-motion: reduce) {
    .monastery-portal-home-tile {
        animation: none;
        transition: border-color 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
    }

    .monastery-portal-home-tile:hover,
    .monastery-portal-home-tile:hover .monastery-portal-home-tile__iconWrap {
        transform: none;
        filter: none;
    }

    .monastery-portal-home-tile:hover::after {
        transform: translateX(-50%) scaleX(0);
        opacity: 0;
    }

    .monastery-portal-home-tile:hover::before {
        opacity: 0;
    }
}

.monastery-portal-home-tile:focus-visible {
    outline: 2px solid rgb(251 191 36);
    outline-offset: 3px;
}

.monastery-portal-home-tile:focus-visible::before {
    opacity: 0.85;
}

.monastery-portal-home-tile:active {
    transform: translateY(-2px) scale(0.99);
    transition-duration: 0.12s;
}

@media (prefers-reduced-motion: reduce) {
    .monastery-portal-home-tile:active {
        transform: none;
    }
}

/* Back row (sub-screens) */
.monastery-portal-back-row__btn {
    display: inline-flex;
    min-height: 2.5rem;
    align-items: center;
    gap: 0.5rem;
    border-radius: 0.875rem;
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
    font-weight: 500;
    line-height: 1.2;
    text-decoration: none;
    border: 1px solid rgb(226 232 240);
    background: rgb(255 255 255);
    color: rgb(30 41 59);
    box-shadow: 0 1px 2px rgb(15 23 42 / 0.04);
    transition:
        background-color 0.18s ease,
        border-color 0.18s ease,
        box-shadow 0.18s ease,
        transform 0.15s ease;
}

.monastery-portal-back-row__btn--neutral:hover {
    background: rgb(248 250 252);
    border-color: rgb(203 213 225);
}

.monastery-portal-back-row__btn--accent {
    border-color: rgb(251 191 36 / 0.45);
    background: linear-gradient(168deg, rgb(255 251 235) 0%, rgb(254 243 199 / 0.85) 100%);
    color: rgb(120 53 15);
}

.monastery-portal-back-row__btn--accent:hover {
    border-color: rgb(245 158 11 / 0.55);
    background: rgb(254 243 199);
}

html.dark .monastery-portal-back-row__btn--neutral {
    border-color: rgb(71 85 105 / 0.85);
    background: rgb(30 41 59 / 0.85);
    color: rgb(248 250 252);
    box-shadow: 0 4px 14px rgb(0 0 0 / 0.2);
}

html.dark .monastery-portal-back-row__btn--neutral:hover {
    background: rgb(51 65 85 / 0.65);
    border-color: rgb(100 116 139 / 0.7);
}

/* Dark accent back — default amber; when portal appearance is on, follow user --ap-btn-* */
html.dark .monastery-portal-back-row__btn--accent {
    border-color: rgb(251 191 36 / 0.42);
    background: linear-gradient(
        168deg,
        color-mix(in srgb, rgb(245 158 11) 22%, rgb(15 23 42)) 0%,
        color-mix(in srgb, rgb(217 119 6) 12%, rgb(2 6 23)) 100%
    );
    color: rgb(255 251 235);
    box-shadow:
        0 1px 0 rgb(255 255 255 / 0.08) inset,
        0 0 0 1px rgb(251 191 36 / 0.12) inset,
        0 6px 20px rgb(0 0 0 / 0.28);
}

html.dark .monastery-portal-back-row__btn--accent:hover {
    border-color: rgb(253 224 71 / 0.5);
    background: linear-gradient(
        168deg,
        color-mix(in srgb, rgb(251 191 36) 28%, rgb(30 41 59)) 0%,
        color-mix(in srgb, rgb(245 158 11) 18%, rgb(15 23 42)) 100%
    );
}

html.dark body.appearance-portal--monastery .monastery-portal-back-row__btn--accent {
    border-color: color-mix(in srgb, var(--ap-btn-bg) 38%, rgb(71 85 105 / 0.55));
    background: linear-gradient(
        168deg,
        color-mix(in srgb, var(--ap-btn-bg) 26%, rgb(15 23 42)) 0%,
        color-mix(in srgb, var(--ap-btn-bg) 10%, rgb(2 6 23)) 100%
    );
    color: var(--ap-btn-text);
    box-shadow:
        0 1px 0 color-mix(in srgb, var(--ap-btn-bg) 18%, transparent) inset,
        0 0 0 1px color-mix(in srgb, var(--ap-btn-bg) 22%, transparent) inset,
        0 6px 22px color-mix(in srgb, var(--ap-btn-bg) 14%, rgb(0 0 0 / 0.45));
}

html.dark body.appearance-portal--monastery .monastery-portal-back-row__btn--accent:hover {
    border-color: color-mix(in srgb, var(--ap-btn-bg) 48%, rgb(253 224 71 / 0.35));
    background: linear-gradient(
        168deg,
        color-mix(in srgb, var(--ap-btn-bg) 34%, rgb(30 41 59)) 0%,
        color-mix(in srgb, var(--ap-btn-bg) 16%, rgb(15 23 42)) 100%
    );
}

.monastery-portal-back-row__btn:active {
    transform: scale(0.98);
}

/* Eligible / portal tables — fit viewport width; wrap long text instead of horizontal scroll */
.mp-eligible-table-wrap {
    width: 100%;
    max-width: 100%;
    min-width: 0;
    overflow-x: visible;
}

.mp-eligible-table-wrap table {
    width: 100%;
    max-width: 100%;
    min-width: 0;
    table-layout: fixed;
    border-collapse: separate;
    border-spacing: 0;
}

.mp-eligible-table-wrap table thead th,
.mp-eligible-table-wrap table tbody td {
    overflow-wrap: anywhere;
    word-break: break-word;
}

@media (max-width: 639px) {
    .mp-eligible-table-wrap table thead th,
    .mp-eligible-table-wrap table tbody td {
        padding-left: 0.35rem !important;
        padding-right: 0.35rem !important;
        padding-top: 0.45rem !important;
        padding-bottom: 0.45rem !important;
    }

    .mp-eligible-table-wrap .mp-action-btn {
        min-width: 0;
        padding-left: 0.45rem;
        padding-right: 0.45rem;
        font-size: 0.62rem;
        line-height: 1.15;
        white-space: normal;
        text-align: center;
    }

    .mp-portal-passfail-table-wrap table th,
    .mp-portal-passfail-table-wrap table td {
        padding-left: 0.35rem !important;
        padding-right: 0.35rem !important;
        padding-top: 0.45rem !important;
        padding-bottom: 0.45rem !important;
    }
}

/* Pass / fail full-width tables (same wrapping behaviour) */
.mp-portal-passfail-table-wrap {
    width: 100%;
    max-width: 100%;
    min-width: 0;
    overflow-x: visible;
}

.mp-portal-passfail-table-wrap table {
    width: 100%;
    max-width: 100%;
    min-width: 0;
    table-layout: fixed;
}

.mp-portal-passfail-table-wrap table th,
.mp-portal-passfail-table-wrap table td {
    overflow-wrap: anywhere;
    word-break: break-word;
}

.mp-eligible-table-wrap table thead th {
    background: rgb(241 245 249);
    color: rgb(51 65 85);
    border-bottom: 1px solid rgb(226 232 240);
}

.mp-eligible-table-wrap table tbody td {
    background: rgb(255 255 255 / 0.92);
    color: rgb(15 23 42);
    border-bottom: 1px solid rgb(226 232 240 / 0.9);
}

.mp-eligible-table-wrap table tbody tr:hover td {
    background: rgb(240 249 255);
}

.mp-eligible-table-wrap .mp-eligible-muted {
    color: rgb(71 85 105);
}

.mp-eligible-table-wrap .mp-eligible-roll {
    font-variant-numeric: tabular-nums;
    font-weight: 700;
    color: rgb(3 105 161);
}

.mp-reason-card {
    border: 1px solid rgb(251 191 36 / 0.24);
    border-radius: 0.75rem;
    background: rgb(255 251 235 / 0.55);
    padding: 0.45rem 0.6rem;
}

.mp-reason-preview {
    font-weight: 600;
}

.mp-reason-link {
    border: 0;
    background: transparent;
    padding: 0;
    font-size: 0.66rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    color: rgb(180 83 9 / 0.9);
    cursor: pointer;
}

.mp-reason-link:hover {
    text-decoration: underline;
}

.mp-row-actions {
    display: inline-flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: flex-end;
    gap: 0.45rem;
}

.mp-action-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 6.75rem;
    border-radius: 0.7rem;
    padding: 0.45rem 0.8rem;
    font-size: 0.72rem;
    font-weight: 700;
    line-height: 1.2;
    text-decoration: none;
    transition: all 0.2s ease;
}

.mp-action-btn--secondary {
    border: 1px solid rgb(148 163 184 / 0.52);
    background: rgb(255 255 255 / 0.88);
    color: rgb(30 41 59);
}

.mp-action-btn--secondary:hover {
    border-color: rgb(56 189 248 / 0.55);
    background: rgb(240 249 255);
    color: rgb(12 74 110);
}

.mp-action-btn--primary {
    border: 1px solid rgb(245 158 11 / 0.75);
    background: rgb(255 251 235);
    color: rgb(120 53 15);
}

.mp-action-btn--primary:hover {
    border-color: rgb(217 119 6 / 0.8);
    background: rgb(254 243 199);
    color: rgb(120 53 15);
}

/* Count badge readability across all themes (including when appearance mode is off). */
.mp-eligible-count-badge {
    font-weight: 700;
}

html.dark .mp-eligible-count-badge {
    border-color: rgb(148 163 184 / 0.45) !important;
    background: rgb(15 23 42 / 0.92) !important;
    color: #ffffff !important;
    -webkit-text-fill-color: #ffffff !important;
    text-shadow: 0 1px 1px rgb(0 0 0 / 0.45);
}

/* Eligible — dark: fallbacks when appearance off; --ap-* when monastery portal colors active */
html.dark .mp-eligible-table-wrap table thead th {
    background: rgb(15 23 42 / 0.98) !important;
    color: rgb(226 232 240) !important;
    border-bottom-color: rgb(51 65 85 / 0.85) !important;
}

html.dark .mp-eligible-table-wrap table tbody td {
    background: rgb(30 41 59 / 0.88) !important;
    color: rgb(248 250 252) !important;
    border-bottom-color: rgb(51 65 85 / 0.55) !important;
}

html.dark .mp-eligible-table-wrap table tbody tr:hover td {
    background: rgb(51 65 85 / 0.72) !important;
}

html.dark .mp-eligible-table-wrap .mp-eligible-muted {
    color: rgb(186 211 230) !important;
}

html.dark .mp-eligible-table-wrap .mp-eligible-roll {
    color: rgb(125 211 252) !important;
}

html.dark .mp-reason-card {
    border-color: rgb(251 191 36 / 0.25);
    background: rgb(30 41 59 / 0.65);
}

html.dark .mp-reason-link {
    color: rgb(253 224 71 / 0.85);
}

html.dark .mp-action-btn--secondary {
    border-color: rgb(100 116 139 / 0.9);
    background: rgb(15 23 42 / 0.9);
    color: rgb(226 232 240);
}

html.dark .mp-action-btn--secondary:hover {
    border-color: rgb(56 189 248 / 0.6);
    background: rgb(30 41 59 / 0.95);
    color: rgb(255 255 255);
}

html.dark .mp-action-btn--primary {
    border-color: rgb(245 158 11 / 0.75);
    background: rgb(120 53 15 / 0.22);
    color: rgb(254 243 199);
}

html.dark .mp-action-btn--primary:hover {
    border-color: rgb(251 191 36 / 0.82);
    background: rgb(146 64 14 / 0.33);
}

@media (max-width: 1024px) {
    .mp-row-actions {
        min-width: 8.5rem;
        flex-direction: column;
        align-items: stretch;
        gap: 0.4rem;
    }

    .mp-action-btn {
        width: 100%;
    }
}

html.dark body.appearance-portal--monastery .mp-eligible-table-wrap table thead th {
    background: color-mix(in srgb, var(--ap-surface-bg) 8%, rgb(2 6 23 / 0.98)) !important;
    color: var(--ap-body-dark) !important;
    border-bottom-color: color-mix(in srgb, var(--ap-btn-bg) 18%, rgb(51 65 85 / 0.55)) !important;
}

html.dark body.appearance-portal--monastery .mp-eligible-table-wrap table tbody td {
    background: color-mix(in srgb, var(--ap-surface-bg) 14%, rgb(15 23 42 / 0.92)) !important;
    color: var(--ap-body-dark) !important;
    border-bottom-color: color-mix(in srgb, var(--ap-btn-bg) 14%, rgb(51 65 85 / 0.45)) !important;
}

html.dark body.appearance-portal--monastery .mp-eligible-table-wrap table tbody tr:hover td {
    background: color-mix(in srgb, var(--ap-btn-bg) 22%, rgb(30 41 59 / 0.88)) !important;
}

html.dark body.appearance-portal--monastery .mp-eligible-table-wrap .mp-eligible-muted {
    color: var(--ap-muted-dark) !important;
}

html.dark body.appearance-portal--monastery .mp-eligible-table-wrap .mp-eligible-roll {
    color: color-mix(in srgb, var(--ap-btn-bg) 6%, rgb(255 251 235)) !important;
}

html.dark .mp-eligible-toolbar input[type='checkbox'],
html.dark .mp-eligible-table-wrap input[type='checkbox'] {
    width: 1rem;
    height: 1rem;
    border-radius: 0.25rem;
    border: 1px solid rgb(100 116 139);
    background-color: rgb(15 23 42);
    accent-color: rgb(245 158 11);
}

html.dark body.appearance-portal--monastery .mp-eligible-toolbar input[type='checkbox'],
html.dark body.appearance-portal--monastery .mp-eligible-table-wrap input[type='checkbox'] {
    border-color: color-mix(in srgb, var(--ap-btn-bg) 28%, rgb(100 116 139)) !important;
    background-color: color-mix(in srgb, var(--ap-page-bg) 35%, rgb(15 23 42)) !important;
    accent-color: var(--ap-btn-bg) !important;
}

html.dark .mp-eligible-table-wrap .js-open-eligible-details {
    border-color: rgb(100 116 139 / 0.95) !important;
    background: rgb(15 23 42 / 0.95) !important;
    color: rgb(248 250 252) !important;
}

html.dark .mp-eligible-table-wrap .js-open-eligible-details:hover {
    border-color: rgb(56 189 248 / 0.55) !important;
    background: rgb(30 41 59 / 0.95) !important;
    color: rgb(255 255 255) !important;
}

html.dark body.appearance-portal--monastery .mp-eligible-table-wrap .js-open-eligible-details {
    border-color: color-mix(in srgb, var(--ap-btn-bg) 22%, rgb(71 85 105 / 0.75)) !important;
    background: color-mix(in srgb, var(--ap-surface-bg) 12%, rgb(15 23 42 / 0.95)) !important;
    color: var(--ap-body-dark) !important;
}

html.dark body.appearance-portal--monastery .mp-eligible-table-wrap .js-open-eligible-details:hover {
    border-color: color-mix(in srgb, var(--ap-btn-bg) 38%, rgb(125 211 252 / 0.35)) !important;
    background: color-mix(in srgb, var(--ap-btn-bg) 14%, rgb(30 41 59 / 0.92)) !important;
    color: var(--ap-btn-text) !important;
}

html.dark body.appearance-portal--monastery .mp-eligible-toolbar {
    background-color: color-mix(in srgb, var(--ap-surface-bg) 18%, rgb(2 6 23 / 0.92)) !important;
    border-color: color-mix(in srgb, var(--ap-btn-bg) 14%, rgb(51 65 85 / 0.55)) !important;
}

html.dark body.appearance-portal--monastery .mp-eligible-submit {
    background-color: var(--ap-btn-bg) !important;
    color: var(--ap-btn-text) !important;
}

html.dark body.appearance-portal--monastery .mp-eligible-submit:hover {
    background-color: color-mix(in srgb, var(--ap-btn-bg) 86%, #000) !important;
}

html.dark body.appearance-portal--monastery .mp-eligible-panel {
    border-color: color-mix(in srgb, var(--ap-btn-bg) 14%, rgb(51 65 85 / 0.55)) !important;
    background: linear-gradient(
        180deg,
        color-mix(in srgb, var(--ap-surface-bg) 10%, rgb(15 23 42 / 0.95)) 0%,
        color-mix(in srgb, var(--ap-page-bg) 18%, rgb(2 6 23 / 0.98)) 100%
    ) !important;
    box-shadow:
        0 1px 0 color-mix(in srgb, var(--ap-btn-bg) 8%, transparent) inset,
        0 18px 44px -28px color-mix(in srgb, var(--ap-btn-bg) 12%, rgb(0 0 0 / 0.55)) !important;
}

html.dark body.appearance-portal--monastery .mp-eligible-accent-bar {
    background: linear-gradient(
        90deg,
        transparent,
        color-mix(in srgb, var(--ap-btn-bg) 55%, transparent),
        transparent
    ) !important;
}

html.dark body.appearance-portal--monastery .mp-eligible-count-badge {
    border-color: color-mix(in srgb, var(--ap-btn-bg) 28%, rgb(71 85 105 / 0.5)) !important;
    background-color: color-mix(in srgb, var(--ap-btn-bg) 22%, rgb(2 6 23 / 0.9)) !important;
    color: #ffffff !important;
    -webkit-text-fill-color: #ffffff !important;
    text-shadow: 0 1px 1px rgb(0 0 0 / 0.45);
    box-shadow:
        0 0 0 1px color-mix(in srgb, var(--ap-btn-bg) 16%, transparent) inset,
        0 4px 14px color-mix(in srgb, var(--ap-btn-bg) 12%, rgb(0 0 0 / 0.42)) !important;
}

/* Eligible “View details” dialog — match portal surfaces in dark + appearance */
html.dark body.appearance-portal--monastery .mp-eligible-details-panel {
    border-color: color-mix(in srgb, var(--ap-btn-bg) 14%, rgb(51 65 85 / 0.55)) !important;
    background: linear-gradient(
        180deg,
        color-mix(in srgb, var(--ap-surface-bg) 10%, rgb(15 23 42 / 0.98)) 0%,
        color-mix(in srgb, var(--ap-page-bg) 16%, rgb(2 6 23 / 0.99)) 100%
    ) !important;
    box-shadow:
        0 1px 0 color-mix(in srgb, var(--ap-btn-bg) 8%, transparent) inset,
        0 24px 48px -24px color-mix(in srgb, var(--ap-btn-bg) 14%, rgb(0 0 0 / 0.55)) !important;
}

/* Programme hub — flexible grid + cards (Primary … Level 3) */
.monastery-portal-programme-hub__header {
    margin-bottom: clamp(1rem, 3vw, 1.35rem);
    padding-bottom: clamp(0.85rem, 2.5vw, 1.1rem);
    border-bottom: 1px solid rgb(226 232 240 / 0.85);
    padding-inline: 0.125rem;
}

html.dark .monastery-portal-programme-hub__header {
    border-bottom-color: rgb(51 65 85 / 0.65);
}

.monastery-portal-programme-hub__title {
    font-size: clamp(1.125rem, 2.8vw + 0.65rem, 1.5rem);
    font-weight: 700;
    letter-spacing: -0.02em;
    line-height: 1.2;
    color: rgb(15 23 42);
}

html.dark .monastery-portal-programme-hub__title {
    color: rgb(248 250 252);
}

.monastery-portal-programme-hub__subtitle {
    margin-top: 0.4rem;
    max-width: 40rem;
    font-size: clamp(0.8125rem, 1.2vw + 0.65rem, 0.9375rem);
    line-height: 1.5;
    color: rgb(71 85 105);
}

html.dark .monastery-portal-programme-hub__subtitle {
    color: rgb(148 163 184);
}

.monastery-portal-programme-hub-wrap {
    width: 100%;
    max-width: min(100%, 58rem);
    margin-left: auto;
    margin-right: auto;
}

.monastery-portal-programme-hub-grid {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    width: 100%;
    gap: clamp(0.75rem, 2.4vw, 1.2rem);
}

.monastery-portal-hub-card {
    touch-action: manipulation;
    -webkit-tap-highlight-color: transparent;
    position: relative;
    display: flex;
    flex: 1 1 9.75rem;
    max-width: min(100%, 11rem);
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: clamp(0.5rem, 1.4vw, 0.7rem);
    min-height: clamp(6.75rem, 17vw, 8rem);
    padding: clamp(0.9rem, 2.6vw, 1.1rem) clamp(0.6rem, 2vw, 0.75rem);
    text-align: center;
    text-decoration: none;
    color: inherit;
    overflow: hidden;
    border-radius: 1.2rem;
    border: 1px solid rgb(226 232 240 / 0.92);
    background:
        linear-gradient(175deg, rgb(255 255 255 / 0.98) 0%, rgb(248 250 252) 48%, rgb(241 245 249 / 0.98) 100%);
    box-shadow:
        0 1px 0 rgb(255 255 255 / 1) inset,
        0 0 0 1px rgb(255 255 255 / 0.55) inset,
        0 1px 2px rgb(15 23 42 / 0.04),
        0 12px 32px rgb(15 23 42 / 0.07);
    transition:
        border-color 0.22s ease,
        box-shadow 0.22s ease,
        background 0.22s ease,
        transform 0.24s cubic-bezier(0.34, 1.25, 0.64, 1);
}

.monastery-portal-hub-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 12%;
    right: 12%;
    height: 3px;
    border-radius: 999px;
    opacity: 0.9;
    pointer-events: none;
    background: linear-gradient(90deg, transparent, rgb(251 191 36 / 0.75), transparent);
}

.monastery-portal-hub-card[data-mp-hub-card='create']::before {
    background: linear-gradient(90deg, transparent, rgb(245 158 11 / 0.85), transparent);
}

.monastery-portal-hub-card[data-mp-hub-card='eligible']::before {
    background: linear-gradient(90deg, transparent, rgb(56 189 248 / 0.85), transparent);
}

.monastery-portal-hub-card[data-mp-hub-card='pending']::before {
    background: linear-gradient(90deg, transparent, rgb(250 204 21 / 0.85), transparent);
}

.monastery-portal-hub-card[data-mp-hub-card='accepted']::before,
.monastery-portal-hub-card[data-mp-hub-card='approved']::before {
    background: linear-gradient(90deg, transparent, rgb(52 211 153 / 0.85), transparent);
}

.monastery-portal-hub-card[data-mp-hub-card='needed-update']::before {
    background: linear-gradient(90deg, transparent, rgb(167 139 250 / 0.85), transparent);
}

.monastery-portal-hub-card[data-mp-hub-card='rejected']::before {
    background: linear-gradient(90deg, transparent, rgb(248 113 113 / 0.85), transparent);
}

.monastery-portal-hub-card[data-mp-hub-card='transfer']::before {
    background: linear-gradient(90deg, transparent, rgb(192 132 252 / 0.85), transparent);
}

html.dark .monastery-portal-hub-card {
    border-color: rgb(148 163 184 / 0.16);
    background: linear-gradient(
        168deg,
        rgb(30 41 59 / 0.58) 0%,
        rgb(15 23 42 / 0.82) 48%,
        rgb(2 6 23 / 0.92) 100%
    );
    box-shadow:
        0 1px 0 rgb(255 255 255 / 0.08) inset,
        0 0 0 1px rgb(255 255 255 / 0.04) inset,
        0 12px 36px rgb(0 0 0 / 0.45);
    backdrop-filter: blur(14px) saturate(1.25);
}

.monastery-portal-hub-card__iconWrap {
    display: flex;
    align-items: center;
    justify-content: center;
    width: clamp(3rem, 8.5vw, 3.5rem);
    height: clamp(3rem, 8.5vw, 3.5rem);
    border-radius: 0.95rem;
    background: linear-gradient(160deg, rgb(248 250 252) 0%, rgb(241 245 249) 100%);
    color: rgb(51 65 85);
    border: 1px solid rgb(226 232 240 / 0.95);
    box-shadow: 0 1px 0 rgb(255 255 255 / 0.9) inset;
    transition:
        transform 0.22s cubic-bezier(0.34, 1.25, 0.64, 1),
        box-shadow 0.22s ease,
        border-color 0.22s ease;
}

html.dark .monastery-portal-hub-card__iconWrap {
    background: linear-gradient(155deg, rgb(255 255 255 / 0.12), rgb(255 255 255 / 0.04));
    color: rgb(248 250 252);
    border: 1px solid rgb(255 255 255 / 0.1);
    box-shadow:
        inset 0 1px 0 rgb(255 255 255 / 0.12),
        0 4px 14px rgb(0 0 0 / 0.22);
}

.monastery-portal-hub-card__svg {
    width: clamp(1.45rem, 4.4vw, 1.7rem);
    height: clamp(1.45rem, 4.4vw, 1.7rem);
}

.monastery-portal-hub-card__label {
    font-size: clamp(0.78rem, 1.1vw + 0.55rem, 0.9rem);
    font-weight: 600;
    line-height: 1.28;
    letter-spacing: 0.012em;
    color: rgb(30 41 59);
    text-wrap: balance;
    padding: 0 0.2rem;
    max-width: 100%;
}

html.dark .monastery-portal-hub-card__label {
    color: rgb(248 250 252);
    text-shadow: 0 1px 2px rgb(0 0 0 / 0.2);
}

/* Icon accent — light: soft tint ring; dark: colored icon + soft glow */
.monastery-portal-hub-card[data-mp-hub-card='create'] .monastery-portal-hub-card__iconWrap {
    color: rgb(180 83 9);
    box-shadow:
        inset 0 1px 0 rgb(255 255 255 / 0.85),
        0 0 0 1px rgb(251 191 36 / 0.35);
}

.monastery-portal-hub-card[data-mp-hub-card='eligible'] .monastery-portal-hub-card__iconWrap {
    color: rgb(3 105 161);
    box-shadow:
        inset 0 1px 0 rgb(255 255 255 / 0.85),
        0 0 0 1px rgb(56 189 248 / 0.35);
}

.monastery-portal-hub-card[data-mp-hub-card='pending'] .monastery-portal-hub-card__iconWrap {
    color: rgb(161 98 7);
    box-shadow:
        inset 0 1px 0 rgb(255 255 255 / 0.85),
        0 0 0 1px rgb(250 204 21 / 0.35);
}

.monastery-portal-hub-card[data-mp-hub-card='accepted'] .monastery-portal-hub-card__iconWrap,
.monastery-portal-hub-card[data-mp-hub-card='approved'] .monastery-portal-hub-card__iconWrap {
    color: rgb(5 122 85);
    box-shadow:
        inset 0 1px 0 rgb(255 255 255 / 0.85),
        0 0 0 1px rgb(52 211 153 / 0.35);
}

.monastery-portal-hub-card[data-mp-hub-card='needed-update'] .monastery-portal-hub-card__iconWrap {
    color: rgb(91 33 182);
    box-shadow:
        inset 0 1px 0 rgb(255 255 255 / 0.85),
        0 0 0 1px rgb(167 139 250 / 0.35);
}

.monastery-portal-hub-card[data-mp-hub-card='rejected'] .monastery-portal-hub-card__iconWrap {
    color: rgb(185 28 28);
    box-shadow:
        inset 0 1px 0 rgb(255 255 255 / 0.85),
        0 0 0 1px rgb(248 113 113 / 0.35);
}

.monastery-portal-hub-card[data-mp-hub-card='transfer'] .monastery-portal-hub-card__iconWrap {
    color: rgb(107 33 168);
    box-shadow:
        inset 0 1px 0 rgb(255 255 255 / 0.85),
        0 0 0 1px rgb(192 132 252 / 0.35);
}

html.dark .monastery-portal-hub-card[data-mp-hub-card='create'] .monastery-portal-hub-card__iconWrap {
    color: rgb(253 224 71);
    box-shadow:
        inset 0 1px 0 rgb(255 255 255 / 0.12),
        0 0 0 1px rgb(251 191 36 / 0.28),
        0 0 28px rgb(245 158 11 / 0.18);
}

html.dark .monastery-portal-hub-card[data-mp-hub-card='eligible'] .monastery-portal-hub-card__iconWrap {
    color: rgb(125 211 252);
    box-shadow:
        inset 0 1px 0 rgb(255 255 255 / 0.12),
        0 0 0 1px rgb(56 189 248 / 0.28),
        0 0 28px rgb(14 165 233 / 0.16);
}

html.dark .monastery-portal-hub-card[data-mp-hub-card='pending'] .monastery-portal-hub-card__iconWrap {
    color: rgb(253 224 71);
    box-shadow:
        inset 0 1px 0 rgb(255 255 255 / 0.12),
        0 0 0 1px rgb(250 204 21 / 0.26),
        0 0 28px rgb(234 179 8 / 0.14);
}

html.dark .monastery-portal-hub-card[data-mp-hub-card='accepted'] .monastery-portal-hub-card__iconWrap,
html.dark .monastery-portal-hub-card[data-mp-hub-card='approved'] .monastery-portal-hub-card__iconWrap {
    color: rgb(167 243 208);
    box-shadow:
        inset 0 1px 0 rgb(255 255 255 / 0.12),
        0 0 0 1px rgb(52 211 153 / 0.28),
        0 0 28px rgb(16 185 129 / 0.14);
}

html.dark .monastery-portal-hub-card[data-mp-hub-card='needed-update'] .monastery-portal-hub-card__iconWrap {
    color: rgb(196 181 253);
    box-shadow:
        inset 0 1px 0 rgb(255 255 255 / 0.12),
        0 0 0 1px rgb(167 139 250 / 0.28),
        0 0 28px rgb(139 92 246 / 0.14);
}

html.dark .monastery-portal-hub-card[data-mp-hub-card='rejected'] .monastery-portal-hub-card__iconWrap {
    color: rgb(252 165 165);
    box-shadow:
        inset 0 1px 0 rgb(255 255 255 / 0.12),
        0 0 0 1px rgb(248 113 113 / 0.28),
        0 0 28px rgb(239 68 68 / 0.12);
}

html.dark .monastery-portal-hub-card[data-mp-hub-card='transfer'] .monastery-portal-hub-card__iconWrap {
    color: rgb(233 213 255);
    box-shadow:
        inset 0 1px 0 rgb(255 255 255 / 0.12),
        0 0 0 1px rgb(192 132 252 / 0.28),
        0 0 28px rgb(168 85 247 / 0.14);
}

@media (hover: hover) and (pointer: fine) {
    .monastery-portal-hub-card:hover {
        border-color: rgb(251 191 36 / 0.38);
        transform: translateY(-5px);
        box-shadow:
            0 1px 0 rgb(255 255 255 / 0.95) inset,
            0 0 0 1px rgb(251 191 36 / 0.12) inset,
            0 2px 8px rgb(15 23 42 / 0.05),
            0 20px 44px rgb(15 23 42 / 0.12);
    }

    .monastery-portal-hub-card:hover .monastery-portal-hub-card__iconWrap {
        transform: scale(1.06);
    }

    html.dark .monastery-portal-hub-card:hover {
        border-color: rgb(253 224 71 / 0.28);
        background: linear-gradient(
            168deg,
            rgb(51 65 85 / 0.55) 0%,
            rgb(30 41 59 / 0.9) 50%,
            rgb(15 23 42 / 0.97) 100%
        );
        box-shadow:
            0 1px 0 rgb(255 255 255 / 0.1) inset,
            0 0 0 1px rgb(251 191 36 / 0.1) inset,
            0 22px 52px rgb(0 0 0 / 0.52);
    }
}

.monastery-portal-hub-card:focus-visible {
    outline: 2px solid rgb(245 158 11);
    outline-offset: 3px;
}

.monastery-portal-hub-card:focus-visible:not(:hover) {
    transform: translateY(-1px);
}

.monastery-portal-hub-card:active {
    transform: translateY(-1px) scale(0.99);
    transition-duration: 0.12s;
}

@media (prefers-reduced-motion: reduce) {
    .monastery-portal-hub-card,
    .monastery-portal-hub-card__iconWrap {
        transition-duration: 0.12s;
    }

    .monastery-portal-hub-card:hover,
    .monastery-portal-hub-card:hover .monastery-portal-hub-card__iconWrap {
        transform: none;
    }

    .monastery-portal-hub-card:active {
        transform: none;
    }

    .monastery-portal-back-row__btn:active {
        transform: none;
    }
}

/* Placeholder / prose panels (Eligible, Needed update, …) */
.monastery-portal-content-panel {
    border-radius: 1.1rem;
    border: 1px solid rgb(226 232 240 / 0.95);
    background: linear-gradient(175deg, rgb(255 255 255) 0%, rgb(248 250 252) 100%);
    padding: clamp(1rem, 3vw, 1.35rem);
    box-shadow:
        0 1px 0 rgb(255 255 255 / 1) inset,
        0 4px 18px rgb(15 23 42 / 0.05);
}

html.dark .monastery-portal-content-panel {
    border-color: rgb(148 163 184 / 0.14);
    background: linear-gradient(168deg, rgb(30 41 59 / 0.55) 0%, rgb(15 23 42 / 0.82) 100%);
    box-shadow:
        0 1px 0 rgb(255 255 255 / 0.06) inset,
        0 8px 28px rgb(0 0 0 / 0.35);
    backdrop-filter: blur(10px) saturate(1.15);
}

.monastery-portal-content-panel__title {
    font-size: clamp(1.05rem, 2vw + 0.55rem, 1.25rem);
    font-weight: 700;
    letter-spacing: -0.015em;
    color: rgb(15 23 42);
}

html.dark .monastery-portal-content-panel__title {
    color: rgb(248 250 252);
}

.monastery-portal-content-panel__body {
    margin-top: 0.5rem;
    max-width: 42rem;
    font-size: clamp(0.8125rem, 1vw + 0.55rem, 0.9375rem);
    line-height: 1.55;
    color: rgb(71 85 105);
}

html.dark .monastery-portal-content-panel__body {
    color: rgb(148 163 184);
}

.monastery-portal-home-blank {
    min-height: min(52vh, 26rem);
    border-radius: 1rem;
    border: 1px dashed rgb(203 213 225 / 0.9);
    background: rgb(248 250 252 / 0.85);
    box-shadow: inset 0 1px 0 rgb(255 255 255 / 0.7);
}

html.dark .monastery-portal-home-blank {
    border-color: rgb(71 85 105 / 0.75);
    background: rgb(15 23 42 / 0.45);
    box-shadow: inset 0 1px 0 rgb(255 255 255 / 0.04);
}

.monastery-portal-bottom-nav {
    position: fixed;
    z-index: 40;
    left: 50%;
    transform: translateX(-50%);
    bottom: max(1.25rem, env(safe-area-inset-bottom, 0px));
    width: min(calc(100% - 1.5rem), calc(100vw - 1.5rem));
    max-width: 36rem;
    padding: 0.25rem 0.5rem;
    border-radius: 1rem;
    border: 1px solid rgb(226 232 240 / 0.95);
    background: rgb(255 255 255 / 0.96);
    box-shadow:
        0 10px 40px rgb(15 23 42 / 0.12),
        0 2px 8px rgb(15 23 42 / 0.06);
    backdrop-filter: blur(10px);
}

html.dark .monastery-portal-bottom-nav {
    border-color: rgb(71 85 105 / 0.9);
    background: rgb(15 23 42 / 0.96);
    box-shadow:
        0 12px 40px rgb(0 0 0 / 0.45),
        0 0 0 1px rgb(255 255 255 / 0.04) inset;
}

.monastery-portal-bottom-nav__inner {
    display: flex;
    flex-wrap: nowrap;
    align-items: stretch;
    justify-content: space-between;
    gap: 0.25rem;
    width: 100%;
}

.monastery-portal-bottom-nav__tab {
    flex: 1 1 0%;
    min-width: 0;
    min-height: 2.1rem;
    touch-action: manipulation;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0.125rem 0.5rem;
    border-radius: 0.75rem;
    font-size: 0.6875rem;
    font-weight: 600;
    line-height: 1.2;
    text-align: center;
    text-decoration: none;
    color: rgb(51 65 85);
    transition:
        background-color 0.15s ease,
        color 0.15s ease,
        transform 0.1s ease;
}

@media (min-width: 640px) {
    .monastery-portal-bottom-nav {
        bottom: max(1.5rem, env(safe-area-inset-bottom, 0px));
        padding: 0.3125rem 0.625rem;
    }

    .monastery-portal-bottom-nav__tab {
        min-height: 2.25rem;
        padding: 0.1875rem 0.5rem;
        font-size: 0.875rem;
    }
}

html.dark .monastery-portal-bottom-nav__tab {
    color: rgb(226 232 240);
}

.monastery-portal-bottom-nav__tab:hover {
    background-color: rgb(241 245 249);
}

html.dark .monastery-portal-bottom-nav__tab:hover {
    background-color: rgb(30 41 59 / 0.95);
}

.monastery-portal-bottom-nav__tab--active {
    background-color: rgb(245 158 11) !important;
    color: rgb(255 255 255) !important;
    box-shadow: 0 2px 8px rgb(180 83 9 / 0.35);
}

.monastery-portal-bottom-nav__tab--active:hover {
    background-color: rgb(217 119 6) !important;
    color: rgb(255 255 255) !important;
}

html.dark .monastery-portal-bottom-nav__tab--active:hover {
    filter: brightness(1.06);
}

.monastery-portal-bottom-nav__tab:focus-visible {
    outline: 2px solid rgb(245 158 11);
    outline-offset: 2px;
}

.monastery-portal-bottom-nav__tab:active {
    transform: scale(0.98);
}
</style>
