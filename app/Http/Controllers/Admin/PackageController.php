<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PackageController extends Controller
{
    /**
     * Display list of all packages.
     */
    public function index()
    {
        $packages = Package::orderBy('day_duration', 'asc')->get();

        return view('admin.packages', compact('packages'));
    }

    /**
     * Store a newly created package.
     */
    public function store(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'day_duration' => 'required|integer|min:1',
            'is_premium' => 'required|boolean',
            'status' => 'required|in:Active,Inactive',
        ], [
            'name.required' => 'Nama paket wajib diisi.',
            'price.required' => 'Harga wajib diisi.',
            'price.numeric' => 'Harga harus berupa angka.',
            'price.min' => 'Harga tidak boleh negatif.',
            'day_duration.required' => 'Durasi wajib diisi.',
            'day_duration.integer' => 'Durasi harus berupa angka.',
            'day_duration.min' => 'Durasi minimal 1 hari.',
            'is_premium.required' => 'Kategori paket wajib dipilih.',
            'status.in' => 'Status tidak valid.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Create package
        Package::create([
            'name' => $request->name,
            'price' => $request->price,
            'day_duration' => $request->day_duration,
            'is_premium' => $request->is_premium,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.packages')->with('success', 'Paket berhasil ditambahkan.');
    }

    /**
     * Update the specified package.
     */
    public function update(Request $request, $id)
    {
        $package = Package::findOrFail($id);

        // Validate input
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'day_duration' => 'required|integer|min:1',
            'is_premium' => 'required|boolean',
            'status' => 'required|in:Active,Inactive',
        ], [
            'name.required' => 'Nama paket wajib diisi.',
            'price.required' => 'Harga wajib diisi.',
            'price.numeric' => 'Harga harus berupa angka.',
            'price.min' => 'Harga tidak boleh negatif.',
            'day_duration.required' => 'Durasi wajib diisi.',
            'day_duration.integer' => 'Durasi harus berupa angka.',
            'day_duration.min' => 'Durasi minimal 1 hari.',
            'is_premium.required' => 'Kategori paket wajib dipilih.',
            'status.in' => 'Status tidak valid.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Update package
        $package->update([
            'name' => $request->name,
            'price' => $request->price,
            'day_duration' => $request->day_duration,
            'is_premium' => $request->is_premium,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.packages')->with('success', 'Paket berhasil diperbarui.');
    }

    /**
     * Remove the specified package.
     */
    public function destroy($id)
    {
        $package = Package::findOrFail($id);

        // Check if package is being used in any registration
        $usageCount = Registration::where('id_package', $id)->count();

        if ($usageCount > 0) {
            return redirect()->route('admin.packages')
                ->with('error', 'Paket tidak bisa dihapus karena masih digunakan oleh member.');
        }

        // Delete package
        $package->delete();

        return redirect()->route('admin.packages')->with('success', 'Paket berhasil dihapus.');
    }
}