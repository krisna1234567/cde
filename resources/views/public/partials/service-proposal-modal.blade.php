<div class="modal fade" id="serviceProposalModal" tabindex="-1" aria-labelledby="serviceProposalTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable modal-fullscreen-md-down">
        <div class="modal-content">
            <div class="modal-header align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <span class="text-danger fs-4 lh-1"><i class="bi bi-file-earmark-pdf-fill"></i></span>
                    <h2 class="modal-title fs-5 mb-0" id="serviceProposalTitle">Service Proposal</h2>
                </div>
                <div class="d-flex align-items-center gap-2 ms-auto me-2">
                    <a data-proposal-download class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1" href="#" download hidden>
                        <i class="bi bi-download"></i><span>Download PDF</span>
                    </a>
                </div>
                <button type="button" class="btn-close ms-0" data-bs-dismiss="modal" aria-label="Tutup proposal"></button>
            </div>
            <div class="modal-body service-proposal-body position-relative">
                <div data-proposal-empty role="status" class="text-center p-5">
                    <i class="bi bi-file-earmark-x text-muted" style="font-size: 3rem;"></i>
                    <h3 class="h5 mt-3">Proposal Belum Tersedia</h3>
                    <p class="text-muted mb-4">Dokumen proposal resmi untuk layanan ini belum diunggah. Silakan hubungi tim kami untuk informasi penawaran lebih lanjut.</p>
                    <a class="btn contact-submit" href="{{ route('contact.index') }}">Hubungi Kami</a>
                </div>
                <div data-proposal-viewer></div>
            </div>
        </div>
    </div>
</div>