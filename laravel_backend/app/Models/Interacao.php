<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interacao extends Model
{
    use HasFactory;

    /**
     * A chave primária associada com a tabela.
     *
     * @var string
     */
    protected $primaryKey = 'id_interacao';

    /**
     * Define a relação de pertencimento a AtvAluno.
     * Uma interação pertence a uma entrada na tabela de atividade do aluno.
     */
    public function atvAluno()
    {
        return $this->belongsTo(AtvAluno::class, 'id_atv_aluno');
    }
}

