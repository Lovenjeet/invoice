<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreShipperRequest;
use App\Http\Requests\Admin\UpdateShipperRequest;
use App\Models\Shipper;
use Illuminate\Http\Request;

class ShipperController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->getShippersData($request);
        }
        
        return view('admin.shippers.index');
    }

    private function getShippersData(Request $request)
    {
        $draw = $request->get('draw', 1);
        $start = $request->get('start', 0);
        $length = $request->get('length', 25);
        $search = $request->get('search')['value'] ?? '';
        
        $columns = ['id', 'name', 'city', 'contact1', 'created_at'];
        $orderColumn = $request->get('order')[0]['column'] ?? 0;
        $orderDir = $request->get('order')[0]['dir'] ?? 'desc';
        $orderBy = $columns[$orderColumn] ?? 'id';
        
        $query = Shipper::query();
        
        // Search functionality
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('contact1', 'like', "%{$search}%")
                  ->orWhere('contact2', 'like', "%{$search}%")
                  ->orWhere('address1', 'like', "%{$search}%");
            });
        }
        
        // Get total records before filtering
        $totalRecords = Shipper::count();
        
        // Get filtered count
        $filteredRecords = $query->count();
        
        // Order and paginate
        $shippers = $query->orderBy($orderBy, $orderDir)
                        ->skip($start)
                        ->take($length)
                        ->get();
        
        $data = [];
        foreach ($shippers as $shipper) {
            $data[] = [
                'id' => '<span class="fw-medium">#' . $shipper->id . '</span>',
                'name' => '<span class="fw-medium">' . e($shipper->name) . '</span>',
                'address' => '<span class="text-muted">' . e($shipper->address1 . ($shipper->address2 ? ', ' . $shipper->address2 : '')) . '</span>',
                'city' => '<span>' . e($shipper->city ?? 'N/A') . '</span>',
                'contact' => '<span>' . e($shipper->contact1 ?? 'N/A') . ($shipper->contact2 ? '<br><small class="text-muted">' . e($shipper->contact2) . '</small>' : '') . '</span>',
                'created_at' => '<span class="text-muted">' . $shipper->created_at->format('M d, Y') . '</span>',
                'actions' => view('admin.shippers.partials.action-buttons', compact('shipper'))->render(),
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
        return view('admin.shippers.create');
    }

    public function store(StoreShipperRequest $request)
    {
        $data = $request->validated();
        
        $shipper = Shipper::create($data);
        
        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Shipper created successfully.',
                'redirect' => route('shippers.index')
            ]);
        }
        
        return redirect()
            ->route('shippers.index')
            ->with('success', 'Shipper created successfully.');
    }

    public function show(Shipper $shipper)
    {
        return view('admin.shippers.show', compact('shipper'));
    }

    public function edit(Shipper $shipper)
    {
        return view('admin.shippers.edit', compact('shipper'));
    }

    public function update(UpdateShipperRequest $request, Shipper $shipper)
    {
        $data = $request->validated();
        
        $shipper->update($data);
        
        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Shipper updated successfully.',
                'redirect' => route('shippers.index')
            ]);
        }
        
        return redirect()
            ->route('shippers.index')
            ->with('success', 'Shipper updated successfully.');
    }

    public function destroy(Request $request, Shipper $shipper)
    {
        $shipper->delete();
        
        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Shipper deleted successfully.'
            ]);
        }
        
        return redirect()
            ->route('shippers.index')
            ->with('success', 'Shipper deleted successfully.');
    }
}

