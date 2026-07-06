<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitudes_repuestos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sucursal_id')->constrained('sucursales')->onDelete('cascade');
            $table->foreignId('solicitante_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('almacenista_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('orden_reparacion_id')->nullable()->constrained('ordenes_reparacion')->nullOnDelete();
            $table->string('nombre_repuesto');
            $table->integer('cantidad')->default(1);
            $table->string('urgencia')->default('media'); // baja, media, alta
            $table->string('estado')->default('pendiente'); // pendiente, enviado, agotado, no_existe
            $table->text('observaciones_solicitante')->nullable();
            $table->text('observaciones_almacenista')->nullable();
            $table->timestamp('fecha_respuesta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes_repuestos');
    }
};
