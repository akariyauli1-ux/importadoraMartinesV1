<div>
    @section('title', 'Checklist Operativo Diario')

    <div style="max-width: 600px; margin: 0 auto;">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Checklist Operativo de Sede - {{ today()->format('d/m/Y') }}</h4>
            </div>
            <div class="card-body">
                @if($checklistDeHoy)
                    <div class="alert alert-success">
                        <span>El checklist de hoy ya fue registrado con éxito por <strong>{{ $checklistDeHoy->registradoPor->nombre_completo }}</strong> a las {{ $checklistDeHoy->created_at->format('H:i') }}.</span>
                    </div>
                @else
                    <p style="color: var(--color-text-light-muted); margin-bottom: 20px;">
                        Por favor marque el cumplimiento de las condiciones de apertura de la sede el día de hoy.
                    </p>
                @endif

                <form wire:submit.prevent="save">
                    <div style="display: flex; flex-direction: column; gap: 15px; margin-bottom: 25px;">
                        
                        <label style="display: flex; align-items: center; gap: 15px; padding: 15px; border: 1px solid var(--border-light); border-radius: 8px; cursor: {{ $checklistDeHoy ? 'default' : 'pointer' }}; background-color: #fafafa; transition: var(--transition-smooth);">
                            <input type="checkbox" wire:model.defer="uniformes_completos" {{ $checklistDeHoy ? 'disabled' : '' }} style="width: 22px; height: 22px; cursor: pointer; accent-color: var(--color-red);">
                            <div>
                                <strong style="display: block; color: var(--color-text-dark); font-size: 0.95rem;">Uniformes Completos</strong>
                                <span style="font-size: 0.8rem; color: var(--color-text-light-muted);">Todo el personal en sala cuenta con el uniforme corporativo impecable.</span>
                            </div>
                        </label>

                        <label style="display: flex; align-items: center; gap: 15px; padding: 15px; border: 1px solid var(--border-light); border-radius: 8px; cursor: {{ $checklistDeHoy ? 'default' : 'pointer' }}; background-color: #fafafa; transition: var(--transition-smooth);">
                            <input type="checkbox" wire:model.defer="limpieza_puestos" {{ $checklistDeHoy ? 'disabled' : '' }} style="width: 22px; height: 22px; cursor: pointer; accent-color: var(--color-red);">
                            <div>
                                <strong style="display: block; color: var(--color-text-dark); font-size: 0.95rem;">Limpieza y Desinfección</strong>
                                <span style="font-size: 0.8rem; color: var(--color-text-light-muted);">Mesas de trabajo, recepción y área de atención limpias y ordenadas.</span>
                            </div>
                        </label>

                        <label style="display: flex; align-items: center; gap: 15px; padding: 15px; border: 1px solid var(--border-light); border-radius: 8px; cursor: {{ $checklistDeHoy ? 'default' : 'pointer' }}; background-color: #fafafa; transition: var(--transition-smooth);">
                            <input type="checkbox" wire:model.defer="herramientas_ordenadas" {{ $checklistDeHoy ? 'disabled' : '' }} style="width: 22px; height: 22px; cursor: pointer; accent-color: var(--color-red);">
                            <div>
                                <strong style="display: block; color: var(--color-text-dark); font-size: 0.95rem;">Herramientas Organizadas</strong>
                                <span style="font-size: 0.8rem; color: var(--color-text-light-muted);">Cautines, multímetros y destornilladores ordenados en sus estaciones.</span>
                            </div>
                        </label>

                    </div>

                    <div class="form-group">
                        <label class="form-label" style="color: var(--color-text-dark);">Observaciones / Comentarios</label>
                        <textarea wire:model.defer="observaciones" {{ $checklistDeHoy ? 'readonly' : '' }} class="form-control form-control-light" rows="4" placeholder="Ej. Retraso en apertura de sede o novedades..."></textarea>
                    </div>

                    @if(!$checklistDeHoy)
                        <div style="margin-top: 25px;">
                            <button type="submit" class="btn btn-primary">
                                Guardar Checklist de Apertura
                            </button>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>
