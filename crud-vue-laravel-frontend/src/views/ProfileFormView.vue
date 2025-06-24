<script setup>
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const props = defineProps({
    id: {
        type: [String, Number],
        default: null
    }
});

const profile = ref({
    name: ''
});

const router = useRouter();
const isEditMode = ref(false);

const fetchProfile = async (id) => {
    try {
        const response = await axios.get(`profiles/${id}`);
        profile.value = response.data;
    } catch (error) {
        console.error('Erro ao buscar perfil para edição:', error);
        
        let errorMessage = 'Ocorreu um erro desconhecido ao carregar dados do perfil. Por favor, tente novamente.';
        if (error.response) {
            if (error.response.status === 404) {
                errorMessage = 'Perfil não encontrado para edição. Verifique o ID fornecido ou se o perfil foi excluído.';
                router.push({ name: 'profile-list' }); // Redireciona se o perfil não existir
            } else if (error.response.data && error.response.data.message) {
                errorMessage = `Erro do servidor: ${error.response.data.message}.`;
            } else {
                errorMessage = `Erro na resposta do servidor (Status: ${error.response.status}). Verifique o console.`;
            }
        } else if (error.request) {
            errorMessage = 'Erro de conexão: Não foi possível conectar ao servidor para carregar o perfil. Verifique sua conexão ou se o backend está ativo.';
        } else {
            errorMessage = `Erro inesperado: ${error.message}. Verifique o console para mais detalhes.`;
        }
        alert(`Falha ao carregar perfil:\n${errorMessage}`);
    }
};

const handleSubmit = async () => {
    try {
        if (isEditMode.value) {
            await axios.put(`profiles/${props.id}`, profile.value);
            alert('Sucesso! Perfil atualizado com sucesso.');
        } else {
            await axios.post('profiles', profile.value);
            alert('Sucesso! Perfil cadastrado com sucesso.');
        }
        router.push({ name: 'profile-list' }); // Volta para a lista
    } catch (error) {
        console.error('Falha ao salvar perfil:', error.response ? error.response.data : error.message);
        
        let errorMessage = 'Ocorreu um erro desconhecido ao salvar o perfil. Por favor, tente novamente.';

        if (error.response) {
            if (error.response.status === 422 && error.response.data && error.response.data.errors) {
                // Erros de validação do Laravel (código 422)
                const validationErrors = error.response.data.errors;
                let customMessages = [];

                // Mapeamento específico para erro de unicidade do nome do perfil
                if (validationErrors.name && validationErrors.name.some(msg => msg.includes('has already been taken'))) {
                     customMessages.push('O nome do perfil informado já está sendo usado.');
                }
                
                // Coletar outros erros de validação que não foram especificamente mapeados
                const generalErrors = Object.entries(validationErrors)
                    .filter(([key]) => !['name'].includes(key)) // Filtra os campos que já tratamos
                    .flatMap(([, messages]) => messages); // Acha as mensagens dos campos restantes

                if (generalErrors.length > 0) {
                    customMessages.push('Outros problemas nos campos:\n' + generalErrors.join('\n'));
                }
                
                // Se não houve nenhuma mensagem específica ou geral, ainda há um problema de validação
                if (customMessages.length === 0) {
                    customMessages.push('Problemas na validação dos dados. Verifique todos os campos.');
                }

                errorMessage = 'Problemas na validação dos dados:\n' + customMessages.join('\n');
                errorMessage += '\nPor favor, revise os campos destacados e tente novamente.';

            } else if (error.response.data && error.response.data.message) {
                // Outros erros de API com mensagem do backend
                errorMessage = `Erro do servidor: ${error.response.data.message}.`;
                errorMessage += '\nPor favor, verifique os dados e sua conexão.';
            } else {
                // Erros de resposta inesperados da API
                errorMessage = `Erro na resposta do servidor (Status: ${error.response.status}). Verifique o console.`;
            }
        } else if (error.request) {
            // Requisição feita, mas nenhuma resposta (problema de rede/servidor offline)
            errorMessage = 'Erro de conexão: Não foi possível se conectar ao servidor. Verifique se o backend está ativo.';
        } else {
            // Erros inesperados no próprio frontend
            errorMessage = `Erro inesperado: ${error.message}. Por favor, contate o suporte técnico.`;
        }
        
        alert(`Falha ao salvar perfil:\n${errorMessage}`);
    }
};

onMounted(() => {
    if (props.id) {
        isEditMode.value = true;
        fetchProfile(props.id);
    }
});

watch(() => props.id, (newId) => {
    if (newId) {
        isEditMode.value = true;
        fetchProfile(newId);
    } else {
        isEditMode.value = false;
        profile.value = { name: '' }; // Reseta para criação
    }
});
</script>

<template>
  <div class="profile-form">
    <h1>{{ isEditMode ? 'Editar Perfil' : 'Cadastrar Perfil' }}</h1>
    <form @submit.prevent="handleSubmit">
      <div class="form-group">
        <label for="name">Nome do Perfil:</label>
        <input type="text" id="name" v-model="profile.name" required />
      </div>

      <div class="form-actions">
        <button type="submit" class="btn-submit">{{ isEditMode ? 'Atualizar' : 'Cadastrar' }}</button>
        <button type="button" @click="router.push({ name: 'profile-list' })" class="btn-cancel">Cancelar</button>
      </div>
    </form>
  </div>
</template>

<style scoped>
.profile-form {
  max-width: 600px;
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

.form-group {
  margin-bottom: 15px;
}

.form-group label {
  display: block;
  margin-bottom: 5px;
  font-weight: bold;
  color: #555;
}

.form-group input[type="text"] {
  width: 100%;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 4px;
  box-sizing: border-box;
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