<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    |  following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */
        
    'accepted' => ':attributeটির গ্রহনযোগ্যতা প্রয়োজন।',
    'active_url' => ':attributeটির url সচল নয়।',
    'after' => ':date তারিখের পরে :attribute  তারিখ হতে হবে।',
    'after_or_equal' => ':attribute  তারিখ :date তারিখ এ অথবা তার পরে হতে হবে।',
    'alpha' => ':attributeটিতে শুধুমাত্র অক্ষর অথবা বর্নমালা ব্যবহার করা যাবে।',
    'alpha_dash' => ':attribute এখানে অক্ষর, সংখ্যা, ড্যাশ (-) এবং আন্ডারস্কোর (_) ব্যবহার করা যাবে।',
    'alpha_num' => ':attribute এখানে শুধুমাত্র অক্ষর এবং সংখ্যা ব্যবহার করা যাবে। ',
    'already_exists' => ':attribute আগে থেকেই আছে।',
    'array' => ':attributeটি অ্যারে হতে হবে।',
    'before' => ' :attribute  তারিখ :date  তারিখের আগে হতে হবে।',
    'before_or_equal' => ' :attribute  তারিখ :date  তারিখ অথবা আগের তারিখে হতে হবে।',
    'between' => [
        'numeric' => ':attributeটি :min থেকে :max  মধ্যে হতে হবে।',
        'file' => ':attributeটি :min থেকে :max কিলোবাইটের মধ্যে হতে হবে।.',
        'string' => ':attributeটির characters :min থেকে :max  ভেতর হতে হবে।',
        'array' => ':attributeটির items :min থেকে :max  ভেতর হতে হবে।',
    ],
    'boolean' => ':attribute ফিল্ডটি শুধু সত্য অথবা মিথ্যা হতে হবে।',
    'confirmed' => ':attributeটির কনফার্মেশন (confirmation) সঠিক নয়।',
    'current_password' => 'পাসওয়ার্ডটি সঠিক নয়।',
    'data_not_exists' => ':attribute পাওয়া যায়নি।',
    'date' => ':attribute তারিখটি সঠিক নয়।',
    'date_equals' => ' :attributeটির তারিখ :date  সাথে মিলতে হবে।.',
    'date_format' => ':attributeটির ফরম্যাট মিলছে না :format  সাথে।',
    'different' => ':attribute এবং :other ভিন্ন হতে হবে।',
    'digits' => ':attributeটি :digits সংখ্যার ভেতর হতে হবে।',
    'digits_between' => ':attributeটি :min থেকে :max সংখ্যার ভেতর হবে।',
    'dimensions' => ':attributeটির image dimensions বা ছবির মাত্রা সঠিক নয়।',
    'distinct' => 'এই :attribute ফিল্ডটিতে অনুরুপ আরেকটি মান আছে।',
    'email' => ':attributeটির ইমেইল বৈধ/সঠিক হতে হবে।',
    'ends_with' => ' :attribute must end with one of the following: :values.',
    'exists' => 'চিহ্নিত :attributeটি সঠিক নয়।',
    'file' => ':attributeটি ফাইল হতে হবে।.',
    'filled' => ':attribute ফিল্ডে ভ্যালু ইনপুট করুন।',
    'gt' => [
        'numeric' => ':attribute  মান :value থেকে বেশি হতে হবে।',
        'file' => ':attribute  মান :value kilobytes থেকে বেশি হতে হবে।',
        'string' => ':attribute  character, :value character থেকে বেশি হতে হবে। ',
        'array' => ':attribute items, :value items থেকে বেশি হতে হবে।',
    ],
    'gte' => [
        'numeric' => ':attribute  মান :value থেকে সমান অথবা বড় হতে হবে।',
        'file' => ':attribute  মান :value kilobytes থেকে সমান অথবা বড় হতে হবে।',
        'string' => ':attribute  মান :value characters থেকে সমান অথবা বড় হতে হবে।',
        'array' => ':attribute  items :value items থেকে বেশি হতে হবে।',
    ],
    'image' => ':attributeটি ছবি হতে হবে।',
    'in' => 'চিহ্নিত :attributeটি অকার্যকর।.',
    'in_array' => ':attribute ফিল্ডটি :other  মধ্যে নেই।',
    'integer' => ':attributeটি কে ইন্টেজার হতে হবে।',
    'input_not_changed' => 'কোন ইনপুট পরিবর্তন হয়নি।',
    'input_not_found' => 'কোন ইনপুট সনাক্ত করা যায়নি।',
    'input_not_valid' => ':attribute সঠিক ইনপুট না।',
    'ip' => ':attributeটির IP address সঠিক হতে হবে।',
    'ipv4' => ':attributeটির IPv4 address সঠিক হতে হবে।',
    'ipv6' => ':attributeটির IPv6 address সঠিক হতে হবে।',
    'json' => ':attributeটির JSON string সঠিক হতে হবে।',
    'lt' => [
        'numeric' => ':attribute  মান মান :value থেকে কম হতে হবে।',
        'file' => ':attribute  মান :value kilobytes থেকে কম হতে হবে।',
        'string' => ':attribute  মান :value characters থেকে কম হতে হবে।',
        'array' => ':attribute  মান :value items থেকে কম হতে হবে।',
    ],
    'lte' => [
        'numeric' => ':attribute  মান :value  সমান অথবা কম হতে হবে।',
        'file' => ' :attribute  মান :value kilobytes  সমান অথবা কম হতে হবে।',
        'string' => ' :attribute  মান :value characters  সমান অথবা কম হতে হবে।',
        'array' => ' :attribute, :value items থেকে বেশি হতে পারবে না।',
    ],
    'max' => [
        'numeric' => ' :attribute  মান :max থেকে বেশি হতে পারবে না।',
        'file' => ' :attribute  মান :max kilobytes থেকে বেশি হতে পারবে না।',
        'string' => ' :attribute  মান :max characters থেকে বেশি হতে পারবে না।',
        'array' => ' :attribute, :max items থেকে বেশি হতে পারবে না।',
    ],
    'mimes' => ' :attributeকে এই ফাইল টাইপের ভেতর হতে হবে, : :values.',
    'mimetypes' => ' :attribute কে এই ফাইল টাইপের ভেতর হতে হবে: :values.',
    'min' => [
        'numeric' => ' :attribute নূন্যতম :min হতে হবে।.',
        'file' => ' :attribute নূন্যতম :min kilobytes হতে হবে।',
        'string' => ' :attribute নূন্যতম :min characters হতে হবে।',
        'array' => ' :attribute নূন্যতম :min items হতে হবে।.',
    ],
    'multiple_of' => ' :attribute এর একাধিক :value থাকতে হবে।',    
    'not_in' => ':attribute টি সঠিক নয়।',
    'not_regex' => ' :attribute ফরম্যাটটি সঠিক নয়।.',
    'numeric' => ' :attribute সংখ্যা হতে হবে।',
    'password' => ' পাসওয়ার্ডটি ভুল হয়েছে।.',
    'present' => ' :attribute ফিল্ড বিদ্যমান থাকতে হবে।',
    'regex' => ' :attribute ফরম্যাট সঠিক নয়।',
    'required' => ' :attribute ফিল্ড আবশ্যক।',
    'required_if' => ' :attribute ফিল্ড আবশ্যক যখন :otherই :value।',
    'required_unless' => ' :attribute ফিল্ড আবশ্যক হবে না, যদি :other, :valuesতে থাকে।',
    'required_with' => ' :attribute ফিল্ড আবশ্যক যখন :values বিদ্যমান থাকবে।',
    'required_with_all' => ' :attribute ফিল্ড আবশ্যক যখন :values গুলো বিদ্যমান থাকবে।',
    'required_without' => ' :attribute ফিল্ড আবশ্যক যখন :values বিদ্যমান থাকবে না।',
    'required_without_all' => ' :attribute ফিল্ড আবশ্যক যখন :valuesগুলো বিদ্যমান থাকবে না।',
    'prohibited' => ' :attribute ফিল্ড নিষিদ্ধ।',
    'prohibited_if' => ' :attribute ফিল্ড নিষিদ্ধ যখন :otherই :value।',
    'prohibited_unless' => ' :attribute নিষিদ্ধ হবে না যদি :other, :valuesতে থাকে।.',
    'save_token_invalid' => 'সেভ টোকেন সঠিক নয়।',
    'save_token_missing' => 'সেভ টোকেন অনুপস্থিত।',    
    'save_token_data_already_saved' => 'ডেটা আগেই সংরক্ষিত হয়েছে।',
    'same' => ' :attribute এবং :other - কে অবশ্যই এক হতে হবে।',
    'size' => [
        'numeric' => ' :attribute কে :size হতে হবে।',
        'file' => ' :attribute কে :size kilobytes হতে হবে।',
        'string' => ' :attribute কে :size characters এর হতে হবে।',
        'array' => ' :attribute - এ :size এর items অবশ্যই থাকতে হবে।',
    ],
    'starts_with' => ' :attribute কে শুরু হতে হবে এই: :values গুলো দিয়ে।',
    'string' => ' :attribute স্ট্রিং হতে হবে।.',
    'timezone' => ' :attribute সঠিক জোনে হতে হবে।.',
    'unique' => ' :attribute ইতিমধ্যে নেওয়া হয়ে গেছে।',
    'uploaded' => ' :attribute ব্যর্থ হয়েছে আপলোড করতে।',
    'url' => ' :attribute এর ফরম্যাট সঠিক হয়নি।',
    'uuid' => ' :attributeকে সঠিক UUID হতে হবে।.',

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
    |  following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [        
        'name'=>'নাম',
        'prefix'=>'প্রিফিক্স',
        'name.en'=>'Name',
        'name.bn'=>'নাম',
        'username'=>'ইউজারনেম',
        'password'=>'পাসওয়ার্ড',
        'user_group_id'=>'ব্যবহারকারী দল',
        'email'=>'ইমেইল',
        'mobile_no'=>'মোবাইল নাম্বার',
        'status'=>'স্ট্যাটাস',
    ],

];
