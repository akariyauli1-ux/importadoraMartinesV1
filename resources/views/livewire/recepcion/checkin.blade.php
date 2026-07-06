<div>
    @section('title', 'Ingreso de Equipo (Check-in)')

    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Registrar Nuevo Ingreso de Equipo</h4>
        </div>
        <div class="card-body">
            @if (session()->has('info_cliente'))
                <div class="alert alert-warning" style="background-color: rgba(0, 123, 255, 0.1); border-color: rgba(0, 123, 255, 0.3); color: #007bff;">
                    <span>{{ session('info_cliente') }}</span>
                </div>
            @endif

            <form wire:submit.prevent="save" id="checkin-form">
                
                <!-- 2-Column Responsive Layout -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; align-items: start;">
                    
                    <!-- Left: Client Information -->
                    <div>
                        <h4 style="border-bottom: 2px solid var(--border-light); padding-bottom: 10px; margin-bottom: 20px; color: var(--color-text-dark); font-weight: 700;">
                            Datos del Cliente
                        </h4>
                        
                        <div class="form-group">
                            <label class="form-label" style="color: var(--color-text-dark);">Carnet de Identidad (C.I.)</label>
                            <input wire:model.lazy="cliente_ci" type="text" class="form-control form-control-light" placeholder="Ej. 6543210" required>
                            @error('cliente_ci') <span class="error-message">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" style="color: var(--color-text-dark);">Nombre Completo</label>
                            <input wire:model.defer="cliente_nombre" type="text" class="form-control form-control-light" placeholder="Ej. Pedro Picapiedra" required>
                            @error('cliente_nombre') <span class="error-message">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" style="color: var(--color-text-dark);">Número de Celular / WhatsApp</label>
                            <input wire:model.defer="cliente_telefono" type="text" class="form-control form-control-light" placeholder="Ej. 78945612" required>
                            <span style="font-size: 0.78rem; color: var(--color-text-light-muted); display: block; margin-top: 4px;">
                                Se usará para enviar alertas automáticas gratuitas de estado de orden.
                            </span>
                            @error('cliente_telefono') <span class="error-message">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" style="color: var(--color-text-dark);">Correo Electrónico (Opcional)</label>
                            <input wire:model.defer="cliente_email" type="email" class="form-control form-control-light" placeholder="Ej. pedro@cliente.com">
                            @error('cliente_email') <span class="error-message">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" style="color: var(--color-text-dark);">Dirección (Opcional)</label>
                            <input wire:model.defer="cliente_direccion" type="text" class="form-control form-control-light" placeholder="Ej. Av. Blanco Galindo Km 4">
                            @error('cliente_direccion') <span class="error-message">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Right: Device details -->
                    <div>
                        <h4 style="border-bottom: 2px solid var(--border-light); padding-bottom: 10px; margin-bottom: 20px; color: var(--color-text-dark); font-weight: 700;">
                            Detalles del Equipo
                        </h4>

                        <div class="form-group">
                            <label class="form-label" style="color: var(--color-text-dark);">Categoría de Dispositivo</label>
                            <select wire:model="categoria" class="form-control form-control-light" required style="padding: 10px;">
                                <option value="celular">Celular / Tablet</option>
                                <option value="laptop">Laptop / Notebook</option>
                                <option value="electrico">Equipo Audiovisual / Eléctrico</option>
                                <option value="cpu">Computadora de Escritorio (CPU)</option>
                            </select>
                            @error('categoria') <span class="error-message">{{ $message }}</span> @enderror
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                            <div class="form-group">
                                <label class="form-label" style="color: var(--color-text-dark);">Marca</label>
                                <input wire:model.defer="marca" type="text" class="form-control form-control-light" placeholder="Ej. Samsung / HP" required>
                                @error('marca') <span class="error-message">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label" style="color: var(--color-text-dark);">Modelo</label>
                                <input wire:model.defer="modelo" type="text" class="form-control form-control-light" placeholder="Ej. S23 Ultra / Pavilion" required>
                                @error('modelo') <span class="error-message">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" style="color: var(--color-text-dark);">Número de Serie (Opcional)</label>
                            <input wire:model.defer="serie" type="text" class="form-control form-control-light" placeholder="Ej. SN1234567890">
                            @error('serie') <span class="error-message">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" style="color: var(--color-text-dark);">Falla / Problema Reportado</label>
                            <textarea wire:model.defer="problema_reportado" class="form-control form-control-light" rows="3" placeholder="Ej. Pantalla rota, no enciende, mojado..." required></textarea>
                            @error('problema_reportado') <span class="error-message">{{ $message }}</span> @enderror
                        </div>

                        <!-- Photos / Waiver selection -->
                        <div class="form-group" style="background-color: #fafafa; padding: 15px; border-radius: 8px; border: 1px solid var(--border-light);">
                            <label class="form-label" style="color: var(--color-text-dark); font-weight: 600;">Evidencia Fotográfica (Anverso/Reverso)</label>
                            <input wire:model="fotos_evidencia" type="file" class="form-control form-control-light" style="padding: 6px;" multiple accept="image/*">
                            @error('fotos_evidencia') <span class="error-message" style="display: block; margin-top: 5px;">{{ $message }}</span> @enderror
                            
                            <div style="display: flex; align-items: center; gap: 10px; margin-top: 12px;">
                                <input wire:model="sin_fotos_motivo" type="checkbox" id="sin_fotos_motivo" style="width: 18px; height: 18px; cursor: pointer;">
                                <label for="sin_fotos_motivo" style="cursor: pointer; font-size: 0.85rem; color: var(--color-text-dark); font-weight: 500;">
                                    El cliente rechaza tomar fotografías (Se requiere firma digital de descargo)
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Digital Signature section -->
                <div style="margin-top: 30px; border-top: 2px solid var(--border-light); padding-top: 20px; display: flex; flex-direction: column; align-items: center;">
                    <h4 style="color: var(--color-text-dark); font-weight: 700; margin-bottom: 10px;">Firma Digital del Cliente (Check-in)</h4>
                    <p style="color: var(--color-text-light-muted); font-size: 0.85rem; margin-bottom: 15px; text-align: center; max-width: 500px;">
                        El cliente firma para autorizar el ingreso del equipo bajo las condiciones declaradas y las cláusulas legales vigentes de Importadora Martinez.
                    </p>

                    <!-- Hidden field to bind base64 string -->
                    <input type="hidden" id="firma_checkin_hidden" wire:model.defer="firma_checkin">
                    
                    <div class="sig-pad-wrapper">
                        <canvas class="sig-pad-canvas" id="signature-canvas"></canvas>
                    </div>

                    <div style="display: flex; gap: 10px; margin-bottom: 20px;">
                        <button type="button" id="clear-sig-btn" class="btn btn-secondary" style="width: auto; padding: 6px 15px; font-size: 0.85rem;">
                            🧼 Limpiar Firma
                        </button>
                    </div>
                    @error('firma_checkin') <span class="error-message" style="margin-bottom: 15px;">{{ $message }}</span> @enderror
                </div>

                <div style="margin-top: 10px; display: flex; align-items: center; gap: 8px; padding: 10px; background-color: #f8f9fa; border-radius: 6px; max-width: 350px; margin-left: auto; margin-right: auto;">
                    <input type="checkbox" wire:model="enviarWhatsapp" id="chk-whatsapp-checkin" style="width: 18px; height: 18px; cursor: pointer;">
                    <label for="chk-whatsapp-checkin" style="font-size: 0.85rem; cursor: pointer; color: var(--color-text-dark); margin: 0;">
                        Enviar notificación de ingreso por WhatsApp al cliente
                    </label>
                </div>

                <div style="margin-top: 20px; display: flex; justify-content: center;">
                    <button type="submit" id="save-order-btn" class="btn btn-primary" style="max-width: 350px;">
                        📝 Registrar Ingreso y Asignar
                    </button>
                </div>

            </form>
        </div>
    </div>

    <!-- Vanilla Javascript canvas script for drawing -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const canvas = document.getElementById('signature-canvas');
            const clearBtn = document.getElementById('clear-sig-btn');
            const form = document.getElementById('checkin-form');
            const hiddenInput = document.getElementById('firma_checkin_hidden');

            if (!canvas) return;

            const ctx = canvas.getContext('2d');
            
            // Adjust canvas resolution to match css size
            function resizeCanvas() {
                const rect = canvas.getBoundingClientRect();
                canvas.width = rect.width;
                canvas.height = rect.height;
                // Style configurations
                ctx.strokeStyle = '#000000'; // Black signature ink
                ctx.lineWidth = 2.5;
                ctx.lineCap = 'round';
            }

            resizeCanvas();
            window.addEventListener('resize', resizeCanvas);

            let drawing = false;
            let lastX = 0;
            let lastY = 0;

            function getCoordinates(e) {
                const rect = canvas.getBoundingClientRect();
                if (e.touches && e.touches.length > 0) {
                    return {
                        x: e.touches[0].clientX - rect.left,
                        y: e.touches[0].clientY - rect.top
                    };
                }
                return {
                    x: e.clientX - rect.left,
                    y: e.clientY - rect.top
                };
            }

            function startDrawing(e) {
                drawing = true;
                const coords = getCoordinates(e);
                lastX = coords.x;
                lastY = coords.y;
                ctx.beginPath();
                ctx.moveTo(lastX, lastY);
                e.preventDefault();
            }

            function draw(e) {
                if (!drawing) return;
                const coords = getCoordinates(e);
                ctx.lineTo(coords.x, coords.y);
                ctx.stroke();
                lastX = coords.x;
                lastY = coords.y;
                e.preventDefault();
            }

            function stopDrawing() {
                drawing = false;
            }

            // Mouse events
            canvas.addEventListener('mousedown', startDrawing);
            canvas.addEventListener('mousemove', draw);
            canvas.addEventListener('mouseup', stopDrawing);
            canvas.addEventListener('mouseleave', stopDrawing);

            // Touch events for tablets/mobiles
            canvas.addEventListener('touchstart', startDrawing);
            canvas.addEventListener('touchmove', draw);
            canvas.addEventListener('touchend', stopDrawing);

            // Clear button
            clearBtn.addEventListener('click', () => {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                hiddenInput.value = '';
                // Update livewire
                @this.set('firma_checkin', '');
            });

            // Capture signature on submit
            form.addEventListener('submit', (e) => {
                // Check if canvas has drawing (isn't completely white/empty)
                // A quick check is to see if user has drawn or if data URL has some content.
                // We'll write the base64 string to the hidden input
                const dataUrl = canvas.toDataURL();
                
                // If the canvas is empty, we don't send the dataUrl or we check if user drew anything.
                // A clean way is checking if a pixel color is drawn, but simple check is setting it on submit.
                // We'll set the value on Livewire
                @this.set('firma_checkin', dataUrl);
            });

            // Listen to Livewire's clear event
            window.addEventListener('clear-sig-pad', () => {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                hiddenInput.value = '';
            });
        });
    </script>
</div>
