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
];