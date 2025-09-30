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

if (!process.env.API_KEY) {
  console.error("❌ ERRO: API_KEY não encontrada. Defina no arquivo .env");
  process.exit(1);
}

const genAI = new GoogleGenerativeAI(process.env.API_KEY);
const model = genAI.getGenerativeModel({ model: 'gemini-1.5-flash-latest' });

// -----------------------------------------------------------------------------
// 3. DEFINIÇÃO DAS ROTAS (ENDPOINTS DA API)
// -----------------------------------------------------------------------------

/**
 * ROTA 1: TIRA-DÚVIDAS INTELIGENTE
 */
app.post('/gemini-tutor', async (req, res) => {
  const { prompt, subject, history = [] } = req.body;

  if (!prompt || !subject) {
    return res
      .status(400)
      .send({ error: 'A pergunta e a matéria são obrigatórias.' });
  }

  const historyText = history
    .map((m) => `${m.role === 'user' ? 'Aluno' : 'Gênio'}: ${m.content}`)
    .join('\n');

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
    NOVA PERGUNTA DO ALUNO: "${prompt}"
  `;

  try {
    const result = await model.generateContent(masterPrompt);
    const response = await result.response;
    return res.send({ text: response.text() });
  } catch (error) {
    console.error('Erro na API do Gemini (Tutor):', error);
    return res
      .status(500)
      .send({ error: 'Ocorreu um erro ao processar sua pergunta.' });
  }
});

/**
 * ROTA 2: GERADOR DE QUIZZES
 */
app.post('/gemini-quiz', async (req, res) => {
  const { topic, questionCount, difficulty } = req.body;

  if (!topic || !questionCount || !difficulty) {
    return res
      .status(400)
      .send({ error: 'Todos os parâmetros são obrigatórios.' });
  }

  const MAX_ATTEMPTS = 5;
  let allQuestions = [];
  let attempts = 0;

  while (allQuestions.length < questionCount && attempts < MAX_ATTEMPTS) {
    attempts++;
    const questionsNeeded = questionCount - allQuestions.length;
    console.log(`Tentativa ${attempts}: Faltam ${questionsNeeded} questões.`);

    try {
      const masterPrompt = `
        Sua única função é retornar um objeto JSON. Não adicione nenhum outro texto, markdown ou explicação.
        Aja como um professor. Crie um quiz sobre o tópico "${topic}".

        Requisitos:
        - Gere EXATAMENTE ${questionsNeeded} nova(s) pergunta(s).
        - A dificuldade deve ser: ${difficulty}.
        - Cada pergunta deve ter 4 alternativas (a, b, c, d).
        - A resposta (answer) deve ser a letra da alternativa correta.

        Estrutura do JSON esperado:
        {
          "topic": "${topic}",
          "questions": [
            {
              "question": "Texto da pergunta...",
              "options": { "a": "...", "b": "...", "c": "...", "d": "..." },
              "answer": "c",
              "explanation": "Explicação da resposta."
            }
          ]
        }
      `;

      const result = await model.generateContent(masterPrompt);
      const response = await result.response;
      const textFromAI = response.text();

      const startIndex = textFromAI.indexOf('{');
      const endIndex = textFromAI.lastIndexOf('}');
      if (startIndex === -1 || endIndex === -1)
        throw new Error('A IA não retornou um JSON válido.');

      const jsonText = textFromAI.substring(startIndex, endIndex + 1);
      const partialQuiz = JSON.parse(jsonText);

      if (partialQuiz.questions && partialQuiz.questions.length > 0) {
        allQuestions.push(...partialQuiz.questions);
      }
    } catch (error) {
      console.error(`Erro na tentativa ${attempts}: ${error.message}`);
    }
  }

  if (allQuestions.length >= questionCount) {
    const finalQuiz = {
      topic,
      questions: allQuestions.slice(0, questionCount),
    };
    return res.send({ quiz: finalQuiz });
  } else {
    return res.status(500).send({
      error: 'A IA não conseguiu gerar o quiz completo. Por favor, tente novamente.',
    });
  }
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
    Aja como um gerador de Jogo da Forca educacional.
    Sua única função é retornar um objeto JSON.
    Escolha uma única palavra ou termo (sem espaços ou hífens) relevante para "${subject}" para alunos do ensino fundamental. A palavra não deve ter acentos.
    Crie uma dica clara e simples sobre essa palavra.
    Retorne APENAS um objeto JSON:
    { "word": "palavra", "hint": "dica sobre a palavra" }
  `;

  try {
    const result = await model.generateContent(masterPrompt);
    const response = await result.response;
    const text = response.text().replace(/```json/g, '').replace(/```/g, '');
    return res.send(JSON.parse(text));
  } catch (error) {
    console.error('Erro na API do Gemini (Forca):', error);
    return res
      .status(500)
      .send({ error: 'Não foi possível gerar a palavra para o jogo.' });
  }
});

// ROTA 5: JOGO QUEM SOU EU?
app.post('/gemini-guesswho', async (req, res) => {
    const { subject } = req.body;
    if (!subject) return res.status(400).send({ error: 'A matéria é obrigatória.' });

    const masterPrompt = `Sua única função é retornar um objeto JSON. Não adicione nenhum outro texto. Escolha um item secreto (personalidade, conceito, lugar) da matéria "${subject}". Crie EXATAMENTE 4 dicas, da mais difícil para a mais fácil. REGRAS: A resposta não pode ter acentos e deve estar em MAIÚSCULAS. Formato JSON: { "answer": "RESPOSTA SEM ACENTO", "hints": ["Dica 1", "Dica 2", "Dica 3", "Dica 4"] }`;
    try {
        const result = await model.generateContent(masterPrompt);
        const text = result.response.text();
        const jsonText = text.substring(text.indexOf('{'), text.lastIndexOf('}') + 1);
        res.send(JSON.parse(jsonText));
    } catch (error) {
        console.error("Erro no 'Quem Sou Eu?':", error);
        res.status(500).send({ error: 'Não foi possível gerar desafio para "Quem Sou Eu?".' });
    }
});

// -----------------------------------------------------------------------------
// 4. INICIALIZAÇÃO DO SERVIDOR
// -----------------------------------------------------------------------------
const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
  console.log(`🚀 Servidor backend rodando na porta ${PORT}`);
});
