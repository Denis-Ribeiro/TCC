<template>
  <div class="tutor-view">
    <header class="tutor-header">
      <h1>🤖 Gênio, o Tutor Virtual</h1>
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

const messages = ref([]);
const currentQuestion = ref('');
const isLoading = ref(false);
const error = ref(null);
const chatWindow = ref(null);

async function sendMessage() {
  const userPrompt = currentQuestion.value.trim();
  if (!userPrompt) return;

  messages.value.push({
    id: Date.now(),
    role: 'user',
    content: userPrompt,
  });

  const historyToSend = messages.value.slice(0, -1).map(msg => ({
    role: msg.role,
    content: msg.content
  }));
  
  currentQuestion.value = '';
  isLoading.value = true;
  error.value = null;
  scrollToBottom();

  try {
    const response = await axios.post('http://localhost:3000/gemini-tutor', {
      prompt: userPrompt,
      subject: 'Conhecimentos Gerais',
      history: historyToSend 
    });

    messages.value.push({
      id: Date.now() + 1,
      role: 'ai',
      content: response.data.text,
    });

  } catch (err) {
    console.error("Erro ao contatar o backend:", err);
    error.value = "Desculpe, não consegui me conectar. Verifique o servidor e tente novamente.";
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
.tutor-view {
  max-width: 900px;
  margin: 2rem auto;
  padding: 1rem;
  display: flex;
  flex-direction: column;
  height: 85vh;
  border-radius: 16px;
  box-shadow: 0 6px 16px rgba(0,0,0,0.3);
  background: linear-gradient(135deg, #22306f, #000000 30%, #ffffff 60%, #1b2a6b 100%);
  color: #fff;
}

.tutor-header {
  text-align: center;
  padding-bottom: 1rem;
  border-bottom: 1px solid rgba(255,255,255,0.2);
}

.tutor-header h1 {
  font-size: 1.8rem;
  color: #61dafb;
}

.tutor-header p {
  color: #cbd5e1;
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
  padding: 0.75rem 1rem;
  border-radius: 16px;
  line-height: 1.5;
  box-shadow: 0 2px 8px rgba(0,0,0,0.2);
  animation: fadeIn 0.3s ease;
}

.message-role {
  font-size: 0.8rem;
  margin-bottom: 0.25rem;
  font-weight: bold;
  display: block;
}

/* Mensagem do Usuário */
.user-message {
  background-color: #007bff;
  color: #fff;
  margin-left: auto;
  border-bottom-right-radius: 4px;
}

.user-message .message-role {
  color: #e0e0e0;
}

/* Mensagem da IA */
.ai-message {
  background-color: rgba(255,255,255,0.9);
  color: #222;
  margin-right: auto;
  border-bottom-left-radius: 4px;
}

/* Indicador de Carregamento */
.loading-indicator {
  text-align: center;
  padding: 0.5rem;
  color: #cbd5e1;
  font-style: italic;
}

/* Área de Input */
.input-area {
  display: flex;
  padding: 1rem;
  border-top: 1px solid rgba(255,255,255,0.2);
  gap: 0.5rem;
}

.input-area input {
  flex-grow: 1;
  padding: 0.75rem;
  border: none;
  border-radius: 20px;
  font-size: 1rem;
  outline: none;
  background: rgba(255,255,255,0.9);
  color: #222;
}

.input-area button {
  padding: 0.75rem 1.5rem;
  border: none;
  background: linear-gradient(135deg, #007bff, #334d99);
  color: white;
  border-radius: 20px;
  cursor: pointer;
  font-size: 1rem;
  font-weight: bold;
  transition: all 0.3s ease;
}

.input-area button:hover {
  background: linear-gradient(135deg, #0056b3, #22306f);
  transform: scale(1.05);
}

.input-area button:disabled,
.input-area input:disabled {
  background-color: #555;
  cursor: not-allowed;
}

/* Mensagem de Erro */
.error-message {
  text-align: center;
  color: #ff6b6b;
  padding: 0.5rem;
}

/* Animação suave */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(5px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
