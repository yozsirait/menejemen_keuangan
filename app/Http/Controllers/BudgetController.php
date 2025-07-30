<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Category;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function index(Request $request)
    {
        return Budget::with('category')
            ->where('user_id', $request->user()->id)
            ->get();
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
            return response()->json(['message' => 'Only expense categories can have budgets.'], 422);
        }

        $budget = Budget::updateOrCreate(
            [
                'user_id' => $user->id,
                'category_id' => $category->id,
                'month' => $data['month']
            ],
            ['amount' => $data['amount']]
        );

        return response()->json($budget, 201);
    }

    public function update(Request $request, $id)
    {
        $budget = Budget::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->with('category')
            ->firstOrFail();

        // Tambahan validasi agar kategori masih tipe expense
        if ($budget->category->type !== 'expense') {
            return response()->json(['message' => 'Only expense category budgets can be updated.'], 422);
        }

        $data = $request->validate([
            'amount' => 'required|numeric|min:0',
        ]);

        $budget->update($data);

        return response()->json($budget);
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
