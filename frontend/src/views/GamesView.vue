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
  text-align: center;
}
.games-header {
  margin-bottom: 2rem;
}

.game-selection-menu p {
  font-size: 1.2rem;
  color: #666;
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
  border-radius: 8px;
  border: 2px solid #007bff;
  background-color: #f8f9fa;
  cursor: pointer;
  text-align: left;
  transition: all 0.2s ease-in-out;
}

.game-option-button:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 16px rgba(0,0,0,0.1);
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
}

.back-to-menu-button {
  display: inline-block;
  margin-bottom: 2rem;
  padding: 0.5rem 1rem;
  background-color: #6c757d;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-weight: bold;
}
</style>

