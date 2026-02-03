<template>
  <q-layout view="lHh Lpr lFf">
    <q-header class="header-custom bg-white text-dark">
      <q-toolbar class="q-px-md">
        <!-- Logo + nome à esquerda -->
        <router-link to="/" class="row items-center  text-dark">
          <img
            src="../assets/logo.png"
            alt="Dropz"
            class="header-logo"
          />
          <span class="header-title ">dropz</span>
        </router-link>

        <q-space />

        <!-- Ícone Git à direita -->
        <a
          href="https://github.com/luizherbet/dropz"
          target="_blank"
          rel="noopener"
          aria-label="Repositório"
          class="git-link"
        >
          <i class="bi bi-github"></i>
        </a>
      </q-toolbar>
    </q-header>
    <!-- Menu abaixo do header -->
    <nav class="menu-nav">
      <router-link to="/kanban" class="menu-link">Kanban</router-link>
      <router-link to="/clientes" class="menu-link">Clientes</router-link>
      <router-link to="/demandas" class="menu-link">Demandas</router-link>
      <router-link to="/relatorio" class="menu-link">Relatório</router-link>
    </nav>

    <q-page-container>
      <router-view />
    </q-page-container>

    <q-footer class="footer-custom">
      <q-toolbar class="justify-center">
        <span class="footer-text">Dropz — Desenvolvido por Luiz Herbet</span>
      </q-toolbar>
    </q-footer>

  </q-layout>
</template>

<script setup>

</script>

<style scoped>
.header-custom {
  font-family: 'Poppins', sans-serif;
  border-bottom: 1px solid #e0e0e0;

}

.header-custom a {
  text-decoration: none !important;
}

.header-logo {
  height: 36px;
  width: auto;
  margin-right: 10px;
}

.header-title {
  font-size: 1.35rem;
  font-weight: 700;
  letter-spacing: -0.02em;
}
.git-link {
  display: inline-flex;
  align-items: center;
  text-decoration: none;
  color: #000;
}

.git-link .bi {
  font-size: 1.5rem;
  color: #000;
}

.menu-nav {
  margin-top: 4rem;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  background: #fff;
  font-family: 'Poppins', sans-serif;
}

.menu-link {
  text-decoration: none;
  color: #333;
  font-weight: 500;
  padding: 0.5rem 1rem;
  border-radius: 6px;
  transition: background 0.2s, color 0.2s;
}

.menu-link:hover {
  background: #f0f0f0;
  color: #000;
}

.menu-link.router-link-active {
  background: #e3f2fd;
  color: #1976d2;
  font-weight: 600;
}

.q-page-container {
  margin: 0 auto;
  max-width: 1650px;
  padding-top: 1rem !important;
}

.footer-custom {
  display: flex;
  align-items: center;
  height: 20px;
  font-family: 'Poppins', sans-serif;
  font-size: 0.7rem;
  background: black;
}

.footer-text {
  font-weight: 100;
  color: white;
}
</style>
