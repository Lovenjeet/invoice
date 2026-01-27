<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreHSCodeRequest;
use App\Http\Requests\Admin\UpdateHSCodeRequest;
use App\Models\HSCode;
use Illuminate\Http\Request;

class HSCodeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->getHSCodesData($request);
        }
        
        return view('admin.hs-codes.index');
    }

    private function getHSCodesData(Request $request)
    {
        $draw = $request->get('draw', 1);
        $start = $request->get('start', 0);
        $length = $request->get('length', 25);
        $search = $request->get('search')['value'] ?? '';
        
        $columns = ['model', 'sku', 'hs_code'];
        $orderColumn = $request->get('order')[0]['column'] ?? 0;
        $orderDir = $request->get('order')[0]['dir'] ?? 'desc';
        $orderBy = isset($columns[$orderColumn]) ? $columns[$orderColumn] : 'id';
        
        $query = HSCode::query();
        
        // Search functionality
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('model', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('hs_code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // Get total records before filtering
        $totalRecords = HSCode::count();
        
        // Get filtered count
        $filteredRecords = $query->count();
        
        // Order and paginate
        $hsCodes = $query->orderBy($orderBy, $orderDir)
                        ->skip($start)
                        ->take($length)
                        ->get();
        
        $data = [];
        foreach ($hsCodes as $hsCode) {
            $data[] = [
                'model' => '<span class="fw-medium">' . e($hsCode->model ?? 'N/A') . '</span>',
                'sku' => '<span>' . e($hsCode->sku ?? 'N/A') . '</span>',
                'hs_code' => '<span>' . e($hsCode->hs_code ?? 'N/A') . '</span>',
                'actions' => view('admin.hs-codes.partials.action-buttons', compact('hsCode'))->render(),
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
        return view('admin.hs-codes.create');
    }

    public function store(StoreHSCodeRequest $request)
    {
        $data = $request->validated();
        
        // Convert dg to boolean if it's Yes/No
        if (isset($data['dg'])) {
            $data['dg'] = $data['dg'] === 'Yes' ? 1 : 0;
        }
        
        $hsCode = HSCode::create($data);
        
        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'SKU created successfully.',
                'redirect' => route('hs-codes.index')
            ]);
        }
        
        return redirect()
            ->route('hs-codes.index')
            ->with('success', 'SKU created successfully.');
    }

    public function show(HSCode $hsCode)
    {
        return view('admin.hs-codes.show', compact('hsCode'));
    }

    public function edit(HSCode $hsCode)
    {
        return view('admin.hs-codes.edit', compact('hsCode'));
    }

    public function update(UpdateHSCodeRequest $request, HSCode $hsCode)
    {
        $data = $request->validated();
        
        // Convert dg to boolean if it's Yes/No
        if (isset($data['dg']) && $data['dg'] !== '') {
            $data['dg'] = $data['dg'] === 'Yes' ? 1 : 0;
        } else {
            $data['dg'] = null;
        }
        
        $hsCode->update($data);
        
        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'SKU updated successfully.',
                'redirect' => route('hs-codes.index')
            ]);
        }
        
        return redirect()
            ->route('hs-codes.index')
            ->with('success', 'SKU updated successfully.');
    }

    public function destroy(Request $request, HSCode $hsCode)
    {
        $hsCode->delete();
        
        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'SKU deleted successfully.'
            ]);
        }
        
        return redirect()
            ->route('hs-codes.index')
            ->with('success', 'SKU deleted successfully.');
    }
}

