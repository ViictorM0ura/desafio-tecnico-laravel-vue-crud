<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const addresses = ref([]);
const router = useRouter();

// Função para formatar o CEP na exibição
const formatZipCode = (zipCode) => {
    if (!zipCode) return '';
    // Remove caracteres não numéricos e aplica a máscara #####-###
    const cleanedZipCode = String(zipCode).replace(/\D/g, '');
    return cleanedZipCode.replace(/(\d{5})(\d{3})/, '$1-$2');
};

const fetchAddresses = async () => {
    try {
        const response = await axios.get('addresses');
        addresses.value = response.data;
    } catch (error) {
        console.error('Erro ao buscar lista de endereços:', error);
        
        let errorMessage = 'Ocorreu um erro desconhecido ao carregar endereços. Por favor, tente novamente.';
        if (error.request) {
            errorMessage = 'Erro de conexão: Não foi possível conectar ao servidor para carregar endereços. Verifique sua conexão ou se o backend está ativo.';
        } else if (error.response && error.response.data && error.response.data.message) {
            errorMessage = `Erro do servidor: ${error.response.data.message}.`;
        } else {
            errorMessage = `Erro inesperado: ${error.message}. Verifique o console para mais detalhes.`;
        }
        alert(`Falha ao carregar endereços:\n${errorMessage}`);
    }
};

const deleteAddress = async (id) => {
    if (confirm('Tem certeza que deseja excluir este endereço? Ele será desvinculado de qualquer usuário associado.')) {
        try {
            await axios.delete(`addresses/${id}`);
            fetchAddresses(); // Recarrega a lista após exclusão
            alert('Endereço excluído com sucesso!');
        } catch (error) {
            console.error('Erro ao excluir endereço:', error);
            
            let errorMessage = 'Ocorreu um erro desconhecido ao excluir o endereço. Por favor, tente novamente.';
            if (error.response) {
                if (error.response.status === 404) {
                    errorMessage = 'Endereço não encontrado. Ele pode já ter sido excluído.';
                } else if (error.response.status === 409) { // Conflito, ex: Foreign Key Constraint (se o endereço tiver usuários vinculados)
                    errorMessage = 'Não foi possível excluir o endereço. Ele pode estar vinculado a um ou mais usuários.';
                } else if (error.response.data && error.response.data.message) {
                    errorMessage = `Erro do servidor: ${error.response.data.message}.`;
                } else {
                    errorMessage = `Erro na resposta do servidor (Status: ${error.response.status}). Verifique o console.`;
                }
            } else if (error.request) {
                errorMessage = 'Erro de conexão: Não foi possível conectar ao servidor para excluir o endereço. Verifique sua conexão ou se o backend está ativo.';
            } else {
                errorMessage = `Erro inesperado: ${error.message}. Verifique o console para mais detalhes.`;
            }
            alert(`Falha ao excluir endereço:\n${errorMessage}`);
        }
    }
};

const goToCreateAddress = () => {
    router.push({ name: 'address-create' });
};

const goToEditAddress = (id) => {
    router.push({ name: 'address-edit', params: { id } });
};

onMounted(fetchAddresses);
</script>

<template>
  <div class="address-list">
    <h1>Lista de Endereços</h1>
    <button @click="goToCreateAddress" class="btn-create">Cadastrar Novo Endereço</button>

    <table class="data-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Rua</th>
          <th>Número</th>
          <th>Bairro</th>
          <th>Cidade</th>
          <th>Estado</th>
          <th>CEP</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
        <tr v-if="addresses.length === 0">
          <td colspan="8" style="text-align: center;">Nenhum endereço encontrado.</td>
        </tr>
        <tr v-for="address in addresses" :key="address.id">
          <td>{{ address.id }}</td>
          <td>{{ address.street }}</td>
          <td>{{ address.number || 'N/A' }}</td>
          <td>{{ address.neighborhood }}</td>
          <td>{{ address.city }}</td>
          <td>{{ address.state }}</td>
          <td>{{ formatZipCode(address.zip_code) }}</td>
          <td>
            <button @click="goToEditAddress(address.id)" class="btn-action edit">Editar</button>
            <button @click="deleteAddress(address.id)" class="btn-action delete">Excluir</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<style scoped>

.address-list {
  max-width: 1000px;
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