<?php

namespace Database\Seeders;

use App\Models\Usuario;
use App\Models\Escola;
use App\Models\Aluno;
use App\Models\Professor;
use App\Models\Independente;
use App\Models\Turma;
use App\Models\TurmaAluno;
use App\Models\Disciplina;
use App\Models\DisciplinaProfessor;
use App\Models\Topico;
use App\Models\TopicoProfessor;
use App\Models\Flashcard;
use App\Models\FlashcardProfessor;
use App\Models\PerguntaFlashcard;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Criar escolas
        $escolas = Escola::factory(5)->create();

        // Criar usuários e seus tipos específicos
        $usuarios = collect();
        
        // Criar professores
        $professores = collect();
        for ($i = 0; $i < 10; $i++) {
            $usuario = Usuario::factory()->professor()->create();
            $professor = Professor::factory()
                ->forUsuario($usuario->id_usuario)
                ->forEscola($escolas->random()->id_escola)
                ->create();
            
            $usuarios->push($usuario);
            $professores->push($professor);
        }

        // Criar alunos
        $alunos = collect();
        for ($i = 0; $i < 50; $i++) {
            $usuario = Usuario::factory()->aluno()->create();
            $aluno = Aluno::factory()
                ->forUsuario($usuario->id_usuario)
                ->forEscola($escolas->random()->id_escola)
                ->create();
            
            $usuarios->push($usuario);
            $alunos->push($aluno);
        }

        // Criar independentes
        for ($i = 0; $i < 15; $i++) {
            $usuario = Usuario::factory()->independente()->create();
            $independente = Independente::factory()
                ->forUsuario($usuario->id_usuario)
                ->create();
            
            $usuarios->push($usuario);
        }

        /*
        // Criar admin
        $admin = Usuario::factory()->admin()->create([
            'nome' => 'Administrador',
            'email' => 'admin@sistema.com'
        ]);*/

        // Criar turmas
        $turmas = collect();
        foreach ($escolas as $escola) {
            $professoresDaEscola = $professores->where('id_escola', $escola->id_escola);
            
            foreach ($professoresDaEscola->take(3) as $professor) {
                $turma = Turma::factory()
                    ->forEscola($escola->id_escola)
                    ->forProfessor($professor->id_usuario)
                    ->create();
                
                $turmas->push($turma);
            }
        }

        // Associar alunos às turmas
        foreach ($turmas as $turma) {
            $alunosDaEscola = $alunos->where('id_escola', $turma->id_escola);
            $alunosParaTurma = $alunosDaEscola->random(min(8, $alunosDaEscola->count()));
            
            foreach ($alunosParaTurma as $aluno) {
                TurmaAluno::factory()
                    ->forTurma($turma->id_turma)
                    ->forAluno($aluno->id_usuario)
                    ->create();
            }
        }

        // Criar disciplinas para usuários independentes
        $independentesUsuarios = $usuarios->where('tipo', 'independente');
        foreach ($independentesUsuarios->take(10) as $usuario) {
            $disciplinas = Disciplina::factory(rand(2, 5))
                ->forUsuario($usuario->id_usuario)
                ->create();

            // Criar tópicos para cada disciplina
            foreach ($disciplinas as $disciplina) {
                $topicos = Topico::factory(rand(3, 8))
                    ->forDisciplina($disciplina->id_disciplina)
                    ->create();

                // Criar flashcards para cada tópico
                foreach ($topicos as $topico) {
                    $flashcards = Flashcard::factory(rand(2, 5))
                        ->forTopico($topico->id_topico)
                        ->create();

                    // Criar perguntas para cada flashcard
                    foreach ($flashcards as $flashcard) {
                        PerguntaFlashcard::factory(rand(3, 7))
                            ->forFlashcard($flashcard->id_flashcard)
                            ->create();
                    }
                }
            }
        }

        // Criar disciplinas do professor para cada turma
        foreach ($turmas as $turma) {
            $disciplinasProfessor = DisciplinaProfessor::factory(rand(2, 4))
                ->forTurma($turma->id_turma)
                ->forProfessor($turma->id_professor)
                ->create();

            // Criar tópicos do professor para cada disciplina
            foreach ($disciplinasProfessor as $disciplinaProfessor) {
                $topicosProfessor = TopicoProfessor::factory(rand(4, 10))
                    ->forDisciplinaProfessor($disciplinaProfessor->id_disciplina_professor)
                    ->create();

                // Criar flashcards do professor para cada tópico
                foreach ($topicosProfessor as $topicoProfessor) {
                    FlashcardProfessor::factory(rand(2, 6))
                        ->forTopicoProfessor($topicoProfessor->id_topico_professor)
                        ->create();
                }
            }
        }

        $this->command->info('Database seeded successfully!');
        $this->command->info('Created:');
        $this->command->info('- ' . $escolas->count() . ' escolas');
        $this->command->info('- ' . $usuarios->count() . ' usuários');
        $this->command->info('- ' . $professores->count() . ' professores');
        $this->command->info('- ' . $alunos->count() . ' alunos');
        $this->command->info('- ' . $turmas->count() . ' turmas');
        $this->command->info('- 1 administrador');
    }
}
