<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Email Verification · Cloud Adoption Summit</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }
  body { min-height: 100vh; background: #F7F8FC; display: flex; align-items: center; justify-content: center; padding: 16px; }
  .card { width: 100%; max-width: 420px; background: #fff; border-radius: 20px; overflow: hidden; box-shadow: 0 8px 30px rgba(52,66,93,.12); }
  .hero { background: #6D2DE6; padding: 34px 24px; text-align: center; }
  .hero h1 { color: #fff; font-size: 22px; font-weight: 800; }
  .hero p { color: rgba(255,255,255,.85); font-size: 13px; margin-top: 6px; }
  .body { padding: 30px 24px; text-align: center; }
  .icon { width: 64px; height: 64px; margin: 0 auto 16px; border-radius: 50%; font-size: 30px; line-height: 64px; }
  .icon.ok { background: #E6F7F1; color: #00A878; }
  .icon.fail { background: #FDECEE; color: #E22F42; }
  h2 { font-size: 18px; color: #1D2639; font-weight: 800; }
  .msg { font-size: 14px; color: #64748B; margin-top: 10px; line-height: 1.6; }
  .note { margin-top: 18px; background: #F0E9FF; border-radius: 12px; padding: 14px; font-size: 13px; color: #6D2DE6; font-weight: 700; line-height: 1.5; }
  .footer { padding: 0 24px 24px; text-align: center; font-size: 12px; color: #97A1B2; }
</style>
</head>
<body>
  <div class="card">
    <div class="hero">
      <h1>Cloud Adoption Summit</h1>
      <p>Email Verification</p>
    </div>
    <div class="body">
      @if ($success)
        <div class="icon ok">✓</div>
        <h2>{{ $title }}</h2>
        <p class="msg">{{ $message }}</p>
        @if ($showApprovalNote)
          <div class="note">Next step: our administrator will review your registration. Once approved, you can log in to the Cloud Adoption Summit app.</div>
        @else
          <div class="note">You can now log in to the Cloud Adoption Summit app.</div>
        @endif
      @else
        <div class="icon fail">✕</div>
        <h2>{{ $title }}</h2>
        <p class="msg">{{ $message }}</p>
        <div class="note" style="background:#FDECEE;color:#E22F42;">Please open the app and register again, or contact support if the problem continues.</div>
      @endif
    </div>
    <div class="footer">&copy; {{ date('Y') }} {{ config('app.name', 'Cloud Adoption Conference') }}</div>
  </div>
</body>
</html>
