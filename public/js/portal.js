// Navbar Scroll Logic
window.addEventListener('scroll', function () {
    const nav = document.getElementById('navbar');
    if (window.scrollY > 80) {
        nav.classList.add('glass-nav', 'py-4', 'shadow-2xl');
        nav.classList.remove('py-8');
    } else {
        nav.classList.remove('glass-nav', 'py-4', 'shadow-2xl');
        nav.classList.add('py-8');
    }
});

// Mobile Menu Logic
const menuBtn = document.getElementById('menu-btn');
const closeBtn = document.getElementById('close-btn');
const overlay = document.getElementById('mobile-menu-overlay');
const links = document.querySelectorAll('.mobile-link');

if (menuBtn && closeBtn && overlay) {
    menuBtn.addEventListener('click', () => {
        overlay.classList.remove('hidden');
        setTimeout(() => overlay.classList.add('open'), 10);
        document.body.style.overflow = 'hidden';
    });

    const closeMenu = () => {
        overlay.classList.remove('open');
        setTimeout(() => {
            overlay.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }, 600);
    };

    closeBtn.addEventListener('click', closeMenu);
    links.forEach(link => link.addEventListener('click', closeMenu));
}

// Smooth Reveal Intersection Observer
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('active');
        }
    });
}, { threshold: 0.15 });

document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

// --- PARALLAX SLIDER JS ---
(function () {
    const wrap = (n, max) => (n + max) % max;
    const lerp = (a, b, t) => a + (b - a) * t;
    const genId = (() => { let count = 0; return () => (count++).toString(); })();

    class Raf {
        constructor() {
            this.rafId = 0;
            this.raf = this.raf.bind(this);
            this.callbacks = [];
            this.start();
        }
        start() { this.raf(); }
        stop() { cancelAnimationFrame(this.rafId); }
        raf() {
            this.callbacks.forEach(({ callback, id }) => callback({ id }));
            this.rafId = requestAnimationFrame(this.raf);
        }
        add(callback, id) { this.callbacks.push({ callback, id: id || genId() }); }
        remove(id) { this.callbacks = this.callbacks.filter((callback) => callback.id !== id); }
    }

    class Vec2 {
        constructor(x = 0, y = 0) { this.x = x; this.y = y; }
        set(x, y) { this.x = x; this.y = y; }
        lerp(v, t) { this.x = lerp(this.x, v.x, t); this.y = lerp(this.y, v.y, t); }
    }

    const vec2 = (x = 0, y = 0) => new Vec2(x, y);
    const rafInstance = new Raf();

    function tilt(node, options) {
        let { trigger, target } = resolveOptions(node, options);
        let lerpAmount = 0.06;
        const rotDeg = { current: vec2(), target: vec2() };
        const bgPos = { current: vec2(), target: vec2() };
        const isMobile = window.matchMedia("(pointer: coarse)").matches;

        let rafId;

        function ticker({ id }) {
            rafId = id;
            rotDeg.current.lerp(rotDeg.target, lerpAmount);
            bgPos.current.lerp(bgPos.target, lerpAmount);

            for (const el of target) {
                el.style.setProperty("--rotX", rotDeg.current.y.toFixed(2) + "deg");
                el.style.setProperty("--rotY", rotDeg.current.x.toFixed(2) + "deg");
                el.style.setProperty("--bgPosX", bgPos.current.x.toFixed(2) + "%");
                el.style.setProperty("--bgPosY", bgPos.current.y.toFixed(2) + "%");
            }
        }

        const onMouseMove = ({ offsetX, offsetY }) => {
            lerpAmount = 0.1;
            for (const el of target) {
                const ox = (offsetX - el.clientWidth * 0.5) / (Math.PI * 3);
                const oy = -(offsetY - el.clientHeight * 0.5) / (Math.PI * 4);
                rotDeg.target.set(ox, oy);
                bgPos.target.set(-ox * 0.3, oy * 0.3);
            }
        };

        const onMouseLeave = () => {
            lerpAmount = 0.06;
            rotDeg.target.set(0, 0);
            bgPos.target.set(0, 0);
        };

        const init = () => {
            if (!isMobile) {
                trigger.addEventListener("mousemove", onMouseMove);
                trigger.addEventListener("mouseleave", onMouseLeave);
            }
            rafInstance.add(ticker);
        };

        const destroy = () => {
            if (!isMobile) {
                trigger.removeEventListener("mousemove", onMouseMove);
                trigger.removeEventListener("mouseleave", onMouseLeave);
            }
            rafInstance.remove(rafId);
        };

        init();
        return { destroy };
    }

    function resolveOptions(node, options) {
        return {
            trigger: options?.trigger ?? node,
            target: options?.target ? (Array.isArray(options.target) ? options.target : [options.target]) : [node]
        };
    }

    function initSlider() {
        const slides = [...document.querySelectorAll(".slide")];
        const slidesInfo = [...document.querySelectorAll(".slide-info")];
        const buttons = {
            prev: document.querySelector(".slider--btn__prev"),
            next: document.querySelector(".slider--btn__next")
        };

        if (!buttons.prev || !buttons.next) return;

        slides.forEach((slide, i) => {
            const slideInner = slide.querySelector(".slide__inner");
            const slideInfoInner = (slidesInfo[i]) ? slidesInfo[i].querySelector(".slide-info__inner") : null;
            if (slideInner && slideInfoInner) {
                tilt(slide, { target: [slideInner, slideInfoInner] });
            }
        });

        const goPrev = changeSlider(-1);
        const goNext = changeSlider(1);

        buttons.prev.addEventListener("click", goPrev);
        buttons.next.addEventListener("click", goNext);

        const sliderEl = document.querySelector('.slider');
        if (!sliderEl) return;

        let touchStartX = 0; let touchEndX = 0;
        sliderEl.addEventListener('touchstart', e => { touchStartX = e.changedTouches[0].screenX; }, { passive: true });
        sliderEl.addEventListener('touchend', e => { touchEndX = e.changedTouches[0].screenX; handleSwipe(); }, { passive: true });

        function handleSwipe() {
            if (touchEndX < touchStartX - 50) goNext();
            if (touchEndX > touchStartX + 50) goPrev();
        }
    }

    function changeSlider(direction) {
        return () => {
            let current = {
                slide: document.querySelector(".slide[data-current]"),
                slideInfo: document.querySelector(".slide-info[data-current]"),
                slideBg: document.querySelector(".slide__bg[data-current]")
            };
            let previous = {
                slide: document.querySelector(".slide[data-previous]"),
                slideInfo: document.querySelector(".slide-info[data-previous]"),
                slideBg: document.querySelector(".slide__bg[data-previous]")
            };
            let next = {
                slide: document.querySelector(".slide[data-next]"),
                slideInfo: document.querySelector(".slide-info[data-next]"),
                slideBg: document.querySelector(".slide__bg[data-next]")
            };

            if (!current.slide || !previous.slide || !next.slide) return;

            Object.values(current).map((el) => el.removeAttribute("data-current"));
            Object.values(previous).map((el) => el.removeAttribute("data-previous"));
            Object.values(next).map((el) => el.removeAttribute("data-next"));

            if (direction === 1) {
                let temp = current;
                current = next;
                next = previous;
                previous = temp;
                current.slide.style.zIndex = "20";
                previous.slide.style.zIndex = "30";
                next.slide.style.zIndex = "10";
            } else {
                let temp = current;
                current = previous;
                previous = next;
                next = temp;
                current.slide.style.zIndex = "20";
                previous.slide.style.zIndex = "10";
                next.slide.style.zIndex = "30";
            }

            Object.values(current).map((el) => el.setAttribute("data-current", ""));
            Object.values(previous).map((el) => el.setAttribute("data-previous", ""));
            Object.values(next).map((el) => el.setAttribute("data-next", ""));
        };
    }

    initSlider();
})();

// --- BLOG MODAL JS ---
function openBlogModal(title, content, date, thumb, video, youtube) {
    const modal = document.getElementById('blog-modal');
    if (!modal) return;
    const body = modal.querySelector('.modal-body');

    let mediaHtml = '';
    if (youtube) {
        const vidId = youtube.split('v=')[1]?.split('&')[0] || youtube.split('/').pop();
        mediaHtml = `<div class="aspect-video w-full rounded-[2rem] overflow-hidden mb-8 shadow-2xl">
            <iframe class="w-full h-full" src="https://www.youtube.com/embed/${vidId}" frameborder="0" allowfullscreen></iframe>
        </div>`;
    } else if (video) {
        mediaHtml = `<video src="${video}" controls class="w-full rounded-[2rem] mb-8 shadow-2xl"></video>`;
    } else {
        mediaHtml = `<img src="${thumb}" class="w-full h-auto rounded-[2rem] mb-8 shadow-2xl object-cover max-h-[500px]">`;
    }

    body.innerHTML = `
        <div class="px-6 py-12 md:px-16 md:py-20">
            <div class="flex items-center space-x-4 mb-4">
                <span class="text-[10px] font-black uppercase tracking-[0.3em] text-emerald-600 px-3 py-1 bg-emerald-50 rounded-full border border-emerald-100">Warta Warga</span>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">${date}</span>
            </div>
            <h1 class="text-3xl md:text-5xl font-extrabold text-emerald-950 mb-10 leading-tight">${title}</h1>
            ${mediaHtml}
            <div class="blog-content-area text-gray-600 text-lg leading-relaxed space-y-6 prose prose-emerald max-w-none">
                ${content}
            </div>
        </div>
    `;

    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeBlogModal() {
    const modal = document.getElementById('blog-modal');
    if (!modal) return;
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
    // Clear content to stop video
    setTimeout(() => { modal.querySelector('.modal-body').innerHTML = ''; }, 400);
}

// Close on ESC
window.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeBlogModal(); });
