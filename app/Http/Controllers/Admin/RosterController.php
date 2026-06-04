<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RosterRequest;
use App\Models\Roster;
use App\Models\Shift;
use App\Models\Staff;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RosterController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->date('date') ?: today();

        return view('admin.rosters.index', [
            'date' => Carbon::parse($date)->startOfDay(),
            'rosters' => Roster::query()
                ->with(['staff', 'shift'])
                ->whereDate('roster_date', $date)
                ->orderBy('roster_date')
                ->orderBy('staff_id')
                ->paginate(20)
                ->withQueryString(),
        ]);
    }

    public function create()
    {
        return view('admin.rosters.form', [
            'roster' => new Roster(['roster_date' => today()]),
            'staff' => $this->staff(),
            'shifts' => $this->shifts(),
        ]);
    }

    public function store(RosterRequest $request)
    {
        Roster::create($request->validated());

        return redirect()
            ->route('admin.rosters.index', ['date' => $request->date('roster_date')->toDateString()])
            ->with('success', 'Roster assignment created.');
    }

    public function edit(Roster $roster)
    {
        return view('admin.rosters.form', [
            'roster' => $roster,
            'staff' => $this->staff(),
            'shifts' => $this->shifts(),
        ]);
    }

    public function update(RosterRequest $request, Roster $roster)
    {
        $roster->update($request->validated());

        return redirect()
            ->route('admin.rosters.index', ['date' => $request->date('roster_date')->toDateString()])
            ->with('success', 'Roster assignment updated.');
    }

    public function destroy(Roster $roster)
    {
        $date = $roster->roster_date->toDateString();

        $roster->delete();

        return redirect()
            ->route('admin.rosters.index', ['date' => $date])
            ->with('success', 'Roster assignment deleted.');
    }

    private function staff()
    {
        return Staff::query()->where('active', true)->orderBy('name')->get();
    }

    private function shifts()
    {
        return Shift::query()->where('active', true)->orderBy('starts_at')->get();
    }
}
