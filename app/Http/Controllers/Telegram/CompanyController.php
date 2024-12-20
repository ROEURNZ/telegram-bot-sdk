<?php

namespace App\Http\Controllers\Telegram;

use Illuminate\Http\Request;
use App\Models\Telegram\Company;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class CompanyController extends Controller
{
    // Display a listing of the companies
    public function index()
    {
        $companies = Company::all();
        return view('companies.index', compact('companies'));
    }

    // Show the form for creating a new company
    public function create()
    {
        return view('companies.create');
    }

    // Store a newly created company in the database
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:companies,name|max:255',
            'email' => 'nullable|email|unique:companies,email',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'address' => 'nullable|string|max:500',
        ]);

        // Handle the logo upload if available
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
        }

        Company::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'logo' => $logoPath ?? null,
            'address' => $validated['address'],
        ]);

        return redirect()->route('companies.index')->with('success', 'Company created successfully!');
    }

    // Display the specified company
    public function show($id)
    {
        $company = Company::findOrFail($id);
        return view('companies.show', compact('company'));
    }

    // Show the form for editing the specified company
    public function edit($id)
    {
        $company = Company::findOrFail($id);
        return view('companies.edit', compact('company'));
    }

    // Update the specified company in the database
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|max:255|unique:companies,name,' . $id,
            'email' => 'nullable|email|unique:companies,email,' . $id,
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'address' => 'nullable|string|max:500',
        ]);

        $company = Company::findOrFail($id);

        // Handle the logo upload if available
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
        }

        $company->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'logo' => $logoPath ?? $company->logo,
            'address' => $validated['address'],
        ]);

        return redirect()->route('companies.index')->with('success', 'Company updated successfully!');
    }

    // Remove the specified company from the database
    public function destroy($id)
    {
        $company = Company::findOrFail($id);
        $company->delete();

        return redirect()->route('companies.index')->with('success', 'Company deleted successfully!');
    }
}
