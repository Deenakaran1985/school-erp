<?php
namespace App\Http\Controllers\Fleet;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Staff;
use App\Models\Student;
use App\Models\StudentTransport;
use App\Models\TransportRoute;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class FleetController extends Controller
{
    public function index()
    {
        $this->authorize('fleet.view');

        $vehicles = Vehicle::with(['route', 'driver.user'])
            ->orderBy('status')
            ->get();

        $routes = TransportRoute::withCount(['studentTransports' => fn($q) =>
            $q->where('is_active', true)
        ])->orderBy('route_number')->get();

        $expiringSoon = Vehicle::where(function($q) {
            $q->where('insurance_expiry', '<=', now()->addDays(30))
              ->orWhere('fitness_expiry',    '<=', now()->addDays(30))
              ->orWhere('permit_expiry',     '<=', now()->addDays(30));
        })->where('status', 'active')->get();

        return view('fleet.index', compact('vehicles', 'routes', 'expiringSoon'));
    }

    public function createVehicle()
    {
        $this->authorize('fleet.manage');
        $routes  = TransportRoute::where('is_active', true)->get();
        $drivers = Staff::active()->where('designation', 'like', '%driver%')->get();
        return view('fleet.create-vehicle', compact('routes', 'drivers'));
    }

    public function storeVehicle(Request $request)
    {
        $this->authorize('fleet.manage');

        $validated = $request->validate([
            'vehicle_number'  => 'required|string|max:20|unique:vehicles',
            'vehicle_type'    => 'required|string|max:30',
            'make_model'      => 'nullable|string|max:100',
            'capacity'        => 'required|integer|min:1',
            'route_id'        => 'nullable|exists:transport_routes,id',
            'driver_id'       => 'nullable|exists:staff,id',
            'insurance_expiry'=> 'nullable|date',
            'fitness_expiry'  => 'nullable|date',
            'permit_expiry'   => 'nullable|date',
        ]);

        Vehicle::create(array_merge($validated, ['status' => 'active']));

        return redirect()
            ->route('admin.fleet.index')
            ->with('success', 'Vehicle added.');
    }

    public function storeRoute(Request $request)
    {
        $this->authorize('fleet.manage');

        $validated = $request->validate([
            'route_name'       => 'required|string|max:100',
            'route_number'     => 'required|string|max:20|unique:transport_routes',
            'stops'            => 'nullable|string',
            'pickup_start_time'=> 'nullable|date_format:H:i',
            'drop_end_time'    => 'nullable|date_format:H:i',
            'monthly_fee'      => 'nullable|numeric|min:0',
        ]);

        // Convert comma-separated stops to JSON array
        if (!empty($validated['stops'])) {
            $validated['stops'] = array_map(
                'trim',
                explode(',', $validated['stops'])
            );
        }

        TransportRoute::create(array_merge($validated, ['is_active' => true]));

        return back()->with('success', 'Route added.');
    }

    public function assignStudent(Request $request)
    {
        $this->authorize('fleet.manage');

        $validated = $request->validate([
            'student_id'  => 'required|exists:students,id',
            'route_id'    => 'required|exists:transport_routes,id',
            'vehicle_id'  => 'nullable|exists:vehicles,id',
            'pickup_stop' => 'nullable|string|max:100',
        ]);

        $year = AcademicYear::current();

        StudentTransport::updateOrCreate(
            ['student_id' => $validated['student_id'], 'academic_year_id' => $year->id],
            array_merge($validated, ['academic_year_id' => $year->id, 'is_active' => true])
        );

        Student::where('id', $validated['student_id'])->update(['uses_transport' => true]);

        return back()->with('success', 'Student assigned to route.');
    }
}