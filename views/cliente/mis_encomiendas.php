<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1"><i class="fa-solid fa-boxes-packing me-2 text-primary"></i>Mis Encomiendas y Cargas Fluviales</h4>
        <p class="text-muted small mb-0">Rastreo y estado de entrega de tus envíos fluviales</p>
    </div>
    <a href="<?= BASE_URL ?>/cliente/enviar-encomienda" class="btn btn-primary fw-semibold">
        <i class="fa-solid fa-plus me-2"></i>Enviar Nueva Encomienda
    </a>
</div>

<div class="card bg-white shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Número de Guía</th>
                        <th>Descripción Carga</th>
                        <th>Destinatario</th>
                        <th>Trayecto Fluvial</th>
                        <th>Peso / Flete</th>
                        <th>Fecha Itinerario</th>
                        <th>Estado Envío</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($encomiendas)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="display-3 text-muted mb-3"><i class="fa-solid fa-boxes-packing"></i></div>
                                <h6 class="fw-bold text-secondary">No tienes encomiendas registradas aún.</h6>
                                <p class="text-muted small">Realiza tus envíos fluviales de paquetes y mercancía con nosotros.</p>
                                <a href="<?= BASE_URL ?>/cliente/enviar-encomienda" class="btn btn-primary btn-sm mt-2">Enviar Encomienda</a>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($encomiendas as $c): ?>
                            <tr>
                                <td>
                                    <span class="badge bg-light text-dark border fw-bold"><?= htmlspecialchars($c['guia_numero'], ENT_QUOTES, 'UTF-8') ?></span>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark"><?= htmlspecialchars($c['descripcion_carga'], ENT_QUOTES, 'UTF-8') ?></div>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($c['destinatario_nombre'], ENT_QUOTES, 'UTF-8') ?></strong>
                                    <div class="small text-muted"><i class="fa-solid fa-phone me-1"></i><?= htmlspecialchars($c['destinatario_telefono'], ENT_QUOTES, 'UTF-8') ?></div>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($c['origen_nombre'], ENT_QUOTES, 'UTF-8') ?></strong> &rarr; <strong><?= htmlspecialchars($c['destino_nombre'], ENT_QUOTES, 'UTF-8') ?></strong>
                                    <div class="small text-muted"><i class="fa-solid fa-ship me-1"></i><?= htmlspecialchars($c['embarcacion_nombre'], ENT_QUOTES, 'UTF-8') ?></div>
                                </td>
                                <td>
                                    <div><span class="badge bg-light text-dark border"><?= number_format($c['peso_kg'], 1) ?> kg</span></div>
                                    <strong class="text-success">$<?= number_format($c['valor_flete'], 0, ',', '.') ?></strong>
                                </td>
                                <td>
                                    <div><?= $c['fecha_salida'] ?></div>
                                    <small class="text-muted"><?= substr($c['hora_salida'], 0, 5) ?></small>
                                </td>
                                <td>
                                    <?php
                                    $cBadge = match($c['estado']) {
                                        'registrada'  => 'bg-info text-dark',
                                        'cargada'     => 'bg-warning text-dark',
                                        'en_transito' => 'bg-primary',
                                        'entregada'   => 'bg-success',
                                        'cancelada'   => 'bg-danger',
                                        default       => 'bg-secondary'
                                    };
                                    ?>
                                    <span class="badge <?= $cBadge ?> text-uppercase py-1 px-2">
                                        <?php if ($c['estado'] === 'entregada'): ?>
                                            <i class="fa-solid fa-circle-check me-1"></i>Entregado
                                        <?php elseif ($c['estado'] === 'en_transito'): ?>
                                            <i class="fa-solid fa-ship me-1"></i>En Tránsito
                                        <?php elseif ($c['estado'] === 'cargada'): ?>
                                            <i class="fa-solid fa-box-archive me-1"></i>Cargada
                                        <?php else: ?>
                                            <?= str_replace('_', ' ', $c['estado']) ?>
                                        <?php endif; ?>
                                    </span>
                                    <?php if ($c['estado'] === 'entregada' && !empty($c['entregado_at'])): ?>
                                        <div class="small text-success mt-1" style="font-size: 0.72rem;">
                                            <i class="fa-regular fa-calendar-check me-1"></i><?= date('d/m/Y h:i A', strtotime($c['entregado_at'])) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end text-nowrap">
                                    <a href="<?= BASE_URL ?>/cliente/factura-encomienda?id=<?= $c['id'] ?>" target="_blank" class="btn btn-success btn-sm fw-semibold" title="Descargar Factura de Flete en PDF">
                                        <i class="fa-solid fa-file-invoice-dollar me-1"></i>Factura PDF
                                    </a>
                                    <a href="<?= BASE_URL ?>/?guia=<?= urlencode($c['guia_numero']) ?>#rastreo" target="_blank" class="btn btn-outline-primary btn-sm" title="Rastrear Guía en Vivo">
                                        <i class="fa-solid fa-route"></i>
                                    </a>

                                    <!-- Botón de Soporte si no ha llegado o tiene dudas -->
                                    <button type="button" class="btn btn-outline-danger btn-sm fw-semibold btn-soporte-trigger" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalSoporteEncomienda"
                                            data-carga-id="<?= $c['id'] ?>"
                                            data-guia="<?= htmlspecialchars($c['guia_numero'], ENT_QUOTES, 'UTF-8') ?>"
                                            data-descripcion="<?= htmlspecialchars($c['descripcion_carga'], ENT_QUOTES, 'UTF-8') ?>"
                                            data-origen="<?= htmlspecialchars($c['origen_nombre'], ENT_QUOTES, 'UTF-8') ?>"
                                            data-destino="<?= htmlspecialchars($c['destino_nombre'], ENT_QUOTES, 'UTF-8') ?>"
                                            data-estado="<?= htmlspecialchars($c['estado'], ENT_QUOTES, 'UTF-8') ?>"
                                            title="¿No llegó tu encomienda o tienes problemas? Contactar Soporte">
                                        <i class="fa-solid fa-headset me-1"></i><?= $c['estado'] === 'entregada' ? 'Soporte' : '¿No llegó?' ?>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Interactivo de Soporte y Reporte de Encomiendas -->
<div class="modal fade" id="modalSoporteEncomienda" tabindex="-1" aria-labelledby="modalSoporteEncomiendaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-danger text-white py-3">
                <h5 class="modal-title fw-bold" id="modalSoporteEncomiendaLabel">
                    <i class="fa-solid fa-headset me-2"></i>Centro de Ayuda y Soporte Fluvial
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center mb-4">
                    <i class="fa-solid fa-triangle-exclamation fs-3 text-warning me-3"></i>
                    <div>
                        <div class="fw-bold">¿Tu encomienda no ha llegado o tienes una novedad?</div>
                        <span class="small">Estamos comprometidos con la seguridad de tu carga fluvial. Puedes comunicarte directamente por WhatsApp o radicar una incidencia formal aquí.</span>
                    </div>
                </div>

                <!-- Resumen de la Encomienda Seleccionada -->
                <div class="card bg-light border-0 mb-4">
                    <div class="card-body p-3">
                        <div class="row g-2 text-dark small">
                            <div class="col-sm-6">
                                <span class="text-muted d-block">Guía de Encomienda:</span>
                                <strong id="soporteModalGuia" class="text-primary fs-6">N/A</strong>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted d-block">Estado Actual:</span>
                                <span id="soporteModalEstado" class="badge bg-secondary text-uppercase">N/A</span>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted d-block">Trayecto Fluvial:</span>
                                <span id="soporteModalTrayecto" class="fw-semibold">N/A</span>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted d-block">Contenido:</span>
                                <span id="soporteModalContenido" class="fw-semibold">N/A</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <!-- Opción 1: WhatsApp y Canales Rápidos -->
                    <div class="col-md-5 border-end-md">
                        <h6 class="fw-bold text-dark mb-3"><i class="fa-brands fa-whatsapp text-success me-2"></i>Atención Inmediata</h6>
                        <p class="small text-muted mb-3">Chatea en vivo con un agente fluvial de guardia para consultar el estatus en tiempo real:</p>
                        
                        <a href="#" id="soporteBtnWhatsapp" target="_blank" class="btn btn-success w-100 fw-bold mb-3 shadow-sm py-2">
                            <i class="fa-brands fa-whatsapp me-2 fs-5 align-middle"></i>Escribir por WhatsApp
                        </a>

                        <div class="p-3 bg-white rounded border">
                            <div class="small fw-bold text-secondary mb-1"><i class="fa-solid fa-phone me-2 text-primary"></i>Línea Nacional Fluvial:</div>
                            <div class="fw-bold text-dark">+57 (601) 745-9000</div>
                            <small class="text-muted">Lunes a Domingo: 5:00 AM - 9:00 PM</small>
                        </div>
                    </div>

                    <!-- Opción 2: Formulario de Radicación de Incidencia -->
                    <div class="col-md-7">
                        <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-file-pen text-primary me-2"></i>Radicar Ticket / Incidencia</h6>
                        <form action="<?= BASE_URL ?>/cliente/reportar-incidencia" method="POST" id="formReporteSoporte">
                            <input type="hidden" name="carga_id" id="soporteFormCargaId" value="">
                            <input type="hidden" name="guia_numero" id="soporteFormGuiaNumero" value="">

                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Asunto / Motivo del Reporte *</label>
                                <select name="asunto" class="form-select form-select-sm" required>
                                    <option value="La encomienda no ha llegado en la fecha estimada">La encomienda no ha llegado en la fecha estimada</option>
                                    <option value="Paquete marcado entregado pero no lo recibí">Paquete marcado como entregado pero no lo he recibido</option>
                                    <option value="Contenido o mercancía con avería / daño">Contenido o mercancía con avería / daño</option>
                                    <option value="Problemas con el muelle de destino o reclamación">Problemas con el muelle de destino o entrega</option>
                                    <option value="Otra consulta sobre la carga">Otra consulta u observación</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Describe detalladamente lo sucedido *</label>
                                <textarea name="mensaje" class="form-control form-control-sm" rows="3" placeholder="Indica fecha, persona que debía recibir y detalles de la novedad..." required></textarea>
                            </div>

                            <div class="row g-2 mb-3">
                                <div class="col-sm-6">
                                    <label class="form-label small fw-semibold">Teléfono de Contacto *</label>
                                    <input type="text" name="contacto_telefono" class="form-control form-control-sm" value="<?= htmlspecialchars($usuario['telefono'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label small fw-semibold">Correo de Contacto *</label>
                                    <input type="email" name="contacto_email" class="form-control form-control-sm" value="<?= htmlspecialchars($usuario['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-danger btn-sm w-100 fw-bold py-2 shadow-sm">
                                <i class="fa-solid fa-paper-plane me-2"></i>Enviar Reporte a Operaciones Fluviales
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
