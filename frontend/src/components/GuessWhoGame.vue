<template>
  <div class="game-component">
    <header class="game-component-header">
      <h2>Quem Sou Eu?</h2>
    </header>

    <!-- Tela inicial -->
    <div v-if="!gameStarted" class="setup-container">
      <h3>Escolha uma matéria para começar:</h3>
      <div class="subject-selection">
        <button @click="startGame('Ciências')">Ciências</button>
        <button @click="startGame('História')">História</button>
        <button @click="startGame('Geografia')">Geografia</button>
      </div>
    </div>

    <!-- Mensagens -->
    <div v-if="isLoading" class="loading-message">Gerando um novo desafio...</div>
    <div v-if="error" class="error-message">{{ error }}</div>

    <!-- Tela do jogo -->
    <div v-if="gameStarted && !isLoading" class="game-container">
      <div class="hints-container">
        <h3>Dicas Reveladas:</h3>
        <ul class="hints-list">
          <li
            v-for="(hint, index) in revealedHints"
            :key="index"
            class="hint-item"
          >
            <strong>Dica {{ index + 1 }}:</strong> {{ hint }}
          </li>
        </ul>
      </div>

      <!-- Área de ações -->
      <div v-if="gameStatus === 'playing'" class="action-area">
        <div class="guess-area">
          <input
            v-model="userGuess"
            @keyup.enter="submitGuess"
            placeholder="Quem ou o que sou eu?"
            class="guess-input"
          />
          <button @click="submitGuess" class="submit-button">Adivinhar</button>
        </div>
        <button
          @click="showNextHint"
          :disabled="allHintsRevealed"
          class="next-hint-button"
        >
          {{ allHintsRevealed ? "Todas as dicas reveladas" : "Próxima Dica" }}
        </button>

        <!-- NOVO BOTÃO DE REVELAR RESPOSTA -->
        <button
          v-if="allHintsRevealed && gameStatus === 'playing'"
          @click="revealAnswer"
          class="reveal-answer-button"
        >
          Revelar Resposta
        </button>
      </div>

      <!-- Mensagem de status -->
      <div class="game-status-message" v-if="gameStatus !== 'playing'">
        <h2 v-if="gameStatus === 'won'" class="won-message">
          🎉 Parabéns, você acertou! 🎉
        </h2>
        <h2 v-if="gameStatus === 'lost'" class="lost-message">
          😢 Fim de jogo! A resposta era: {{ answer }}
        </h2>
        <button @click="startGame(lastSubject)" class="play-again-button">
          Jogar de Novo
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from "vue";
import axios from "axios";

const isLoading = ref(false);
const error = ref(null);
const gameStarted = ref(false);
const lastSubject = ref("");

const answer = ref("");
const allHints = ref([]);
const currentHintIndex = ref(0);
const userGuess = ref("");
const gameStatus = ref("playing");

const revealedHints = computed(() =>
  allHints.value.slice(0, currentHintIndex.value + 1)
);
const allHintsRevealed = computed(
  () => currentHintIndex.value >= allHints.value.length - 1
);

async function startGame(subject) {
  isLoading.value = true;
  error.value = null;
  lastSubject.value = subject;
  resetGameInternals();

  try {
    const response = await axios.post(
      "http://localhost:3000/gemini-guesswho",
      { subject }
    );
    answer.value = response.data.answer;
    allHints.value = response.data.hints;
    gameStarted.value = true;
  } catch (err) {
    error.value = "Não foi possível iniciar o jogo. Tente novamente.";
  } finally {
    isLoading.value = false;
  }
}

function normalizeText(text) {
  return text
    .toString()
    .normalize("NFD")
    .replace(/[\u0300-\u036f]/g, "")
    .replace(/\s+/g, "")
    .toUpperCase();
}

function submitGuess() {
  if (!userGuess.value.trim()) return;
  if (normalizeText(userGuess.value) === normalizeText(answer.value)) {
    gameStatus.value = "won";
  } else {
    alert(`"${userGuess.value}" não é a resposta correta. Tente novamente!`);
    userGuess.value = "";
  }
}

function showNextHint() {
  if (!allHintsRevealed.value) {
    currentHintIndex.value++;
  }
}

// Função para revelar resposta
function revealAnswer() {
  gameStatus.value = "lost";
}

function resetGameInternals() {
  gameStarted.value = true; // Mantém na tela do jogo
  answer.value = "";
  allHints.value = [];
  currentHintIndex.value = 0;
  userGuess.value = "";
  gameStatus.value = "playing";
}
</script>

<style scoped>
/* Estilos específicos para o Quem Sou Eu? */
.game-component {
  padding: 1rem;
}
.hints-container {
  margin-bottom: 2rem;
  text-align: left;
}
.hints-list {
  list-style-type: none;
  padding: 0;
}
.hint-item {
  background-color: #f8f9fa;
  padding: 1rem;
  border-radius: 4px;
  margin-bottom: 0.5rem;
  border-left: 4px solid #17a2b8;
  color: #333; /* <-- CORREÇÃO APLICADA AQUI */
}
.action-area {
  margin-top: 1.5rem;
}
.guess-area {
  display: flex;
  gap: 0.5rem;
  margin-bottom: 1rem;
}
.guess-input {
  flex-grow: 1;
  padding: 0.75rem;
  font-size: 1rem;
  border: 1px solid #ccc;
  border-radius: 4px;
}
.submit-button {
  background-color: #28a745;
  color: white;
  padding: 0.75rem 1.5rem;
  font-weight: bold;
  border-radius: 4px;
  border: none;
  cursor: pointer;
}
.next-hint-button {
  width: 100%;
  background-color: #ffc107;
  color: #212529;
  padding: 0.75rem 1.5rem;
  font-size: 1rem;
  font-weight: bold;
  border-radius: 4px;
  border: none;
  cursor: pointer;
}
.next-hint-button:disabled {
  background-color: #e9ecef;
  cursor: not-allowed;
}
/* Novo estilo para botão de revelar resposta */
.reveal-answer-button {
  width: 100%;
  background-color: #dc3545;
  color: #fff;
  padding: 0.75rem 1.5rem;
  font-size: 1rem;
  font-weight: bold;
  border-radius: 4px;
  border: none;
  cursor: pointer;
  margin-top: 1rem;
}
/* Estilos genéricos (podem ser compartilhados) */
.setup-container,
.game-container {
  padding: 2rem;
  border-radius: 8px;
  margin-top: 1rem;
}
.subject-selection {
  display: flex;
  justify-content: center;
  gap: 1rem;
  margin-top: 1rem;
  flex-wrap: wrap;
}
.subject-selection button {
  padding: 0.8rem 1.5rem;
  font-size: 1rem;
  cursor: pointer;
  border-radius: 25px;
  border: 2px solid #007bff;
  background-color: #fff;
  color: #007bff;
  font-weight: bold;
}
.loading-message,
.error-message {
  text-align: center;
  margin-top: 1rem;
  font-size: 1.1rem;
}
.error-message {
  color: #dc3545;
}
.game-status-message {
  margin: 2rem 0;
}
.won-message {
  color: #28a745;
}
.lost-message {
  color: #dc3545;
}
.play-again-button {
  padding: 0.8rem 2rem;
  font-size: 1.1rem;
  margin-top: 1rem;
  cursor: pointer;
  border-radius: 25px;
  border: none;
  background-color: #28a745;
  color: #fff;
  font-weight: bold;
}
</style>
