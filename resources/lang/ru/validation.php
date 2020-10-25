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

    'accepted'             => 'Поле :attribute must be accepted.',
    'active_url'           => 'Поле :attribute is not a valid URL.',
    'after'                => 'Поле :attribute must be a date after :date.',
    'after_or_equal'       => 'Поле :attribute must be a date after or equal to :date.',
    'alpha'                => 'Поле :attribute may only contain letters.',
    'alpha_dash'           => 'Поле :attribute may only contain letters, numbers, and dashes.',
    'alpha_num'            => 'Поле :attribute may only contain letters and numbers.',
    'array'                => 'Поле :attribute must be an array.',
    'before'               => 'Поле :attribute must be a date before :date.',
    'before_or_equal'      => 'Поле :attribute must be a date before or equal to :date.',
    'between'              => [
        'numeric' => 'Поле :attribute must be between :min and :max.',
        'file'    => 'Поле :attribute must be between :min and :max kilobytes.',
        'string'  => 'Поле :attribute must be between :min and :max characters.',
        'array'   => 'Поле :attribute must have between :min and :max items.',
    ],
    'boolean'              => 'Поле :attribute field must be true or false.',
    'confirmed'            => 'Поле :attribute confirmation does not match.',
    'date'                 => 'Поле :attribute is not a valid date.',
    'date_format'          => 'Поле :attribute does not match the format :format.',
    'different'            => 'Поле :attribute and :other must be different.',
    'digits'               => 'Поле :attribute must be :digits digits.',
    'digits_between'       => 'Поле :attribute must be between :min and :max digits.',
    'dimensions'           => 'Поле :attribute has invalid image dimensions.',
    'distinct'             => 'Поле :attribute field has a duplicate value.',
    'email'                => 'Поле :attribute must be a valid email address.',
    'exists'               => 'Поле selected :attribute is invalid.',
    'file'                 => 'Поле :attribute must be a file.',
    'filled'               => 'Поле :attribute field must have a value.',
    'image'                => 'Поле :attribute must be an image.',
    'in'                   => 'Поле selected :attribute is invalid.',
    'in_array'             => 'Поле :attribute field does not exist in :other.',
    'integer'              => 'Поле :attribute должно быть числом.',
    'ip'                   => 'Поле :attribute must be a valid IP address.',
    'ipv4'                 => 'Поле :attribute must be a valid IPv4 address.',
    'ipv6'                 => 'Поле :attribute must be a valid IPv6 address.',
    'json'                 => 'Поле :attribute must be a valid JSON string.',
    'max'                  => [
        'numeric' => 'Поле :attribute may not be greater than :max.',
        'file'    => 'Поле :attribute may not be greater than :max kilobytes.',
        'string'  => 'Поле :attribute may not be greater than :max characters.',
        'array'   => 'Поле :attribute may not have more than :max items.',
    ],
    'mimes'                => 'Поле :attribute must be a file of type: :values.',
    'mimetypes'            => 'Поле :attribute must be a file of type: :values.',
    'min'                  => [
        'numeric' => 'Поле :attribute must be at least :min.',
        'file'    => 'Поле :attribute must be at least :min kilobytes.',
        'string'  => 'Поле :attribute must be at least :min characters.',
        'array'   => 'Поле :attribute must have at least :min items.',
    ],
    'not_in'               => 'Поле selected :attribute is invalid.',
    'numeric'              => 'Поле :attribute must be a number.',
    'present'              => 'Поле :attribute field must be present.',
    'regex'                => 'Поле :attribute format is invalid.',
    'required'             => 'Поле :attribute обязательно для заполнения.',
    'required_if'          => 'Поле :attribute обязательно для заполнения when :other is :value.',
    'required_unless'      => 'Поле :attribute обязательно для заполнения unless :other is in :values.',
    'required_with'        => 'Поле :attribute обязательно для заполнения when :values is present.',
    'required_with_all'    => 'Поле :attribute обязательно для заполнения when :values is present.',
    'required_without'     => 'Поле :attribute обязательно для заполнения when :values is not present.',
    'required_without_all' => 'Поле :attribute обязательно для заполнения when none of :values are present.',
    'same'                 => 'Поле :attribute and :other must match.',
    'size'                 => [
        'numeric' => 'Поле :attribute must be :size.',
        'file'    => 'Поле :attribute must be :size kilobytes.',
        'string'  => 'Поле :attribute must be :size characters.',
        'array'   => 'Поле :attribute must contain :size items.',
    ],
    'string'               => 'Поле :attribute must be a string.',
    'timezone'             => 'Поле :attribute must be a valid zone.',
    'unique'               => 'Поле :attribute has already been taken.',
    'uploaded'             => 'Поле :attribute failed to upload.',
    'url'                  => 'Поле :attribute format is invalid.',

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
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [
        'template_id' => '"шаблон"',
        'mailing_list_id' => '"список рассылки"',
    ],

];
