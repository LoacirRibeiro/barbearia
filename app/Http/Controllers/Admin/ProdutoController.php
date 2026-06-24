namespace App\Http\Controllers;

use App\Models\Produto;
use Illuminate\Http\Request;

class ProdutoController extends Controller
{
    // Listagem de produtos
    public function index()
    {
        $produtos = Produto::orderBy('nome')->get();
        return view('admin.produtos.index', compact('produtos'));
    }

    // Formulário de criação
    public function create()
    {
        return view('admin.produtos.create');
    }

    // Salvar novo produto no banco
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'preco_venda' => 'required|numeric|min:0',
            'estoque' => 'required|integer|min:0',
        ]);

        Produto::create($request->all());

        return redirect()->route('admin.produtos.index')->with('sucesso', 'Produto cadastrado com sucesso!');
    }

    // Formulário de edição
    public function edit(Produto $produto)
    {
        return view('admin.produtos.edit', compact('produto'));
    }

    // Atualizar produto editado
    public function update(Request $request, Produto $produto)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'preco_venda' => 'required|numeric|min:0',
            'estoque' => 'required|integer|min:0',
        ]);

        $produto->update($request->all());

        return redirect()->route('admin.produtos.index')->with('sucesso', 'Produto atualizado com sucesso!');
    }

    // Deletar produto
    public function destroy(Produto $produto)
    {
        $produto->delete();
        return redirect()->route('admin.produtos.index')->with('sucesso', 'Produto removido com sucesso!');
    }
}