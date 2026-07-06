<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('solicitudes_repuestos', function (Blueprint $table) {
            $table->boolean('confirmado_recibido')->default(false)->after('estado');
            $table->timestamp('fecha_confirmacion')->nullable()->after('confirmado_recibido');
            $table->boolean('leido_por_solicitante')->default(true)->after('fecha_confirmacion');
        });
    }

    public function down(): void
    {
        Schema::table('solicitudes_repuestos', function (Blueprint $table) {
            $table->dropColumn(['confirmado_recibido', 'fecha_confirmacion', 'leido_por_solicitante']);
        });
    }
};
