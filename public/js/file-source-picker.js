(function () {
    function isMediaCapableInput(input) {
        if (!input || input.type !== 'file' || input.disabled) {
            return false;
        }

        var accept = (input.getAttribute('accept') || '').toLowerCase();
        if (!accept) {
            return false;
        }

        return accept.indexOf('image/') !== -1 || accept.indexOf('video/') !== -1;
    }

    function askSourceWithSwal() {
        return Swal.fire({
            title: 'Pilih sumber file',
            text: 'Gunakan kamera langsung atau pilih dari lampiran file.',
            icon: 'question',
            showCancelButton: true,
            showDenyButton: true,
            confirmButtonText: 'Kamera',
            denyButtonText: 'Lampiran',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then(function (result) {
            if (result.isConfirmed) return 'camera';
            if (result.isDenied) return 'file';
            return 'cancel';
        });
    }

    function askSourceWithConfirm() {
        var useCamera = window.confirm('Pilih sumber file. Tekan OK untuk Kamera, atau Cancel untuk Lampiran file.');
        return Promise.resolve(useCamera ? 'camera' : 'file');
    }

    function askSource() {
        if (typeof Swal !== 'undefined' && Swal && typeof Swal.fire === 'function') {
            return askSourceWithSwal();
        }
        return askSourceWithConfirm();
    }

    function openInputWithSource(input, source) {
        if (source === 'cancel') {
            return;
        }

        if (source === 'camera') {
            input.setAttribute('capture', 'environment');
        } else {
            input.removeAttribute('capture');
        }

        input.dataset.fileSourceBypass = '1';
        input.click();
    }

    document.addEventListener('click', function (event) {
        var input = event.target && event.target.closest ? event.target.closest('input[type="file"]') : null;
        if (!isMediaCapableInput(input)) {
            return;
        }

        if (input.dataset.fileSourceBypass === '1') {
            delete input.dataset.fileSourceBypass;
            return;
        }

        event.preventDefault();
        event.stopPropagation();

        askSource().then(function (source) {
            openInputWithSource(input, source);
        });
    }, true);
})();