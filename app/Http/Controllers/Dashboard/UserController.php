<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * عرض قائمة المستخدمين
     */
    public function index(Request $request)
    {
        $query = User::query();

        // البحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // الترتيب
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $users = $query->paginate(10);

        return view('dashboard.users.index', compact('users'));
    }

    /**
     * عرض صفحة إنشاء مستخدم جديد
     */
    public function create()
    {
        return view('dashboard.users.create');
    }

    /**
     * حفظ مستخدم جديد
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'name.required' => __('The name field is required.'),
            'email.required' => __('The email field is required.'),
            'email.email' => __('The email must be a valid email address.'),
            'email.unique' => __('The email has already been taken.'),
            'password.required' => __('The password field is required.'),
            'password.min' => __('The password must be at least 8 characters.'),
            'password.confirmed' => __('The password confirmation does not match.'),
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('dashboard.users.index')
            ->with('success', __('User created successfully.'));
    }

    /**
     * عرض صفحة تعديل المستخدم
     */
    public function edit(User $user)
    {
        return view('dashboard.users.edit', compact('user'));
    }

    /**
     * تحديث بيانات المستخدم
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'password' => 'nullable|string|min:8|confirmed',
        ], [
            'name.required' => __('The name field is required.'),
            'email.required' => __('The email field is required.'),
            'email.email' => __('The email must be a valid email address.'),
            'email.unique' => __('The email has already been taken.'),
            'password.min' => __('The password must be at least 8 characters.'),
            'password.confirmed' => __('The password confirmation does not match.'),
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($request->filled('password')) {
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        return redirect()->route('dashboard.users.index')
            ->with('success', __('User updated successfully.'));
    }

    /**
     * حذف المستخدم
     */
    public function destroy(User $user)
    {
        // منع حذف المستخدم الحالي
        if ($user->id === auth()->id()) {
            return redirect()->route('dashboard.users.index')
                ->with('error', __('You cannot delete your own account.'));
        }

        $user->delete();

        return redirect()->route('dashboard.users.index')
            ->with('success', __('User deleted successfully.'));
    }

    /**
     * تغيير حالة المستخدم (تفعيل/إلغاء تفعيل)
     */
    public function toggleStatus(User $user)
    {
        // منع إلغاء تفعيل المستخدم الحالي
        if ($user->id === auth()->id()) {
            return redirect()->route('dashboard.users.index')
                ->with('error', __('You cannot deactivate your own account.'));
        }

        $user->update([
            'is_active' => !$user->is_active,
        ]);

        $status = $user->is_active ? __('activated') : __('deactivated');

        return redirect()->route('dashboard.users.index')
            ->with('success', __('User :status successfully.', ['status' => $status]));
    }
}
