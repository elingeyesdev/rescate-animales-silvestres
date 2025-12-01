<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Líneas de lenguaje de validación
    |--------------------------------------------------------------------------
    |
    | Las siguientes líneas contienen los mensajes de error por defecto usados
    | por la clase validadora. Siéntete libre de ajustar estas líneas de acuerdo
    | a los requerimientos de tu aplicación.
    |
    */

    'accepted'             => 'El campo :attribute debe ser aceptado.',
    'active_url'           => 'El campo :attribute no es una URL válida.',
    'after'                => 'El campo :attribute debe ser una fecha posterior a :date.',
    'after_or_equal'       => 'El campo :attribute debe ser una fecha posterior o igual a :date.',
    'alpha'                => 'El campo :attribute solo puede contener letras.',
    'alpha_dash'           => 'El campo :attribute solo puede contener letras, números, guiones y guiones bajos.',
    'alpha_num'            => 'El campo :attribute solo puede contener letras y números.',
    'array'                => 'El campo :attribute debe ser un arreglo.',
    'before'               => 'El campo :attribute debe ser una fecha anterior a :date.',
    'before_or_equal'      => 'El campo :attribute debe ser una fecha anterior o igual a :date.',
    'between'              => [
        'numeric' => 'El campo :attribute debe estar entre :min y :max.',
        'file'    => 'El archivo :attribute debe pesar entre :min y :max kilobytes.',
        'string'  => 'El campo :attribute debe contener entre :min y :max caracteres.',
        'array'   => 'El campo :attribute debe contener entre :min y :max elementos.',
    ],
    'boolean'              => 'El campo :attribute debe ser verdadero o falso.',
    'confirmed'            => 'La confirmación de :attribute no coincide.',
    'date'                 => 'El campo :attribute no es una fecha válida.',
    'date_equals'          => 'El campo :attribute debe ser una fecha igual a :date.',
    'date_format'          => 'El campo :attribute no corresponde al formato :format.',
    'different'            => 'Los campos :attribute y :other deben ser diferentes.',
    'digits'               => 'El campo :attribute debe ser de :digits dígitos.',
    'digits_between'       => 'El campo :attribute debe tener entre :min y :max dígitos.',
    'dimensions'           => 'El campo :attribute tiene dimensiones de imagen inválidas.',
    'distinct'             => 'El campo :attribute tiene un valor duplicado.',
    'email'                => 'El campo :attribute debe ser una dirección de correo válida.',
    'exists'               => 'El valor seleccionado en :attribute es inválido.',
    'file'                 => 'El campo :attribute debe ser un archivo.',
    'filled'               => 'El campo :attribute debe tener un valor.',
    'gt'                   => [
        'numeric' => 'El campo :attribute debe ser mayor que :value.',
        'file'    => 'El archivo :attribute debe ser mayor que :value kilobytes.',
        'string'  => 'El campo :attribute debe ser mayor que :value caracteres.',
        'array'   => 'El campo :attribute debe tener más de :value elementos.',
    ],
    'gte'                  => [
        'numeric' => 'El campo :attribute debe ser mayor o igual que :value.',
        'file'    => 'El archivo :attribute debe ser mayor o igual que :value kilobytes.',
        'string'  => 'El campo :attribute debe ser mayor o igual que :value caracteres.',
        'array'   => 'El campo :attribute debe tener :value elementos o más.',
    ],
    'image'                => 'El campo :attribute debe ser una imagen.',
    'in'                   => 'El valor seleccionado en :attribute es inválido.',
    'in_array'             => 'El campo :attribute no existe en :other.',
    'integer'              => 'El campo :attribute debe ser un número entero.',
    'ip'                   => 'El campo :attribute debe ser una dirección IP válida.',
    ' ipv4'                => 'El campo :attribute debe ser una dirección IPv4 válida.',
    ' ipv6'                => 'El campo :attribute debe ser una dirección IPv6 válida.',
    'json'                 => 'El campo :attribute debe ser una cadena JSON válida.',
    'lt'                   => [
        'numeric' => 'El campo :attribute debe ser menor que :value.',
        'file'    => 'El archivo :attribute debe ser menor que :value kilobytes.',
        'string'  => 'El campo :attribute debe ser menor que :value caracteres.',
        'array'   => 'El campo :attribute debe tener menos de :value elementos.',
    ],
    'lte'                  => [
        'numeric' => 'El campo :attribute debe ser menor o igual que :value.',
        'file'    => 'El archivo :attribute debe ser menor o igual que :value kilobytes.',
        'string'  => 'El campo :attribute debe ser menor o igual que :value caracteres.',
        'array'   => 'El campo :attribute no debe tener más de :value elementos.',
    ],
    'max'                  => [
        'numeric' => 'El campo :attribute no debe ser mayor que :max.',
        'file'    => 'El archivo :attribute no debe superar :max kilobytes.',
        'string'  => 'El campo :attribute no debe superar :max caracteres.',
        'array'   => 'El campo :attribute no debe tener más de :max elementos.',
    ],
    'mimes'                => 'El campo :attribute debe ser un archivo de tipo: :values.',
    'mimetypes'            => 'El campo :attribute debe ser un archivo de tipo: :values.',
    'min'                  => [
        'numeric' => 'El campo :attribute debe ser al menos :min.',
        'file'    => 'El archivo :attribute debe pesar al menos :min kilobytes.',
        'string'  => 'El campo :attribute debe contener al menos :min caracteres.',
        'array'   => 'El campo :attribute debe tener al menos :min elementos.',
    ],
    'multiple_of'          => 'El campo :attribute debe ser múltiplo de :value.',
    'not_in'               => 'El valor seleccionado en :attribute es inválido.',
    'not_regex'            => 'El formato del campo :attribute es inválido.',
    'numeric'              => 'El campo :attribute debe ser un número.',
    'present'              => 'El campo :attribute debe estar presente.',
    'regex'                => 'El formato del campo :attribute es inválido.',
    'required'             => 'El campo :attribute es obligatorio.',
    'required_if'          => 'El campo :attribute es obligatorio cuando :other es :value.',
    'required_unless'      => 'El campo :attribute es obligatorio a menos que :other esté en :values.',
    'required_with'        => 'El campo :attribute es obligatorio cuando :values está presente.',
    'required_with_all'    => 'El campo :attribute es obligatorio cuando :values están presentes.',
    'required_without'     => 'El campo :attribute es obligatorio cuando :values no está presente.',
    'required_without_all' => 'El campo :attribute es obligatorio cuando ninguno de :values están presentes.',
    'same'                 => 'Los campos :attribute y :other deben coincidir.',
    'size'                 => [
        'numeric' => 'El campo :attribute debe ser :size.',
        'file'    => 'El archivo :attribute debe pesar :size kilobytes.',
        'string'  => 'El campo :attribute debe contener :size caracteres.',
        'array'   => 'El campo :attribute debe contener :size elementos.',
    ],
    'starts_with'          => 'El campo :attribute debe comenzar con uno de los siguientes valores: :values',
    'string'               => 'El campo :attribute debe ser una cadena de texto.',
    'timezone'             => 'El campo :attribute debe ser una zona horaria válida.',
    'unique'               => 'El campo :attribute ya ha sido registrado.',
    'uploaded'             => 'No se pudo cargar el archivo :attribute.',
    'url'                  => 'El formato del campo :attribute es inválido.',
    'uuid'                 => 'El campo :attribute debe ser un UUID válido.',

    /*
    |--------------------------------------------------------------------------
    | Mensajes de validación personalizados
    |--------------------------------------------------------------------------
    |
    | Aquí puedes especificar mensajes personalizados para atributos usando la
    | convención "attribute.rule" para nombrar las líneas. Esto nos permite
    | rápidamente especificar un mensaje específico para una regla dada.
    |
    */

    'custom' => [
        'apto_traslado' => [
            'required' => 'Debe indicar si el animal es apto para traslado inmediato.',
            'in' => 'La opción seleccionada en :attribute no es válida.',
        ],
        'veterinario_id' => [
            'required' => 'Seleccione un veterinario.',
            'exists' => 'El veterinario seleccionado no existe.',
        ],
        'animal_file_id' => [
            'required' => 'Seleccione la hoja de animal.',
            'exists' => 'La hoja de animal seleccionada no existe.',
        ],
        'imagen' => [
            'image' => 'La evidencia debe ser una imagen.',
            'mimes' => 'La evidencia debe ser de tipo: :values.',
            'max'   => 'La evidencia no debe superar :max kilobytes.',
        ],
        'persona_id' => [
            'exists' => 'El reportante seleccionado no existe.',
        ],
        'tipo_incidente_id' => [
            'exists' => 'El tipo de incidente seleccionado no existe.',
        ],
        'aprobado' => [
            'in' => 'El valor de aprobado no es válido.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Atributos de validación personalizados
    |--------------------------------------------------------------------------
    |
    | Las siguientes líneas se usan para intercambiar nuestro marcador de
    | posición de atributo por algo más amigable para el lector como "Dirección"
    | en lugar de "direccion". Esto simplemente nos ayuda a hacer el mensaje
    | más expresivo.
    |
    */

    'attributes' => [
        'animal_file_id'      => 'hoja de animal',
        'tratamiento_id'      => 'tipo de tratamiento',
        'tratamiento_texto'   => 'detalles adicionales',
        'veterinario_id'      => 'veterinario',
        'estado_id'           => 'nuevo estado del animal',
        'descripcion'         => 'descripción',
        'diagnostico'         => 'diagnóstico',
        'peso'                => 'peso',
        'temperatura'         => 'temperatura',
        'recomendacion'       => 'recomendación',
        'apto_traslado'       => 'aptitud para traslado inmediato',
        'fecha'               => 'fecha',
        'observaciones'       => 'observaciones',
        'imagen'              => 'imagen',
        'imagen_url'          => 'imagen',
        'persona_id'          => 'reportante',
        'tipo_incidente_id'   => 'tipo de incidente',
        'aprobado'            => 'aprobado',
        'tamano'              => 'tamaño del animal',
        'puede_moverse'       => '¿puede moverse?',
        'direccion'           => 'dirección',
        'latitud'             => 'latitud',
        'longitud'            => 'longitud',
        'traslado_inmediato'  => 'traslado inmediato',
        'centro_id'           => 'centro de destino',
    ],

];


