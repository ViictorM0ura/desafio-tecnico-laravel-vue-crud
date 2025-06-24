<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const users = ref([]);
const searchQuery = ref({
    name: '',
    cpf: '',
    start_date: '',
    end_date: ''
});
const router = useRouter();

// Função para formatar o CPF na exibição
const formatCpf = (cpf) => {
    if (!cpf) return '';
    // Remove caracteres não numéricos e aplica a máscara ###.###.###-##
    const cleanedCpf = String(cpf).replace(/\D/g, '');
    return cleanedCpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
};

const fetchUsers = async () => {
    try {
        const params = {};
        if (searchQuery.value.name) params.name = searchQuery.value.name;
        // Remova a máscara do CPF no filtro antes de enviar para o backend, se houver
        if (searchQuery.value.cpf) params.cpf = searchQuery.value.cpf.replace(/\D/g, '');
        if (searchQuery.value.start_date) params.start_date = searchQuery.value.start_date;
        if (searchQuery.value.end_date) params.end_date = searchQuery.value.end_date;

        const response = await axios.get('users', { params });
        users.value = response.data;
    } catch (error) {
        // Log no console, sem prefixo
        console.error('Erro ao buscar lista de usuários:', error);
        
        let errorMessage = 'Ocorreu um erro desconhecido ao carregar usuários. Por favor, tente novamente.';
        if (error.request) {
            errorMessage = 'Erro de conexão: Não foi possível conectar ao servidor para carregar usuários. Verifique sua conexão ou se o backend está ativo.';
        } else if (error.response && error.response.data && error.response.data.message) {
            errorMessage = `Erro do servidor: ${error.response.data.message}.`;
        } else {
            errorMessage = `Erro inesperado: ${error.message}. Verifique o console para mais detalhes.`;
        }
        alert(`Falha ao carregar usuários:\n${errorMessage}`);
    }
};

const deleteUser = async (id) => {
    if (confirm('Tem certeza que deseja excluir este usuário?')) {
        try {
            await axios.delete(`users/${id}`);
            fetchUsers(); // Recarrega a lista após exclusão
            alert('Usuário excluído com sucesso!');
        } catch (error) {
            // Log no console, sem prefixo
            console.error('Erro ao excluir usuário:', error);
            
            let errorMessage = 'Ocorreu um erro desconhecido ao excluir o usuário. Por favor, tente novamente.';
            if (error.response) {
                if (error.response.status === 404) {
                    errorMessage = 'Usuário não encontrado. Ele pode já ter sido excluído.';
                } else if (error.response.status === 409) { // Conflito, ex: Foreign Key Constraint
                    errorMessage = 'Não foi possível excluir o usuário. Verifique se existem dados vinculados a ele (ex: endereços).';
                } else if (error.response.data && error.response.data.message) {
                    errorMessage = `Erro do servidor: ${error.response.data.message}.`;
                } else {
                    errorMessage = `Erro na resposta do servidor (Status: ${error.response.status}). Verifique o console.`;
                }
            } else if (error.request) {
                errorMessage = 'Erro de conexão: Não foi possível conectar ao servidor para excluir o usuário. Verifique sua conexão ou se o backend está ativo.';
            } else {
                errorMessage = `Erro inesperado: ${error.message}. Verifique o console para mais detalhes.`;
            }
            alert(`Falha ao excluir usuário:\n${errorMessage}`);
        }
    }
};

const goToCreateUser = () => {
    router.push({ name: 'user-create' });
};

const goToEditUser = (id) => {
    router.push({ name: 'user-edit', params: { id } });
};

const goToUserDetail = (id) => {
    router.push({ name: 'user-detail', params: { id } });
};

onMounted(fetchUsers); // Carrega os usuários ao montar o componente
</script>

<template>
  <div class="user-list">
    <h1>Lista de Usuários</h1>

    <div class="filters">
      <input v-model="searchQuery.name" placeholder="Filtrar por Nome" @input="fetchUsers" />
      <input v-model="searchQuery.cpf" placeholder="Filtrar por CPF" @input="fetchUsers" v-mask="'###.###.###-##'" />
      <input type="date" v-model="searchQuery.start_date" @change="fetchUsers" />
      <input type="date" v-model="searchQuery.end_date" @change="fetchUsers" />
    </div>

    <button @click="goToCreateUser" class="btn-create">Cadastrar Novo Usuário</button>

    <table class="data-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nome</th>
          <th>Email</th>
          <th>CPF</th>
          <th>Perfil</th>
          <th>Criado Em</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
        <tr v-if="users.length === 0">
          <td colspan="7" style="text-align: center;">Nenhum usuário encontrado.</td>
        </tr>
        <tr v-for="user in users" :key="user.id">
          <td>{{ user.id }}</td>
          <td>{{ user.name }}</td>
          <td>{{ user.email }}</td>
          <td>{{ formatCpf(user.cpf) }}</td>
          <td>{{ user.profile ? user.profile.name : 'N/A' }}</td>
          <td>{{ new Date(user.created_at).toLocaleDateString() }}</td>
          <td>
            <button @click="goToUserDetail(user.id)" class="btn-action view">Detalhar</button>
            <button @click="goToEditUser(user.id)" class="btn-action edit">Editar</button>
            <button @click="deleteUser(user.id)" class="btn-action delete">Excluir</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<style scoped>

.user-list {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
  background-color: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

h1 {
  color: #333;
  text-align: center;
  margin-bottom: 20px;
}

.filters {
  display: flex;
  gap: 10px;
  margin-bottom: 20px;
  flex-wrap: wrap;
}

.filters input {
  padding: 8px;
  border: 1px solid #ddd;
  border-radius: 4px;
  flex: 1;
  min-width: 150px;
}

.btn-create {
  display: block;
  width: 200px;
  padding: 10px 15px;
  background-color: #28a745;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  font-size: 16px;
  margin: 0 auto 20px auto;
  transition: background-color 0.3s ease;
}

.btn-create:hover {
  background-color: #218838;
}

.data-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
}

.data-table th,
.data-table td {
  border: 1px solid #eee;
  padding: 12px 15px;
  text-align: left;
}

.data-table th {
  background-color: #f2f2f2;
  font-weight: bold;
  color: #555;
}

.data-table tbody tr:nth-child(even) {
  background-color: #f9f9f9;
}

.data-table tbody tr:hover {
  background-color: #f1f1f1;
}

.btn-action {
  padding: 6px 10px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px;
  margin-right: 5px;
  transition: background-color 0.3s ease;
}

.btn-action.view {
  background-color: #007bff;
  color: white;
}

.btn-action.view:hover {
  background-color: #0056b3;
}

.btn-action.edit {
  background-color: #ffc107;
  color: #333;
}

.btn-action.edit:hover {
  background-color: #e0a800;
}

.btn-action.delete {
  background-color: #dc3545;
  color: white;
}

.btn-action.delete:hover {
  background-color: #c82333;
}
</style>