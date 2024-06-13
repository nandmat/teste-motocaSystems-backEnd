<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProdutoResource;
use App\Models\Categoria;
use App\Models\Produto;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProdutoController extends Controller
{

    protected $produto;
    protected $categoria;

    public function __construct(Produto $produto, Categoria $categoria)
    {
        $this->produto = $produto;
        $this->categoria = $categoria;
    }
    public function index()
    {
        $produtos = $this->produto
            ->with(['categoria'])
            ->paginate(10);

        return ProdutoResource::collection($produtos);
    }

    public function show($produtoId)
    {
        $produto = $this->produto
            ->where('id', $produtoId)
            ->first();

        if ($produto) {
            return new ProdutoResource($produto);
        }

        return response()->json(['mensagem' => 'Produto não encontrado.'], 404);
    }

    public function store(Request $request)
    {
        $categoria = $this->categoria
            ->where('id', $request->categoria_id)
            ->first();

        if (!$categoria) {
            return response()->json(['mensagem' => 'Categoria não encontrada.'], 404);
        }
        try {

            DB::beginTransaction();

            $produto = $this->produto
                ->create([
                    'nome' => $request->nome,
                    'descricao' => $request->descricao,
                    'preco' => floatval($request->preco),
                    'categoria_id' => intval($request->categoria_id)
                ]);

            DB::commit();

            return new ProdutoResource($produto);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Não foi possível cadastrar o produto.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $produtoId)
    {
        $produto = $this->produto
            ->where('id', $produtoId)
            ->first();

        if (!$produto) {
            return response()->json(['mensagem' => 'Produto não encontrado.'], 404);
        }

        $categoria = $this->categoria
            ->where('id', $request->categoria_id)
            ->first();

        if (!$categoria) {
            return response()->json(['mensagem' => 'Categoria não encontrada.'], 404);
        }

        try {

            DB::beginTransaction();

            $produto->update([
                'nome' => $request->nome,
                'descricao' => $request->descricao,
                'preco' => floatval($request->preco),
                'categoria_id' => $categoria->id
            ]);

            DB::commit();

            return new ProdutoResource($produto);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Não foi possível atualizar o produto.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($produtoId)
    {
        try {
            $produto = $this->produto->findOrFail($produtoId);

            $produto->delete(); // Corrigido para usar o método delete() para deletar o produto

            return response()->json(['message' => 'Produto deletado com sucesso.']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Produto não encontrado.'], 404);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Não foi possível deletar o produto.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
