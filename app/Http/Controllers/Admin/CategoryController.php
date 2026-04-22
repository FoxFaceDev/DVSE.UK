<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('questions')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $request->validate(['name_en' => 'required|string', 'name_ku' => 'nullable|string']);
        Category::create($request->all());
        return redirect()->route('admin.categories.index')->with('success', 'Category added');
    }
}
