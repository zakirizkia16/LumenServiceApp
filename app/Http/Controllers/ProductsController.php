<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    // GET /products (Read All)
    public function index()
    {
        $products = Product::orderBy('id', 'DESC')->paginate(10);
        return response()->json($products, 200);
    }

    // POST /products (Create)
    public function store(Request $request)
    {
        $this->validate($request, [
            'category_id' => 'required|integer',
            'sku'         => 'required|unique:products',
            'name'        => 'required|string',
            'price'       => 'required|numeric',
            'stock'       => 'required|integer',
            'description' => 'nullable|string',
            'status'      => 'required|in:active,inactive'
        ]);

        $input = $request->all();
        $product = Product::create($input);

        return response()->json($product, 201);
    }

    // GET /product/{id} (Read Detail)
    public function show($id)
    {
        $product = Product::find($id);

        if (!$product) {
            abort(404);
        }

        return response()->json($product, 200);
    }

    // PUT /product/{id} (Update)
    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            abort(404);
        }

        $this->validate($request, [
            'category_id' => 'sometimes|required|integer',
            'sku'         => 'sometimes|required|unique:products,sku,' . $id,
            'name'        => 'sometimes|required|string',
            'price'       => 'sometimes|required|numeric',
            'stock'       => 'sometimes|required|integer',
            'description' => 'nullable|string',
            'status'      => 'sometimes|required|in:active,inactive'
        ]);

        $input = $request->all();
        $product->fill($input);
        $product->save();

        return response()->json($product, 200);
    }

    // DELETE /product/{id} (Delete)
    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            abort(404);
        }

        $product->delete();
        $message = ['message' => 'deleted successfully', 'product_id' => $id];

        return response()->json($message, 200);
    }
}