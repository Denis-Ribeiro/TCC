<template>
  <div id="app-layout">
    <aside class="sidebar">
      <div class="logo">
        Plataforma<br>de Estudos
      </div>
      <nav class="main-nav">
        <a href="http://127.0.0.1:8000/aluno/dashboard">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          <span>Painel do Aluno</span>
        </a>
        <RouterLink to="/tutor">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
          <span>Tutor com IA</span>
        </RouterLink>
        <RouterLink to="/quiz">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
          <span>Gerador de Quiz</span>
        </RouterLink>
        <RouterLink to="/games">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="8" y1="12" x2="16" y2="12"></line><line x1="12" y1="8" x2="12" y2="16"></line></svg>
          <span>Jogos Educativos</span>
        </RouterLink>
      </nav>
    </aside>
    <main class="main-content">
      <RouterView />
    </main>
  </div>
</template>

<style>
body {
  margin: 0;
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  background: #22306f;
  color: #f0f0f0;
}

#app-layout {
  display: flex;
}

.sidebar {
  width: 260px;
  min-height: 100vh;
  background: linear-gradient(180deg, #001e3c, #334d99);
  color: #fff;
  padding: 1.5rem 1rem;
  display: flex;
  flex-direction: column;
  box-shadow: 4px 0 12px rgba(0,0,0,0.3);
}

.logo {
  font-size: 1.8rem;
  font-weight: bold;
  text-align: center;
  margin-bottom: 2.5rem;
  color: #61dafb;
  line-height: 1.2;
}

.main-nav {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.main-nav a {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 0.9rem 1rem;
  text-decoration: none;
  color: #cbd5e1;
  font-weight: 500;
  border-radius: 8px;
  transition: background 0.3s, color 0.3s, transform 0.2s;
}

.main-nav a:hover {
  background-color: #4a6fb3;
  color: #fff;
  transform: translateX(3px);
}

.main-nav a.router-link-exact-active {
  background-color: #007bff;
  color: #fff;
  font-weight: bold;
}

.main-nav a svg {
  width: 20px;
  height: 20px;
}

.main-content {
  flex-grow: 1;
  padding: 2rem;
  overflow-y: auto;
  height: 100vh;
  box-sizing: border-box;
  /* Gradiente com azul → preto → branco → azul */
  background: linear-gradient(135deg, #22306f, #000000 30%, #ffffff 60%, #1b2a6b 100%);
  color: #ffffff;
}
</style>
