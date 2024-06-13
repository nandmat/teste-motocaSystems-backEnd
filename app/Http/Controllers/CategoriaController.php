<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoriaResource;
use App\Models\Categoria;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoriaController extends Controller
{

    protected $categoria;

    public function __construct(Categoria $categoria)
    {
        $this->categoria = $categoria;
    }

    public function index()
    {
        $categorias = $this->categoria->all();

        return CategoriaResource::collection($categorias);
    }

    public function show($categoriaId)
    {
        $categoria = $this->categoria
            ->where('id', $categoriaId)
            ->first();

        if ($categoria) {
            return new CategoriaResource($categoria);
        }

        return response()->json(['mensagem' => 'Categoria não encontrada.'], 404);
    }

    public function store(Request $request)
    {
        try {
            $request->validate(
                [
                    'nome' => 'required',
                ]
            );

            DB::beginTransaction();

            $categoria = $this->categoria
                ->create([
                    'nome' => $request->nome
                ]);

            DB::commit();

            return new CategoriaResource($categoria);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Não foi possível cadastrar a categoria.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $categoriaId)
    {

        $categoria = $this->categoria
            ->where('id', $categoriaId)
            ->first();

        if (!$categoria) {
            return response()->json(['mensagem' => 'Categoria não encontrada.'], 404);
        }
        try {
            $request->validate([
                'nome' => 'required'
            ]);

            DB::beginTransaction();

            $categoria->update([
                'nome' => $request->nome
            ]);

            DB::commit();

            return new CategoriaResource($categoria);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Não foi possível atualizar a categoria.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($categoriaId)
    {
        $categoria = $this->categoria->find($categoriaId);

        if (!$categoria) {
            return response()->json(['mensagem' => 'Categoria não encontrada.'], 404);
        }
        try {

            //DELETAR PRODUTOS VINCULADOS A CATEGORIA
            foreach ($categoria->produtos as $produto) {
                $produto->delete();
            }
            $categoria->delete();

            return response()->json(['message' => 'Categoria deletada com sucesso.']);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Não foi possível deletar a categoria.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
