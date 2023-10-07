<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'يجب قبول الحقل :attribute.',
    'active_url' => 'الحقل :attribute ليس عنوان URL صالح.',
    'after' => 'يجب أن يكون الحقل :attribute تاريخًا بعد :date.',
    'after_or_equal' => 'يجب أن يكون الحقل :attribute تاريخًا بعد أو مساويًا لـ :date.',
    'alpha' => 'يجب أن يحتوي الحقل :attribute على أحرف فقط.',
    'alpha_dash' => 'يجب أن يحتوي الحقل :attribute على أحرف وأرقام وشرطات وشرطات سفلية فقط.',
    'alpha_num' => 'يجب أن يحتوي الحقل :attribute على أحرف وأرقام فقط.',
    'array' => 'يجب أن يكون الحقل :attribute مصفوفة.',
    'before' => 'يجب أن يكون الحقل :attribute تاريخًا قبل :date.',
    'before_or_equal' => 'يجب أن يكون الحقل :attribute تاريخًا قبل أو مساويًا لـ :date.',
    'between' => [
        'numeric' => 'يجب أن يكون الحقل :attribute بين :min و :max.',
        'file' => 'يجب أن يكون حجم الحقل :attribute بين :min و :max كيلوبايت.',
        'string' => 'يجب أن يكون عدد أحرف الحقل :attribute بين :min و :max.',
        'array' => 'يجب أن يحتوي الحقل :attribute على بين :min و :max عنصرًا.',
    ],
    'boolean' => 'يجب أن يكون الحقل :attribute صحيحًا أو خاطئًا.',
    'confirmed' => 'تأكيد الحقل :attribute غير مطابق.',
    'date' => 'الحقل :attribute ليس تاريخًا صالحًا.',
    'date_equals' => 'يجب أن يكون الحقل :attribute تاريخًا مساويًا لـ :date.',
    'date_format' => 'الحقل :attribute لا يتطابق مع الشكل :format.',
    'different' => 'الحقل :attribute و :other يجب أن يكونا مختلفين.',
    'digits' => 'يجب أن يحتوي الحقل :attribute على :digits أرقام.',
    'digits_between' => 'يجب أن يحتوي الحقل :attribute على بين :min و :max أرقام.',
    'dimensions' => 'الحقل :attribute يحتوي على أبعاد صورة غير صالحة.',
    'distinct' => 'الحقل :attribute يحتوي على قيمة مكررة.',
    'email' => 'الحقل :attribute يجب أن يكون عنوان بريد إلكتروني صالح.',
    'ends_with' => 'يجب أن ينتهي الحقل :attribute بأحد القيم التالية: :values.',
    'exists' => 'القيمة المحددة للحقل :attribute غير صالحة.',
    'file' => 'الحقل :attribute يجب أن يكون ملفًا.',
    'filled' => 'يجب أن يحتوي الحقل :attribute على قيمة.',
    'gt' => [
        'numeric' => 'يجب أن يكون الحقل :attribute أكبر من :value.',
        'file' => 'يجب أن يكون حجم الحقل :attribute أكبر من :value كيلوبايت.',
        'string' => 'يجب أن يكون عدد أحرف الحقل :attribute أكبر من :value.',
        'array' => 'يجب أن يحتوي الحقل :attribute على أكثر من :value عنصر.',
    ],
    'gte' => [
        'numeric' => 'يجب أن يكون الحقل :attribute أكبر من أو مساويًا لـ :value.',
        'file' => 'يجب أن يكون حجم الحقل :attribute أكبر من أو مساويًا لـ :value كيلوبايت.',
        'string' => 'يجب أن يكون عدد أحرف الحقل :attribute أكبر من أو مساويًا لـ :value.',
        'array' => 'يجب أن يحتوي الحقل :attribute على :value عنصر أو أكثر.',
    ],
    'image' => 'الحقل :attribute يجب أن يكون صورة.',
    'in' => 'القيمة المحددة للحقل :attribute غير صالحة.',
    'in_array' => 'الحقل :attribute غير موجود في :other.',
    'integer' => 'الحقل :attribute يجب أن يكون عددًا صحيحًا.',
    'ip' => 'الحقل :attribute يجب أن يكون عنوان IP صالحًا.',
    'ipv4' => 'الحقل :attribute يجب أن يكون عنوان IPv4 صالحًا.',
    'ipv6' => 'الحقل :attribute يجب أن يكون عنوان IPv6 صالحًا.',
    'json' => 'الحقل :attribute يجب أن يكون نص JSON صالحًا.',
    'lt' => [
        'numeric' => 'يجب أن يكون الحقل :attribute أصغر من :value.',
        'file' => 'يجب أن يكون حجم الحقل :attribute أصغر من :value كيلوبايت.',
        'string' => 'يجب أن يكون عدد أحرف الحقل :attribute أصغر من :value.',
        'array' => 'يجب أن يحتوي الحقل :attribute على أقل من :value عنصر.',
    ],
    'lte' => [
        'numeric' => 'يجب أن يكون الحقل :attribute أصغر من أو مساويًا لـ :value.',
        'file' => 'يجب أن يكون حجم الحقل :attribute أصغر من أو مساويًا لـ :value كيلوبايت.',
        'string' => 'يجب أن يكون عدد أحرف الحقل :attribute أصغر من أو مساويًا لـ :value.',
        'array' => 'يجب أن لا يحتوي الحقل :attribute على أكثر من :value عنصر.',
    ],
    'max' => [
        'numeric' => 'يجب ألا يكون الحقل :attribute أكبر من :max.',
        'file' => 'يجب ألا يكون حجم الحقل :attribute أكبر من :max كيلوبايت.',
        'string' => 'يجب ألا يحتوي الحقل :attribute على أكثر من :max حرف.',
        'array' => 'يجب ألا يحتوي الحقل :attribute على أكثر من :max عنصر.',
    ],
    'mimes' => 'يجب أن يكون الحقل :attribute ملف من النوع: :values.',
    'mimetypes' => 'يجب أن يكون الحقل :attribute ملف من النوع: :values.',
    'min' => [
        'numeric' => 'يجب أن يكون الحقل :attribute على الأقل :min.',
        'file' => 'يجب أن يكون حجم الحقل :attribute على الأقل :min كيلوبايت.',
        'string' => 'يجب أن يحتوي الحقل :attribute على الأقل :min حرف.',
        'array' => 'يجب أن يحتوي الحقل :attribute على الأقل :min عنصر.',
    ],
    'not_in' => 'القيمة المحددة للحقل :attribute غير صالحة.',
    'not_regex' => 'شكل الحقل :attribute غير صالح.',
    'numeric' => 'يجب أن يكون الحقل :attribute رقمًا.',
    'password' => 'كلمة المرور غير صحيحة.',
    'present' => 'يجب أن يكون الحقل :attribute موجودًا.',
    'regex' => 'شكل الحقل :attribute غير صالح.',
    'required' => 'حقل :attribute مطلوب.',
    'required_if' => 'يجب أن يكون الحقل :attribute مطلوبًا عندما يكون :other هو :value.',
    'required_unless' => 'يجب أن يكون الحقل :attribute مطلوبًا ما لم يكن :other موجودًا في :values.',
    'required_with' => 'يجب أن يكون الحقل :attribute مطلوبًا عندما يكون :values موجودًا.',
    'required_with_all' => 'يجب أن يكون الحقل :attribute مطلوبًا عندما تكون جميع :values موجودة.',
    'required_without' => 'يجب أن يكون الحقل :attribute مطلوبًا عندما لا يكون :values موجودًا.',
    'required_without_all' => 'يجب أن يكون الحقل :attribute مطلوبًا عندما لا تكون أي من :values موجودة.',
    'same' => 'الحقل :attribute و :other يجب أن يتطابقان.',
    'size' => [
        'numeric' => 'يجب أن يكون الحقل :attribute بحجم :size.',
        'file' => 'يجب أن يكون حجم الحقل :attribute بحجم :size كيلوبايت.',
        'string' => 'يجب أن يكون الحقل :attribute بطول :size أحرف.',
        'array' => 'يجب أن يحتوي الحقل :attribute على :size عنصر.',
    ],
    'starts_with' => 'يجب أن يبدأ الحقل :attribute بأحد القيم التالية: :values.',
    'string' => 'يجب أن يكون الحقل :attribute نصًا.',
    'timezone' => 'يجب أن يكون الحقل :attribute منطقة زمنية صالحة.',
    'unique' => 'الحقل :attribute موجود مسبقاً.',
    'uploaded' => 'فشل في تحميل الحقل :attribute.',
    'url' => 'شكل الحقل :attribute غير صالح.',
    'uuid' => 'يجب أن يكون الحقل :attribute UUID صالحًا.',


    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];
