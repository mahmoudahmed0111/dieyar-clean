<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Cleaner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    use ResponseTrait;

    /**
     * تسجيل دخول المنظف
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
            'password' => 'required|string|min:6',
        ], [
            'phone.required' => 'رقم الهاتف مطلوب',
            'password.required' => 'كلمة المرور مطلوبة',
            'password.min' => 'كلمة المرور يجب أن تكون 6 أحرف على الأقل',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(null, $validator->errors()->first(), 422);
        }

        $cleaner = Cleaner::where('phone', $request->phone)->first();

        if (!$cleaner || !Hash::check($request->password, $cleaner->password)) {
            return $this->apiResponse(null, 'رقم الهاتف أو كلمة المرور غير صحيحة', 401);
        }

        if ($cleaner->status !== 'active') {
            return $this->apiResponse(null, 'الحساب غير مفعل', 403);
        }

        $token = $cleaner->createToken('cleaner-token')->plainTextToken;


        return response()->json([
            'message' => 'تم تسجيل الدخول بنجاح',
            'token' => $token,
        ], 200);
    }

    /**
     * تسجيل خروج المنظف
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->apiResponse(null, 'تم تسجيل الخروج بنجاح');
    }

    /**
     * معلومات المنظف الحالي
     */
    public function profile(Request $request)
    {
        $cleaner = $request->user()->only(['id', 'name', 'email', 'phone', 'address']);

        return $this->apiResponse($cleaner, 'تم جلب البيانات بنجاح');
    }

    /**
     * تحديث بيانات الملف الشخصي
     */
    public function updateProfile(Request $request)
    {
        $cleaner = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:cleaners,email,' . $cleaner->id,
            'phone' => 'sometimes|string|max:20',
            'address' => 'sometimes|string|max:255',
        ], [
            'name.string' => 'الاسم يجب أن يكون نص',
            'email.email' => 'البريد الإلكتروني غير صحيح',
            'email.unique' => 'البريد الإلكتروني مستخدم بالفعل',
            'phone.string' => 'رقم الهاتف يجب أن يكون نص',
            'phone.max' => 'رقم الهاتف يجب أن يكون أقل من 20 حرف',
            'address.string' => 'العنوان يجب أن يكون نص',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(null, $validator->errors()->first(), 422);
        }

        $cleaner->update($request->only(['name', 'email', 'phone', 'address']));

        return $this->apiResponse(null, 'تم تحديث البيانات بنجاح');
    }

    /**
     * تحديث كلمة المرور
     */
    public function updatePassword(Request $request)
    {
        $cleaner = $request->user();

        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
            'new_password_confirmation' => 'required|string|min:6',
        ], [
            'current_password.required' => 'كلمة المرور الحالية مطلوبة',
            'new_password.required' => 'كلمة المرور الجديدة مطلوبة',
            'new_password.min' => 'كلمة المرور الجديدة يجب أن تكون 6 أحرف على الأقل',
            'new_password.confirmed' => 'تأكيد كلمة المرور غير متطابق',
            'new_password_confirmation.required' => 'تأكيد كلمة المرور مطلوب',
            'new_password_confirmation.min' => 'تأكيد كلمة المرور يجب أن يكون 6 أحرف على الأقل',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(null, $validator->errors()->first(), 422);
        }

        // التحقق من كلمة المرور الحالية
        if (!Hash::check($request->current_password, $cleaner->password)) {
            return $this->apiResponse(null, 'كلمة المرور الحالية غير صحيحة', 400);
        }

        // تحديث كلمة المرور
        $cleaner->update([
            'password' => Hash::make($request->new_password)
        ]);

        return $this->apiResponse(null, 'تم تحديث كلمة المرور بنجاح');
    }
}
