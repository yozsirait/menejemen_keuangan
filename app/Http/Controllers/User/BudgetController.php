<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Budget;
use App\Models\Category;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function index(Request $request, $categoryId)
    {
        $user = $request->user();
        $month = $request->query('month') ?? now()->format('Y-m');

        $category = Category::where('user_id', $user->id)
            ->where('id', $categoryId)
            ->where('type', 'expense')
            ->firstOrFail();

        return Budget::where('user_id', $user->id)
            ->where('category_id', $category->id)
            ->where('month', $month)
            ->get();
    }

    public function show(Request $request, $categoryId, $id)
    {
        $user = $request->user();

        $category = Category::where('user_id', $user->id)
            ->where('id', $categoryId)
            ->firstOrFail();

        return Budget::where('id', $id)
            ->where('user_id', $user->id)
            ->where('category_id', $category->id)
            ->firstOrFail();
    }

    public function store(Request $request, $categoryId)
    {
        $user = $request->user();

        $data = $request->validate([
            'amount' => 'required|numeric|min:0',
            'month' => 'required|date_format:Y-m',
        ]);

        $category = Category::where('id', $categoryId)
            ->where('user_id', $user->id)
            ->where('type', 'expense')
            ->firstOrFail();

        $budget = Budget::updateOrCreate(
            [
                'user_id' => $user->id,
                'category_id' => $category->id,
                'month' => $data['month'],
            ],
            ['amount' => $data['amount']]
        );

        return response()->json($budget, 201);
    }

    public function update(Request $request, $categoryId, $id)
    {
        $user = $request->user();

        $category = Category::where('id', $categoryId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $budget = Budget::where('id', $id)
            ->where('user_id', $user->id)
            ->where('category_id', $category->id)
            ->firstOrFail();

        $data = $request->validate([
            'amount' => 'required|numeric|min:0',
        ]);

        $budget->update($data);

        return response()->json($budget);
    }

    public function destroy(Request $request, $categoryId, $id)
    {
        $user = $request->user();

        $category = Category::where('id', $categoryId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $budget = Budget::where('id', $id)
            ->where('user_id', $user->id)
            ->where('category_id', $category->id)
            ->firstOrFail();

        $budget->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
