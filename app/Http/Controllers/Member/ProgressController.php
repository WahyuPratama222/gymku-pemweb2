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
    $user = auth()->user();

    // 1. Handle Logika Toggle (Jika ada request action dari form/link)
    if ($request->input('action') === 'toggle_show_all') {
        $showAll = session('progress_show_all', false);
        session(['progress_show_all' => !$showAll]);
        return redirect()->route('member.progress');
    }

    if ($request->input('action') === 'set_compare_preset') {
        $preset = $request->input('preset', 'all');
        $allowed = ['week', 'month', 'quarter', 'year', 'all'];
        if (in_array($preset, $allowed)) {
            session(['compare_preset' => $preset]);
        }
        return redirect()->route('member.progress');
    }

    // 2. Ambil data utama (Hanya 1 Query untuk mencegah duplikasi)
    $rows = Progress::where('id_user', $user->id_user)
        ->latest() // Ini sama dengan orderBy('created_at', 'desc')
        ->get();

    // 3. Ambil status dari Session atau Query String
    $preset = session('compare_preset', 'all');
    // Cek session dulu, kalau kosong cek query string 'showAll'
    $showAll = session('progress_show_all', $request->query('showAll', false));

    // 4. Hitung Latest dan Baseline untuk Comparison agar Blade tidak error
    $latest = null;
    $baseline = null;

    if ($rows->isNotEmpty()) {
        $latest = $rows->first();
        // Memanggil fungsi helper untuk mencari data pembanding
        $baseline = $this->getBaselineForComparison($rows, $preset);
    }

    // 5. Kirim semua variabel ke View
    return view('member.progress', compact(
        'rows',
        'latest',
        'baseline',
        'preset',
        'showAll'
    ));
}

// JANGAN LUPA: Tambahkan fungsi ini di bawah index() tapi masih di dalam class
    private function getBaselineForComparison($rows, $preset)
    {
        if ($rows->isEmpty()) return null;

        $latestDate = \Carbon\Carbon::parse($rows->first()->created_at);

        $compareDate = match ($preset) {
            'week'    => $latestDate->copy()->subWeek(),
            'month'   => $latestDate->copy()->subMonth(),
            'quarter' => $latestDate->copy()->subMonths(3),
            'year'    => $latestDate->copy()->subYear(),
            default   => null,
        };

        if ($compareDate) {
            return $rows->first(fn($row) => \Carbon\Carbon::parse($row->created_at)->lte($compareDate)) ?: $rows->last();
        }

        return $rows->last(); // Preset 'all' mengambil data tertua
    }

    /**
     * Store new progress record.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // Validate input
        $validator = Validator::make($request->all(), [
            'record_date' => 'nullable|date',
            'weight' => 'nullable|numeric|min:0|max:500',
            'height' => 'nullable|numeric|min:0|max:300',
            'body_fat' => 'nullable|numeric|min:0|max:100',
            'muscle_mass' => 'nullable|numeric|min:0|max:500',
            'chest' => 'nullable|numeric|min:0|max:300',
            'waist' => 'nullable|numeric|min:0|max:300',
            'biceps' => 'nullable|numeric|min:0|max:100',
            'thigh' => 'nullable|numeric|min:0|max:200',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Get last progress for default values
        $lastProgress = Progress::where('id_user', $user->id_user)
            ->latest()
            ->first();

        // Create progress record
        Progress::create([
            'id_user' => $user->id_user,
            'record_date' => $request->record_date ?: now()->format('Y-m-d'),
            'weight' => $request->weight ?: $lastProgress?->weight,
            'height' => $request->height ?: $lastProgress?->height,
            'body_fat' => $request->body_fat ?: $lastProgress?->body_fat,
            'muscle_mass' => $request->muscle_mass ?: $lastProgress?->muscle_mass,
            'chest' => $request->chest ?: $lastProgress?->chest,
            'waist' => $request->waist ?: $lastProgress?->waist,
            'biceps' => $request->biceps ?: $lastProgress?->biceps,
            'thigh' => $request->thigh ?: $lastProgress?->thigh,
        ]);

        return redirect()->route('member.progress')
            ->with('success', 'Progress berhasil ditambahkan.');
    }

    /**
     * Update existing progress record.
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();

        // Find progress record
        $progress = Progress::where('id_progress', $id)
            ->where('id_user', $user->id_user)
            ->firstOrFail();

        // Validate input
        $validator = Validator::make($request->all(), [
            'record_date' => 'nullable|date',
            'weight' => 'nullable|numeric|min:0|max:500',
            'height' => 'nullable|numeric|min:0|max:300',
            'body_fat' => 'nullable|numeric|min:0|max:100',
            'muscle_mass' => 'nullable|numeric|min:0|max:500',
            'chest' => 'nullable|numeric|min:0|max:300',
            'waist' => 'nullable|numeric|min:0|max:300',
            'biceps' => 'nullable|numeric|min:0|max:100',
            'thigh' => 'nullable|numeric|min:0|max:200',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Update progress record
        $progress->update([
            'record_date' => $request->record_date ?: $progress->record_date,
            'weight' => $request->weight !== null ? $request->weight : $progress->weight,
            'height' => $request->height !== null ? $request->height : $progress->height,
            'body_fat' => $request->body_fat !== null ? $request->body_fat : $progress->body_fat,
            'muscle_mass' => $request->muscle_mass !== null ? $request->muscle_mass : $progress->muscle_mass,
            'chest' => $request->chest !== null ? $request->chest : $progress->chest,
            'waist' => $request->waist !== null ? $request->waist : $progress->waist,
            'biceps' => $request->biceps !== null ? $request->biceps : $progress->biceps,
            'thigh' => $request->thigh !== null ? $request->thigh : $progress->thigh,
        ]);

        return redirect()->route('member.progress')
            ->with('success', 'Progress berhasil diupdate.');
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
            ->with('success', 'Progress berhasil dihapus.');
    }

    /**
     * Get baseline progress for comparison based on preset.
     */
}