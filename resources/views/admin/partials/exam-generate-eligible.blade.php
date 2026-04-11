@php
    $msgGenerateOk = t('admin_exam_generate_eligible_toast', 'Generated successfully.');
    $msgGenerateFail = t('admin_exam_generate_eligible_error', 'Could not generate. Please try again.');
@endphp
{{-- Toast + script: use buttons with class js-admin-exam-generate-eligible and data-url (POST admin generate-eligible-list). --}}
<div id="admin-exam-generate-toast-wrap" class="pointer-events-none fixed inset-x-0 top-3 z-[260] flex justify-center px-3 sm:top-4 sm:px-4" aria-live="polite">
    <div id="admin-exam-generate-toast" class="max-w-[min(95vw,56rem)] rounded-xl border border-amber-400/80 bg-amber-950/90 px-4 py-2.5 text-center text-base font-semibold text-amber-50 shadow-xl shadow-amber-950/45 opacity-0 -translate-y-3 transition-all duration-250">
        <span id="admin-exam-generate-toast-message">{{ $msgGenerateOk }}</span>
    </div>
</div>
<style>
@keyframes admin-exam-generate-spin { to { transform: rotate(360deg); } }
/* Loading: only spinner — label must lose to admin button styles (use !important). */
.js-admin-exam-generate-eligible[data-loading="1"] .js-admin-exam-generate-label {
    display: none !important;
}
.js-admin-exam-generate-eligible[data-loading="1"] .js-admin-exam-generate-spinner {
    display: block !important;
}
.js-admin-exam-generate-eligible[data-loading="1"] {
    min-height: 2.5rem;
}
</style>
@push('scripts')
<script>
(function () {
    var MSG_OK = @json($msgGenerateOk);
    var MSG_FAIL = @json($msgGenerateFail);
    function csrfToken() {
        var m = document.querySelector('meta[name="csrf-token"]');
        return m ? m.getAttribute('content') : '';
    }
    var toast = document.getElementById('admin-exam-generate-toast');
    var toastMsg = document.getElementById('admin-exam-generate-toast-message');
    var toastTimer = null;
    if (!toast) return;

    var MIN_SPINNER_MS = 1000;

    function delayAfterMinSpinner(startedAt) {
        var elapsed = Date.now() - startedAt;
        var wait = Math.max(0, MIN_SPINNER_MS - elapsed);
        return new Promise(function (resolve) {
            window.setTimeout(resolve, wait);
        });
    }

    function showToast(text, isError) {
        if (toastMsg && text) toastMsg.textContent = text;
        toast.classList.remove('opacity-0', '-translate-y-3');
        toast.classList.add('opacity-100', 'translate-y-0');
        toast.classList.toggle('border-amber-400/80', !isError);
        toast.classList.toggle('bg-amber-950/90', !isError);
        toast.classList.toggle('text-amber-50', !isError);
        toast.classList.toggle('border-rose-400/70', !!isError);
        toast.classList.toggle('bg-rose-900/85', !!isError);
        toast.classList.toggle('text-rose-100', !!isError);
        if (toastTimer) window.clearTimeout(toastTimer);
        toastTimer = window.setTimeout(function () {
            toast.classList.add('opacity-0', '-translate-y-3');
            toast.classList.remove('opacity-100', 'translate-y-0');
        }, 2400);
    }

    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.js-admin-exam-generate-eligible');
        if (!btn || btn.disabled) return;
        e.preventDefault();
        var url = btn.getAttribute('data-url');
        if (!url) return;

        var startedAt = Date.now();
        var label = btn.querySelector('.js-admin-exam-generate-label');
        var spin = btn.querySelector('.js-admin-exam-generate-spinner');
        btn.setAttribute('data-loading', '1');
        btn.setAttribute('aria-busy', 'true');
        if (spin) spin.classList.remove('hidden');
        btn.classList.add('pointer-events-none');
        btn.disabled = true;

        fetch(url, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken(),
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin',
            body: JSON.stringify({})
        })
            .then(function (r) {
                return r.json().then(function (data) {
                    return { ok: r.ok, status: r.status, data: data };
                }).catch(function () {
                    return { ok: false, status: r.status, data: {} };
                });
            })
            .then(function (res) {
                return delayAfterMinSpinner(startedAt).then(function () {
                    return res;
                });
            })
            .then(function (res) {
                var msg;
                if (res.ok) {
                    msg = (res.data && res.data.message) ? res.data.message : MSG_OK;
                    showToast(msg, false);
                } else {
                    msg = (res.data && res.data.message) ? res.data.message : MSG_FAIL;
                    showToast(msg, true);
                }
            })
            .catch(function () {
                return delayAfterMinSpinner(startedAt).then(function () {
                    showToast(MSG_FAIL, true);
                });
            })
            .finally(function () {
                btn.removeAttribute('data-loading');
                btn.removeAttribute('aria-busy');
                if (spin) spin.classList.add('hidden');
                btn.classList.remove('pointer-events-none');
                btn.disabled = false;
            });
    });
})();
</script>
@endpush
