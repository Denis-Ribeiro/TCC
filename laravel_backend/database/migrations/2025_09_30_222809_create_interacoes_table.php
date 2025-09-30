<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('interacoes', function (Blueprint $table) {
            $table->increments('id_interacao');
            
            $table->unsignedInteger('id_atv_aluno');
            $table->foreign('id_atv_aluno')->references('id_atv_aluno')->on('atv_alunos')->onDelete('cascade');

            $table->text('mensagem');
            $table->dateTime('data_interacao')->useCurrent();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interacoes');
    }
};
