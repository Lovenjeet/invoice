<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreShipToRequest;
use App\Http\Requests\Admin\UpdateShipToRequest;
use App\Models\ShipTo;
use Illuminate\Http\Request;

class ShipToController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->getShipTosData($request);
        }
        
        return view('admin.ship-tos.index');
    }

    private function getShipTosData(Request $request)
    {
        $draw = $request->get('draw', 1);
        $start = $request->get('start', 0);
        $length = $request->get('length', 25);
        $search = $request->get('search')['value'] ?? '';
        
        $columns = ['id', 'name', 'address', 'created_at'];
        $orderColumn = $request->get('order')[0]['column'] ?? 0;
        $orderDir = $request->get('order')[0]['dir'] ?? 'desc';
        $orderBy = $columns[$orderColumn] ?? 'id';
        
        $query = ShipTo::query();
        
        // Search functionality
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }
        
        // Get total records before filtering
        $totalRecords = ShipTo::count();
        
        // Get filtered count
        $filteredRecords = $query->count();
        
        // Order and paginate
        $shipTos = $query->orderBy($orderBy, $orderDir)
                        ->skip($start)
                        ->take($length)
                        ->get();
        
        $data = [];
        foreach ($shipTos as $shipTo) {
            $data[] = [
                'id' => '<span class="fw-medium">#' . $shipTo->id . '</span>',
                'name' => '<span class="fw-medium">' . e($shipTo->name) . '</span>',
                'address' => '<span class="text-muted">' . e($shipTo->address ?? 'N/A') . '</span>',
                'created_at' => '<span class="text-muted">' . $shipTo->created_at->format('M d, Y') . '</span>',
                'actions' => view('admin.ship-tos.partials.action-buttons', compact('shipTo'))->render(),
            ];
        }
        
        return response()->json([
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ]);
    }

    public function create()
    {
        return view('admin.ship-tos.create');
    }

    public function store(StoreShipToRequest $request)
    {
        $data = $request->validated();
        
        $shipTo = ShipTo::create($data);
        
        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Ship To created successfully.',
                'redirect' => route('ship-tos.index')
            ]);
        }
        
        return redirect()
            ->route('ship-tos.index')
            ->with('success', 'Ship To created successfully.');
    }

    public function show(ShipTo $shipTo)
    {
        return view('admin.ship-tos.show', compact('shipTo'));
    }

    public function edit(ShipTo $shipTo)
    {
        return view('admin.ship-tos.edit', compact('shipTo'));
    }

    public function update(UpdateShipToRequest $request, ShipTo $shipTo)
    {
        $data = $request->validated();
        
        $shipTo->update($data);
        
        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Ship To updated successfully.',
                'redirect' => route('ship-tos.index')
            ]);
        }
        
        return redirect()
            ->route('ship-tos.index')
            ->with('success', 'Ship To updated successfully.');
    }

    public function destroy(Request $request, ShipTo $shipTo)
    {
        $shipTo->delete();
        
        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Ship To deleted successfully.'
            ]);
        }
        
        return redirect()
            ->route('ship-tos.index')
            ->with('success', 'Ship To deleted successfully.');
    }
}

