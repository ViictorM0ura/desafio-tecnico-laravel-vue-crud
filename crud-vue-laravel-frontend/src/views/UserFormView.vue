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

const user = ref({
    name: '',
    email: '',
    password: '',
    cpf: '',
    profile_id: '',
    addresses: []
});

const profiles = ref([]);
const availableAddresses = ref([]);
const router = useRouter();
const isEditMode = ref(false);

const fetchProfiles = async () => {
    try {
        const response = await axios.get('profiles');
        profiles.value = response.data;
    } catch (error) {
        console.error('Erro ao buscar perfis disponíveis:', error);
        alert('Erro ao carregar perfis. Por favor, verifique sua conexão ou se o backend está ativo.');
    }
};

const fetchAvailableAddresses = async () => {
    try {
        const response = await axios.get('addresses');
        availableAddresses.value = response.data;
    } catch (error) {
        console.error('Erro ao buscar endereços disponíveis:', error);
        alert('Erro ao carregar endereços disponíveis. Por favor, verifique sua conexão ou se o backend está ativo.');
    }
};

const fetchUser = async (id) => {
    try {
        const response = await axios.get(`users/${id}`);
        user.value = {
            ...response.data,
            addresses: response.data.addresses.map(addr => addr.id)
        };
    } catch (error) {
        console.error('Erro ao buscar dados do usuário para edição:', error);
        if (error.response && error.response.status === 404) {
            alert('Erro: Usuário não encontrado para edição. Verifique o ID ou se o usuário foi excluído.');
            router.push({ name: 'user-list' }); 
        } else if (error.request) {
            alert('Erro: Não foi possível conectar ao servidor para carregar o usuário. Verifique sua conexão ou se o backend está ativo.');
        } else {
            alert('Erro inesperado: Problema ao carregar dados do usuário. Verifique o console para mais detalhes.');
        }
    }
};

const handleSubmit = async () => {
    try {
        if (isEditMode.value) {
            const dataToSend = { ...user.value };
            if (!dataToSend.password) {
                delete dataToSend.password;
            }
            await axios.put(`users/${props.id}`, dataToSend);
            alert('Sucesso! Usuário atualizado com sucesso.');
        } else {
            await axios.post('users', user.value);
            alert('Sucesso! Usuário cadastrado com sucesso.');
        }
        router.push({ name: 'user-list' });
    } catch (error) {
        console.error('Falha ao salvar usuário:', error.response ? error.response.data : error.message);

        let errorMessage = 'Ocorreu um erro desconhecido ao salvar o usuário. Por favor, tente novamente.';

        if (error.response) {
            if (error.response.status === 422 && error.response.data && error.response.data.errors) {
                // Erros de validação do Laravel (código 422)
                const validationErrors = error.response.data.errors;
                let customMessages = [];

                // Mapeamento específico para erros de unicidade
                if (validationErrors.name && validationErrors.name.some(msg => msg.includes('has already been taken'))) {
                     customMessages.push('O nome informado já está sendo usado.');
                }
                if (validationErrors.email && validationErrors.email.some(msg => msg.includes('has already been taken'))) {
                    customMessages.push('O e-mail informado já está em uso.');
                }
                if (validationErrors.cpf && validationErrors.cpf.some(msg => msg.includes('has already been taken'))) {
                    customMessages.push('O CPF informado já está em uso.');
                }

                // Coletar outros erros de validação que não foram especificamente mapeados
                const generalErrors = Object.entries(validationErrors)
                    .filter(([key]) => !['name', 'email', 'cpf'].includes(key))
                    .flatMap(([, messages]) => messages);

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
                errorMessage = `Erro na resposta do servidor (Status: ${error.response.status}).`;
                errorMessage += '\nPor favor, verifique o console para mais detalhes.';
            }
        } else if (error.request) {
            // Requisição feita, mas nenhuma resposta (problema de rede/servidor offline)
            errorMessage = 'Erro de conexão: Não foi possível se conectar ao servidor. Verifique se o backend está ativo.';
        } else {
            // Erros inesperados no próprio frontend
            errorMessage = `Erro inesperado: ${error.message}. Por favor, contate o suporte técnico.`;
        }
        
        alert(`Falha ao salvar usuário:\n${errorMessage}`);
    }
};

onMounted(() => {
    fetchProfiles();
    fetchAvailableAddresses();
    if (props.id) {
        isEditMode.value = true;
        fetchUser(props.id);
    }
});

watch(() => props.id, (newId) => {
    if (newId) {
        isEditMode.value = true;
        fetchUser(newId);
    } else {
        isEditMode.value = false;
        user.value = {
            name: '',
            email: '',
            password: '',
            cpf: '',
            profile_id: '',
            addresses: []
        };
    }
});
</script>

<template>
  <div class="user-form">
    <h1>{{ isEditMode ? 'Editar Usuário' : 'Cadastrar Usuário' }}</h1>
    <form @submit.prevent="handleSubmit">
      <div class="form-group">
        <label for="name">Nome:</label>
        <input type="text" id="name" v-model="user.name" required />
      </div>

      <div class="form-group">
        <label for="email">Email:</label>
        <input type="email" id="email" v-model="user.email" required />
      </div>

      <div class="form-group">
        <label for="password">{{ isEditMode ? 'Nova Senha (opcional):' : 'Senha:' }}</label>
        <input type="password" id="password" v-model="user.password" :required="!isEditMode" />
      </div>

      <div class="form-group">
        <label for="cpf">CPF:</label>
        <input type="text" id="cpf" v-model="user.cpf" required placeholder="Ex: 123.456.789-00" v-mask="'###.###.###-##'" />
      </div>

      <div class="form-group">
        <label for="profile">Perfil:</label>
        <select id="profile" v-model="user.profile_id" required>
          <option value="">Selecione um perfil</option>
          <option v-for="profile in profiles" :key="profile.id" :value="profile.id">
            {{ profile.name }}
          </option>
        </select>
      </div>

      <div class="form-group">
        <label for="addresses">Endereços (selecione múltiplos):</label>
        <select id="addresses" v-model="user.addresses" multiple>
          <option v-for="address in availableAddresses" :key="address.id" :value="address.id">
            {{ address.street }}, {{ address.number }} - {{ address.neighborhood }}, {{ address.city }}
          </option>
        </select>
        <small>Mantenha Ctrl/Cmd pressionado para selecionar vários.</small>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn-submit">{{ isEditMode ? 'Atualizar' : 'Cadastrar' }}</button>
        <button type="button" @click="router.push({ name: 'user-list' })" class="btn-cancel">Cancelar</button>
      </div>
    </form>
  </div>
</template>

<style scoped>
/* Seu CSS atual (não alterado neste segmento) */
.user-form {
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

.form-group {
  margin-bottom: 15px;
}

.form-group label {
  display: block;
  margin-bottom: 5px;
  font-weight: bold;
  color: #555;
}

.form-group input[type="text"],
.form-group input[type="email"],
.form-group input[type="password"],
.form-group select {
  width: 100%;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 4px;
  box-sizing: border-box;
}

.form-group select[multiple] {
    min-height: 100px;
}

.form-group small {
    font-size: 0.8em;
    color: #777;
    margin-top: 5px;
    display: block;
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