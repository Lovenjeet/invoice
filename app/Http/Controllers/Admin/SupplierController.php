<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSupplierRequest;
use App\Http\Requests\Admin\UpdateSupplierRequest;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->getSuppliersData($request);
        }
        
        return view('admin.suppliers.index');
    }

    private function getSuppliersData(Request $request)
    {
        $draw = $request->get('draw', 1);
        $start = $request->get('start', 0);
        $length = $request->get('length', 25);
        $search = $request->get('search')['value'] ?? '';
        
        $columns = ['id', 'name', 'city', 'contact1', 'created_at'];
        $orderColumn = $request->get('order')[0]['column'] ?? 0;
        $orderDir = $request->get('order')[0]['dir'] ?? 'desc';
        $orderBy = $columns[$orderColumn] ?? 'id';
        
        $query = Supplier::query();
        
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
        $totalRecords = Supplier::count();
        
        // Get filtered count
        $filteredRecords = $query->count();
        
        // Order and paginate
        $suppliers = $query->orderBy($orderBy, $orderDir)
                        ->skip($start)
                        ->take($length)
                        ->get();
        
        $data = [];
        foreach ($suppliers as $supplier) {
            $data[] = [
                'id' => '<span class="fw-medium">#' . $supplier->id . '</span>',
                'name' => '<span class="fw-medium">' . e($supplier->name) . '</span>',
                'address' => '<span class="text-muted">' . e($supplier->address1 . ($supplier->address2 ? ', ' . $supplier->address2 : '')) . '</span>',
                'city' => '<span>' . e($supplier->city ?? 'N/A') . '</span>',
                'contact' => '<span>' . e($supplier->contact1 ?? 'N/A') . ($supplier->contact2 ? '<br><small class="text-muted">' . e($supplier->contact2) . '</small>' : '') . '</span>',
                'created_at' => '<span class="text-muted">' . $supplier->created_at->format('M d, Y') . '</span>',
                'actions' => view('admin.suppliers.partials.action-buttons', compact('supplier'))->render(),
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
        return view('admin.suppliers.create');
    }

    public function store(StoreSupplierRequest $request)
    {
        $data = $request->validated();
        
        $supplier = Supplier::create($data);
        
        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Supplier created successfully.',
                'redirect' => route('suppliers.index')
            ]);
        }
        
        return redirect()
            ->route('suppliers.index')
            ->with('success', 'Supplier created successfully.');
    }

    public function show(Supplier $supplier)
    {
        return view('admin.suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier)
    {
        return view('admin.suppliers.edit', compact('supplier'));
    }

    public function update(UpdateSupplierRequest $request, Supplier $supplier)
    {
        $data = $request->validated();
        
        $supplier->update($data);
        
        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Supplier updated successfully.',
                'redirect' => route('suppliers.index')
            ]);
        }
        
        return redirect()
            ->route('suppliers.index')
            ->with('success', 'Supplier updated successfully.');
    }

    public function destroy(Request $request, Supplier $supplier)
    {
        $supplier->delete();
        
        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Supplier deleted successfully.'
            ]);
        }
        
        return redirect()
            ->route('suppliers.index')
            ->with('success', 'Supplier deleted successfully.');
    }
}

