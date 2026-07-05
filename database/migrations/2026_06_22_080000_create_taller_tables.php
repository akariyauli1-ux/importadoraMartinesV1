<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('configuraciones_empresa', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_comercial')->default('IMPORTADORA MARTINEZ');
            $table->string('logo_path')->nullable();
            $table->timestamps();
        });

        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('carnet_identidad')->nullable()->unique();
            $table->string('telefono');
            $table->string('email')->nullable();
            $table->string('direccion')->nullable();
            $table->timestamps();
        });

        Schema::create('ordenes_reparacion', function (Blueprint $table) {
            $table->id();
            $table->string('numero_ticket')->unique();
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->foreignId('sucursal_id')->constrained('sucursales')->onDelete('cascade');
            $table->string('categoria'); // celular, laptop, electrico, cpu
            $table->string('marca');
            $table->string('modelo');
            $table->string('serie')->nullable();
            $table->text('problema_reportado');
            $table->decimal('costo_estimado', 10, 2)->default(0.00);
            
            $table->foreignId('tecnico_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('recepcionista_id')->nullable()->constrained('users')->nullOnDelete();
            
            $table->string('estado')->default('por_diagnosticar'); // por_diagnosticar, diagnosticado, esperando_aprobacion, en_reparacion, reparado, entregado, rechazado
            
            $table->longText('firma_checkin_base64')->nullable();
            $table->longText('firma_checkout_base64')->nullable();
            $table->longText('firma_aprobacion_presupuesto_base64')->nullable();
            
            $table->text('motivo_derivacion')->nullable();
            $table->boolean('es_asignacion_cruzada')->default(false);
            $table->timestamps();
        });

        Schema::create('fotos_orden', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orden_reparacion_id')->constrained('ordenes_reparacion')->onDelete('cascade');
            $table->string('foto_path');
            $table->string('tipo'); // checkin, checkout, reparacion
            $table->timestamps();
        });

        Schema::create('checklist_operativo_diario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sucursal_id')->constrained('sucursales')->onDelete('cascade');
            $table->date('fecha');
            $table->boolean('uniformes_completos')->default(false);
            $table->boolean('limpieza_puestos')->default(false);
            $table->boolean('herramientas_ordenadas')->default(false);
            $table->text('observaciones')->nullable();
            $table->foreignId('registrado_por')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['sucursal_id', 'fecha']);
        });

        Schema::create('mensajes_whatsapp_pendientes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orden_reparacion_id')->constrained('ordenes_reparacion')->onDelete('cascade');
            $table->string('telefono');
            $table->text('mensaje');
            $table->boolean('enviado')->default(false);
            $table->timestamp('enviado_at')->nullable();
            $table->timestamps();
        });

        // Diagnostic detail tables
        Schema::create('detalle_revision_celular', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orden_reparacion_id')->constrained('ordenes_reparacion')->onDelete('cascade');
            $table->string('tactil')->nullable();
            $table->string('pantalla')->nullable();
            $table->string('camaras')->nullable();
            $table->string('conector_carga')->nullable();
            $table->string('bateria')->nullable();
            $table->string('botones')->nullable();
            $table->string('senal')->nullable();
            $table->string('altavoz')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });

        Schema::create('detalle_revision_laptop', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orden_reparacion_id')->constrained('ordenes_reparacion')->onDelete('cascade');
            $table->string('teclado')->nullable();
            $table->string('pantalla')->nullable();
            $table->string('cargador')->nullable();
            $table->string('disco_duro')->nullable();
            $table->string('memoria_ram')->nullable();
            $table->string('bisagras')->nullable();
            $table->string('wifi')->nullable();
            $table->string('encendido')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });

        Schema::create('detalle_revision_electrico', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orden_reparacion_id')->constrained('ordenes_reparacion')->onDelete('cascade');
            $table->string('fuente_alimentacion')->nullable();
            $table->string('placa_principal')->nullable();
            $table->string('salidas_audio')->nullable();
            $table->string('entradas_video')->nullable();
            $table->string('control_remoto')->nullable();
            $table->string('botones_fisicos')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });

        Schema::create('detalle_revision_cpu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orden_reparacion_id')->constrained('ordenes_reparacion')->onDelete('cascade');
            $table->string('fuente_poder')->nullable();
            $table->string('tarjeta_madre')->nullable();
            $table->string('procesador')->nullable();
            $table->string('memoria_ram')->nullable();
            $table->string('disco_duro')->nullable();
            $table->string('tarjeta_video')->nullable();
            $table->string('puertos_usb')->nullable();
            $table->string('refrigeracion')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_revision_cpu');
        Schema::dropIfExists('detalle_revision_electrico');
        Schema::dropIfExists('detalle_revision_laptop');
        Schema::dropIfExists('detalle_revision_celular');
        Schema::dropIfExists('mensajes_whatsapp_pendientes');
        Schema::dropIfExists('checklist_operativo_diario');
        Schema::dropIfExists('fotos_orden');
        Schema::dropIfExists('ordenes_reparacion');
        Schema::dropIfExists('clientes');
        Schema::dropIfExists('configuraciones_empresa');
    }
};
