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

const address = ref({
    street: '',
    number: '',
    complement: '',
    neighborhood: '',
    city: '',
    state: '',
    zip_code: ''
});

const router = useRouter();
const isEditMode = ref(false);

const fetchAddress = async (id) => {
    try {
        const response = await axios.get(`addresses/${id}`);
        address.value = response.data;
    } catch (error) {
        console.error('Erro ao buscar dados do endereço:', error); 
        
        let userMessage = 'Ocorreu um erro desconhecido ao carregar dados do endereço. Por favor, tente novamente.';
        
        if (error.response) {
            if (error.response.status === 404) {
                userMessage = 'Endereço não encontrado para edição. Verifique o ID fornecido ou se o endereço foi excluído.';
                router.push({ name: 'address-list' }); // Redireciona se não encontrar
            } else if (error.response.data && error.response.data.message) {
                userMessage = `Erro do servidor: ${error.response.data.message}.`;
            } else {
                userMessage = `Erro na resposta do servidor (Status: ${error.response.status}).`;
            }
        } else if (error.request) {
            userMessage = 'Erro de conexão: Não foi possível conectar ao servidor para carregar o endereço. Verifique sua conexão ou se o backend está ativo.';
        } else {
            userMessage = `Erro inesperado: ${error.message}.`;
        }
        alert(`Falha ao carregar endereço:\n${userMessage}`);
    }
};

const handleSubmit = async () => {
    try {
        if (isEditMode.value) {
            await axios.put(`addresses/${props.id}`, address.value);
            alert('Sucesso! Endereço atualizado com sucesso.');
        } else {
            await axios.post('addresses', address.value);
            alert('Sucesso! Endereço cadastrado com sucesso.');
        }
        router.push({ name: 'address-list' }); // Volta para a lista após sucesso
    } catch (error) {
        console.error('Falha ao salvar endereço:', error.response ? error.response.data : error.message); 

        let userMessage = 'Ocorreu um erro desconhecido ao salvar o endereço. Por favor, tente novamente.';

        if (error.response) {
            if (error.response.status === 422 && error.response.data && error.response.data.errors) {
                // Erros de validação do Laravel (código 422)
                const validationErrors = error.response.data.errors;
                let specificErrors = [];

                // Coleta todas as mensagens de erro de validação para exibir
                Object.values(validationErrors).flat().forEach(msg => {
                    specificErrors.push(msg);
                });
                
                userMessage = 'Problemas na validação dos dados:\n' + specificErrors.join('\n');
                userMessage += '\nPor favor, revise os campos destacados e tente novamente.';

            } else if (error.response.data && error.response.data.message) {
                // Outros erros de API com mensagem do backend
                userMessage = `Erro do servidor: ${error.response.data.message}.`;
                userMessage += '\nPor favor, verifique os dados e sua conexão.';
            } else {
                // Erros de resposta inesperados da API
                userMessage = `Erro na resposta do servidor (Status: ${error.response.status}).`;
                userMessage += '\nPor favor, verifique o console para mais detalhes.';
            }
        } else if (error.request) {
            // Requisição feita, mas nenhuma resposta (problema de rede/servidor offline)
            userMessage = 'Erro de conexão: Não foi possível se conectar ao servidor. Verifique se o backend está ativo.';
        } else {
            // Erros inesperados no próprio frontend
            userMessage = `Erro inesperado: ${error.message}. Por favor, contate o suporte técnico.`;
        }
        
        alert(`Falha ao salvar endereço:\n${userMessage}`);
    }
};

onMounted(() => {
    if (props.id) {
        isEditMode.value = true;
        fetchAddress(props.id);
    }
});

watch(() => props.id, (newId) => {
    if (newId) {
        isEditMode.value = true;
        fetchAddress(newId);
    } else {
        isEditMode.value = false;
        address.value = { // Reseta para criação
            street: '',
            number: '',
            complement: '',
            neighborhood: '',
            city: '',
            state: '',
            zip_code: ''
        };
    }
});
</script>

<template>
  <div class="address-form">
    <h1>{{ isEditMode ? 'Editar Endereço' : 'Cadastrar Endereço' }}</h1>
    <form @submit.prevent="handleSubmit">
      <div class="form-group">
        <label for="street">Rua:</label>
        <input type="text" id="street" v-model="address.street" required />
      </div>

      <div class="form-group">
        <label for="number">Número:</label>
        <input type="text" id="number" v-model="address.number" />
      </div>

      <div class="form-group">
        <label for="complement">Complemento:</label>
        <input type="text" id="complement" v-model="address.complement" />
      </div>

      <div class="form-group">
        <label for="neighborhood">Bairro:</label>
        <input type="text" id="neighborhood" v-model="address.neighborhood" required />
      </div>

      <div class="form-group">
        <label for="city">Cidade:</label>
        <input type="text" id="city" v-model="address.city" required />
      </div>

      <div class="form-group">
        <label for="state">Estado:</label>
        <input type="text" id="state" v-model="address.state" required />
      </div>

      <div class="form-group">
        <label for="zip_code">CEP:</label>
        <input type="text" id="zip_code" v-model="address.zip_code" v-mask="'#####-###'" required placeholder="Ex: 00000-000" />
      </div>

      <div class="form-actions">
        <button type="submit" class="btn-submit">{{ isEditMode ? 'Atualizar' : 'Cadastrar' }}</button>
        <button type="button" @click="router.push({ name: 'address-list' })" class="btn-cancel">Cancelar</button>
      </div>
    </form>
  </div>
</template>

<style scoped>
.address-form {
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