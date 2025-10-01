<template>
  <div class="games-view">
    <header class="games-header">
      <h1>Jogos Educativos</h1>
    </header>

    <!-- Menu de Seleção de Jogo -->
    <div v-if="!selectedGame" class="game-selection-menu">
      <p>Escolha um jogo abaixo para começar a se divertir e aprender!</p>
      <div class="game-options">
        <button @click="selectedGame = 'hangman'" class="game-option-button">
          <span class="game-title">Jogo da Forca</span>
          <span class="game-description">Adivinhe a palavra secreta letra por letra.</span>
        </button>
        <button @click="selectedGame = 'guesswho'" class="game-option-button">
          <span class="game-title">Quem Sou Eu?</span>
          <span class="game-description">Use as dicas para descobrir o item secreto.</span>
        </button>
      </div>
    </div>

    <!-- Área de Exibição do Jogo Selecionado -->
    <div v-else class="selected-game-container">
      <button @click="selectedGame = null" class="back-to-menu-button">
        &larr; Voltar para o Menu de Jogos
      </button>
      
      <!-- Renderiza o componente do jogo escolhido -->
      <HangmanGame v-if="selectedGame === 'hangman'" />
      <GuessWhoGame v-if="selectedGame === 'guesswho'" />
    </div>

  </div>
</template>

<script setup>
import { ref } from 'vue';
import HangmanGame from '../components/HangmanGame.vue';
import GuessWhoGame from '../components/GuessWhoGame.vue';

const selectedGame = ref(null); // 'hangman', 'guesswho', ou null
</script>

<style scoped>
.games-view {
  max-width: 900px;
  margin: 2rem auto;
  padding: 2rem;
  border-radius: 16px;
  box-shadow: 0 8px 20px rgba(0,0,0,0.25);
  background: linear-gradient(135deg, #22306f, #000000 30%, #1b2a6b 100%);
  color: #f0f0f0;
  text-align: center;
}

.games-header {
  margin-bottom: 2rem;
}

.game-selection-menu p {
  font-size: 1.2rem;
  color: #c0c0c0;
  margin-bottom: 2rem;
}

.game-options {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 2rem;
}

.game-option-button {
  display: flex;
  flex-direction: column;
  padding: 2rem;
  border-radius: 12px;
  border: 2px solid #4a6fb3;
  background: rgba(30, 40, 70, 0.6);
  cursor: pointer;
  text-align: left;
  transition: all 0.2s ease-in-out;
  color: #f0f0f0;
}

.game-option-button:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 16px rgba(0,0,0,0.3);
  background-color: #007bff;
  color: white;
}

.game-title {
  font-size: 1.5rem;
  font-weight: bold;
  margin-bottom: 0.5rem;
}

.game-description {
  font-size: 1rem;
}

.selected-game-container {
  margin-top: 1rem;
  padding: 1rem;
  border-radius: 12px;
  background: rgba(20, 30, 60, 0.7);
  box-shadow: inset 0 0 10px rgba(0,0,0,0.3);
}

.back-to-menu-button {
  display: inline-block;
  margin-bottom: 2rem;
  padding: 0.5rem 1rem;
  background-color: #6c757d;
  color: white;
  border: none;
  border-radius: 12px;
  cursor: pointer;
  font-weight: bold;
  transition: background 0.3s;
}

.back-to-menu-button:hover {
  background-color: #5a6268;
}
</style>
