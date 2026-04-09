// --- GSAP Infinity Gallery Logic ---
let galleryGsapContext;

function openGsapGallery(el) {
    // Pendaftaran Plugin GSAP
    gsap.registerPlugin(ScrollTrigger);
    
    document.getElementById('gsap-gallery-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    document.querySelector('.gsap-loader').style.opacity = 1;

    // Dapatkan array gambar yang diklik dan perbanyak agar efek loop 3D terasa panjang
    const originalImages = JSON.parse(decodeURIComponent(el.getAttribute('data-gallery')));
    let imageUrls = [];
    while (imageUrls.length < 12) { imageUrls = imageUrls.concat(originalImages); }

    const cardContainer = document.querySelector('.gsap-cards');
    cardContainer.innerHTML = ''; 
    const totalCards = imageUrls.length;

    for (let i = 0; i < totalCards; i++) {
        const li = document.createElement('li');
        li.className = 'gsap-card';
        const isVideo = imageUrls[i].match(/\.(mp4|webm|ogg)$/i) != null;
        if(isVideo) {
            li.style.backgroundColor = '#111';
            li.innerHTML = `<video src="${imageUrls[i]}" autoplay loop muted playsinline style="width:100%; height:100%; object-fit:cover; position:absolute; top:0; left:0; z-index:0;"></video><div class="gsap-card-content" style="z-index: 10;"><span class="gsap-card-num">VIDEO ${i + 1}</span><h3 class="gsap-card-title">Galeri Kegiatan</h3></div>`;
        } else {
            li.style.backgroundImage = `url(${imageUrls[i]})`;
            li.innerHTML = `<div class="gsap-card-content"><span class="gsap-card-num">FOTO ${i + 1}</span><h3 class="gsap-card-title">Galeri Kegiatan</h3></div>`;
        }
        cardContainer.appendChild(li);
    }

    const bgImages = [document.getElementById('gsap-bg1'), document.getElementById('gsap-bg2')];
    let currentBgIndex = 0;
    const isFirstVideo = imageUrls[0].match(/\.(mp4|webm|ogg)$/i) != null;
    
    bgImages[0].style.backgroundImage = isFirstVideo ? 'none' : `url(${imageUrls[0]})`;
    if(isFirstVideo) bgImages[0].style.backgroundColor = '#000';
    
    bgImages[0].classList.add('active');
    bgImages[1].classList.remove('active');

    // Bersihkan instansi GSAP sebelumnya jika modal pernah dibuka
    if (galleryGsapContext) galleryGsapContext.revert();

    galleryGsapContext = gsap.context(() => {
        const spacing = 0.1;
        const cards = gsap.utils.toArray(".gsap-cards li");
        const seamlessLoop = buildSeamlessLoop(cards, spacing);

        const scrub = gsap.to(seamlessLoop, { totalTime: 0, duration: 0.5, ease: "power1.out", paused: true });
        let iteration = 0; let activeCardIndex = 0;
        const scroller = document.querySelector('.gsap-scroll-container');
        scroller.scrollTop = 0; // Reset scroll palsu

        const trigger = ScrollTrigger.create({
            scroller: scroller,
            start: 0, end: "+=6000", scrub: 1,
            onUpdate(self) {
                if (self.progress === 1 && self.direction > 0 && !self.wrapping) {
                    iteration++; self.wrapping = true; self.scroll(self.start + 1);
                } else if (self.progress < 1e-5 && self.direction < 0 && !self.wrapping) {
                    iteration--;
                    if (iteration < 0) { iteration = 9; seamlessLoop.totalTime(seamlessLoop.totalTime() + seamlessLoop.duration() * 10); scrub.pause(); }
                    self.wrapping = true; self.scroll(self.end - 1);
                } else {
                    const scrubTime = (iteration + self.progress) * seamlessLoop.duration();
                    const snappedTime = gsap.utils.snap(spacing, scrubTime);
                    scrub.vars.totalTime = snappedTime; scrub.invalidate().restart();
                    self.wrapping = false;
                    
                    // Update Active Card & Background
                    const totalDuration = seamlessLoop.duration();
                    let index = Math.round(((scrubTime % totalDuration) / totalDuration) * totalCards) % totalCards;
                    if (index < 0) index = totalCards + index;
                    if (index !== activeCardIndex) {
                        activeCardIndex = index;
                        cards.forEach((c, i) => { if (i === index) c.classList.add('active'); else c.classList.remove('active'); });
                        const nextBgIndex = (currentBgIndex + 1) % 2;
                        const nextBg = bgImages[nextBgIndex]; const currentBg = bgImages[currentBgIndex];
                        
                        const isVid = imageUrls[index].match(/\.(mp4|webm|ogg)$/i) != null;
                        if (isVid) {
                            nextBg.style.backgroundImage = 'none'; nextBg.style.backgroundColor = '#000';
                            nextBg.classList.add('active'); currentBg.classList.remove('active'); currentBgIndex = nextBgIndex;
                        } else {
                            const img = new Image(); img.src = imageUrls[index];
                            img.onload = () => { nextBg.style.backgroundImage = `url(${imageUrls[index]})`; nextBg.classList.add('active'); currentBg.classList.remove('active'); currentBgIndex = nextBgIndex; }
                        }
                    }
                }
            }
        });

        function scrubTo(totalTime) {
            let progress = (totalTime - seamlessLoop.duration() * iteration) / seamlessLoop.duration();
            if (progress > 1) { iteration++; trigger.wrapping = true; trigger.scroll(trigger.start + 1); } 
            else if (progress < 0) { iteration--; if (iteration < 0) { iteration = 9; seamlessLoop.totalTime(seamlessLoop.totalTime() + seamlessLoop.duration() * 10); scrub.pause(); } trigger.wrapping = true; trigger.scroll(trigger.end - 1); } 
            else { trigger.scroll(trigger.start + progress * (trigger.end - trigger.start)); }
        }

        document.querySelector(".gsap-next").addEventListener("click", () => scrubTo(scrub.vars.totalTime + spacing));
        document.querySelector(".gsap-prev").addEventListener("click", () => scrubTo(scrub.vars.totalTime - spacing));

        let touchStartX = 0; let touchEndX = 0;
        scroller.addEventListener('touchstart', e => { touchStartX = e.changedTouches[0].screenX; }, { passive: true });
        scroller.addEventListener('touchend', e => { touchEndX = e.changedTouches[0].screenX; const limit = 50; if (touchStartX - touchEndX > limit) scrubTo(scrub.vars.totalTime + spacing); if (touchEndX - touchStartX > limit) scrubTo(scrub.vars.totalTime - spacing); }, { passive: true });

        cards[0].classList.add('active');

        function buildSeamlessLoop(items, spacing) {
            let overlap = Math.ceil(1 / spacing), startTime = items.length * spacing + 0.5, loopTime = (items.length + overlap) * spacing + 1,
                rawSequence = gsap.timeline({ paused: true }), seamlessLoop = gsap.timeline({ paused: true, repeat: -1, onRepeat() { this._time === this._dur && (this._tTime += this._dur - 0.01); } }),
                l = items.length + overlap * 2, time = 0, i, index, item;
            gsap.set(items, { xPercent: 400, autoAlpha: 0, scale: 0 });
            const blurVal = window.innerWidth < 768 ? "0px" : "4px";
            for (i = 0; i < l; i++) {
                index = i % items.length; item = items[index]; time = i * spacing;
                rawSequence.fromTo(item, { scale: 0.5, autoAlpha: 0.3, zIndex: 1, filter: `blur(${blurVal})` }, { scale: 1.5, autoAlpha: 1, zIndex: 100, filter: "blur(0px)", duration: 0.5, yoyo: true, repeat: 1, ease: "sine.inOut", immediateRender: false }, time)
                           .fromTo(item, { xPercent: 450 }, { xPercent: -450, duration: 1, ease: "none", immediateRender: false }, time);
                i <= items.length && seamlessLoop.add("label" + i, time);
            }
            rawSequence.time(startTime);
            seamlessLoop.to(rawSequence, { time: loopTime, duration: loopTime - startTime, ease: "none" }).fromTo(rawSequence, { time: overlap * spacing + 1 }, { time: startTime, duration: startTime - (overlap * spacing + 1), immediateRender: false, ease: "none" });
            return seamlessLoop;
        }
    }); // Akhir Context

    setTimeout(() => { document.querySelector('.gsap-loader').style.opacity = 0; }, 500);
}

function closeGsapGallery() {
    document.getElementById('gsap-gallery-modal').classList.add('hidden');
    document.body.style.overflow = '';
    if (galleryGsapContext) galleryGsapContext.revert();
}
