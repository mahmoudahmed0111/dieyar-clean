<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Cleaner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    use ResponseTrait;

    /**
     * تسجيل دخول المنظف
     */
    public function login(Request $request)
    {
        $validator = validator($request->all(), [
            'email' => 'sometimes|email',
            'phone' => 'sometimes|string|max:20',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(null, $validator->errors()->first(), 403);
        }

        // التحقق من أن المستخدم أرسل إما email أو phone
        if (!$request->email && !$request->phone) {
            return $this->apiResponse(null, 'يجب إرسال البريد الإلكتروني أو رقم الهاتف', 403);
        }

        // البحث عن المريض بناءً على البيانات المرسلة
        if ($request->email) {
            $cleaner = Cleaner::where('email', $request->email)->first();
        } elseif ($request->phone) {
            $cleaner = Cleaner::where('phone', $request->phone)->first();
        }

        if (!$cleaner) {
            $errorMessage = $request->email ? 'البريد الإلكتروني غير مسجل لدينا' : 'رقم الهاتف غير مسجل لدينا';
            return $this->apiResponse(null, $errorMessage, 404);
        }

        // التحقق من صحة كلمة المرور
        if (!Hash::check($request->password, $cleaner->password)) {
            $errorMessage = $request->email ? 'كلمة المرور غير صحيحة للبريد الإلكتروني' : 'كلمة المرور غير صحيحة لرقم الهاتف';
            return $this->apiResponse(null, $errorMessage, 403);
        }

        $token = $cleaner->createToken('cleaner')->plainTextToken;

        return response()->json([
            'message' => 'تم تسجيل الدخول بنجاح',
            'access_token' => $token,
            'status' => 200,
            'data' => [
                'id' => $cleaner->id,
                'name' => $cleaner->name,
                'phone' => $cleaner->phone,
                'email' => $cleaner->email,
                'national_id' => $cleaner->national_id ?? null,
                'address' => $cleaner->address ?? null,
                'hire_date' => $cleaner->hire_date->format('Y-m-d') ?? null,
                'status' => $cleaner->status ?? null,
                'image' => $cleaner->image_url ?? null,
            ]
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

    public function deleteAccount(Request $request)
    {
        $cleaner = $request->user();

        $cleaner->currentAccessToken()->delete();

        $cleaner->delete();

        return $this->apiResponse(null, 'تم حذف الحساب بنجاح', 200);
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
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif',
        ], [
            'name.string' => 'الاسم يجب أن يكون نص',
            'email.email' => 'البريد الإلكتروني غير صحيح',
            'email.unique' => 'البريد الإلكتروني مستخدم بالفعل',
            'phone.string' => 'رقم الهاتف يجب أن يكون نص',
            'phone.max' => 'رقم الهاتف يجب أن يكون أقل من 20 حرف',
            'address.string' => 'العنوان يجب أن يكون نص',
            'image.image' => 'الملف يجب أن يكون صورة',
            'image.mimes' => 'نوع الصورة يجب أن يكون jpeg, png, jpg, gif',
            'image.max' => 'حجم الصورة يجب أن يكون أقل من 2 ميجابايت',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(null, $validator->errors()->first(), 422);
        }

        $updateData = $request->only(['name', 'email', 'phone', 'address']);

        // معالجة رفع الصورة
        if ($request->hasFile('image')) {
            // حذف الصورة القديمة إذا كانت موجودة
            if ($cleaner->image && Storage::disk('public')->exists($cleaner->image)) {
                Storage::disk('public')->delete($cleaner->image);
            }

            // حفظ الصورة الجديدة
            $imagePath = $request->file('image')->store('cleaners', 'public');
            $updateData['image'] = $imagePath;
        }

        $cleaner->update($updateData);

        // إعادة تحميل البيانات المحدثة
        $cleaner->refresh();

        // تنسيق البيانات للرد
        $cleanerData = [
            'id' => $cleaner->id,
            'name' => $cleaner->name,
            'email' => $cleaner->email,
            'phone' => $cleaner->phone,
            'address' => $cleaner->address,
            'image' => $cleaner->image ? asset('storage/' . $cleaner->image) : null,

        ];

        return $this->apiResponse($cleanerData, 'تم تحديث البيانات بنجاح');
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
