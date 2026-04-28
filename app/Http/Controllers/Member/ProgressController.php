<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Progress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ProgressController extends Controller
{
    /**
     * Display progress tracking page.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Ambil semua data progress user, urutkan dari terbaru
        $allRows = Progress::where('id_user', $user->id_user)
            ->orderBy('record_date', 'desc')
            ->get();

        // Ambil status show all dari query string
        $showAll = $request->query('showAll', false);

        // Ambil preset comparison dari query string, default 'all'
        $preset = $request->query('preset', 'all');

        // Data untuk ditampilkan
        $latest = $allRows->first();
        $baseline = $this->getBaselineForComparison($allRows, $preset);

        return view('member.progress', compact(
            'allRows',
            'latest',
            'baseline',
            'preset',
            'showAll'
        ));
    }

    /**
     * Get baseline progress for comparison based on preset.
     */
    private function getBaselineForComparison($rows, $preset)
    {
        if ($rows->isEmpty()) {
            return null;
        }

        $latestDate = Carbon::parse($rows->first()->record_date);

        $compareDate = match ($preset) {
            'week' => $latestDate->copy()->subWeek(),
            'month' => $latestDate->copy()->subMonth(),
            'quarter' => $latestDate->copy()->subMonths(3),
            'year' => $latestDate->copy()->subYear(),
            default => null,
        };

        if ($compareDate) {
            // Cari data terdekat dengan compareDate
            return $rows->first(function ($row) use ($compareDate) {
                return Carbon::parse($row->record_date)->lte($compareDate);
            }) ?? $rows->last();
        }

        // Preset 'all' mengambil data tertua
        return $rows->last();
    }

    /**
     * Store new progress record.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'record_date' => 'required|date',
            'weight' => 'required|numeric|min:0|max:500',
            'height' => 'required|numeric|min:0|max:300',
            'body_fat' => 'nullable|numeric|min:0|max:100',
            'muscle_mass' => 'nullable|numeric|min:0|max:500',
        ], [
            'record_date.required' => 'Tanggal wajib diisi.',
            'weight.required' => 'Berat badan wajib diisi.',
            'weight.numeric' => 'Berat badan harus berupa angka.',
            'weight.min' => 'Berat badan tidak boleh negatif.',
            'weight.max' => 'Berat badan maksimal 500 kg.',
            'height.required' => 'Tinggi badan wajib diisi.',
            'height.numeric' => 'Tinggi badan harus berupa angka.',
            'height.min' => 'Tinggi badan tidak boleh negatif.',
            'height.max' => 'Tinggi badan maksimal 300 cm.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Create progress record
        Progress::create([
            'id_user' => $user->id_user,
            'record_date' => $request->record_date,
            'weight' => $request->weight,
            'height' => $request->height,
            'body_fat' => $request->body_fat,
            'muscle_mass' => $request->muscle_mass,
        ]);

        return redirect()->route('member.progress')
            ->with('success', 'Progress berhasil ditambahkan!');
    }

    /**
     * Delete progress record.
     */
    public function destroy($id)
    {
        $user = Auth::user();

        // Find and delete progress record
        $progress = Progress::where('id_progress', $id)
            ->where('id_user', $user->id_user)
            ->firstOrFail();

        $progress->delete();

        return redirect()->route('member.progress')
            ->with('success', 'Progress berhasil dihapus!');
    }
}
