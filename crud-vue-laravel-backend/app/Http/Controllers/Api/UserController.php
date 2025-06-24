<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;     // Importar o modelo User
use App\Models\Address;   // Importar o modelo Address
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; // Para validação de unicidade na atualização
use Illuminate\Support\Facades\Hash; // Para criptografar a senha

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     * Exibe uma lista de usuários com seus perfis e endereços.
     */
    public function index(Request $request)
    {
        $query = User::with('profile', 'addresses'); // Eager loading para carregar perfil e endereços

        // Filtros (Nome, CPF e Período de cadastro) 
        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->has('cpf')) {
            $query->where('cpf', 'like', '%' . $request->cpf . '%');
        }

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        }

        $users = $query->get();
        return response()->json($users);
    }

    /**
     * Store a newly created resource in storage.
     * Armazena um novo usuário no banco de dados.
     */
    public function store(Request $request)
    {
        // Validação dos dados de entrada (nome, email, cpf, perfil e endereços) 
        $request->validate([
            'name' => 'required|string|max:255|unique:users,name',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'cpf' => 'required|string|max:14|unique:users,cpf', // CPF com máscara ou formatação específica
            'profile_id' => 'required|exists:profiles,id', // O profile_id deve existir na tabela profiles 
            'addresses' => 'array', // Lista de IDs de endereços (opcional) 
            'addresses.*' => 'exists:addresses,id', // Cada ID na lista deve existir na tabela addresses
        ]);

        // Criptografa a senha antes de criar o usuário
        $data = $request->all();
        $data['password'] = Hash::make($data['password']);

        // Cria o usuário
        $user = User::create($data);

        // Anexa os endereços ao usuário (se houver) 
        if ($request->has('addresses')) {
            $user->addresses()->attach($request->input('addresses'));
        }

        // Retorna o usuário criado com perfil e endereços
        return response()->json($user->load('profile', 'addresses'), 201);
    }

    /**
     * Display the specified resource.
     * Exibe um usuário específico com seus perfis e endereços. 
     */
    public function show(User $user)
    {
        // Carrega o perfil e os endereços do usuário
        return response()->json($user->load('profile', 'addresses'));
    }

    /**
     * Update the specified resource in storage.
     * Atualiza um usuário específico no banco de dados. 
     */
    public function update(Request $request, User $user)
    {
        // Validação dos dados de entrada (nome, email, cpf, perfil e endereços) 
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users')->ignore($user->id), // nome único, exceto para o próprio usuário
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id), // Email único, exceto para o próprio usuário
            ],
            'password' => 'sometimes|string|min:8', // 'sometimes' significa que só valida se o campo estiver presente
            'cpf' => [
                'required',
                'string',
                'max:14',
                Rule::unique('users')->ignore($user->id), // CPF único, exceto para o próprio usuário
            ],
            'profile_id' => 'required|exists:profiles,id', // 
            'addresses' => 'array', // 
            'addresses.*' => 'exists:addresses,id',
        ]);

        $data = $request->except('password'); // Não atualiza a senha se não for fornecida

        // Se uma nova senha for fornecida, criptografa
        if ($request->has('password')) {
            $data['password'] = Hash::make($request->input('password'));
        }

        // Atualiza o usuário
        $user->update($data);

        // Sincroniza os endereços (remove os que não estão na lista e adiciona os novos) 
        if ($request->has('addresses')) {
            $user->addresses()->sync($request->input('addresses'));
        } else {
            // Se 'addresses' não for enviado, desanexa todos os endereços do usuário
            $user->addresses()->detach();
        }


        // Retorna o usuário atualizado com perfil e endereços
        return response()->json($user->load('profile', 'addresses'));
    }

    /**
     * Remove the specified resource from storage.
     * Remove um usuário específico do banco de dados. 
     */
    public function destroy(User $user)
    {
        // Desanexa todos os endereços do usuário antes de excluí-lo 
        $user->addresses()->detach();

        // Exclui o usuário
        $user->delete();

        // Retorna uma resposta vazia com status 204 (No Content)
        return response()->json(null, 204);
    }
}