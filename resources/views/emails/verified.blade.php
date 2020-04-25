@component('emails.base')

# Hello, {{ $user->user_type == 'student' ? $user->userable->full_name : $user->userable->name }}!

You have successfully verified your account.

We look forward to your contributions on our platform.

@endcomponent
