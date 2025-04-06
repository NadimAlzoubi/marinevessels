<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $users = User::query(); // استرجاع جميع المستخدمين
            return DataTables::of($users)
                ->addColumn('action', function ($user) {
                    $delete_button = "";
                    if ($user->role !== 'admin' || User::where('role', 'admin')->count() > 1) {
                        $delete_button = "
                            <li>
                                <form action=\"" . route('admin.users.destroy', $user->id) . "\" method=\"POST\">
                                    " . csrf_field() . "
                                    " . method_field('DELETE') . "
                                    <button type=\"submit\" class=\"dropdown-item\"><i class=\"bx bx-trash\"></i> Delete</button>
                                </form> 
                            </li>
                        ";
                    }


                    return '
                    <div class="dropdown">
                        <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton' . $user->id . '" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bx bx-dots-vertical-rounded"></i>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton' . $user->id . '">
                            <li>
                                <a class="dropdown-item" href="' . route('admin.users.edit', $user->id) . '" data-id="' . $user->id . '">
                                    <i class="bx bx-edit"></i> Edit
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="' . route('admin.users.show', $user->id) . '">
                                    <i class="bx bx-show-alt"></i> View
                                </a>
                            </li>
                            ' . $delete_button . '
                        </ul>
                    </div>
                ';
                })
                ->rawColumns(['action']) // السماح بعرض الـ HTML في عمود "action"
                ->make(true); // إرجاع الاستجابة بتنسيق JSON
        }

        return view('admin.users.index'); // إرجاع الصفحة عند عدم استخدام AJAX
    }


    public function show($id)
    {
        // جلب بيانات السفينة باستخدام ID
        $user = User::findOrFail($id); // إذا لم يتم العثور على السفينة، سيتم توجيه المستخدم إلى صفحة 404

        // تحقق إذا كان الطلب هو AJAX
        if (request()->ajax()) {
            // إذا كان الطلب AJAX، أعد البيانات بتنسيق JSON
            return response()->json($user);
        }

        // $fields = VesselsReportFields::all();
        return view('admin.users.show', compact('user'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:4|confirmed',
            'role' => ['required', Rule::in(['guest', 'contributor', 'editor', 'admin'])],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)], 
            'role' => ['nullable', Rule::in(['guest', 'contributor', 'editor', 'admin'])],
            'password' => ['nullable', 'string', 'min:4', 'confirmed'],
            'active' => 'nullable|boolean', 
        ]);

        if ($user->role === 'admin' && User::where('role', 'admin')->count() === 1 && $request->role !== 'admin') {
            return back()->with('error', 'Cannot change role of the only admin.');
        }

        if ($user->role === 'admin' && User::where('role', 'admin')->count() === 1 && $request->has('active') && (int)$request->active !== (int)$user->active) {
            return back()->with('error', 'Cannot change status of the only admin.');
        }

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        if ($request->has('active') && $request->active !== $user->active) {
            $updateData['active'] = $request->active;
        }

        $user->update($updateData);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy($id)
    {
        // جلب المستخدم الذي سيتم حذفه
        $user = User::findOrFail($id);

        // التحقق إذا كان المستخدم هو الأدمين الوحيد
        if ($user->role === 'admin' && User::where('role', 'admin')->count() == 1) {
            // إذا كان الأدمين الوحيد، لا نسمح بحذفه
            return redirect()->route('admin.users.index')->with('error', 'Cannot delete the only admin user.');
        }

        // إذا لم يكن الأدمين الوحيد، يمكن الحذف
        $user->delete();

        // إعادة التوجيه مع رسالة نجاح
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
}
