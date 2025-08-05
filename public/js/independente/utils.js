// ===== FUNÇÕES UTILITÁRIAS =====

// FUNÇÃO PARA CARREGAR AS ESTATÍSTICAS DO DASHBOARD
function carregarEstatisticas() {
    // FAZ UMA REQUISIÇÃO FETCH PARA A API DE ESTATÍSTICAS
    fetch(`./api/independente/estatisticas`, {
        method: "GET", // MÉTODO HTTP GET
        headers: { // CABEÇALHOS DA REQUISIÇÃO
            "Content-Type": "application/json", // TIPO DE CONTEÚDO JSON
            "X-CSRF-TOKEN": document.querySelector("meta[name=\"csrf-token\"]")?.getAttribute("content") || "", // TOKEN CSRF
            "Accept": "application/json" // ACEITA RESPOSTAS JSON
        }
    })
    .then(response => {
        // VERIFICA SE A RESPOSTA DA REQUISIÇÃO FOI BEM-SUCEDIDA
        if (!response.ok) {
            // SE NÃO FOI BEM-SUCEDIDA, LANÇA UM ERRO
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        // RETORNA A RESPOSTA COMO JSON
        return response.json();
    })
    .then(data => {
        // PROCESSA OS DADOS RECEBIDOS DO SERVIDOR
        if (data.success) {
            // SE A OPERAÇÃO FOI BEM-SUCEDIDA, ATUALIZA AS ESTATÍSTICAS NA INTERFACE
            atualizarEstatisticas(data.data || data.estatisticas || {});
        } else {
            // SE HOUVE ERRO, REGISTRA NO CONSOLE
            console.error("Erro ao carregar estatísticas:", data.message);
        }
    })
    .catch(error => {
        // TRATA ERROS DE REDE OU OUTROS ERROS DURANTE A REQUISIÇÃO
        console.error("Erro ao carregar estatísticas:", error);
    });
}

// FUNÇÃO PARA ATUALIZAR AS ESTATÍSTICAS NA INTERFACE
function atualizarEstatisticas(estatisticas) {
    // ATUALIZA O NÚMERO TOTAL DE DISCIPLINAS
    const totalDisciplinas = document.getElementById("totalDisciplinas");
    if (totalDisciplinas) {
        totalDisciplinas.textContent = estatisticas.totalDisciplinas || 0;
    }
    
    // ATUALIZA O NÚMERO TOTAL DE TÓPICOS
    const totalTopicos = document.getElementById("totalTopicos");
    if (totalTopicos) {
        totalTopicos.textContent = estatisticas.totalTopicos || 0;
    }
    
    // ATUALIZA O NÚMERO TOTAL DE FLASHCARDS
    const totalFlashcards = document.getElementById("totalFlashcards");
    if (totalFlashcards) {
        totalFlashcards.textContent = estatisticas.totalFlashcards || 0;
    }
    
    // ATUALIZA O NÚMERO TOTAL DE PERGUNTAS
    const totalPerguntas = document.getElementById("totalPerguntas");
    if (totalPerguntas) {
        totalPerguntas.textContent = estatisticas.totalPerguntas || 0;
    }
}

// FUNÇÃO PARA MOSTRAR MENSAGEM DE SUCESSO
function mostrarSucesso(mensagem) {
    // VERIFICA SE A FUNÇÃO showToast EXISTE (DEFINIDA EM toast.js)
    if (typeof showToast === "function") {
        // CHAMA A FUNÇÃO showToast COM TIPO 'success'
        showToast(mensagem, "success");
    } else {
        // SE NÃO EXISTE, USA UM ALERT SIMPLES
        alert("Sucesso: " + mensagem);
    }
}

// FUNÇÃO PARA MOSTRAR MENSAGEM DE ERRO
function mostrarErro(mensagem) {
    // VERIFICA SE A FUNÇÃO showToast EXISTE (DEFINIDA EM toast.js)
    if (typeof showToast === "function") {
        // CHAMA A FUNÇÃO showToast COM TIPO 'error'
        showToast(mensagem, "error");
    } else {
        // SE NÃO EXISTE, USA UM ALERT SIMPLES
        alert("Erro: " + mensagem);
    }
}

// FUNÇÃO PARA MOSTRAR MENSAGEM DE AVISO
function mostrarAviso(mensagem) {
    // VERIFICA SE A FUNÇÃO showToast EXISTE (DEFINIDA EM toast.js)
    if (typeof showToast === "function") {
        // CHAMA A FUNÇÃO showToast COM TIPO 'warning'
        showToast(mensagem, "warning");
    } else {
        // SE NÃO EXISTE, USA UM ALERT SIMPLES
        alert("Aviso: " + mensagem);
    }
}

// FUNÇÃO PARA MOSTRAR MENSAGEM DE INFORMAÇÃO
function mostrarInfo(mensagem) {
    // VERIFICA SE A FUNÇÃO showToast EXISTE (DEFINIDA EM toast.js)
    if (typeof showToast === "function") {
        // CHAMA A FUNÇÃO showToast COM TIPO 'info'
        showToast(mensagem, "info");
    } else {
        // SE NÃO EXISTE, USA UM ALERT SIMPLES
        alert("Info: " + mensagem);
    }
}

// FUNÇÃO PARA VALIDAR SE UM ELEMENTO EXISTE NO DOM
function validarElemento(seletor, nomeFuncao = "função") {
    // SELECIONA O ELEMENTO PELO SELETOR
    const elemento = document.querySelector(seletor);
    // VERIFICA SE O ELEMENTO EXISTE
    if (!elemento) {
        // REGISTRA UM ERRO NO CONSOLE SE O ELEMENTO NÃO FOR ENCONTRADO
        console.error(`Elemento '${seletor}' não encontrado em ${nomeFuncao}`);
        return false; // RETORNA FALSE INDICANDO QUE O ELEMENTO NÃO EXISTE
    }
    return elemento; // RETORNA O ELEMENTO SE ELE EXISTE
}

// FUNÇÃO PARA LIMPAR FORMULÁRIO
function limparFormulario(formId) {
    // SELECIONA O FORMULÁRIO PELO ID
    const form = document.getElementById(formId);
    // VERIFICA SE O FORMULÁRIO EXISTE
    if (form) {
        // LIMPA TODOS OS CAMPOS DO FORMULÁRIO
        form.reset();
    } else {
        // REGISTRA UM ERRO NO CONSOLE SE O FORMULÁRIO NÃO FOR ENCONTRADO
        console.error(`Formulário '${formId}' não encontrado`);
    }
}

// FUNÇÃO PARA FECHAR MODAL
function fecharModal(modalId) {
    // SELECIONA O MODAL PELO ID
    const modalElement = document.getElementById(modalId);
    // VERIFICA SE O MODAL EXISTE
    if (modalElement) {
        // OBTÉM A INSTÂNCIA DO MODAL DO BOOTSTRAP
        const modal = bootstrap.Modal.getInstance(modalElement);
        // VERIFICA SE A INSTÂNCIA EXISTE
        if (modal) {
            // FECHA O MODAL
            modal.hide();
        }
    } else {
        // REGISTRA UM ERRO NO CONSOLE SE O MODAL NÃO FOR ENCONTRADO
        console.error(`Modal '${modalId}' não encontrado`);
    }
}

// FUNÇÃO PARA DEBOUNCE (EVITAR MÚLTIPLAS EXECUÇÕES RÁPIDAS)
function debounce(func, wait) {
    // VARIÁVEL PARA ARMAZENAR O TIMEOUT
    let timeout;
    // RETORNA UMA FUNÇÃO QUE SERÁ EXECUTADA COM DELAY
    return function executedFunction(...args) {
        // FUNÇÃO QUE SERÁ EXECUTADA APÓS O DELAY
        const later = () => {
            // LIMPA O TIMEOUT
            clearTimeout(timeout);
            // EXECUTA A FUNÇÃO ORIGINAL COM OS ARGUMENTOS
            func(...args);
        };
        // LIMPA O TIMEOUT ANTERIOR SE EXISTIR
        clearTimeout(timeout);
        // DEFINE UM NOVO TIMEOUT
        timeout = setTimeout(later, wait);
    };
}

// FUNÇÃO PARA FORMATAR DATA PARA EXIBIÇÃO
function formatarData(data) {
    // VERIFICA SE A DATA É VÁLIDA
    if (!data) return "";
    
    // CRIA UM OBJETO DATE A PARTIR DA STRING
    const dataObj = new Date(data);
    // VERIFICA SE A DATA É VÁLIDA
    if (isNaN(dataObj.getTime())) return "";
    
    // RETORNA A DATA FORMATADA NO PADRÃO BRASILEIRO
    return dataObj.toLocaleDateString("pt-BR");
}

// FUNÇÃO PARA FORMATAR DATA E HORA PARA EXIBIÇÃO
function formatarDataHora(data) {
    // VERIFICA SE A DATA É VÁLIDA
    if (!data) return "";
    
    // CRIA UM OBJETO DATE A PARTIR DA STRING
    const dataObj = new Date(data);
    // VERIFICA SE A DATA É VÁLIDA
    if (isNaN(dataObj.getTime())) return "";
    
    // RETORNA A DATA E HORA FORMATADAS NO PADRÃO BRASILEIRO
    return dataObj.toLocaleString("pt-BR");
}

// FUNÇÃO PARA TRUNCAR TEXTO LONGO
function truncarTexto(texto, limite = 100) {
    // VERIFICA SE O TEXTO EXISTE
    if (!texto) return "";
    
    // VERIFICA SE O TEXTO É MAIOR QUE O LIMITE
    if (texto.length <= limite) return texto;
    
    // RETORNA O TEXTO TRUNCADO COM RETICÊNCIAS
    return texto.substring(0, limite) + "...";
}

// FUNÇÃO PARA CAPITALIZAR PRIMEIRA LETRA
function capitalizarPrimeiraLetra(texto) {
    // VERIFICA SE O TEXTO EXISTE
    if (!texto) return "";
    
    // RETORNA O TEXTO COM A PRIMEIRA LETRA MAIÚSCULA
    return texto.charAt(0).toUpperCase() + texto.slice(1).toLowerCase();
}

// FUNÇÃO PARA VALIDAR EMAIL
function validarEmail(email) {
    // EXPRESSÃO REGULAR PARA VALIDAR EMAIL
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    // RETORNA TRUE SE O EMAIL É VÁLIDO, FALSE CASO CONTRÁRIO
    return regex.test(email);
}

// FUNÇÃO PARA GERAR ID ÚNICO
function gerarIdUnico() {
    // RETORNA UM ID ÚNICO BASEADO NO TIMESTAMP E UM NÚMERO ALEATÓRIO
    return Date.now().toString(36) + Math.random().toString(36).substr(2);
}

// FUNÇÃO PARA SCROLL SUAVE PARA UM ELEMENTO
function scrollParaElemento(seletor) {
    // SELECIONA O ELEMENTO PELO SELETOR
    const elemento = document.querySelector(seletor);
    // VERIFICA SE O ELEMENTO EXISTE
    if (elemento) {
        // FAZ O SCROLL SUAVE PARA O ELEMENTO
        elemento.scrollIntoView({ behavior: "smooth" });
    }
}

// FUNÇÃO PARA COPIAR TEXTO PARA A ÁREA DE TRANSFERÊNCIA
function copiarTexto(texto) {
    // VERIFICA SE A API navigator.clipboard ESTÁ DISPONÍVEL
    if (navigator.clipboard) {
        // USA A API MODERNA PARA COPIAR O TEXTO
        navigator.clipboard.writeText(texto).then(() => {
            mostrarSucesso("Texto copiado para a área de transferência!");
        }).catch(err => {
            console.error("Erro ao copiar texto:", err);
            mostrarErro("Erro ao copiar texto");
        });
    } else {
        // FALLBACK PARA NAVEGADORES MAIS ANTIGOS
        const textArea = document.createElement("textarea");
        textArea.value = texto;
        document.body.appendChild(textArea);
        textArea.select();
        try {
            document.execCommand("copy");
            mostrarSucesso("Texto copiado para a área de transferência!");
        } catch (err) {
            console.error("Erro ao copiar texto:", err);
            mostrarErro("Erro ao copiar texto");
        }
        document.body.removeChild(textArea);
    }
}
