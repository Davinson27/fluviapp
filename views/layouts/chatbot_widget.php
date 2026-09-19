<?php
// =======================================================
// Componente: Widget Chatbot Fluvial IA 2.0 - FluviApp
// Botón flotante y ventana de chat interactiva
// =======================================================
?>

<div id="fluviapp-chat-container" class="position-fixed bottom-0 end-0 p-3" style="z-index: 1080;">
    <!-- Ventana del Chat (Oculta por defecto) -->
    <div id="fluviapp-chat-window" class="card shadow-lg border-0 rounded-4 overflow-hidden d-none mb-2" style="width: 340px; max-width: 90vw; height: 460px; display: flex; flex-direction: column;">
        <div class="card-header bg-gradient text-white py-3 px-3 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);">
            <div class="d-flex align-items-center gap-2">
                <div class="p-2 bg-white text-primary rounded-circle shadow-sm" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                    <i class="fa-solid fa-robot" style="font-size: 14px;"></i>
                </div>
                <div>
                    <strong class="d-block small lh-1">Asistente Fluvial IA</strong>
                    <span class="text-white-50" style="font-size: 10px;"><i class="fa-solid fa-circle text-success me-1"></i>En línea &bull; FluviApp v2.0</span>
                </div>
            </div>
            <button type="button" class="btn-close btn-close-white" onclick="toggleChatWindow()" aria-label="Cerrar"></button>
        </div>

        <!-- Historial de Mensajes -->
        <div id="fluviapp-chat-messages" class="card-body p-3 overflow-y-auto flex-grow-1 bg-light small" style="display: flex; flex-direction: column; gap: 10px;">
            <div class="d-flex align-items-start gap-2">
                <div class="p-2 bg-white text-dark rounded-3 shadow-sm border" style="max-width: 85%;">
                    ¡Hola! 🚢 Soy el Asistente Virtual de <strong>FluviApp</strong>. ¿En qué te puedo orientar hoy? Puedes preguntarme por rutas, horarios, pasajes o cotización de carga.
                </div>
            </div>
        </div>

        <!-- Chips de Preguntas Frecuentes -->
        <div class="px-2 py-1 bg-white border-top d-flex gap-1 overflow-x-auto" style="white-space: nowrap; scrollbar-width: none;">
            <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2" style="font-size: 11px;" onclick="enviarChip('¿Cuáles son los horarios de viaje?')">Horarios</button>
            <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2" style="font-size: 11px;" onclick="enviarChip('¿Cuánto vale el pasaje?')">Precios</button>
            <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2" style="font-size: 11px;" onclick="enviarChip('¿Cómo enviar una encomienda?')">Carga</button>
            <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2" style="font-size: 11px;" onclick="enviarChip('¿Cómo rastreo mi lancha?')">Rastreo</button>
        </div>

        <!-- Input de Mensaje -->
        <div class="card-footer bg-white p-2 border-top">
            <form id="fluviapp-chat-form" onsubmit="enviarMensajeChat(event)" class="d-flex gap-2">
                <input type="text" id="chat_user_input" class="form-control form-control-sm" placeholder="Escribe tu consulta..." autocomplete="off" required>
                <button type="submit" class="btn btn-primary btn-sm px-3" id="btn_send_chat">
                    <i class="fa-solid fa-paper-plane"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- Botón Flotante para Abrir Chat -->
    <button type="button" id="fluviapp-chat-toggle-btn" class="btn btn-primary rounded-circle shadow-lg d-flex align-items-center justify-content-center ms-auto" style="width: 56px; height: 56px;" onclick="toggleChatWindow()">
        <i class="fa-solid fa-comment-dots fs-3"></i>
    </button>
</div>

<script>
function toggleChatWindow() {
    const win = document.getElementById('fluviapp-chat-window');
    win.classList.toggle('d-none');
    if (!win.classList.contains('d-none')) {
        document.getElementById('chat_user_input').focus();
    }
}

function enviarChip(texto) {
    document.getElementById('chat_user_input').value = texto;
    enviarMensajeChat(new Event('submit'));
}

function enviarMensajeChat(e) {
    e.preventDefault();
    const input = document.getElementById('chat_user_input');
    const msg = input.value.trim();
    if (!msg) return;

    input.value = '';
    const container = document.getElementById('fluviapp-chat-messages');

    // Agregar mensaje del usuario
    container.innerHTML += `
        <div class="d-flex align-items-end justify-content-end gap-2">
            <div class="p-2 bg-primary text-white rounded-3 shadow-sm" style="max-width: 85%;">
                ${msg}
            </div>
        </div>
    `;
    container.scrollTop = container.scrollHeight;

    // Indicador de escribiendo...
    const typingId = 'typing_' + Date.now();
    container.innerHTML += `
        <div id="${typingId}" class="d-flex align-items-start gap-2">
            <div class="p-2 bg-white text-muted rounded-3 shadow-sm border" style="font-size: 11px;">
                <span class="spinner-grow spinner-grow-sm me-1" style="width: 8px; height: 8px;"></span> Consultando...
            </div>
        </div>
    `;
    container.scrollTop = container.scrollHeight;

    fetch('<?= BASE_URL ?>/api/chatbot/consultar', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ pregunta: msg })
    })
    .then(r => r.json())
    .then(res => {
        const typingEl = document.getElementById(typingId);
        if (typingEl) typingEl.remove();

        container.innerHTML += `
            <div class="d-flex align-items-start gap-2">
                <div class="p-2 bg-white text-dark rounded-3 shadow-sm border" style="max-width: 85%;">
                    ${res.respuesta || 'En este momento no pude procesar tu solicitud.'}
                </div>
            </div>
        `;
        container.scrollTop = container.scrollHeight;
    })
    .catch(() => {
        const typingEl = document.getElementById(typingId);
        if (typingEl) typingEl.remove();

        container.innerHTML += `
            <div class="d-flex align-items-start gap-2">
                <div class="p-2 bg-danger-subtle text-danger rounded-3 shadow-sm border" style="max-width: 85%;">
                    Hubo un problema de conexión. Intenta de nuevo.
                </div>
            </div>
        `;
        container.scrollTop = container.scrollHeight;
    });
}
</script>
