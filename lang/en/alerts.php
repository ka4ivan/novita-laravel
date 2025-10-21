<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Language resources of messages
    |--------------------------------------------------------------------------
    |
    */
    'message_data_invalid' => 'The given data was invalid.',

    'store' => [
        'success' => 'Data saved successfully!',
    ],
    'update' => [
        'success' => 'Data updated successfully!',
    ],
    'destroy' => [
        'success' => 'Data deleted successfully!',
        'error' => 'Delete error. You may not have permission or access!',
        'error_children' => 'Delete error! To remove the current element, you need to remove all of its children.',
    ],
    'operation' => [
        'success' => 'Operation completed successfully.',
        'error' => 'Operation error.',
        'success-queue' => 'The operation was successfully queued.',
        'performed' => 'Operation performed.',
        'counts' => [
            'error' => 'Unsuccessful operations :count.',
            'success' => 'Successful operations :count.',
            'all' => 'Total operations :count.',
        ]
    ],

    'file' => [
        'store' => [
            'success' => 'File uploaded successfully!',
            'error' => 'File upload error. You may have exceeded your limit.',
        ],
        'destroy' => [
            'success' => 'File deleted successfully!',
            'error' => 'File deletion error. The specified file may not exist.'
        ],
    ],

    'excellent' => 'Excellent!',
    'failure' => 'Error!',
    'warning' => 'Warning!',
    'information' => 'Information!',

    'cart' => [
        'invalid' => 'Shopping cart is not possible!',
        'empty' => 'Cart is empty!',
        'min_qty' => 'There are not enough items in the basket :value',
        'min_sum' => 'There are not enough items in the shopping cart :value',
        'multiplicity' => 'There are not enough items in the shopping cart',
        'success' => 'The order has been successfully placed!',
    ],
];
