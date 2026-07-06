<div style="position: relative;">
    <button wire:click="toggle"
        style="background: none; border: none; cursor: pointer; position: relative; font-size: 1.2rem; padding: 8px; line-height: 1;"
        title="Notificaciones de repuestos">
        🔔
        @if($noLeidas > 0)
            <span style="position: absolute; top: 0; right: 0; background-color: var(--color-red); color: #fff; border-radius: 50%; width: 18px; height: 18px; font-size: 0.65rem; font-weight: 700; display: flex; align-items: center; justify-content: center;">
                {{ $noLeidas }}
            </span>
        @endif
    </button>

    @if($abierto)
        <div style="position: fixed; inset: 0; z-index: 9998;" wire:click="cerrar"></div>
        <div style="position: absolute; right: 0; top: 40px; z-index: 9999; background-color: #fff; border-radius: 10px; box-shadow: 0 8px 30px rgba(0,0,0,0.18); width: 380px; max-height: 450px; overflow-y: auto; border: 1px solid var(--border-light);">
            <div style="padding: 12px 16px; border-bottom: 2px solid var(--color-red); display: flex; justify-content: space-between; align-items: center;">
                <strong style="font-size: 0.9rem; color: var(--color-text-dark);">Notificaciones de Repuestos</strong>
                <button wire:click="cerrar" style="background: none; border: none; font-size: 1.1rem; cursor: pointer; color: var(--color-text-light-muted);">&times;</button>
            </div>

            <div style="padding: 8px 0;">
                @forelse($notificaciones as $not)
                    <div style="padding: 12px 16px; border-bottom: 1px solid #f1f3f5; {{ !$not->leido_por_solicitante ? 'background-color: rgba(229,9,20,0.04);' : '' }}">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 8px;">
                            <div style="flex-grow: 1;">
                                <div style="font-weight: 600; font-size: 0.82rem; color: var(--color-text-dark);">
                                    @if($not->estado === 'enviado')
                                        📦 Tu repuesto <strong>"{{ $not->nombre_repuesto }}"</strong> fue enviado
                                    @elseif($not->estado === 'agotado')
                                        📭 El repuesto <strong>"{{ $not->nombre_repuesto }}"</strong> está <span style="color: #ff9800;">agotado</span>
                                    @elseif($not->estado === 'no_existe')
                                        🚫 El repuesto <strong>"{{ $not->nombre_repuesto }}"</strong> <span style="color: #dc3545;">no existe</span> en almacén
                                    @endif
                                </div>
                                <div style="font-size: 0.7rem; color: var(--color-text-light-muted); margin-top: 3px;">
                                    {{ $not->updated_at->format('d/m/Y H:i') }}
                                    @if($not->almacenista)
                                        · {{ $not->almacenista->nombre_completo }}
                                    @endif
                                    · Cant: {{ $not->cantidad }}
                                </div>
                                @if($not->observaciones_almacenista)
                                    <div style="font-size: 0.75rem; color: #495057; margin-top: 4px; font-style: italic;">
                                        "{{ $not->observaciones_almacenista }}"
                                    </div>
                                @endif
                            </div>
                            @if(!$not->leido_por_solicitante)
                                <span style="flex-shrink: 0; width: 8px; height: 8px; background-color: var(--color-red); border-radius: 50%; margin-top: 4px;"></span>
                            @endif
                        </div>

                        <div style="display: flex; gap: 8px; margin-top: 8px;">
                            @if($not->estado === 'enviado')
                                @if(!$not->confirmado_recibido)
                                    <button wire:click="confirmarRecepcion({{ $not->id }})"
                                        style="padding: 4px 12px; border-radius: 4px; font-size: 0.72rem; font-weight: 600; cursor: pointer; border: none; background-color: #28a745; color: #fff;">
                                        ✅ Confirmar recepción
                                    </button>
                                @else
                                    <span style="font-size: 0.72rem; color: #28a745; font-weight: 600;">
                                        ✅ Recepción confirmada {{ $not->fecha_confirmacion ? $not->fecha_confirmacion->format('d/m/Y H:i') : '' }}
                                    </span>
                                @endif
                            @endif

                            @if(!$not->leido_por_solicitante)
                                <button wire:click="marcarLeido({{ $not->id }})"
                                    style="padding: 4px 10px; border-radius: 4px; font-size: 0.7rem; cursor: pointer; border: 1px solid #dee2e6; background-color: #f8f9fa; color: #6c757d;">
                                    Marcar leído
                                </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <div style="padding: 30px 20px; text-align: center; color: var(--color-text-light-muted); font-size: 0.85rem;">
                        No tienes notificaciones de repuestos.
                    </div>
                @endforelse
            </div>
        </div>
    @endif
</div>
