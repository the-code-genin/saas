@extends('emails.base')

@section('content')
<h2>
    Hello, {{ $user->user_type == 'student' ? $user->userable->full_name : $user->userable->name }}!
</h2>

<br>

<p>
    Welcome to the Students as a Service platfrom! We are so glad that you have decided to joined our community.
</p>

<p>
    Please follow the link below to verify your account.
</p>

<br>

<p>
    <a href="{{ route('user.unauthorized', ['token' => $token->token]) }}">
        Verify your account.
    </a>
</p>
@endsection
