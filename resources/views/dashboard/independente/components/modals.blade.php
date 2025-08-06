<!-- Modal Disciplina -->
<div id="disciplinaModal" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nova Disciplina</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="disciplinaForm">
                    @csrf
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome da Disciplina</label>
                        <input type="text" class="form-control" id="nome" name="nome" required>
                    </div>
                    <div class="mb-3">
                        <label for="descricao" class="form-label">Descrição (opcional)</label>
                        <textarea class="form-control" id="descricao" name="descricao" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="criarDisciplina()">Criar Disciplina</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tópico -->
<div id="topicoModal" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Novo Tópico</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="topicoForm">
                    @csrf
                    <input type="hidden" id="disciplina_id" name="disciplina_id">
                    <div class="mb-3">
                        <label for="nome_topico" class="form-label">Nome do Tópico</label>
                        <input type="text" class="form-control" id="nome_topico" name="nome" required>
                    </div>
                    <div class="mb-3">
                        <label for="descricao_topico" class="form-label">Descrição (opcional)</label>
                        <textarea class="form-control" id="descricao_topico" name="descricao" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="criarTopico()">Criar Tópico</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Flashcard -->
<div id="flashcardModal" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Novo Flashcard</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="flashcardForm">
                    @csrf
                    <input type="hidden" id="topico_id" name="topico_id">
                    <div class="mb-3">
                        <label for="titulo_flashcard" class="form-label">Título do Flashcard</label>
                        <input type="text" class="form-control" id="titulo_flashcard" name="titulo" required>
                    </div>
                    <div class="mb-3">
                        <label for="descricao_flashcard" class="form-label">Descrição (opcional)</label>
                        <textarea class="form-control" id="descricao_flashcard" name="descricao" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="criarFlashcard()">Criar Flashcard</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Pergunta/Resposta -->
<div id="perguntaModal" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nova Pergunta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="perguntaForm">
                    @csrf
                    <input type="hidden" id="flashcard_id" name="flashcard_id">
                    <div class="mb-3">
                        <label for="pergunta" class="form-label">Pergunta</label>
                        <textarea class="form-control" id="pergunta" name="pergunta" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="resposta" class="form-label">Resposta</label>
                        <textarea class="form-control" id="resposta" name="resposta" rows="3" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="criarPergunta()">Criar Pergunta</button>
            </div>
        </div>
    </div>
</div>

