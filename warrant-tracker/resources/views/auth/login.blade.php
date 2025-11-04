@extends('layouts.app')

@section('content')
  <h2>Login</h2>
  <form method="POST" action="{{ route('login.post') }}">
    @csrf
    <div class="form-row">
      <label for="email">Email</label>
      <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
      @error('email')<div class="muted">{{ $message }}</div>@enderror
    </div>

    <div class="form-row">
      <label for="password">Password</label>
      <input id="password" type="password" name="password" required>
      @error('password')<div class="muted">{{ $message }}</div>@enderror
    </div>

    <div class="form-row">
      <label><input type="checkbox" name="remember"> Remember me</label>
    </div>

    <div class="form-row">
      <button type="submit">Sign in</button>
    </div>
  </form>

@endsection
