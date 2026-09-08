    </div>
    <!-- /.container-fluid -->

    <!-- Footer -->
    <footer class="bg-white border-top text-center py-3 text-muted small mt-auto">
        <div class="container">
            &copy; <?= date('Y') ?> <strong><?= APP_NAME ?></strong> - <?= APP_TAGLINE ?> | Versión <?= APP_VERSION ?>
        </div>
    </footer>
</div>
<!-- /#page-content-wrapper -->

</div>
<!-- /#wrapper -->

<!-- Modal Visor de Imágenes Ampliadas (Logo y Foto de Perfil) -->
<div class="modal fade" id="imageViewerModal" tabindex="-1" aria-labelledby="imageViewerTitle" aria-hidden="true" style="z-index: 1080;">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content text-white border border-secondary shadow-2xl" style="background: linear-gradient(180deg, #0b1728 0%, #060d17 100%) !important; border-radius: 20px; overflow: hidden;">
            <div class="modal-header border-secondary py-3 px-4 d-flex justify-content-between align-items-center" style="border-color: rgba(255, 255, 255, 0.1) !important;">
                <div class="d-flex align-items-center gap-2">
                    <i class="fa-solid fa-magnifying-glass-plus text-info fs-5"></i>
                    <h5 class="modal-title fw-bold text-white mb-0" id="imageViewerTitle">Vista Previa Ampliada</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body text-center p-3 p-md-4 d-flex flex-column align-items-center justify-content-center" style="min-height: 300px; background: rgba(0, 0, 0, 0.4);">
                <div class="position-relative d-inline-block">
                    <img id="imageViewerImg" src="" alt="Vista Previa" class="img-fluid rounded-4 shadow-lg" style="max-height: 70vh; max-width: 100%; object-fit: contain; border: 3px solid rgba(14, 165, 233, 0.4);">
                </div>
                <div id="imageViewerCaption" class="mt-3 text-secondary small fw-semibold"></div>
            </div>
            <div class="modal-footer border-secondary py-2 px-4 d-flex justify-content-end align-items-center" style="border-color: rgba(255, 255, 255, 0.1) !important;">
                <button type="button" class="btn btn-secondary btn-sm px-4 fw-semibold" data-bs-dismiss="modal">
                    <i class="fa-solid fa-xmark me-1"></i>Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Custom JS -->
<script src="<?= BASE_URL ?>/public/js/app.js?v=<?= file_exists(ROOT_PATH . '/public/js/app.js') ? filemtime(ROOT_PATH . '/public/js/app.js') : time() ?>"></script>
</body>
</html>
