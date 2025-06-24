<script setup>
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const props = defineProps({
    id: {
        type: [String, Number],
        required: true
    }
});

const user = ref(null);
const router = useRouter();

// Função para formatar o CPF na exibição
const formatCpf = (cpf) => {
    if (!cpf) return '';
    const cleanedCpf = String(cpf).replace(/\D/g, '');
    return cleanedCpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
};

const fetchUser = async (id) => {
    try {
        const response = await axios.get(`users/${id}`);
        user.value = response.data;
    } catch (error) {
        console.error('Erro ao buscar detalhes do usuário:', error);
        
        let errorMessage = 'Ocorreu um erro desconhecido ao carregar detalhes do usuário. Por favor, tente novamente.';
        if (error.response) {
            if (error.response.status === 404) {
                errorMessage = 'Usuário não encontrado. Ele pode ter sido excluído ou o ID está incorreto.';
                router.push({ name: 'user-list' }); // Redireciona para a lista se não encontrar
            } else if (error.response.data && error.response.data.message) {
                errorMessage = `Erro do servidor: ${error.response.data.message}.`;
            } else {
                errorMessage = `Erro na resposta do servidor (Status: ${error.response.status}). Verifique o console.`;
            }
        } else if (error.request) {
            errorMessage = 'Erro de conexão: Não foi possível conectar ao servidor para carregar detalhes do usuário. Verifique sua conexão ou se o backend está ativo.';
        } else {
            errorMessage = `Erro inesperado: ${error.message}. Verifique o console para mais detalhes.`;
        }
        alert(`Falha ao carregar detalhes do usuário:\n${errorMessage}`);
    }
};

const goBack = () => {
    router.push({ name: 'user-list' });
};

const goToEdit = () => {
    router.push({ name: 'user-edit', params: { id: props.id } });
};

onMounted(() => {
    fetchUser(props.id);
});

watch(() => props.id, (newId) => {
    if (newId) {
        fetchUser(newId);
    }
});
</script>

<template>
  <div class="user-detail" v-if="user">
    <h1>Detalhes do Usuário</h1>
    <div class="detail-group">
      <strong>ID:</strong> {{ user.id }}
    </div>
    <div class="detail-group">
      <strong>Nome:</strong> {{ user.name }}
    </div>
    <div class="detail-group">
      <strong>Email:</strong> {{ user.email }}
    </div>
    <div class="detail-group">
      <strong>CPF:</strong> {{ formatCpf(user.cpf) }} </div>
    <div class="detail-group">
      <strong>Perfil:</strong> {{ user.profile ? user.profile.name : 'N/A' }}
    </div>
    <div class="detail-group">
      <strong>Criado Em:</strong> {{ new Date(user.created_at).toLocaleDateString() }}
    </div>
    <div class="detail-group">
      <strong>Atualizado Em:</strong> {{ new Date(user.updated_at).toLocaleDateString() }}
    </div>

    <div class="addresses-section">
      <h2>Endereços Associados:</h2>
      <ul v-if="user.addresses && user.addresses.length > 0">
        <li v-for="address in user.addresses" :key="address.id">
          {{ address.street }}, {{ address.number }} - {{ address.neighborhood }}, {{ address.city }} / {{ address.state }} (CEP: {{ address.zip_code }})
        </li>
      </ul>
      <p v-else>Nenhum endereço associado.</p>
    </div>

    <div class="form-actions">
      <button @click="goBack" class="btn-cancel">Voltar</button>
      <button @click="goToEdit" class="btn-submit">Editar</button>
    </div>
  </div>
  <div v-else>
    <p>Carregando detalhes do usuário...</p>
  </div>
</template>

<style scoped>
.user-detail {
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

.detail-group {
  margin-bottom: 10px;
  padding: 8px 0;
  border-bottom: 1px dashed #eee;
}

.detail-group strong {
  color: #555;
  display: inline-block;
  width: 120px; /* Para alinhar bem */
}

.addresses-section {
  margin-top: 30px;
  border-top: 1px solid #eee;
  padding-top: 20px;
}

.addresses-section h2 {
  color: #333;
  margin-bottom: 15px;
  font-size: 1.5em;
}

.addresses-section ul {
  list-style-type: disc;
  padding-left: 20px;
}

.addresses-section li {
  margin-bottom: 8px;
  color: #666;
}

.addresses-section p {
    color: #777;
    font-style: italic;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  margin-top: 20px;
}

.btn-submit,
.btn-cancel {
  padding: 10px 20px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  font-size: 16px;
  transition: background-color 0.3s ease;
}

.btn-submit {
  background-color: #007bff;
  color: white;
}

.btn-submit:hover {
  background-color: #0056b3;
}

.btn-cancel {
  background-color: #6c757d;
  color: white;
}

.btn-cancel:hover {
  background-color: #5a6268;
}
</style>