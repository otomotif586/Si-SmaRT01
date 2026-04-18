    <!-- Blog Modal Structure -->
    <div id="blog-modal" class="blog-modal" data-state="closed" aria-hidden="true">
        <div class="blog-modal__backdrop" data-blog-close></div>
        <div class="blog-modal__panel" role="dialog" aria-modal="true" aria-labelledby="blog-modal-title">
            <button type="button" class="blog-modal__close" data-blog-close aria-label="Tutup berita">
                <i class="fas fa-times"></i>
            </button>

            <div class="modal-scroll blog-modal__scroll">
                <div class="blog-modal__shell">
                    <div class="blog-modal__meta">
                        <span class="blog-modal__eyebrow">Warta Warga</span>
                        <span id="blog-modal-date" class="blog-modal__date">-</span>
                    </div>
                    <h1 id="blog-modal-title" class="blog-modal__title">Berita Warga</h1>
                    <div id="blog-modal-media" class="blog-modal__media"></div>
                    <div id="blog-modal-content" class="blog-modal__content"></div>
                </div>
            </div>
        </div>
    </div>
