<?php

namespace App\Http\Controllers;

use App\Models\Lga;
use App\Models\Phc;
use Illuminate\Http\Request;

class PhcController extends Controller
{
    // public function index()
    // {
    //     $phcs = Phc::all();
    //     return view('phcs.index');
    // }

    public function index()
    {
        $phcs = PHC::with('lga')->get();
        $lgas = Lga::all();
        return view('admin.phcs', compact('phcs', 'lgas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'facility_type' => 'required|string',
            'lga_id' => 'required|exists:lgas,id',
            'address' => 'nullable|string'
        ]);

        PHC::create($validated);

        return redirect()->route('phcs.index')
            ->with('success', 'PHC created successfully');
    }

    public function destroy(PHC $phc)
    {
        $phc->delete();
        return response()->json(['success' => true]);
    }
}

