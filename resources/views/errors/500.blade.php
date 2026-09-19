@include('errors._page', [
    'code' => 500,
    'title' => 'Something went wrong on our side',
    'lede' => 'We could not complete that request. Our team has been notified — please try again in a moment.',
    'retry' => true,
])
