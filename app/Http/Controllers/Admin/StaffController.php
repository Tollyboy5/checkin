<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StaffRequest;
use App\Models\Shift;
use App\Models\Staff;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    public function index()
    {
        return view('admin.staff.index', [
            'staff' => Staff::query()->with('shift')->orderBy('name')->paginate(15),
        ]);
    }

    public function create()
    {
        return view('admin.staff.form', [
            'staffMember' => new Staff(['active' => true]),
            'shifts' => $this->shifts(),
        ]);
    }

    public function store(StaffRequest $request)
    {
        $validated = $request->validated();
        $validated['pin_hash'] = Hash::make($validated['pin']);
        unset($validated['pin']);

        Staff::create($validated);

        return redirect()->route('admin.staff.index')->with('success', 'Staff member created.');
    }

    public function edit(Staff $staff)
    {
        return view('admin.staff.form', [
            'staffMember' => $staff,
            'shifts' => $this->shifts(),
        ]);
    }

    public function update(StaffRequest $request, Staff $staff)
    {
        $validated = $request->validated();

        if (! empty($validated['pin'])) {
            $validated['pin_hash'] = Hash::make($validated['pin']);
        }

        unset($validated['pin']);
        $staff->update($validated);

        return redirect()->route('admin.staff.index')->with('success', 'Staff member updated.');
    }

    public function destroy(Staff $staff)
    {
        $staff->delete();

        return redirect()->route('admin.staff.index')->with('success', 'Staff member deleted.');
    }

    private function shifts()
    {
        return Shift::query()->where('active', true)->orderBy('name')->get();
    }
}
