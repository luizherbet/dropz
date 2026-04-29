const routes = [
  {
    path: '/authentication',
    component: () => import('pages/AuthPage.vue'),
  },
  {
    path: '/',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      {path: 'kanban', component: () => import('pages/KanbanPage.vue'), meta: {requiresAuth: true}},
      {path: 'clientes', component: () => import('pages/ClientsPage.vue'), meta: {requiresAuth: true}},
      {path: 'demandas', component: () => import('pages/DemandsPage.vue'), meta: {requiresAuth: true}},
      {path: 'relatorio', component: () => import('pages/ReportPage.vue'), meta: {requiresAuth: true}}
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
