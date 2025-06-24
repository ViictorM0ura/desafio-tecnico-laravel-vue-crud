<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const profiles = ref([]);
const router = useRouter();

const fetchProfiles = async () => {
    try {
        const response = await axios.get('profiles');
        profiles.value = response.data;
    } catch (error) {
        console.error('Erro ao buscar lista de perfis:', error);
        
        let errorMessage = 'Ocorreu um erro desconhecido ao carregar perfis. Por favor, tente novamente.';
        if (error.request) {
            errorMessage = 'Erro de conexão: Não foi possível conectar ao servidor para carregar perfis. Verifique sua conexão ou se o backend está ativo.';
        } else if (error.response && error.response.data && error.response.data.message) {
            errorMessage = `Erro do servidor: ${error.response.data.message}.`;
        } else {
            errorMessage = `Erro inesperado: ${error.message}. Verifique o console para mais detalhes.`;
        }
        alert(`Falha ao carregar perfis:\n${errorMessage}`);
    }
};

const deleteProfile = async (id) => {
    if (confirm('Tem certeza que deseja excluir este perfil? Usuários associados a este perfil não terão mais um perfil vinculado!')) {
        try {
            await axios.delete(`profiles/${id}`);
            fetchProfiles(); // Recarrega a lista após exclusão
            alert('Perfil excluído com sucesso!');
        } catch (error) {
            console.error('Erro ao excluir perfil:', error);
            
            let errorMessage = 'Ocorreu um erro desconhecido ao excluir o perfil. Por favor, tente novamente.';
            if (error.response) {
                if (error.response.status === 404) {
                    errorMessage = 'Perfil não encontrado. Ele pode já ter sido excluído.';
                } else if (error.response.status === 409) { // Conflito, ex: Foreign Key Constraint (se o perfil tiver usuários vinculados)
                    errorMessage = 'Não foi possível excluir o perfil. Existem usuários vinculados a ele. Primeiro desvincule ou exclua os usuários.';
                } else if (error.response.data && error.response.data.message) {
                    errorMessage = `Erro do servidor: ${error.response.data.message}.`;
                } else {
                    errorMessage = `Erro na resposta do servidor (Status: ${error.response.status}). Verifique o console.`;
                }
            } else if (error.request) {
                errorMessage = 'Erro de conexão: Não foi possível conectar ao servidor para excluir o perfil. Verifique sua conexão ou se o backend está ativo.';
            } else {
                errorMessage = `Erro inesperado: ${error.message}. Verifique o console para mais detalhes.`;
            }
            alert(`Falha ao excluir perfil:\n${errorMessage}`);
        }
    }
};

const goToCreateProfile = () => {
    router.push({ name: 'profile-create' });
};

const goToEditProfile = (id) => {
    router.push({ name: 'profile-edit', params: { id } });
};

onMounted(fetchProfiles);
</script>

<template>
  <div class="profile-list">
    <h1>Lista de Perfis</h1>
    <button @click="goToCreateProfile" class="btn-create">Cadastrar Novo Perfil</button>

    <table class="data-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nome</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
        <tr v-if="profiles.length === 0">
          <td colspan="3" style="text-align: center;">Nenhum perfil encontrado.</td>
        </tr>
        <tr v-for="profile in profiles" :key="profile.id">
          <td>{{ profile.id }}</td>
          <td>{{ profile.name }}</td>
          <td>
            <button @click="goToEditProfile(profile.id)" class="btn-action edit">Editar</button>
            <button @click="deleteProfile(profile.id)" class="btn-action delete">Excluir</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<style scoped>

.profile-list {
  max-width: 800px;
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