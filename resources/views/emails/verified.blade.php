@extends('emails.base')

@section('content')
<h2>
    Hello, {{ $user->user_type == 'student' ? $user->userable->full_name : $user->userable->name }}!
</h2>

<br>

<p>You have successfully verified your account.</p>
@endsection
