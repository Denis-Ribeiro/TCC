<template>
  <div class="game-component">
    <header class="game-component-header">
      <h2>Jogo da Forca</h2>
    </header>
    <!-- Conteúdo e Lógica do Jogo da Forca aqui -->
    <div v-if="!word" class="setup-container">
      <h3>Escolha uma matéria para a palavra secreta:</h3>
      <div class="subject-selection">
        <button @click="fetchWord('Ciências')">Ciências</button>
        <button @click="fetchWord('História')">História</button>
        <button @click="fetchWord('Geografia')">Geografia</button>
      </div>
    </div>
    <div v-if="isLoading" class="loading-message">Gerando um novo desafio...</div>
    <div v-if="error" class="error-message">{{ error }}</div>
    <div v-if="word" class="game-container">
      <div class="hint-box"><strong>Dica:</strong> {{ hint }}</div>
      <div class="word-display">
        <span v-for="(letter, index) in word" :key="index" class="letter-box">
          {{ guessedLetters.includes(letter) ? letter : '_' }}
        </span>
      </div>
      <div class="game-status-message" v-if="gameStatus !== 'playing'">
        <h2 v-if="gameStatus === 'won'" class="won-message">🎉 Você Venceu! 🎉</h2>
        <h2 v-if="gameStatus === 'lost'" class="lost-message">😢 Você Perdeu! A palavra era: {{ word }}</h2>
        <button @click="fetchWord(lastSubject)" class="play-again-button">Jogar de Novo</button>
      </div>
      <div class="keyboard" v-if="gameStatus === 'playing'">
        <button v-for="letter in 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'" :key="letter" @click="handleGuess(letter)" :disabled="guessedLetters.includes(letter) || wrongGuesses.includes(letter)" class="key">{{ letter }}</button>
      </div>
      <div class="wrong-guesses" v-if="wrongGuesses.length > 0 && gameStatus === 'playing'">
        <p><strong>Letras Erradas:</strong> {{ wrongGuesses.join(', ') }}</p>
        <p><strong>Tentativas restantes:</strong> {{ 6 - wrongGuesses.length }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import axios from 'axios';

const isLoading = ref(false);
const error = ref(null);
const word = ref('');
const hint = ref('');
const guessedLetters = ref([]);
const wrongGuesses = ref([]);
const lastSubject = ref('');

const gameStatus = computed(() => {
  if (wrongGuesses.value.length >= 6) return 'lost';
  if (word.value && [...word.value].every(letter => guessedLetters.value.includes(letter))) return 'won';
  return 'playing';
});

async function fetchWord(subject) {
  isLoading.value = true;
  error.value = null;
  lastSubject.value = subject;
  resetGameInternals();

  try {
    const response = await axios.post('http://localhost:3000/gemini-hangman', { subject });
    word.value = response.data.word.toUpperCase();
    hint.value = response.data.hint;
  } catch (err) {
    error.value = "Não foi possível buscar uma palavra. Tente novamente.";
  } finally {
    isLoading.value = false;
  }
}

function handleGuess(letter) {
  if (gameStatus.value !== 'playing') return;
  if (word.value.includes(letter)) {
    guessedLetters.value.push(letter);
  } else {
    wrongGuesses.value.push(letter);
  }
}

function resetGameInternals() {
  word.value = '';
  hint.value = '';
  guessedLetters.value = [];
  wrongGuesses.value = [];
}
</script>

<style scoped>
/* Estilos específicos para o Jogo da Forca */
.game-component { padding: 1rem; }
.word-display { display: flex; justify-content: center; gap: 0.5rem; margin-bottom: 2rem; }
.letter-box { width: 40px; height: 40px; display: flex; justify-content: center; align-items: center; font-size: 1.5rem; font-weight: bold; border-bottom: 3px solid #333; color: #333; }
.keyboard { display: flex; flex-wrap: wrap; justify-content: center; gap: 0.5rem; max-width: 600px; margin: 0 auto 2rem auto; }
.key { width: 40px; height: 40px; font-size: 1rem; cursor: pointer; border-radius: 4px; border: 1px solid #ccc; background-color: #f8f9fa; }
.key:disabled { background-color: #adb5bd; color: #fff; cursor: not-allowed; }
.wrong-guesses { margin-top: 1.5rem; }
.hint-box { padding: 1rem; background-color: #e9f5ff; border-left: 5px solid #007bff; border-radius: 4px; margin-bottom: 2rem; font-size: 1.1rem; color: #333; text-align: left; }
/* Estilos genéricos (podem ser compartilhados) */
.setup-container, .game-container { padding: 2rem; border-radius: 8px; margin-top: 1rem; }
.subject-selection { display: flex; justify-content: center; gap: 1rem; margin-top: 1rem; flex-wrap: wrap; }
.subject-selection button { padding: 0.8rem 1.5rem; font-size: 1rem; cursor: pointer; border-radius: 25px; border: 2px solid #007bff; background-color: #fff; color: #007bff; font-weight: bold; }
.loading-message, .error-message { text-align: center; margin-top: 1rem; font-size: 1.1rem; }
.error-message { color: #dc3545; }
.game-status-message { margin: 2rem 0; }
.won-message { color: #28a745; }
.lost-message { color: #dc3545; }
.play-again-button { padding: 0.8rem 2rem; font-size: 1.1rem; margin-top: 1rem; cursor: pointer; border-radius: 25px; border: none; background-color: #28a745; color: #fff; font-weight: bold; }
</style>
