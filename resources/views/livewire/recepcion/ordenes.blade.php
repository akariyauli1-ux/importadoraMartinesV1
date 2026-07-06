<div>
    @section('title', 'Registro y Órdenes del Taller')

    <!-- Search and Filter Bar -->
    <div class="card">
        <div class="card-body" style="padding: 20px;">
            <div style="display: flex; gap: 15px; flex-wrap: wrap; align-items: center;">
                <div style="flex-grow: 1; min-width: 250px;">
                    <input wire:model="search" type="text" class="form-control form-control-light" placeholder="🔍 Buscar por ticket, cliente o C.I.">
                </div>
                <div style="width: 220px;">
                    <select wire:model="statusFilter" class="form-control form-control-light" style="padding: 10px;">
                        <option value="">Todos los Estados</option>
                        <option value="por_diagnosticar">Por Diagnosticar</option>
                        <option value="diagnosticado">Diagnosticado</option>
                        <option value="esperando_aprobacion">Esperando Aprobación</option>
                        <option value="en_reparacion">En Reparación</option>
                        <option value="reparado">Reparado</option>
                        <option value="entregado">Entregado</option>
                        <option value="rechazado">Rechazado</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card">
        <div class="card-body" style="padding: 0;">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Ticket</th>
                            <th>Cliente</th>
                            <th>Equipo</th>
                            <th>Técnico</th>
                            <th>Estado</th>
                            <th>Firma Check-in</th>
                            <th>Firma Check-out</th>
                            <th style="text-align: right;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ordenes as $ord)
                            <tr>
                                <td style="font-weight: 700; color: var(--color-red);">{{ $ord->numero_ticket }}</td>
                                <td>
                                    <span style="font-weight: 600; display: block;">{{ $ord->cliente->nombre }}</span>
                                    <span style="font-size: 0.8rem; color: var(--color-text-light-muted);">C.I. {{ $ord->cliente->carnet_identidad }}</span>
                                </td>
                                <td>
                                    <span style="font-weight: 500; display: block;">{{ ucfirst($ord->categoria) }}</span>
                                    <span style="font-size: 0.8rem; color: var(--color-text-light-muted);">{{ $ord->marca }} {{ $ord->modelo }}</span>
                                </td>
                                <td>{{ $ord->tecnico->nombre_completo ?? 'No asignado' }}</td>
                                <td>
                                    @switch($ord->estado)
                                        @case('por_diagnosticar')
                                            <span class="badge badge-black">Por Diagnosticar</span>
                                            @break
                                        @case('diagnosticado')
                                            <span class="badge badge-yellow">Diagnosticado</span>
                                            @break
                                        @case('esperando_aprobacion')
                                            <span class="badge badge-yellow">Esperando Aprobación</span>
                                            @break
                                        @case('en_reparacion')
                                            <span class="badge badge-red">En Reparación</span>
                                            @break
                                        @case('reparado')
                                            <span class="badge badge-green">Reparado</span>
                                            @break
                                        @case('entregado')
                                            <span class="badge badge-green" style="background-color: #e2f0d9; color: #385723;">Entregado</span>
                                            @break
                                        @case('rechazado')
                                            <span class="badge badge-black" style="background-color: #f2f2f2; color: #7f7f7f;">Rechazado</span>
                                            @break
                                    @endswitch
                                </td>
                                <td>
                                    @if($ord->firma_checkin_base64)
                                        <span style="font-size: 1.2rem; color: green;" title="Firmado">✍️ Si</span>
                                    @else
                                        <span style="font-size: 1.2rem; color: red;" title="Pendiente">❌ No</span>
                                    @endif
                                </td>
                                <td>
                                    @if($ord->firma_checkout_base64)
                                        <span style="font-size: 1.2rem; color: green;" title="Firmado">✍️ Si</span>
                                    @else
                                        <span style="font-size: 1.2rem; color: red;" title="Pendiente">❌ No</span>
                                    @endif
                                </td>
                                <td style="text-align: right;">
                                    <div style="display: inline-flex; gap: 8px;">
                                        <a href="{{ route('cliente.orden', $ord->numero_ticket) }}" target="_blank" class="btn btn-light" style="padding: 6px 12px; width: auto; font-size: 0.85rem;">
                                            👁️ Ver Ficha
                                        </a>
                                        @if(in_array($ord->estado, ['reparado', 'diagnosticado', 'por_diagnosticar']))
                                            <button wire:click="abrirCheckout({{ $ord->id }})" class="btn btn-primary" style="padding: 6px 12px; width: auto; font-size: 0.85rem;">
                                                📦 Entregar (Check-out)
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="text-align: center; color: var(--color-text-light-muted); padding: 30px;">
                                    No se encontraron órdenes registradas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Checkout Signature Modal -->
    @if($mostrarModalCheckout)
        <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.65); display: flex; align-items: center; justify-content: center; z-index: 1000; padding: 20px;">
            <div class="card" style="width: 100%; max-width: 500px; box-shadow: 0 10px 40px rgba(0,0,0,0.5); border-top: 4px solid var(--color-red); margin-bottom: 0;">
                <div class="card-header">
                    <h4 class="card-title">Retirar de Taller (Check-out)</h4>
                    <button wire:click="cerrarCheckout" style="background: none; border: none; font-size: 1.2rem; cursor: pointer; color: var(--color-text-dark);">✕</button>
                </div>
                <div class="card-body" style="text-align: center;">
                    <p style="color: var(--color-text-light-muted); font-size: 0.88rem; margin-bottom: 20px;">
                        Para proceder con la entrega física del equipo reparado, el cliente debe firmar digitalmente la constancia de conformidad de Check-out.
                    </p>

                    <form wire:submit.prevent="entregar" id="checkout-form">
                        
                        <input type="hidden" id="firma_checkout_hidden" wire:model.defer="firma_checkout">

                        <div class="sig-pad-wrapper" style="margin: 0 auto 15px auto;">
                            <canvas class="sig-pad-canvas" id="checkout-canvas"></canvas>
                        </div>

                        <div style="display: flex; justify-content: center; gap: 10px; margin-bottom: 20px;">
                            <button type="button" id="clear-checkout-sig-btn" class="btn btn-secondary" style="width: auto; padding: 6px 15px; font-size: 0.85rem;">
                                🧼 Limpiar Firma
                            </button>
                        </div>
                        @error('firma_checkout') <span class="error-message" style="display: block; margin-bottom: 15px;">{{ $message }}</span> @enderror

                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 15px; padding: 10px; background-color: #f8f9fa; border-radius: 6px;">
                            <input type="checkbox" wire:model="enviarWhatsapp" id="chk-whatsapp" style="width: 18px; height: 18px; cursor: pointer;">
                            <label for="chk-whatsapp" style="font-size: 0.85rem; cursor: pointer; color: var(--color-text-dark); margin: 0;">
                                Enviar notificación de entrega por WhatsApp al cliente
                            </label>
                        </div>

                        <div style="display: flex; justify-content: flex-end; gap: 10px; border-top: 1px solid var(--border-light); padding-top: 20px;">
                            <button type="button" wire:click="cerrarCheckout" class="btn btn-secondary" style="width: auto;">
                                Cancelar
                            </button>
                            <button type="submit" class="btn btn-primary" style="width: auto;">
                                📦 Registrar Entrega y Salida
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            // Delayed canvas script run to wait for modal visibility in DOM
            setTimeout(() => {
                const canvas = document.getElementById('checkout-canvas');
                const clearBtn = document.getElementById('clear-checkout-sig-btn');
                const form = document.getElementById('checkout-form');
                const hiddenInput = document.getElementById('firma_checkout_hidden');

                if (!canvas) return;
                const ctx = canvas.getContext('2d');
                
                function resizeCanvas() {
                    const rect = canvas.getBoundingClientRect();
                    canvas.width = rect.width;
                    canvas.height = rect.height;
                    ctx.strokeStyle = '#000000';
                    ctx.lineWidth = 2.5;
                    ctx.lineCap = 'round';
                }

                resizeCanvas();

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

                canvas.addEventListener('mousedown', startDrawing);
                canvas.addEventListener('mousemove', draw);
                canvas.addEventListener('mouseup', () => drawing = false);
                canvas.addEventListener('mouseleave', () => drawing = false);

                canvas.addEventListener('touchstart', startDrawing);
                canvas.addEventListener('touchmove', draw);
                canvas.addEventListener('touchend', () => drawing = false);

                clearBtn.addEventListener('click', () => {
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                    hiddenInput.value = '';
                    @this.set('firma_checkout', '');
                });

                form.addEventListener('submit', () => {
                    const dataUrl = canvas.toDataURL();
                    @this.set('firma_checkout', dataUrl);
                });

                window.addEventListener('clear-checkout-sig-pad', () => {
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                    hiddenInput.value = '';
                });
            }, 100);
        </script>
    @endif
</div>
