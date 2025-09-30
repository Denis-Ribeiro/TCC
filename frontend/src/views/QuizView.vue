<template>
  <div class="quiz-view">
    <header class="quiz-header">
      <h1>Gerador de Quizzes</h1>
      <p>Digite um tópico, escolha as opções e teste seus conhecimentos!</p>
    </header>

    <!-- Área de Input do Tópico -->
    <div class="input-area">
      <input
        v-model="topic"
        @keyup.enter="generateQuiz"
        placeholder="Ex: Revolução Francesa, Fotossíntese..."
        :disabled="isLoading"
        class="topic-input"
      />
    </div>

    <!-- Opções de Customização -->
    <div class="options-area">
      <div class="option-group">
        <label for="question-count">Número de Questões:</label>
        <input
          type="number"
          id="question-count"
          v-model.number="questionCount"
          min="1"
          max="10"
          :disabled="isLoading"
        />
      </div>
      <div class="option-group">
        <label>Dificuldade:</label>
        <div class="difficulty-selectors">
          <button 
            @click="difficulty = 'fácil'" 
            :class="{ active: difficulty === 'fácil' }"
            :disabled="isLoading"
          >Fácil</button>
          <button 
            @click="difficulty = 'médio'" 
            :class="{ active: difficulty === 'médio' }"
            :disabled="isLoading"
          >Médio</button>
          <button 
            @click="difficulty = 'difícil'" 
            :class="{ active: difficulty === 'difícil' }"
            :disabled="isLoading"
          >Difícil</button>
        </div>
      </div>
    </div>
    
    <button @click="generateQuiz" :disabled="isLoading" class="generate-button">
      {{ isLoading ? 'Gerando...' : 'Gerar Quiz' }}
    </button>

    <!-- Exibição de Erro -->
    <p v-if="error" class="error-message">{{ error }}</p>

    <!-- Área de Exibição do Quiz -->
    <div v-if="quiz" class="quiz-container">
      <h2>Quiz sobre: {{ quiz.topic }}</h2>
      <div v-for="(question, index) in quiz.questions" :key="index" class="question-block">
        <p class="question-text"><strong>{{ index + 1 }}. {{ question.question }}</strong></p>
        <div class="options-list">
          <div v-for="(optionText, optionKey) in question.options" :key="optionKey" class="option">
            <input 
              type="radio" 
              :name="'question_' + index" 
              :value="optionKey"
              v-model="userAnswers[index]"
              :disabled="showResults"
            />
            <label>{{ optionKey.toUpperCase() }}) {{ optionText }}</label>
          </div>
        </div>
      </div>
      
      <!-- Mensagem de aviso para responder tudo -->
      <p v-if="!allQuestionsAnswered && !showResults" class="warning-message">
        Por favor, responda todas as questões para verificar o resultado.
      </p>

      <!-- Botão para verificar as respostas AGORA COM VALIDAÇÃO -->
      <button @click="checkAnswers" v-if="!showResults" class="check-button" :disabled="!allQuestionsAnswered">
        Verificar Respostas
      </button>

      <!-- Área de Resultados -->
      <div v-if="showResults" class="results-container">
        <h3>Resultados:</h3>
        <p class="score">Você acertou {{ score }} de {{ quiz.questions.length }}!</p>
        <div v-for="(question, index) in quiz.questions" :key="'result_' + index" class="result-block">
          <p><strong>Pergunta {{ index + 1 }}: {{ question.question }}</strong></p>
          <p :class="isCorrect(index) ? 'correct-answer' : 'incorrect-answer'">
            Sua resposta: {{ userAnswers[index] ? userAnswers[index].toUpperCase() : 'N/A' }}. 
            <span v-if="isCorrect(index)">✅ Correto!</span>
            <span v-else>❌ Incorreto. A resposta certa era {{ question.answer.toUpperCase() }}.</span>
          </p>
          <p class="explanation"><em>Explicação: {{ question.explanation }}</em></p>
        </div>
        <button @click="resetQuiz" class="reset-button">Gerar Novo Quiz</button>
      </div>
    </div>

  </div>
</template>

<script setup>
// Adicionado 'computed' para criar a lógica de validação
import { ref, computed } from 'vue';
import axios from 'axios';

const topic = ref('');
const quiz = ref(null);
const isLoading = ref(false);
const error = ref(null);
const userAnswers = ref({});
const showResults = ref(false);
const score = ref(0);
const questionCount = ref(3);
const difficulty = ref('médio');

// ▼▼▼ NOVA PROPRIEDADE COMPUTADA ▼▼▼
// Esta função reativa verifica se o número de respostas é igual ao número de questões.
const allQuestionsAnswered = computed(() => {
  if (!quiz.value) {
    return false;
  }
  const answeredCount = Object.keys(userAnswers.value).length;
  const totalQuestions = quiz.value.questions.length;
  return answeredCount === totalQuestions;
});

async function generateQuiz() {
  if (!topic.value.trim()) {
    error.value = 'Por favor, digite um tópico.';
    return;
  }
  
  isLoading.value = true;
  error.value = null;
  resetQuizState();

  try {
    const response = await axios.post('http://localhost:3000/gemini-quiz', {
      topic: topic.value,
      questionCount: questionCount.value,
      difficulty: difficulty.value
    });
    quiz.value = response.data.quiz;
  } catch (err) {
    console.error("Erro ao gerar o quiz:", err);
    error.value = 'Não foi possível gerar o quiz. Tente um tópico diferente ou verifique o servidor.';
  } finally {
    isLoading.value = false;
  }
}

function checkAnswers() {
  // A validação agora acontece antes mesmo do clique, mas mantemos a guarda aqui
  if (!allQuestionsAnswered.value) return;

  score.value = 0;
  if (!quiz.value) return;

  for (let i = 0; i < quiz.value.questions.length; i++) {
    if (userAnswers.value[i] === quiz.value.questions[i].answer) {
      score.value++;
    }
  }
  showResults.value = true;
}

function isCorrect(index) {
  return userAnswers.value[index] === quiz.value.questions[index].answer;
}

function resetQuizState() {
  quiz.value = null;
  userAnswers.value = {};
  showResults.value = false;
  score.value = 0;
}

function resetQuiz() {
  resetQuizState();
  topic.value = '';
}
</script>

<style scoped>
/* (Estilos permanecem os mesmos, omitidos para brevidade, mas devem ser mantidos no seu arquivo) */
.quiz-view {
  max-width: 800px;
  margin: 2rem auto;
  padding: 2rem;
  border-radius: 8px;
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
  background-color: #ffffff;
  color: #333;
}

.quiz-header {
  text-align: center;
  margin-bottom: 2rem;
}

.input-area {
  display: flex;
  margin-bottom: 1.5rem;
}

.topic-input {
  flex-grow: 1;
  padding: 0.75rem;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 1rem;
}

.options-area {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
  gap: 2rem;
  flex-wrap: wrap; /* Permite que os itens quebrem a linha em telas menores */
}

.option-group {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

#question-count {
  width: 60px;
  padding: 0.5rem;
  border-radius: 4px;
  border: 1px solid #ccc;
}

.difficulty-selectors {
  display: flex;
}

.difficulty-selectors button {
  padding: 0.5rem 1rem;
  border: 1px solid #007bff;
  background-color: white;
  color: #007bff;
  cursor: pointer;
  transition: background-color 0.2s, color 0.2s;
}

.difficulty-selectors button.active {
  background-color: #007bff;
  color: white;
}

.difficulty-selectors button:first-child {
  border-radius: 4px 0 0 4px;
}
.difficulty-selectors button:last-child {
  border-radius: 0 4px 4px 0;
}
.difficulty-selectors button:not(:last-child) {
  border-right: none;
}

.generate-button {
  display: block;
  width: 100%;
  padding: 1rem;
  font-size: 1.1rem;
  margin-top: 1rem;
  background-color: #28a745;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-weight: bold;
}

.generate-button:disabled {
  background-color: #a0a0a0;
}

.quiz-container {
  margin-top: 2rem;
}

.question-block {
  margin-bottom: 1.5rem;
  padding: 1rem;
  border: 1px solid #eee;
  border-radius: 4px;
}

.question-text {
  margin-bottom: 1rem;
}

.options-list .option {
  margin-bottom: 0.5rem;
  display: flex;
  align-items: center;
}

.options-list .option label {
  margin-left: 0.5rem;
}

.check-button, .reset-button {
  display: block;
  width: 100%;
  padding: 1rem;
  font-size: 1.1rem;
  margin-top: 1rem;
  background-color: #007bff;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

/* Novo estilo para o botão desabilitado */
.check-button:disabled {
  background-color: #a0a0a0;
  cursor: not-allowed;
}

.results-container {
  margin-top: 2rem;
  padding: 1rem;
  background-color: #f8f9fa;
  border-radius: 4px;
}

.score {
  font-size: 1.2rem;
  font-weight: bold;
  text-align: center;
  margin-bottom: 1.5rem;
}

.result-block {
  margin-bottom: 1rem;
}

.correct-answer {
  color: #28a745;
}

.incorrect-answer {
  color: #dc3545;
}

.explanation {
  font-size: 0.9rem;
  color: #6c757d;
  margin-top: 0.5rem;
  padding-left: 1rem;
  border-left: 3px solid #ccc;
}

.error-message {
  color: #dc3545;
  text-align: center;
}

/* Novo estilo para a mensagem de aviso */
.warning-message {
  text-align: center;
  color: #6c757d;
  font-style: italic;
  margin-top: 1rem;
}
</style>

