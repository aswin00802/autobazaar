@include('errors._page', [
    'code' => 429,
    'title' => 'Too many requests',
    'lede' => 'You have tried that a few too many times. Please wait a minute and try again.',
    'retry' => true,
])
