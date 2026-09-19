@include('errors._page', [
    'code' => 405,
    'title' => 'That action is not available here',
    'lede' => 'The page was opened in a way it does not support. Please go back and try again from the page you came from.',
    'retry' => false,
])
