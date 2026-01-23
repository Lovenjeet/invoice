<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->getUsersData($request);
        }
        
        return view('admin.users.index');
    }

    private function getUsersData(Request $request)
    {
        $draw = $request->get('draw', 1);
        $start = $request->get('start', 0);
        $length = $request->get('length', 25);
        $search = $request->get('search')['value'] ?? '';
        
        $columns = ['id', 'name', 'email', 'role', 'created_at'];
        $orderColumn = $request->get('order')[0]['column'] ?? 0;
        $orderDir = $request->get('order')[0]['dir'] ?? 'desc';
        $orderBy = $columns[$orderColumn] ?? 'id';
        
        $query = User::query();
        
        // Search functionality
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        // Get total records before filtering
        $totalRecords = User::count();
        
        // Get filtered count
        $filteredRecords = $query->count();
        
        // Order and paginate
        $users = $query->orderBy($orderBy, $orderDir)
                        ->where('role', 'admin')   
                        ->skip($start)
                        ->take($length)
                        ->get();
        
        $data = [];
        foreach ($users as $user) {
            $roleBadge = $user->role === 'admin' 
                ? '<span class="badge bg-success bg-opacity-10 text-success rounded-pill">Admin</span>'
                : '<span class="badge bg-primary bg-opacity-10 text-primary rounded-pill">User</span>';
            
            $data[] = [
                'id' => '<span class="fw-medium">#' . $user->id . '</span>',
                'name' => '<span class="fw-medium">' . e($user->name) . '</span>',
                'email' => '<span>' . e($user->email) . '</span>',
                'phone' => '<span>' . e($user->phone ?? 'N/A') . '</span>',
                'role' => $roleBadge,
                'created_at' => '<span class="text-muted">' . $user->created_at->format('M d, Y') . '</span>',
                'actions' => view('admin.users.partials.action-buttons', compact('user'))->render(),
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
        return view('admin.users.create');
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'role' => 'admin',
        ]);
        
        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'User created successfully.',
                'redirect' => route('users.index')
            ]);
        }
        
        return redirect()
            ->route('users.index')
            ->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();
        
        // Update password only if provided
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        
        // Remove password_confirmation from data
        unset($data['password_confirmation']);
        
        $user->update($data);
        
        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'User updated successfully.',
                'redirect' => route('users.index')
            ]);
        }
        
        return redirect()
            ->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(Request $request, User $user)
    {
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot delete your own account.'
                ], 422);
            }
            
            return redirect()
                ->route('users.index')
                ->with('error', 'You cannot delete your own account.');
        }
        
        $user->delete();
        
        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully.'
            ]);
        }
        
        return redirect()
            ->route('users.index')
            ->with('success', 'User deleted successfully.');
    }
}
