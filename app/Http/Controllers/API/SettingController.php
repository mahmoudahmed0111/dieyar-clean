<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\API\ResponseTrait;
use App\Http\Controllers\Controller;
use App\Models\Setting;

use Illuminate\Http\Request;

class SettingController extends Controller
{
    use ResponseTrait;

    /**
     * جلب الإعدادات
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $settings = Setting::select(['name', 'phone', 'email', 'address', 'logo'])->first();

        if (!$settings) {
            return $this->apiResponse(null, 'لا توجد إعدادات متاحة', 404);
        }

        return $this->apiResponse($settings, 'تم جلب الإعدادات بنجاح', 200);
    }
}
