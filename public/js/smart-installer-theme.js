(function () {
    function initInstallerProgress() {
        var fill = document.querySelector('.si-install-fill');
        var steps = Array.prototype.slice.call(document.querySelectorAll('.si-step'));
        if (!fill || !steps.length) return;

        var idx = 0;
        var values = [24, 48, 76, 100];

        function tick() {
            fill.style.width = values[idx] + '%';
            steps.forEach(function (step, i) {
                step.classList.toggle('is-active', i <= idx);
            });
            idx += 1;
            if (idx < values.length) {
                setTimeout(tick, 700);
            }
        }

        setTimeout(tick, 450);
    }

    function bindInstallButtons() {
        var buttons = document.querySelectorAll('[data-si-install]');
        buttons.forEach(function (btn) {
            btn.addEventListener('click', function () {
                if (typeof window.showToast === 'function') {
                    window.showToast('Installer Si SmaRT v2.0 disiapkan');
                    return;
                }

                if (typeof Swal !== 'undefined' && Swal && typeof Swal.fire === 'function') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Siap Diunduh',
                        text: 'Installer Si SmaRT v2.0 sedang disiapkan.',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });
    }

    function initRevealAnimations() {
        var targets = document.querySelectorAll('.si-card, .sx-review-card, .sx-screen, .si-panel');
        if (!targets.length || typeof IntersectionObserver === 'undefined') return;

        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) return;
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            });
        }, { threshold: 0.18 });

        targets.forEach(function (el, idx) {
            el.classList.add('si-anim-in');
            el.style.transitionDelay = Math.min(idx * 40, 220) + 'ms';
            observer.observe(el);
        });
    }

    function initScreensAutoScroll() {
        var rail = document.querySelector('.sx-screens');
        if (!rail) return;

        var cards = rail.querySelectorAll('.sx-screen');
        if (cards.length < 2) return;

        var index = 0;
        setInterval(function () {
            index = (index + 1) % cards.length;
            var card = cards[index];
            if (!card) return;

            rail.scrollTo({
                left: card.offsetLeft - 4,
                behavior: 'smooth'
            });
        }, 2600);
    }

    document.addEventListener('DOMContentLoaded', function () {
        initInstallerProgress();
        bindInstallButtons();
        initRevealAnimations();
        initScreensAutoScroll();
    });
})();
