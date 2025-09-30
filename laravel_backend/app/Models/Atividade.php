<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Atividade extends Model
{
    use HasFactory;

    /**
     * A chave primária associada à tabela.
     *
     * @var string
     */
    protected $primaryKey = 'id_atividade';

    /**
     * Os atributos que podem ser atribuídos em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'titulo',
        'descricao',
        'id_professor',
    ];

    /**
     * Obtém o professor que criou a atividade.
     */
    public function professor(): BelongsTo
    {
        return $this->belongsTo(Professor::class, 'id_professor', 'id_professor');
    }

    /**
     * Os alunos que estão associados a esta atividade.
     */
    public function alunos(): BelongsToMany
    {
        return $this->belongsToMany(Aluno::class, 'atv_alunos', 'id_atividade', 'id_aluno')
                    ->using(AtvAluno::class)
                    ->withPivot('status', 'nota');
    }
}

