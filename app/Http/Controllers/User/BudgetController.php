<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Budget;
use App\Models\Category;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $month = $request->query('month') ?? now()->format('Y-m');

        return Budget::with('category')
            ->where('user_id', $user->id)
            ->where('month', $month)
            ->get();
    }

    public function show(Request $request, $id)
    {
        return Budget::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:0',
            'month' => 'required|date_format:Y-m',
        ]);

        $category = Category::where('id', $data['category_id'])
            ->where('user_id', $user->id)
            ->firstOrFail();

        if ($category->type !== 'expense') {
            return response()->json([
                'message' => 'Only expense categories can have budgets.'
            ], 422);
        }

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

    public function update(Request $request, $id)
    {
        $budget = Budget::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $data = $request->validate([
            'amount' => 'required|numeric|min:0',
        ]);

        $budget->update($data);

        return $budget;
    }

    public function destroy(Request $request, $id)
    {
        $budget = Budget::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $budget->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
