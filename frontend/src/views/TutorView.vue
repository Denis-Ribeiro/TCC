<template>
  <div class="tutor-view">
    <header class="tutor-header">
      <h1>Gênio, o Tutor Virtual</h1>
      <p>Faça qualquer pergunta sobre suas matérias escolares!</p>
    </header>

    <div class="chat-window" ref="chatWindow">
      <div v-for="message in messages" :key="message.id" class="message-wrapper">
        <div class="message" :class="message.role === 'user' ? 'user-message' : 'ai-message'">
          <strong class="message-role">{{ message.role === 'user' ? 'Você' : 'Gênio' }}</strong>
          <p class="message-content">{{ message.content }}</p>
        </div>
      </div>
    </div>

    <div v-if="isLoading" class="loading-indicator">
      Gênio está digitando...
    </div>

    <div class="input-area">
      <input
        v-model="currentQuestion"
        @keyup.enter="sendMessage"
        placeholder="Digite sua pergunta aqui..."
        :disabled="isLoading"
      />
      <button @click="sendMessage" :disabled="isLoading">
        Enviar
      </button>
    </div>
    
    <p v-if="error" class="error-message">{{ error }}</p>

  </div>
</template>

<script setup>
import { ref, nextTick } from 'vue';
import axios from 'axios';

// --- Estado Reativo do Componente ---

// Array para armazenar o histórico da conversa
const messages = ref([]);
// String para guardar a pergunta que está sendo digitada no input
const currentQuestion = ref('');
// Booleano para controlar a exibição do loading e desabilitar o input/botão
const isLoading = ref(false);
// String para armazenar mensagens de erro
const error = ref(null);
// Referência para a div da janela de chat, para o scroll automático
const chatWindow = ref(null);

// --- Funções ---

async function sendMessage() {
  const userPrompt = currentQuestion.value.trim();
  if (!userPrompt) return;

  // Adiciona a pergunta do usuário ao histórico local para exibição imediata
  messages.value.push({
    id: Date.now(),
    role: 'user',
    content: userPrompt,
  });

  // Prepara para a chamada da API
  // Cria uma cópia do histórico SEM a última pergunta do usuário, pois ela já vai no 'prompt'
  const historyToSend = messages.value.slice(0, -1).map(msg => ({
    role: msg.role,
    content: msg.content
  }));
  
  currentQuestion.value = '';
  isLoading.value = true;
  error.value = null;
  scrollToBottom();

  try {
    // Faz a requisição POST, agora ENVIANDO O HISTÓRICO junto com a nova pergunta
    const response = await axios.post('http://localhost:3000/gemini-tutor', {
      prompt: userPrompt,
      subject: 'Conhecimentos Gerais',
      history: historyToSend 
    });

    // Adiciona a resposta da IA ao histórico do chat
    messages.value.push({
      id: Date.now() + 1,
      role: 'ai',
      content: response.data.text,
    });

  } catch (err) {
    console.error("Erro ao contatar o backend:", err);
    error.value = "Desculpe, não consegui me conectar ao meu cérebro. Verifique se o servidor está rodando e tente novamente.";
    messages.value.push({
      id: Date.now() + 1,
      role: 'ai',
      content: "Ops! Ocorreu um erro. Por favor, tente novamente mais tarde.",
    });

  } finally {
    isLoading.value = false;
    scrollToBottom();
  }
}

async function scrollToBottom() {
  await nextTick();
  if (chatWindow.value) {
    chatWindow.value.scrollTop = chatWindow.value.scrollHeight;
  }
}
</script>

<style scoped>
/* Estilo geral do container */
.tutor-view {
  max-width: 800px;
  margin: 2rem auto;
  padding: 1rem;
  display: flex;
  flex-direction: column;
  height: 85vh;
  border: 1px solid #ccc;
  border-radius: 8px;
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
  background-color: #f9f9f9;
}

.tutor-header {
  text-align: center;
  border-bottom: 1px solid #eee;
  padding-bottom: 1rem;
}

/* Janela do Chat */
.chat-window {
  flex-grow: 1;
  overflow-y: auto;
  padding: 1rem;
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.message-wrapper {
  display: flex;
  width: 100%;
}

.message {
  max-width: 70%;
  padding: 0.5rem 1rem;
  border-radius: 12px;
  line-height: 1.5;
}

.message-role {
  font-size: 0.8rem;
  margin-bottom: 0.25rem;
  color: #555;
  font-weight: bold;
}

/* Estilo da mensagem do Usuário */
.user-message {
  background-color: #007bff;
  color: white;
  margin-left: auto;
}

.user-message .message-role {
  color: #e0e0e0;
}

/* Estilo da mensagem da IA */
.ai-message {
  background-color: #e9ecef;
  color: #333;
  margin-right: auto;
}

/* Indicador de Carregamento */
.loading-indicator {
  text-align: center;
  padding: 0.5rem;
  color: #888;
  font-style: italic;
}

/* Área de Input */
.input-area {
  display: flex;
  padding: 1rem;
  border-top: 1px solid #eee;
  gap: 0.5rem;
}

.input-area input {
  flex-grow: 1;
  padding: 0.75rem;
  border: 1px solid #ccc;
  border-radius: 20px;
  font-size: 1rem;
}

.input-area button {
  padding: 0.75rem 1.5rem;
  border: none;
  background-color: #007bff;
  color: white;
  border-radius: 20px;
  cursor: pointer;
  font-size: 1rem;
  font-weight: bold;
  transition: background-color 0.2s;
}

.input-area button:hover {
  background-color: #0056b3;
}

.input-area button:disabled,
.input-area input:disabled {
  background-color: #a0a0a0;
  cursor: not-allowed;
}

/* Mensagem de Erro */
.error-message {
  text-align: center;
  color: #d93025;
  padding: 0.5rem;
}
</style>