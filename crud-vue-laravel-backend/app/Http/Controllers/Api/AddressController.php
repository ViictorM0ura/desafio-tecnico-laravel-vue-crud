<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address; // Importar o modelo Address
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     * Exibe uma lista de endereços.
     */
    public function index()
    {
        // Retorna todos os endereços
        $addresses = Address::all();
        return response()->json($addresses);
    }

    /**
     * Store a newly created resource in storage.
     * Armazena um novo endereço no banco de dados.
     */
    public function store(Request $request)
    {
        // Validação dos dados de entrada
        $request->validate([
            'street' => 'required|string|max:255',
            'number' => 'nullable|string|max:20',
            'complement' => 'nullable|string|max:255',
            'neighborhood' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'zip_code' => 'required|string|max:10', // CEP
        ]);

        // Cria um novo endereço
        $address = Address::create($request->all());

        // Retorna o endereço criado com status 201 (Created)
        return response()->json($address, 201);
    }

    /**
     * Display the specified resource.
     * Exibe um endereço específico.
     */
    public function show(Address $address)
    {
        // Retorna o endereço encontrado
        return response()->json($address);
    }

    /**
     * Update the specified resource in storage.
     * Atualiza um endereço específico no banco de dados. 
     */
    public function update(Request $request, Address $address)
    {
        // Validação dos dados de entrada
        $request->validate([
            'street' => 'required|string|max:255',
            'number' => 'nullable|string|max:20',
            'complement' => 'nullable|string|max:255',
            'neighborhood' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'zip_code' => 'required|string|max:10',
        ]);

        // Atualiza o endereço
        $address->update($request->all());

        // Retorna o endereço atualizado
        return response()->json($address);
    }

    /**
     * Remove the specified resource from storage.
     * Remove um endereço específico do banco de dados. 
     */
    public function destroy(Address $address)
    {
        // Antes de excluir um endereço, é bom desanexá-lo de qualquer usuário
        // que possa estar associado a ele.
        // O `onDelete('cascade')` na migração `address_user` já cuida disso se o usuário ou endereço for excluído
        // Mas para garantir que a relação é limpa se o endereço for excluído isoladamente:
        // address->users()->detach(); // Não é estritamente necessário aqui por causa do onDelete('cascade') na migração da tabela pivô

        $address->delete();

        // Retorna uma resposta vazia com status 204 (No Content)
        return response()->json(null, 204);
    }
}