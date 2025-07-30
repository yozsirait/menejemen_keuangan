<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $type = $request->query('type'); // optional filter

        $query = Category::where('user_id', $user->id);

        if ($type) {
            $query->where('type', $type);
        }

        return $query->get();
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

    public function show(Request $request, $id)
    {
        return Category::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();
    }

    public function update(Request $request, $id)
    {
        $category = Category::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $data = $request->validate([
            'name' => 'sometimes|string',
            'type' => 'sometimes|in:income,expense',
        ]);

        $category->update($data);

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
