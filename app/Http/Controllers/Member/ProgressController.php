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

        // Handle toggle show all
        if ($request->input('action') === 'toggle_show_all') {
            $showAll = session('progress_show_all', false);
            session(['progress_show_all' => !$showAll]);
            return redirect()->route('member.progress');
        }

        // Handle preset comparison change
        if ($request->input('action') === 'set_compare_preset') {
            $preset = $request->input('preset', 'all');
            $allowed = ['week', 'month', 'quarter', 'year', 'all'];
            if (in_array($preset, $allowed)) {
                session(['compare_preset' => $preset]);
            }
            return redirect()->route('member.progress');
        }

        // Get all progress records
        $rows = Progress::where('id_user', $user->id_user)
            ->latest()
            ->get();

        // Get preset and show_all from session
        $preset = session('compare_preset', 'all');
        $showAll = session('progress_show_all', false);

        // Calculate latest and baseline for comparison
        $latest = null;
        $baseline = null;

        if ($rows->isNotEmpty()) {
            $latest = $rows->first();
            $baseline = $this->getBaselineForComparison($rows, $preset);
        }

        return view('member.progress', compact(
            'rows',
            'latest',
            'baseline',
            'preset',
            'showAll'
        ));
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
    protected function getBaselineForComparison($rows, $preset)
    {
        if ($preset === 'all') {
            return $rows->last();
        }

        $latest = $rows->first();
        $targetDate = null;

        switch ($preset) {
            case 'week':
                $targetDate = Carbon::parse($latest->record_date)->subDays(7);
                break;
            case 'month':
                $targetDate = Carbon::parse($latest->record_date)->subMonth();
                break;
            case 'quarter':
                $targetDate = Carbon::parse($latest->record_date)->subMonths(3);
                break;
            case 'year':
                $targetDate = Carbon::parse($latest->record_date)->subYear();
                break;
        }

        if ($targetDate) {
            // Find closest record to target date
            $baseline = $rows->first(function ($row) use ($targetDate) {
                return Carbon::parse($row->record_date)->lte($targetDate);
            });

            return $baseline ?: $rows->last();
        }

        return $rows->last();
    }
}