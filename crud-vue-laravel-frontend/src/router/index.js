import { createRouter, createWebHistory } from 'vue-router'
import UserListView from '../views/UserListView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'user-list',
      component: UserListView // Rota principal para a lista de usuários
    },
    {
      path: '/users/create',
      name: 'user-create',
      component: () => import('../views/UserFormView.vue') // Formulário de criação
    },
    {
      path: '/users/:id/edit',
      name: 'user-edit',
      component: () => import('../views/UserFormView.vue'), // Formulário de edição
      props: true // Permite que o ID da URL seja passado como uma propriedade para o componente
    },
    {
      path: '/users/:id',
      name: 'user-detail',
      component: () => import('../views/UserDetailView.vue'), // Tela de detalhes
      props: true
    },
    {
      path: '/profiles',
      name: 'profile-list',
      component: () => import('../views/ProfileListView.vue') // Lista de perfis
    },
    {
      path: '/profiles/create',
      name: 'profile-create',
      component: () => import('../views/ProfileFormView.vue') // Formulário de criação de perfil
    },
    {
      path: '/profiles/:id/edit',
      name: 'profile-edit',
      component: () => import('../views/ProfileFormView.vue'), // Formulário de edição de perfil
      props: true
    },
    {
      path: '/addresses',
      name: 'address-list',
      component: () => import('../views/AddressListView.vue') // Lista de endereços
    },
    {
      path: '/addresses/create',
      name: 'address-create',
      component: () => import('../views/AddressFormView.vue') // Formulário de criação de endereço
    },
    {
      path: '/addresses/:id/edit',
      name: 'address-edit',
      component: () => import('../views/AddressFormView.vue'), // Formulário de edição de endereço
      props: true
    },
  ]
})

export default router