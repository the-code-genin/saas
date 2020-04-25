@component('emails.base')

# Hello, {{ $user->user_type == 'student' ? $user->userable->full_name : $user->userable->name }}!

Welcome to the Students as a Service platfrom!

We are so glad that you have decided to join our community.

There is just one extra thing to do.

Please follow the link below to verify your account.

@component('mail::button', ['url' => route('user.verify', ['token' => $token->token]), 'color' => 'primary' ])
    Verify your account.
@endcomponent

@endcomponent
