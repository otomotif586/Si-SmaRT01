    <!-- Blog Modal Structure -->
    <div id="blog-modal" class="fixed inset-0 z-[200] hidden flex items-center justify-center p-4 md:p-8">
        <div class="absolute inset-0 bg-emerald-950/70 backdrop-blur-xl" onclick="closeBlogModal()"></div>
        <button onclick="closeBlogModal()" class="absolute top-6 right-6 z-[210] w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-emerald-950 shadow-2xl hover:scale-110 active:scale-95 transition-transform">
            <i class="fas fa-times text-xl"></i>
        </button>
        <div class="relative w-full max-w-5xl max-h-[90vh] bg-[#fdfaf3] rounded-[3rem] shadow-3xl overflow-y-auto reveal-modal modal-body">
            <!-- Content will be injected here -->
        </div>
    </div>
