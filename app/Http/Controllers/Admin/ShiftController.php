<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ShiftRequest;
use App\Models\Shift;

class ShiftController extends Controller
{
    public function index()
    {
        return view('admin.shifts.index', [
            'shifts' => Shift::query()->withCount('staff')->orderBy('starts_at')->paginate(15),
        ]);
    }

    public function create()
    {
        return view('admin.shifts.form', [
            'shift' => new Shift(['active' => true, 'grace_minutes' => 0]),
        ]);
    }

    public function store(ShiftRequest $request)
    {
        Shift::create($request->validated());

        return redirect()->route('admin.shifts.index')->with('success', 'Shift created.');
    }

    public function edit(Shift $shift)
    {
        return view('admin.shifts.form', [
            'shift' => $shift,
        ]);
    }

    public function update(ShiftRequest $request, Shift $shift)
    {
        $shift->update($request->validated());

        return redirect()->route('admin.shifts.index')->with('success', 'Shift updated.');
    }

    public function destroy(Shift $shift)
    {
        $shift->delete();

        return redirect()->route('admin.shifts.index')->with('success', 'Shift deleted.');
    }

}
