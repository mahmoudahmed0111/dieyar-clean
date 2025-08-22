import Uppy from '@uppy/core'
import Dashboard from '@uppy/dashboard'
import XHRUpload from '@uppy/xhr-upload'
import ImageCompression from '@uppy/image-compression'
import ProgressBar from '@uppy/progress-bar'
import StatusBar from '@uppy/status-bar'

class CleaningUploader {
    constructor(options = {}) {
        this.token = options.token || ''
        this.endpoint = options.endpoint || '/api/cleaning/upload'
        this.uppy = null
        this.init()
    }

    init() {
        // إنشاء Uppy instance
        this.uppy = new Uppy({
            restrictions: {
                maxFileSize: 102400000, // 100MB للفيديوهات
                maxNumberOfFiles: 20,
                allowedFileTypes: ['image/*', 'video/*']
            },
            autoProceed: false,
            debug: true
        })

        // إضافة Image Compression للصور
        this.uppy.use(ImageCompression, {
            quality: 0.8,
            maxWidth: 1920,
            maxHeight: 1080
        })

        // إضافة Progress Bar
        this.uppy.use(ProgressBar, {
            target: '#progress-bar',
            hideAfterFinish: false
        })

        // إضافة Status Bar
        this.uppy.use(StatusBar, {
            target: '#status-bar',
            hideAfterFinish: false
        })

        // إضافة Dashboard
        this.uppy.use(Dashboard, {
            inline: true,
            target: '#uppy-dashboard',
            height: 400,
            showProgressDetails: true,
            proudlyDisplayPoweredByUppy: false,
            locale: {
                strings: {
                    dropHereOr: 'اسحب الملفات هنا أو %{browse}',
                    browse: 'اختر الملفات',
                    uploadComplete: 'تم الرفع بنجاح',
                    uploadFailed: 'فشل في الرفع',
                    processing: 'جاري المعالجة...',
                    uploadXFiles: {
                        0: 'رفع %{smart_count} ملف',
                        1: 'رفع %{smart_count} ملف'
                    },
                    uploadXNewFiles: {
                        0: 'رفع +%{smart_count} ملف جديد',
                        1: 'رفع +%{smart_count} ملف جديد'
                    }
                }
            }
        })

        // إعداد XHR Upload
        this.uppy.use(XHRUpload, {
            endpoint: this.endpoint,
            formData: true,
            fieldName: 'file',
            headers: {
                'Authorization': `Bearer ${this.token}`,
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            metaFields: [
                'cleaning_type',
                'chalet_id',
                'cleaning_time',
                'date',
                'cleaning_cost',
                'inventory_items'
            ]
        })

        // إضافة Event Listeners
        this.setupEventListeners()
    }

    setupEventListeners() {
        // معالجة النجاح
        this.uppy.on('upload-success', (file, response) => {
            console.log('تم رفع الملف بنجاح:', file.name)
            console.log('الاستجابة:', response.body)

            // إظهار رسالة نجاح
            this.showNotification('تم رفع الملف بنجاح: ' + file.name, 'success')
        })

        // معالجة الأخطاء
        this.uppy.on('upload-error', (file, error, response) => {
            console.error('خطأ في رفع الملف:', file.name, error)

            // إظهار رسالة خطأ
            this.showNotification('خطأ في رفع الملف: ' + file.name, 'error')
        })

        // معالجة اكتمال الرفع
        this.uppy.on('complete', (result) => {
            console.log('اكتمل الرفع:', result)

            if (result.successful.length > 0) {
                this.showNotification(`تم رفع ${result.successful.length} ملف بنجاح`, 'success')
            }

            if (result.failed.length > 0) {
                this.showNotification(`فشل في رفع ${result.failed.length} ملف`, 'error')
            }
        })
    }

    // دالة لرفع النظافة
    async uploadCleaning(cleaningData) {
        try {
            // إضافة البيانات إلى Uppy
            this.uppy.setMeta({
                cleaning_type: cleaningData.cleaning_type,
                chalet_id: cleaningData.chalet_id,
                cleaning_time: cleaningData.cleaning_time,
                date: cleaningData.date,
                cleaning_cost: cleaningData.cleaning_cost || '',
                inventory_items: cleaningData.inventory_items ? JSON.stringify(cleaningData.inventory_items) : ''
            })

            // بدء الرفع
            this.uppy.upload()

        } catch (error) {
            console.error('خطأ في رفع النظافة:', error)
            this.showNotification('حدث خطأ أثناء رفع النظافة', 'error')
            throw error
        }
    }

    // دالة لرفع ملفات محددة
    addFiles(files) {
        this.uppy.addFiles(files)
    }

    // دالة لحذف جميع الملفات
    reset() {
        this.uppy.reset()
    }

    // دالة لإظهار الإشعارات
    showNotification(message, type = 'info') {
        // يمكن استخدام مكتبة إشعارات مثل Toastr أو SweetAlert
        if (typeof toastr !== 'undefined') {
            toastr[type](message)
        } else {
            alert(message)
        }
    }

    // دالة لتحديث التوكن
    updateToken(token) {
        this.token = token
        // تحديث التوكن في XHR Upload
        this.uppy.getPlugin('XHRUpload').opts.headers.Authorization = `Bearer ${token}`
    }
}

// مثال على الاستخدام
document.addEventListener('DOMContentLoaded', function() {
    // إنشاء instance من CleaningUploader
    const uploader = new CleaningUploader({
        token: document.querySelector('meta[name="auth-token"]')?.getAttribute('content') || '',
        endpoint: '/api/cleaning/upload'
    })

    // دالة لرفع النظافة
    window.uploadCleaning = function(cleaningData) {
        uploader.uploadCleaning(cleaningData)
    }

    // دالة لإضافة ملفات
    window.addCleaningFiles = function(files) {
        uploader.addFiles(files)
    }

    // دالة لإعادة تعيين
    window.resetCleaningUpload = function() {
        uploader.reset()
    }

    // مثال على استخدام الدالة
    window.exampleUpload = function() {
        const cleaningData = {
            cleaning_type: 'regular',
            chalet_id: 1,
            cleaning_time: 'before',
            cleaning_date: '2024-01-15',
            start_time: '09:00',
            end_time: '11:30',
            notes: 'ملاحظات إضافية'
        }

        uploader.uploadCleaning(cleaningData)
    }
})

// تصدير الكلاس للاستخدام في ملفات أخرى
export default CleaningUploader
