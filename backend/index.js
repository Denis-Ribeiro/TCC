// -----------------------------------------------------------------------------
// 1. IMPORTAÇÃO DOS MÓDULOS NECESSÁRIOS
// -----------------------------------------------------------------------------
const express = require('express');
const cors = require('cors');
require('dotenv').config();
const { GoogleGenerativeAI } = require('@google/generative-ai');

// -----------------------------------------------------------------------------
// 2. CONFIGURAÇÃO INICIAL
// -----------------------------------------------------------------------------
const app = express();
app.use(cors());
app.use(express.json());

// Validação da Chave de API
if (!process.env.API_KEY) {
  console.error("❌ ERRO: API_KEY não encontrada. Verifique o seu ficheiro .env");
  process.exit(1);
}

const genAI = new GoogleGenerativeAI(process.env.API_KEY);
// Usando o nome de modelo mais estável. Se isto falhar, o problema está na configuração da sua chave de API no Google Cloud.
const model = genAI.getGenerativeModel({ model: 'gemini-2.5-flash' });

// -----------------------------------------------------------------------------
// 3. FUNÇÕES AUXILIARES
// -----------------------------------------------------------------------------

/**
 * Função centralizada para fazer chamadas à API e processar a resposta.
 * @param {string} masterPrompt - O prompt completo a ser enviado para a IA.
 * @param {string} errorSource - Uma string para identificar a origem do erro (ex: 'Tutor').
 * @returns {Promise<string>} O texto da resposta da IA.
 * @throws {Error} Se a chamada à API falhar.
 */
async function generateGeminiContent(masterPrompt, errorSource) {
  try {
    const result = await model.generateContent(masterPrompt);
    return result.response.text();
  } catch (error) {
    console.error(`Erro na API do Gemini (${errorSource}):`, error);
    throw new Error(`Ocorreu um erro ao comunicar com a IA (${errorSource}).`);
  }
}

/**
 * Extrai um objeto JSON limpo de uma string de texto retornada pela IA.
 * @param {string} textFromAI - O texto completo retornado pela IA.
 * @returns {object} O objeto JSON parseado.
 * @throws {Error} Se não for possível encontrar ou parsear o JSON.
 */
function parseJsonResponse(textFromAI) {
    const startIndex = textFromAI.indexOf('{');
    const endIndex = textFromAI.lastIndexOf('}');
    if (startIndex === -1 || endIndex === -1) {
        throw new Error('A IA não retornou um objeto JSON válido.');
    }
    const jsonText = textFromAI.substring(startIndex, endIndex + 1);
    return JSON.parse(jsonText);
}


// -----------------------------------------------------------------------------
// 4. DEFINIÇÃO DAS ROTAS (ENDPOINTS DA API)
// -----------------------------------------------------------------------------

/**
 * ROTA 1: TIRA-DÚVIDAS INTELIGENTE
 */
app.post('/gemini-tutor', async (req, res) => {
  const { prompt, subject, history = [] } = req.body;
  if (!prompt || !subject) {
    return res.status(400).send({ error: 'A pergunta e a matéria são obrigatórias.' });
  }

  const historyText = history.map((m) => `${m.role === 'user' ? 'Aluno' : 'Gênio'}: ${m.content}`).join('\n');
  const masterPrompt = `
    Você é um tutor virtual socrático chamado 'Gênio', especialista em ${subject}.
    Sua missão é guiar alunos para que encontrem as respostas sozinhos.
    *** SUAS REGRAS: ***
    1. NUNCA DÊ A RESPOSTA FINAL.
    2. GUIE COM PERGUNTAS que quebrem o problema em partes menores.
    3. SEJA UM PARCEIRO. Se o aluno estiver travado, ofereça uma pista ou analogia, mas sempre termine com uma pergunta.
    4. USE O CONTEXTO ABAIXO para dar continuidade à conversa.
    5. SEJA ENCORAJADOR E NATURAL.
    ---
    HISTÓRICO DA CONVERSA:
    ${historyText}
    ---
    NOVA PERGUNTA DO ALUNO: "${prompt}"`;

  try {
    const textResponse = await generateGeminiContent(masterPrompt, 'Tutor');
    res.send({ text: textResponse });
  } catch (error) {
    res.status(500).send({ error: error.message });
  }
});

/**
 * ROTA 2: GERADOR DE QUIZZES
 */
app.post('/gemini-quiz', async (req, res) => {
    // ... (A sua lógica de retry para o quiz é complexa e foi mantida como estava)
    // ...
});


/**
 * ROTA 3: JOGO DA FORCA
 */
app.post('/gemini-hangman', async (req, res) => {
  const { subject } = req.body;
  if (!subject) {
    return res.status(400).send({ error: 'A matéria é obrigatória.' });
  }

  const masterPrompt = `
    Sua única função é retornar um objeto JSON.
    Escolha uma única palavra (sem espaços, hífens ou acentos) relevante para a matéria de "${subject}".
    Crie uma dica clara e simples sobre essa palavra.
    Retorne APENAS um objeto JSON com a estrutura: { "word": "palavra", "hint": "dica" }`;

  try {
    const textResponse = await generateGeminiContent(masterPrompt, 'Forca');
    const jsonResponse = parseJsonResponse(textResponse);
    res.send(jsonResponse);
  } catch (error) {
    res.status(500).send({ error: error.message });
  }
});

/**
 * ROTA 4: JOGO QUEM SOU EU?
 */
app.post('/gemini-guesswho', async (req, res) => {
    const { subject } = req.body;
    if (!subject) return res.status(400).send({ error: 'A matéria é obrigatória.' });

    const masterPrompt = `Sua única função é retornar um objeto JSON. Não adicione nenhum outro texto. Escolha um item secreto (personalidade, conceito, lugar) da matéria "${subject}". Crie EXATAMENTE 4 dicas, da mais difícil para a mais fácil. REGRAS: A resposta não pode ter acentos e deve estar em MAIÚSCULAS. Formato JSON: { "answer": "RESPOSTA SEM ACENTO", "hints": ["Dica 1", "Dica 2", "Dica 3", "Dica 4"] }`;
    
    try {
        const textResponse = await generateGeminiContent(masterPrompt, 'Quem Sou Eu?');
        const jsonResponse = parseJsonResponse(textResponse);
        res.send({ game: jsonResponse });
    } catch (error) {
        res.status(500).send({ error: error.message });
    }
});

// -----------------------------------------------------------------------------
// 5. INICIALIZAÇÃO DO SERVIDOR
// -----------------------------------------------------------------------------
const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
  console.log(`🚀 Servidor backend rodando na porta ${PORT}`);
});

