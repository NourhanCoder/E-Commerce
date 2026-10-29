<?php

return [
    'status' => [
        'pending' => 'قيد التنفيذ',
        'processing' => 'التجهيز',
        'shipped' => 'الشحن',
        'delivered' => 'التوصيل',
        'cancelled' => 'ملغي',
    ],
    'payment_method' => [
        'cash' => 'نقدا',
        'card' => 'بالبطاقة',
    ],

    'out_of_stock' => 'الكمية غير متوفرة للمنتج: :title',
    'out_of_stock_after_payment' => 'تم الدفع لكن الكمية غير متوفرة للمنتج: :title. برجاء التواصل معنا.',
    'checkout_failed' => 'حدث خطأ أثناء إتمام الطلب. حاول مرة أخرى.',
    'cart_empty' => 'السلة فارغة',

    'payment_invalid' => 'تعذر التحقق من عملية الدفع.',
    'payment_mismatch' => 'بيانات الدفع لا تطابق الطلب.',
    'payment_failed' => 'فشل في عملية الدفع.',
    'payment_incomplete' => 'بيانات الدفع غير مكتملة.',

    'view_forbidden' => 'غير مسموح بعرض هذا الطلب.',
    'cancel_forbidden' => 'غير مسموح لك بإلغاء هذا الطلب.',
    'cancelled' => 'تم إلغاء الطلب وإرجاع الكمية بنجاح.',
    'cancel_failed' => 'حدث خطأ أثناء إلغاء الطلب.',
    'status_updated' => 'تم تحديث حالة الطلب بنجاح.',

];