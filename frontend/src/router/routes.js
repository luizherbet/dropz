const routes = [
  {
    path: '/',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      { path: '', redirect: '/kanban' },
      { path: 'kanban', component: () => import('pages/KanbanPage.vue') },
      { path: 'clientes', component: () => import('pages/ClientsPage.vue') },
      { path: 'demandas', component: () => import('pages/DemandsPage.vue') },
      { path: 'relatorio', component: () => import('pages/ReportPage.vue') },
    ],
  },

  // Always leave this as last one,
  // but you can also remove it
  {
    path: '/:catchAll(.*)*',
    component: () => import('pages/ErrorNotFound.vue'),
  },
]

export default routes
