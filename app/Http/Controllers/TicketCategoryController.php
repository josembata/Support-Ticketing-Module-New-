<?php

namespace App\Http\Controllers;

use App\Models\TicketCategory;
use Illuminate\Http\Request;

class TicketCategoryController extends Controller
{
    
     // Display a listing of the categories.
     
    public function index()
    {
        $categories = TicketCategory::latest()->paginate(10);
        return view('categories.index', compact('categories'));
    }

    
     // Show the form for creating a new category.
     
    public function create()
    {
        return view('categories.create');
    }

    
     // Store a newly created category in storage.
     
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:ticket_categories,name',
            'description' => 'nullable|string|max:255',
        ]);

        TicketCategory::create($validated);

        return redirect()->route('categories.index')
                         ->with('success', 'Category created successfully.');
    }

    
     // Show the form for editing the specified category.
    
    public function edit(TicketCategory $category)
    {
        return view('categories.edit', compact('category'));
    }

    
     // Update the specified category in storage.
     
    public function update(Request $request, TicketCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:ticket_categories,name,' . $category->id,
            'description' => 'nullable|string|max:255',
        ]);

        $category->update($validated);

        return redirect()->route('categories.index')
                         ->with('success', 'Category updated successfully.');
    }

    
    // Remove the specified category from storage.
     
    public function destroy(TicketCategory $category)
    {
        $category->delete();
        return redirect()->route('categories.index')
                         ->with('success', 'Category deleted successfully.');
    }
}
