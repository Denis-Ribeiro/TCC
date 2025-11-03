<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Aluno extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $primaryKey = 'id_aluno';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nome',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    public function atividades(){

    
        
    return $this->belongsToMany(Atividade::class, 'atv_alunos', 'id_aluno', 'id_atividade')
                ->withPivot('status', 'nota', 'answers') // ← adicionamos 'answers' aqui
                ->withTimestamps(); // ← adiciona suporte aos timestamps criados na migration
    }
}

