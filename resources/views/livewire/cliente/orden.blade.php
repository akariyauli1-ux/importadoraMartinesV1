<div class="auth-wrapper" style="align-items: flex-start; padding: 40px 20px;">
    <div class="auth-card" style="max-width: 800px; background-color: #ffffff; color: var(--color-text-dark); border-top: 5px solid var(--color-red);">
        
        <!-- Branding Header -->
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid var(--border-light); padding-bottom: 15px; margin-bottom: 25px;">
            <div>
                @if($empConfig && $empConfig->logo_path)
                    <img src="{{ asset('storage/' . $empConfig->logo_path) }}" style="max-height: 50px; max-width: 150px; object-fit: contain;">
                @else
                    <h2 style="font-weight: 800; font-size: 1.4rem; color: #000; border-left: 4px solid var(--color-red); padding-left: 8px;">
                        IMPORTADORA <span style="color: var(--color-red);">MARTINEZ</span>
                    </h2>
                @endif
                <span style="font-size: 0.85rem; color: var(--color-text-light-muted); display: block; margin-top: 3px;">Servicio Técnico Autorizado</span>
            </div>
            <div style="text-align: right;">
                <span style="font-weight: 700; font-size: 1.1rem; color: var(--color-red); display: block;">TICKET {{ $order->numero_ticket }}</span>
                <span style="font-size: 0.82rem; color: var(--color-text-light-muted);">Ingresado: {{ $order->created_at->format('d/m/Y') }}</span>
            </div>
        </div>

        @if (session()->has('success'))
            <div class="alert alert-success">
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="alert alert-danger">
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Tracking Bar -->
        <div style="margin-bottom: 35px;">
            <h4 style="font-weight: 700; color: var(--color-text-dark); margin-bottom: 20px;">Estado de su Reparación</h4>
            
            @php
                $statusMap = [
                    'por_diagnosticar' => 1,
                    'esperando_aprobacion' => 2,
                    'en_reparacion' => 3,
                    'reparado' => 4,
                    'entregado' => 5,
                    'diagnosticado' => 2,
                    'rechazado' => 0
                ];
                $currentStep = $statusMap[$order->estado] ?? 1;
            @endphp

            @if($order->estado === 'rechazado')
                <div class="alert alert-danger" style="background-color: rgba(0,0,0,0.05); color: #000; border-color: #ccc;">
                    <span>❌ <strong>Orden Rechazada:</strong> El servicio ha sido rechazado por el cliente o no se pudo reparar. Equipo devuelto.</span>
                </div>
            @else
                <!-- Interactive timeline -->
                <div style="display: flex; justify-content: space-between; position: relative; margin: 20px 0;">
                    <!-- Line -->
                    <div style="position: absolute; top: 15px; left: 5%; right: 5%; height: 4px; background-color: #e9ecef; z-index: 1;">
                        <div style="width: {{ (($currentStep - 1) / 4) * 100 }}%; height: 100%; background-color: var(--color-red); transition: var(--transition-smooth);"></div>
                    </div>
                    
                    <!-- Step 1 -->
                    <div style="text-align: center; z-index: 2; width: 20%;">
                        <div style="width: 32px; height: 32px; border-radius: 50%; background-color: {{ $currentStep >= 1 ? 'var(--color-red)' : '#e9ecef' }}; color: #fff; display: flex; align-items: center; justify-content: center; margin: 0 auto; font-weight: 700; font-size: 0.9rem;">
                            1
                        </div>
                        <span style="font-size: 0.75rem; font-weight: 600; display: block; margin-top: 8px; color: {{ $currentStep >= 1 ? '#000' : '#888' }}">Recibido</span>
                    </div>
                    
                    <!-- Step 2 -->
                    <div style="text-align: center; z-index: 2; width: 20%;">
                        <div style="width: 32px; height: 32px; border-radius: 50%; background-color: {{ $currentStep >= 2 ? 'var(--color-red)' : '#e9ecef' }}; color: #fff; display: flex; align-items: center; justify-content: center; margin: 0 auto; font-weight: 700; font-size: 0.9rem;">
                            2
                        </div>
                        <span style="font-size: 0.75rem; font-weight: 600; display: block; margin-top: 8px; color: {{ $currentStep >= 2 ? '#000' : '#888' }}">Diagnosticado</span>
                    </div>

                    <!-- Step 3 -->
                    <div style="text-align: center; z-index: 2; width: 20%;">
                        <div style="width: 32px; height: 32px; border-radius: 50%; background-color: {{ $currentStep >= 3 ? 'var(--color-red)' : '#e9ecef' }}; color: #fff; display: flex; align-items: center; justify-content: center; margin: 0 auto; font-weight: 700; font-size: 0.9rem;">
                            3
                        </div>
                        <span style="font-size: 0.75rem; font-weight: 600; display: block; margin-top: 8px; color: {{ $currentStep >= 3 ? '#000' : '#888' }}">En Taller</span>
                    </div>

                    <!-- Step 4 -->
                    <div style="text-align: center; z-index: 2; width: 20%;">
                        <div style="width: 32px; height: 32px; border-radius: 50%; background-color: {{ $currentStep >= 4 ? 'var(--color-red)' : '#e9ecef' }}; color: #fff; display: flex; align-items: center; justify-content: center; margin: 0 auto; font-weight: 700; font-size: 0.9rem;">
                            4
                        </div>
                        <span style="font-size: 0.75rem; font-weight: 600; display: block; margin-top: 8px; color: {{ $currentStep >= 4 ? '#000' : '#888' }}">Reparado</span>
                    </div>

                    <!-- Step 5 -->
                    <div style="text-align: center; z-index: 2; width: 20%;">
                        <div style="width: 32px; height: 32px; border-radius: 50%; background-color: {{ $currentStep >= 5 ? 'var(--color-red)' : '#e9ecef' }}; color: #fff; display: flex; align-items: center; justify-content: center; margin: 0 auto; font-weight: 700; font-size: 0.9rem;">
                            5
                        </div>
                        <span style="font-size: 0.75rem; font-weight: 600; display: block; margin-top: 8px; color: {{ $currentStep >= 5 ? '#000' : '#888' }}">Entregado</span>
                    </div>
                </div>
            @endif
        </div>

        <!-- 2-Column Details -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 25px; border-top: 1px solid var(--border-light); padding-top: 20px;">
            <div>
                <h5 style="font-weight: 700; color: var(--color-text-dark); margin-bottom: 12px;">Ficha del Equipo</h5>
                <table class="table" style="background-color: #fafafa; font-size: 0.88rem;">
                    <tbody>
                        <tr>
                            <td style="font-weight: 600;">Categoría</td>
                            <td>{{ ucfirst($order->categoria) }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: 600;">Marca / Modelo</td>
                            <td>{{ $order->marca }} {{ $order->modelo }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: 600;">Falla Reportada</td>
                            <td>{{ $order->problema_reportado }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div>
                <h5 style="font-weight: 700; color: var(--color-text-dark); margin-bottom: 12px;">Sede de Recepción</h5>
                <div style="background-color: #fafafa; padding: 15px; border-radius: 8px; border: 1px solid var(--border-light); font-size: 0.88rem;">
                    <strong style="color: var(--color-text-dark); display: block;">📍 {{ $order->sucursal->nombre }}</strong>
                    <span style="display: block; margin-top: 5px; color: var(--color-text-light-muted);">Dirección: {{ $order->sucursal->direccion }}</span>
                    <span style="display: block; margin-top: 5px; color: var(--color-text-light-muted);">Teléfono: {{ $order->sucursal->telefono }}</span>
                </div>
            </div>
        </div>

        <!-- Diagnostics & Actions -->
        @if($currentStep >= 2)
            <div style="background-color: #fafafa; border: 1px solid var(--border-light); border-radius: 8px; padding: 20px; margin-bottom: 25px;">
                <h5 style="font-weight: 700; color: var(--color-text-dark); margin-bottom: 12px;">Informe Técnico del Taller</h5>
                <p style="font-size: 0.9rem; color: #495057; line-height: 1.5; margin-bottom: 15px;">
                    {{ $order->getRevision()->observaciones ?? 'Sin observaciones' }}
                </p>
                <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--border-light); padding-top: 15px;">
                    <div>
                        <span style="font-size: 0.82rem; color: var(--color-text-light-muted);">Costo Estimado del Servicio:</span>
                        <strong style="font-size: 1.3rem; color: var(--color-red); display: block;">Bs. {{ number_format($order->costo_estimado, 2) }}</strong>
                    </div>

                    <div>
                        <a href="{{ route('cliente.orden.pdf', $order->numero_ticket) }}" class="btn btn-secondary" style="width: auto; padding: 8px 18px; font-size: 0.85rem;">
                            Descargar Ficha Tecnica (PDF)
                        </a>
                    </div>

                </div>
            </div>
        @endif

        <!-- Client Quote Approval signature (Rule #11 / #16) -->
        @if($order->estado === 'esperando_aprobacion')
            <div style="border: 2px dashed var(--color-red); background-color: rgba(229, 9, 20, 0.02); border-radius: 8px; padding: 25px; text-align: center; margin-top: 25px;">
                <h4 style="color: var(--color-red); font-weight: 700; margin-bottom: 10px;">Requiere Autorización Digital</h4>
                <p style="color: var(--color-text-light-muted); font-size: 0.85rem; margin-bottom: 20px; max-width: 600px; margin-left: auto; margin-right: auto;">
                    Para proceder con la reparación, solicitamos su firma de consentimiento digital. Esto nos autoriza formalmente a manipular su equipo y cargar el costo presupuestado de Bs. {{ number_format($order->costo_estimado, 2) }} (Regla #11).
                </p>

                <form wire:submit.prevent="aprobarPresupuesto" id="approval-form">
                    <input type="hidden" id="firma_aprobacion_hidden" wire:model.defer="firma_aprobacion">
                    
                    <div class="sig-pad-wrapper" style="margin: 0 auto 15px auto;">
                        <canvas class="sig-pad-canvas" id="approval-canvas"></canvas>
                    </div>

                    <div style="display: flex; justify-content: center; gap: 10px; margin-bottom: 25px;">
                        <button type="button" id="clear-approval-sig" class="btn btn-secondary" style="width: auto; padding: 6px 15px; font-size: 0.85rem;">
                            🧼 Limpiar Firma
                        </button>
                    </div>
                    @error('firma_aprobacion') <span class="error-message" style="display: block; margin-bottom: 15px;">{{ $message }}</span> @enderror

                    <button type="submit" class="btn btn-primary" style="max-width: 350px;">
                        ✍️ Firmar y Autorizar Reparación
                    </button>
                </form>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const canvas = document.getElementById('approval-canvas');
                    const clearBtn = document.getElementById('clear-approval-sig');
                    const form = document.getElementById('approval-form');
                    const hiddenInput = document.getElementById('firma_aprobacion_hidden');

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

                    canvas.addEventListener('mousedown', (e) => {
                        drawing = true;
                        const coords = getCoordinates(e);
                        lastX = coords.x;
                        lastY = coords.y;
                        ctx.beginPath();
                        ctx.moveTo(lastX, lastY);
                        e.preventDefault();
                    });

                    canvas.addEventListener('mousemove', (e) => {
                        if (!drawing) return;
                        const coords = getCoordinates(e);
                        ctx.lineTo(coords.x, coords.y);
                        ctx.stroke();
                        lastX = coords.x;
                        lastY = coords.y;
                        e.preventDefault();
                    });

                    canvas.addEventListener('mouseup', () => drawing = false);
                    canvas.addEventListener('mouseleave', () => drawing = false);
                    canvas.addEventListener('touchstart', (e) => {
                        drawing = true;
                        const coords = getCoordinates(e);
                        lastX = coords.x;
                        lastY = coords.y;
                        ctx.beginPath();
                        ctx.moveTo(lastX, lastY);
                        e.preventDefault();
                    });
                    canvas.addEventListener('touchmove', (e) => {
                        if (!drawing) return;
                        const coords = getCoordinates(e);
                        ctx.lineTo(coords.x, coords.y);
                        ctx.stroke();
                        lastX = coords.x;
                        lastY = coords.y;
                        e.preventDefault();
                    });
                    canvas.addEventListener('touchend', () => drawing = false);

                    clearBtn.addEventListener('click', () => {
                        ctx.clearRect(0, 0, canvas.width, canvas.height);
                        hiddenInput.value = '';
                        @this.set('firma_aprobacion', '');
                    });

                    form.addEventListener('submit', () => {
                        const dataUrl = canvas.toDataURL();
                        @this.set('firma_aprobacion', dataUrl);
                    });
                });
            </script>
        @endif

        @if($order->estado === 'en_reparacion')
            <div class="alert alert-success" style="margin-top: 25px;">
                <span>🛠️ <strong>Reparación Autorizada:</strong> Has firmado la conformidad de presupuesto. El técnico se encuentra reparando el equipo activamente. Te avisaremos cuando finalice.</span>
            </div>
        @endif
        
    </div>
</div>
