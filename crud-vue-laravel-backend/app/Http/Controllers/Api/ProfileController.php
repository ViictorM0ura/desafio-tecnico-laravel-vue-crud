<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Profile; // Importar o modelo Profile
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; // Para validação de unicidade na atualização

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     * Exibe uma lista de perfis.
     */
    public function index()
    {
        // Retorna todos os perfis do banco de dados
        $profiles = Profile::all();
        return response()->json($profiles);
    }

    /**
     * Store a newly created resource in storage.
     * Armazena um novo perfil no banco de dados.
     */
    public function store(Request $request)
    {
        // Validação dos dados de entrada
        $request->validate([
            'name' => 'required|string|max:255|unique:profiles,name',
        ]);

        // Cria um novo perfil com os dados validados
        $profile = Profile::create($request->all());

        // Retorna a resposta JSON com o perfil criado e status 201 (Created)
        return response()->json($profile, 201);
    }

    /**
     * Display the specified resource.
     * Exibe um perfil específico.
     */
    public function show(Profile $profile)
    {
        // Retorna o perfil encontrado
        return response()->json($profile);
    }

    /**
     * Update the specified resource in storage.
     * Atualiza um perfil específico no banco de dados.
     */
    public function update(Request $request, Profile $profile)
    {
        // Validação dos dados de entrada
        $request->validate([
            // 'name' deve ser único, exceto para o próprio perfil que está sendo atualizado
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('profiles')->ignore($profile->id),
            ],
        ]);

        // Atualiza o perfil com os novos dados
        $profile->update($request->all());

        // Retorna a resposta JSON com o perfil atualizado
        return response()->json($profile);
    }

    /**
     * Remove the specified resource from storage.
     * Remove um perfil específico do banco de dados.
     */
    public function destroy(Profile $profile)
    {
        // Exclui o perfil
        $profile->delete();

        // Retorna uma resposta vazia com status 204 (No Content)
        return response()->json(null, 204);
    }
}