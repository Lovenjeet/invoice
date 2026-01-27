<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBillToRequest;
use App\Http\Requests\Admin\UpdateBillToRequest;
use App\Models\BillTo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BillToController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->getBillTosData($request);
        }
        
        return view('admin.bill-tos.index');
    }

    private function getBillTosData(Request $request)
    {
        $draw = $request->get('draw', 1);
        $start = $request->get('start', 0);
        $length = $request->get('length', 25);
        $search = $request->get('search')['value'] ?? '';
        
        $columns = ['id', 'location', 'name', 'city', 'created_at'];
        $orderColumn = $request->get('order')[0]['column'] ?? 0;
        $orderDir = $request->get('order')[0]['dir'] ?? 'desc';
        $orderBy = $columns[$orderColumn] ?? 'id';
        
        $query = BillTo::query();
        
        // Search functionality
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('vat_eori', 'like', "%{$search}%")
                  ->orWhere('address1', 'like', "%{$search}%");
            });
        }
        
        // Get total records before filtering
        $totalRecords = BillTo::count();
        
        // Get filtered count
        $filteredRecords = $query->count();
        
        // Order and paginate
        $billTos = $query->orderBy($orderBy, $orderDir)
                        ->skip($start)
                        ->take($length)
                        ->get();
        
        $data = [];
        foreach ($billTos as $billTo) {
            $data[] = [
                'id' => '<span class="fw-medium">#' . $billTo->id . '</span>',
                'location' => '<span class="fw-medium">' . e($billTo->location ?? 'N/A') . '</span>',
                'name' => '<span class="fw-medium">' . e($billTo->name) . '</span>',
                'address' => '<span class="text-muted">' . e($billTo->address1 . ($billTo->address2 ? ', ' . $billTo->address2 : '') . ($billTo->city ? ', ' . $billTo->city : '')) . '</span>',
                'vat_eori' => '<span>' . e($billTo->vat_eori ?? 'N/A') . '</span>',
                'created_at' => '<span class="text-muted">' . $billTo->created_at->format('M d, Y') . '</span>',
                'actions' => view('admin.bill-tos.partials.action-buttons', compact('billTo'))->render(),
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
        return view('admin.bill-tos.create');
    }

    public function store(StoreBillToRequest $request)
    {
        $data = $request->validated();
        
        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
            $data['logo'] = $logoPath;
        }
        
        $billTo = BillTo::create($data);
        
        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Bill To created successfully.',
                'redirect' => route('bill-tos.index')
            ]);
        }
        
        return redirect()
            ->route('bill-tos.index')
            ->with('success', 'Bill To created successfully.');
    }

    public function show(BillTo $billTo)
    {
        return view('admin.bill-tos.show', compact('billTo'));
    }

    public function edit(BillTo $billTo)
    {
        return view('admin.bill-tos.edit', compact('billTo'));
    }

    public function update(UpdateBillToRequest $request, BillTo $billTo)
    {
        $data = $request->validated();
        
        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($billTo->logo && Storage::disk('public')->exists($billTo->logo)) {
                Storage::disk('public')->delete($billTo->logo);
            }
            
            // Store new logo
            $logoPath = $request->file('logo')->store('logos', 'public');
            $data['logo'] = $logoPath;
        }
        
        $billTo->update($data);
        
        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Bill To updated successfully.',
                'redirect' => route('bill-tos.index')
            ]);
        }
        
        return redirect()
            ->route('bill-tos.index')
            ->with('success', 'Bill To updated successfully.');
    }

    public function destroy(Request $request, BillTo $billTo)
    {
        $billTo->delete();
        
        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Bill To deleted successfully.'
            ]);
        }
        
        return redirect()
            ->route('bill-tos.index')
            ->with('success', 'Bill To deleted successfully.');
    }
}

