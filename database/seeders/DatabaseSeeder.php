<?php

namespace Database\Seeders;

use App\Models\Usuario;
use App\Models\Independente;
use App\Models\Disciplina;
use App\Models\Topico;
use App\Models\Flashcard;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        // Criar usuários e seus tipos específicos
        $usuarios = collect();


        // Criar independentes
        for ($i = 0; $i < 15; $i++) {
            $usuario = Usuario::factory()->independente()->create();
            $independente = Independente::factory()
                ->forUsuario($usuario->id_usuario)
                ->create();
            
            $usuarios->push($usuario);
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



        $this->command->info('Database seeded successfully!');
        $this->command->info('Created:');
        $this->command->info('- ' . $usuarios->count() . ' usuários');

    }
}
