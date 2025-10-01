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
      
      <p v-if="!allQuestionsAnswered && !showResults" class="warning-message">
        Por favor, responda todas as questões para verificar o resultado.
      </p>

      <button @click="checkAnswers" v-if="!showResults" class="check-button" :disabled="!allQuestionsAnswered">
        Verificar Respostas
      </button>

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

const allQuestionsAnswered = computed(() => {
  if (!quiz.value) return false;
  return Object.keys(userAnswers.value).length === quiz.value.questions.length;
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
    error.value = 'Não foi possível gerar o quiz. Tente novamente ou verifique o servidor.';
  } finally {
    isLoading.value = false;
  }
}

function checkAnswers() {
  if (!allQuestionsAnswered.value) return;
  score.value = quiz.value.questions.filter((q, i) => userAnswers.value[i] === q.answer).length;
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
.quiz-view {
  max-width: 900px;
  margin: 2rem auto;
  padding: 2rem;
  border-radius: 16px;
  box-shadow: 0 8px 20px rgba(0,0,0,0.25);
  background: linear-gradient(135deg, #22306f, #000000 30%, #1b2a6b 100%);
  color: #f0f0f0;
}

.quiz-header {
  text-align: center;
  margin-bottom: 2rem;
}

.topic-input {
  flex-grow: 1;
  padding: 0.8rem;
  border: none;
  border-radius: 20px;
  font-size: 1rem;
  outline: none;
}

.options-area {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
  gap: 2rem;
  flex-wrap: wrap;
}

.option-group {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

#question-count {
  width: 70px;
  padding: 0.6rem;
  border-radius: 12px;
  border: none;
  text-align: center;
}

.difficulty-selectors button {
  padding: 0.6rem 1.2rem;
  border: none;
  background-color: #4a6fb3;
  color: white;
  border-radius: 12px;
  cursor: pointer;
  transition: 0.3s;
}

.difficulty-selectors button.active {
  background-color: #007bff;
  font-weight: bold;
}

.generate-button, .check-button, .reset-button {
  display: block;
  width: 100%;
  padding: 1rem;
  font-size: 1.1rem;
  margin-top: 1rem;
  background: #28a745;
  color: white;
  border: none;
  border-radius: 20px;
  cursor: pointer;
  font-weight: bold;
  transition: background 0.3s;
}

.generate-button:hover,
.check-button:hover,
.reset-button:hover {
  background: #218838;
}

.generate-button:disabled,
.check-button:disabled {
  background-color: #a0a0a0;
  cursor: not-allowed;
}

.quiz-container {
  margin-top: 2rem;
  padding: 1.5rem;
  border-radius: 12px;
  background: rgba(30,40,70,0.6);
  box-shadow: inset 0 0 10px rgba(0,0,0,0.3);
}

.question-block {
  margin-bottom: 1.5rem;
  padding: 1rem;
  border-radius: 12px;
  background: rgba(40,50,90,0.7);
}

.options-list .option {
  margin-bottom: 0.5rem;
  display: flex;
  align-items: center;
}

.options-list .option label {
  margin-left: 0.5rem;
  color: #e0e0e0;
}

.results-container {
  margin-top: 2rem;
  padding: 1rem;
  border-radius: 12px;
  background: rgba(20,30,60,0.7);
}

.score {
  font-size: 1.3rem;
  font-weight: bold;
  text-align: center;
  margin-bottom: 1.5rem;
}

.correct-answer {
  color: #28a745;
}

.incorrect-answer {
  color: #dc3545;
}

.error-message {
  color: #ff4d4d;
  text-align: center;
  font-weight: bold;
  margin-top: 1rem;
}

.warning-message {
  text-align: center;
  color: #c0c0c0;
  font-style: italic;
  margin-top: 1rem;
}
</style>
