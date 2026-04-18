<?php
// Ambil judul bersih (hanya simpan <br> jika ada)
$raw_title = $settingsData['web_hero_title'] ?? "Kampung Impian <br> Kini Jadi Nyata.";
$clean_title = strip_tags($raw_title, '<br>');
?>
<style>
    /* Styling Dasar Stacking Cards */
    .hero-stack-wrapper {
        background-color: transparent;
        color: #111;
        font-family: 'Space Grotesk', sans-serif;
    }

    /* --- 1. Hero Section --- */
    .hero-main {
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        position: relative;
        z-index: 10;
        padding: 0 20px;
    }

    .reveal-text {
        font-size: 5vw;
        line-height: 1.1;
        text-align: center;
        clip-path: polygon(0 100%, 100% 100%, 100% 100%, 0% 100%);
        opacity: 0;
        transform: translateY(50px);
        animation: textReveal 1.2s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        color: #064e3b; /* emerald-950 */
    }

    .reveal-text span {
        color: #10b981; /* emerald-500 */
    }

    /* --- 2. Stacking Cards Container --- */
    .stack-area {
        padding-bottom: 20vh;
        max-width: 1100px;
        margin: 0 auto;
        position: relative;
        padding-left: 20px;
        padding-right: 20px;
    }

    /* --- 3. The Card Styling --- */
    .card-stack {
        height: 450px;
        position: sticky;
        top: 15vh;
        background: #111;
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 40px;
        padding: 40px;
        margin-bottom: 15vh;
        display: flex;
        align-items: center;
        justify-content: space-between;
        overflow: hidden;
        backdrop-filter: blur(20px);
        box-shadow: 0 -20px 50px rgba(0, 0, 0, 0.5);
        transform-origin: center top;
        transition: transform 0.4s cubic-bezier(0.2, 1, 0.3, 1), filter 0.4s ease;
        will-change: transform;
    }

    .card-stack:nth-child(1) { background: linear-gradient(145deg, #121212, #080808); }
    .card-stack:nth-child(2) { background: linear-gradient(145deg, #1a1a1a, #0d0d0d); }
    .card-stack:nth-child(3) { background: linear-gradient(145deg, #222222, #111111); }

    .card-stack-content {
        flex: 1.2;
        padding-right: 40px;
        z-index: 2;
    }

    .card-stack h2 {
        font-size: 3.5rem;
        margin-bottom: 15px;
        text-transform: uppercase;
        font-weight: 800;
        line-height: 1;
        letter-spacing: -2px;
    }

    .card-stack .card-num {
        font-size: 1rem;
        color: #10b981;
        margin-bottom: 25px;
        display: block;
        font-weight: bold;
        letter-spacing: 4px;
    }

    .card-stack p {
        font-size: 1.2rem;
        color: #94a3b8;
        max-width: 450px;
        line-height: 1.6;
    }

    .card-stack-img {
        flex: 1;
        height: 100%;
        background: #000;
        border-radius: 24px;
        position: relative;
        overflow: hidden;
    }

    .card-stack-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: 0.7;
        transition: transform 0.8s cubic-bezier(0.2, 1, 0.3, 1);
    }

    .card-stack:hover .card-stack-img img {
        transform: scale(1.1);
        opacity: 1;
    }

    @keyframes textReveal {
        to {
            clip-path: polygon(0 0, 100% 0, 100% 100%, 0% 100%);
            opacity: 1;
            transform: translateY(0);
        }
    }

    @media (max-width: 768px) {
        .reveal-text { font-size: 3rem; }
        .card-stack {
            flex-direction: column;
            height: auto;
            min-height: 550px;
            padding: 30px;
            top: 10vh;
            margin-bottom: 8vh;
            border-radius: 30px;
        }
        .card-stack-content { padding-right: 0; margin-bottom: 30px; }
        .card-stack h2 { font-size: 2.2rem; }
        .card-stack-img { height: 280px; width: 100%; }
    }
</style>

<div class="hero-stack-wrapper" data-parallax-section>
    <!-- Hero Section -->
    <section class="hero-main" data-parallax-speed="0.06">
        <div class="inline-flex items-center space-x-3 px-5 py-3 mb-8 rounded-full bg-emerald-600/10 border border-emerald-600/20 text-emerald-400 text-[10px] font-bold tracking-[0.2em] uppercase backdrop-blur-md" data-parallax-speed="0.06" data-parallax-mode="scroll">
            <span class="flex h-2 w-2 rounded-full bg-emerald-500 animate-ping"></span>
            <span>RT 001 Go-Digital</span>
        </div>
        
        <h1 class="reveal-text font-black">
            <?= str_replace('<br>', '<br><span>', $clean_title) . '</span>' ?>
        </h1>
        
        <p class="mt-8 text-lg md:text-xl text-emerald-900/60 leading-relaxed max-w-2xl text-center font-medium opacity-0 translate-y-10 animate-[textReveal_1s_ease_0.3s_forwards]">
            <?= nl2br(htmlspecialchars($web_visi)) ?>
        </p>
        
        <div class="flex flex-col sm:flex-row justify-center gap-4 sm:gap-6 w-full max-w-xs sm:max-w-none mx-auto pt-12 opacity-0 translate-y-10 animate-[textReveal_1s_ease_0.6s_forwards] hero-btn-group">
            <a href="pasar.php" class="group relative px-6 sm:px-10 py-4 sm:py-5 bg-emerald-600 text-white font-bold rounded-2xl flex items-center justify-center space-x-3 hover:bg-emerald-500 transition-all shadow-xl shadow-emerald-900/20 w-full sm:w-auto">
                <i class="fa-solid fa-shop text-xl"></i>
                <span class="tracking-tight uppercase whitespace-nowrap">Pasar Warga</span>
            </a>
            <a href="https://www.google.com/maps/place/Bimbel+Become/@-6.4617173,106.9727219,3a,73.9y,173.83h,88.65t/data=!3m7!1e1!3m5!1s0-jsU8IuF6zRD2dAo4a4pQ!2e0!6shttps:%2F%2Fstreetviewpixels-pa.googleapis.com%2Fv1%2Fthumbnail%3Fcb_client%3Dmaps_sv.tactile%26w%3D900%26h%3D600%26pitch%3D1.3543519995511133%26panoid%3D0-jsU8IuF6zRD2dAo4a4pQ%26yaw%3D173.82917027237332!7i16384!8i8192!4m6!3m5!1s0x2e69bf52d8d30d3b:0x94ee6f0e357a0db5!8m2!3d-6.4600501!4d106.9744559!16s%2Fg%2F11y2dtt465?entry=ttu&g_ep=EgoyMDI2MDQwNy4wIKXMDSoASAFQAw%3D%3D" target="_blank" class="px-6 sm:px-10 py-4 sm:py-5 bg-white border border-emerald-100 text-emerald-900 font-bold rounded-2xl hover:bg-emerald-50 transition-all flex items-center justify-center space-x-3 shadow-sm w-full sm:w-auto">
                <i class="fas fa-play-circle text-emerald-500 text-xl"></i>
                <span class="whitespace-nowrap">TUR KAWASAN</span>
            </a>
        </div>

        <div class="relative mt-12 sm:absolute sm:bottom-12 sm:mt-0 left-1/2 -translate-x-1/2 flex flex-col items-center gap-2 opacity-40 text-emerald-900 scroll-indicator" data-parallax-speed="0.1" data-parallax-mode="scroll">
            <span class="text-[10px] uppercase tracking-[0.3em] font-bold">Scroll Down</span>
            <div class="w-px h-12 bg-gradient-to-b from-emerald-500 to-transparent"></div>
        </div>
    </section>

    <!-- Stacking Cards area -->
    <div class="stack-area">
        <?php foreach($slides as $i => $s): ?>
        <div class="card-stack js-card">
            <div class="card-stack-content">
                <span class="card-num">SCENE 0<?= $i ?></span>
                <h2 class="text-white"><?= htmlspecialchars($s['title']) ?></h2>
                <p><?= htmlspecialchars($s['description']) ?></p>
            </div>
            <div class="card-stack-img parallax-media-shell" data-parallax-media-speed="0.16">
                <img src="<?= htmlspecialchars($s['image']) ?>" alt="<?= htmlspecialchars($s['title']) ?>" data-parallax-media-speed="0.18">
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const cards = document.querySelectorAll('.js-card');
        
        const handleScroll = () => {
            const viewportHeight = window.innerHeight;
            const isMobile = window.innerWidth <= 768;
            const stickyTopOffset = isMobile ? viewportHeight * 0.10 : viewportHeight * 0.15;

            cards.forEach((card, index) => {
                const nextCard = cards[index + 1];

                if (nextCard) {
                    const nextRect = nextCard.getBoundingClientRect();
                    const distance = nextRect.top - stickyTopOffset;

                    if (distance < viewportHeight && distance > 0) {
                        const maxShrink = isMobile ? 0.95 : 0.90;
                        const factor = (1 - maxShrink) / viewportHeight;
                        const scale = 1 - ((viewportHeight - distance) * factor);
                        
                        const finalScale = Math.max(maxShrink, Math.min(1, scale));
                        const brightness = Math.max(0.6, Math.min(1, scale));

                        card.style.transform = `scale(${finalScale})`;
                        card.style.filter = `brightness(${brightness})`;
                    } else if (distance <= 0) {
                        const maxShrink = isMobile ? 0.95 : 0.90;
                        card.style.transform = `scale(${maxShrink})`;
                        card.style.filter = `brightness(0.6)`;
                    } else {
                        card.style.transform = `scale(1)`;
                        card.style.filter = `brightness(1)`;
                    }
                }
            });
        };

        window.addEventListener('scroll', handleScroll);
        // Trigger once to set initial state
        handleScroll();
    });
</script>
