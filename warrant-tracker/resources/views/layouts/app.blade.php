<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ config('app.name', 'Warrant') }}</title>
    <style>
      body{font-family:Inter, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;margin:0;padding:0;background:#f7fafc;color:#1a202c}
      .container{max-width:980px;margin:32px auto;padding:16px}
      .card{background:#fff;padding:20px;border-radius:8px;box-shadow:0 2px 6px rgba(0,0,0,.06)}
      .form-row{margin-bottom:12px}
      label{display:block;font-weight:600;margin-bottom:6px}
      input[type=text],input[type=password],input[type=email]{width:100%;padding:10px;border:1px solid #e2e8f0;border-radius:6px}
      button{background:#2563eb;color:#fff;padding:10px 14px;border-radius:6px;border:0;cursor:pointer}
      nav{display:flex;justify-content:space-between;align-items:center;padding:12px 0}
      a.btn{display:inline-block;padding:8px 10px;background:#edf2ff;border-radius:6px;color:#1e40af;text-decoration:none}
      table{width:100%;border-collapse:collapse}
      th,td{padding:8px;border-bottom:1px solid #eef2f7}
      .muted{color:#6b7280}
    </style>
  </head>
  <body>
    <div class="container">
      <nav>
        <div><strong>{{ config('app.name','Warrant') }}</strong></div>
        <div>
          @auth
              <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display:inline">
                @csrf
                <button class="btn">Logout</button>
              </form>
            @endauth
        </div>
      </nav>

      <div class="card">
        @yield('content')
      </div>
    </div>
    
    <!-- If a token was flashed by the server during redirect-after-login, persist it to localStorage -->
    @if(session('api_token'))
    <script>
      try {
        localStorage.setItem('api_token', {!! json_encode(session('api_token')) !!});
      } catch(e) { /* ignore storage errors */ }
    </script>
    @endif

    <script>
      // Remove token from localStorage when logging out via the logout form
      document.addEventListener('DOMContentLoaded', function(){
        var lf = document.getElementById('logout-form');
        if(lf){
          lf.addEventListener('submit', function(){ try{ localStorage.removeItem('api_token'); } catch(e){} });
        }
      });
    </script>

  </body>
</html>
