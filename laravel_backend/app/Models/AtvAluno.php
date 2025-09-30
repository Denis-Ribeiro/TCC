<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class AtvAluno extends Pivot
{
    /**
     * O nome da tabela associada ao modelo.
     *
     * @var string
     */
    protected $table = 'atv_alunos';

    /**
     * Indica se a chave primária é auto-incrementável.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * A chave primária associada à tabela.
     *
     * @var string
     */
    protected $primaryKey = 'id_atv_aluno';

    /**
     * Define a relação de um para muitos com Interacao.
     * Uma entrada em atv_alunos pode ter várias interações.
     */
    public function interacoes()
    {
        return $this->hasMany(Interacao::class, 'id_atv_aluno');
    }
}

