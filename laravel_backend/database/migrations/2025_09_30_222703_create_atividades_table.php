<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('atividades', function (Blueprint $table) {
            $table->increments('id_atividade');
            $table->string('titulo', 100);
            $table->text('descricao'); // Para guardar o JSON do quiz
            
            $table->unsignedInteger('id_professor');
            $table->foreign('id_professor')->references('id_professor')->on('professors')->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('atividades');
    }
};
