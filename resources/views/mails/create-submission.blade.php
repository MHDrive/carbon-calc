@component('mail::message')
{{-- # {{ $details['subject'] }} --}}

{{ $details['body'] }}

@component('mail::button', ['url' => 'https://your-app.com/submissions'])
View Your Submission
@endcomponent

Thank you,<br>
{{ config('app.name') }}
@endcomponent
