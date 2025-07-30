<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        return Category::where('user_id', $request->user()->id)->get();
    }

    public function show(Request $request, $id)
    {
        $user = $request->user();

        // Load budgets juga untuk kategori ini
        $category = Category::where('user_id', $user->id)
            ->where('id', $id)
            ->with('budgets') // include budgets in response
            ->firstOrFail();

        return response()->json($category);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'type' => 'required|in:income,expense',
        ]);

        $data['user_id'] = $request->user()->id;

        return Category::create($data);
    }

    public function update(Request $request, $id)
    {
        $category = Category::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $category->update($request->validate([
            'name' => 'sometimes|string',
            'type' => 'sometimes|in:income,expense',
        ]));

        return $category;
    }

    public function destroy(Request $request, $id)
    {
        $category = Category::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $category->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
