<script setup>
import { RouterLink, RouterView } from 'vue-router'
</script>

<template>
  <header>
    <div class="wrapper">
      <nav>
        <RouterLink to="/">Usuários</RouterLink>
        <RouterLink to="/profiles">Perfis</RouterLink>
        <RouterLink to="/addresses">Endereços</RouterLink>
      </nav>
    </div>
  </header>

  <main>
    <RouterView /> </main>
</template>

<style scoped>
header {
  line-height: 1.5;
  max-height: 100vh;
  padding: 1rem;
  background-color: #333; /* Fundo escuro para o cabeçalho */
  border-bottom: 1px solid #eee;
  color: white; /* Garante que qualquer texto direto no header seja branco */
}

.wrapper {
  display: flex;
  justify-content: center;
}

nav {
  width: 100%;
  font-size: 16px;
  text-align: center;
  margin-top: 1rem;
}

nav a.router-link-exact-active {
  color: #42b883; /* Verde para o link ativo, para contraste */
  font-weight: bold;
}

nav a.router-link-exact-active:hover {
  background-color: transparent;
}

nav a {
  display: inline-block;
  padding: 0 1rem;
  border-left: 1px solid #777; /* Borda mais escura */
  color: #fff; /* Cor do texto branco para os links não ativos */
}

nav a:first-of-type {
  border: 0;
}

main {
  padding: 2rem;
  background-color: #fefefe; /* Fundo CLARO para o conteúdo principal */
  color: #333; /* Cor do texto ESCURA para o conteúdo principal */
  min-height: calc(100vh - 70px); /* Garante que o main ocupe o restante da altura, 70px é uma estimativa do header */
}
</style>