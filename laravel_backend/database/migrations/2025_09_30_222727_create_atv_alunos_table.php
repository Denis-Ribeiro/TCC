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
        Schema::create('atv_alunos', function (Blueprint $table) {
            $table->increments('id_atv_aluno');
            
            $table->unsignedInteger('id_aluno');
            $table->foreign('id_aluno')->references('id_aluno')->on('alunos')->onDelete('cascade');

            $table->unsignedInteger('id_atividade');
            $table->foreign('id_atividade')->references('id_atividade')->on('atividades')->onDelete('cascade');

            $table->string('status', 50)->default('Pendente');
            $table->decimal('nota', 5, 2)->nullable();
            $table->json('answers')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('atv_alunos');
    }
};
