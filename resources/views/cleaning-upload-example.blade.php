<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="auth-token" content="{{ auth()->user()->createToken('cleaner')->plainTextToken ?? '' }}">
    <title>رفع النظافة - مثال</title>

    <!-- Uppy CSS -->
    <link href="https://releases.transloadit.com/uppy/v3.0.0/uppy.min.css" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        .uppy-Dashboard {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .uppy-Dashboard-inner {
            border-radius: 8px;
            border: 2px dashed #e5e7eb;
        }

        .uppy-Dashboard-dropzone {
            background-color: #f9fafb;
        }

        .uppy-Dashboard-dropzone.dragover {
            background-color: #dbeafe;
            border-color: #3b82f6;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <!-- العنوان -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">رفع النظافة</h1>
                <p class="text-gray-600">قم برفع الصور والفيديوهات قبل وبعد النظافة</p>
            </div>

            <!-- نموذج البيانات -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4">بيانات النظافة</h2>

                <form id="cleaningForm" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- نوع النظافة -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">نوع النظافة</label>
                        <select id="cleaningType" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="regular">نظافة عادية</option>
                            <option value="deep">نظافة عميقة</option>
                        </select>
                    </div>

                    <!-- معرف الشاليه -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">معرف الشاليه</label>
                        <input type="number" id="chaletId" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="أدخل معرف الشاليه">
                    </div>

                    <!-- وقت النظافة -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">وقت النظافة</label>
                        <select id="cleaningTime" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="before">قبل النظافة</option>
                            <option value="after">بعد النظافة</option>
                        </select>
                    </div>

                    <!-- تاريخ النظافة -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">تاريخ النظافة</label>
                        <input type="date" id="cleaningDate" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>



                    <!-- سعر النظافة (يظهر فقط في حالة after) -->
                    <div id="costField" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">سعر النظافة</label>
                        <input type="number" id="cleaningCost" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="أدخل سعر النظافة">
                    </div>



                    <!-- المنتجات المستخدمة (يظهر فقط في حالة after) -->
                    <div id="inventoryField" class="md:col-span-2 hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">المنتجات المستخدمة</label>
                        <div id="inventoryItems" class="space-y-2">
                            <div class="flex gap-2">
                                <select class="inventory-select flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">اختر المنتج</option>
                                    <option value="1">منظف أرضيات</option>
                                    <option value="2">منظف حمامات</option>
                                    <option value="3">منظف مطبخ</option>
                                </select>
                                <input type="number" class="inventory-quantity w-20 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="كمية" min="1">
                                <button type="button" class="remove-inventory px-3 py-2 bg-red-500 text-white rounded-md hover:bg-red-600">حذف</button>
                            </div>
                        </div>
                        <button type="button" id="addInventory" class="mt-2 px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">إضافة منتج</button>
                    </div>
                </form>
            </div>

            <!-- منطقة رفع الملفات -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4">رفع الملفات</h2>

                <!-- Progress Bar -->
                <div id="progress-bar" class="mb-4"></div>

                <!-- Uppy Dashboard -->
                <div id="uppy-dashboard"></div>

                <!-- Status Bar -->
                <div id="status-bar" class="mt-4"></div>
            </div>

            <!-- أزرار التحكم -->
            <div class="flex justify-center gap-4">
                <button id="uploadBtn" class="px-6 py-3 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    رفع النظافة
                </button>
                <button id="resetBtn" class="px-6 py-3 bg-gray-500 text-white rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500">
                    إعادة تعيين
                </button>
            </div>

            <!-- منطقة النتائج -->
            <div id="results" class="mt-8 bg-white rounded-lg shadow-md p-6 hidden">
                <h2 class="text-xl font-semibold mb-4">نتائج الرفع</h2>
                <div id="resultsContent"></div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://releases.transloadit.com/uppy/v3.0.0/uppy.min.js"></script>
    <script type="module">
        import CleaningUploader from '/js/cleaning-upload.js'

        // إنشاء instance من CleaningUploader
        const uploader = new CleaningUploader({
            token: document.querySelector('meta[name="auth-token"]').getAttribute('content'),
            endpoint: '/api/cleaning/upload'
        })

        // إعداد Event Listeners
        document.addEventListener('DOMContentLoaded', function() {
            // إظهار/إخفاء حقول التكلفة والمنتجات حسب وقت النظافة
            const cleaningTimeSelect = document.getElementById('cleaningTime')
            const costField = document.getElementById('costField')
            const inventoryField = document.getElementById('inventoryField')

            cleaningTimeSelect.addEventListener('change', function() {
                if (this.value === 'after') {
                    costField.classList.remove('hidden')
                    inventoryField.classList.remove('hidden')
                } else {
                    costField.classList.add('hidden')
                    inventoryField.classList.add('hidden')
                }
            })

            // إضافة منتج جديد
            const addInventoryBtn = document.getElementById('addInventory')
            const inventoryItems = document.getElementById('inventoryItems')

            addInventoryBtn.addEventListener('click', function() {
                const newItem = document.createElement('div')
                newItem.className = 'flex gap-2'
                newItem.innerHTML = `
                    <select class="inventory-select flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">اختر المنتج</option>
                        <option value="1">منظف أرضيات</option>
                        <option value="2">منظف حمامات</option>
                        <option value="3">منظف مطبخ</option>
                    </select>
                    <input type="number" class="inventory-quantity w-20 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="كمية" min="1">
                    <button type="button" class="remove-inventory px-3 py-2 bg-red-500 text-white rounded-md hover:bg-red-600">حذف</button>
                `
                inventoryItems.appendChild(newItem)
            })

            // حذف منتج
            inventoryItems.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-inventory')) {
                    e.target.parentElement.remove()
                }
            })

            // زر الرفع
            const uploadBtn = document.getElementById('uploadBtn')
            uploadBtn.addEventListener('click', function() {
                // جمع البيانات من النموذج
                const cleaningData = {
                    cleaning_type: document.getElementById('cleaningType').value,
                    chalet_id: parseInt(document.getElementById('chaletId').value),
                    cleaning_time: document.getElementById('cleaningTime').value,
                    date: document.getElementById('cleaningDate').value
                }

                // إضافة التكلفة والمنتجات في حالة after
                if (cleaningData.cleaning_time === 'after') {
                    cleaningData.cleaning_cost = parseFloat(document.getElementById('cleaningCost').value) || 0

                    // جمع المنتجات
                    const inventoryItems = []
                    document.querySelectorAll('.inventory-select').forEach((select, index) => {
                        const quantity = document.querySelectorAll('.inventory-quantity')[index]
                        if (select.value && quantity.value) {
                            inventoryItems.push({
                                inventory_id: parseInt(select.value),
                                quantity: parseInt(quantity.value)
                            })
                        }
                    })
                    cleaningData.inventory_items = inventoryItems
                }

                // التحقق من صحة البيانات
                if (!cleaningData.chalet_id || !cleaningData.cleaning_date || !cleaningData.start_time || !cleaningData.end_time) {
                    alert('يرجى ملء جميع الحقول المطلوبة')
                    return
                }

                // رفع النظافة
                uploader.uploadCleaning(cleaningData)
            })

            // زر إعادة التعيين
            const resetBtn = document.getElementById('resetBtn')
            resetBtn.addEventListener('click', function() {
                uploader.reset()
                document.getElementById('cleaningForm').reset()
                document.getElementById('costField').classList.add('hidden')
                document.getElementById('inventoryField').classList.add('hidden')
                document.getElementById('results').classList.add('hidden')
            })

            // معالجة اكتمال الرفع
            uploader.uppy.on('complete', (result) => {
                const resultsDiv = document.getElementById('results')
                const resultsContent = document.getElementById('resultsContent')

                let html = '<div class="space-y-4">'

                if (result.successful.length > 0) {
                    html += `<div class="p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                        <strong>تم رفع ${result.successful.length} ملف بنجاح</strong>
                    </div>`
                }

                if (result.failed.length > 0) {
                    html += `<div class="p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                        <strong>فشل في رفع ${result.failed.length} ملف</strong>
                    </div>`
                }

                html += '</div>'
                resultsContent.innerHTML = html
                resultsDiv.classList.remove('hidden')
            })
        })
    </script>
</body>
</html>
