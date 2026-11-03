<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reset Password · Cloud Adoption Summit</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }
  body { min-height: 100vh; background: #F7F8FC; display: flex; align-items: center; justify-content: center; padding: 16px; }
  .card { width: 100%; max-width: 420px; background: #fff; border-radius: 20px; overflow: hidden; box-shadow: 0 8px 30px rgba(52,66,93,.12); }
  .hero { background: #6D2DE6; padding: 34px 24px; text-align: center; }
  .hero h1 { color: #fff; font-size: 22px; font-weight: 800; }
  .hero p { color: rgba(255,255,255,.85); font-size: 13px; margin-top: 6px; }
  .body { padding: 24px; }
  label { display: block; font-size: 10px; font-weight: 800; letter-spacing: .6px; text-transform: uppercase; color: #6B7280; margin: 14px 0 6px; }
  .email-box { background: #FBFBFD; border: 1px solid #E3E7EF; border-radius: 9px; padding: 12px; font-size: 13px; color: #64748B; }
  input[type=password] { width: 100%; height: 44px; border: 1px solid #E3E7EF; border-radius: 9px; padding: 0 12px; font-size: 14px; color: #21243D; background: #FBFBFD; outline: none; }
  input[type=password]:focus { border-color: #7C3AED; }
  .hint { font-size: 11px; color: #97A1B2; margin-top: 5px; }
  button { width: 100%; height: 48px; margin-top: 20px; border: 0; border-radius: 10px; background: #6D2DE6; color: #fff; font-size: 15px; font-weight: 800; cursor: pointer; }
  button:disabled { opacity: .6; cursor: default; }
  .msg { margin-top: 14px; font-size: 13px; text-align: center; display: none; }
  .msg.error { display: block; color: #D92D3A; }
  .msg.success { display: block; color: #00A878; font-weight: 700; }
  .success-panel { display: none; text-align: center; padding: 10px 0 4px; }
  .success-panel .tick { width: 56px; height: 56px; margin: 0 auto 14px; border-radius: 50%; background: #E6F7F1; color: #00A878; font-size: 28px; line-height: 56px; }
  .success-panel h2 { font-size: 17px; color: #1D2639; font-weight: 800; }
  .success-panel p { font-size: 13px; color: #64748B; margin-top: 8px; line-height: 1.5; }
</style>
</head>
<body>
  <div class="card">
    <div class="hero">
      <h1>Reset Your Password</h1>
      <p>Cloud Adoption Summit</p>
    </div>
    <div class="body">
      <form id="resetForm">
        <label>Account Email</label>
        <div class="email-box" id="emailBox">{{ $email }}</div>

        <label for="password">New Password</label>
        <input type="password" id="password" placeholder="Enter new password" autocomplete="new-password" required>
        <div class="hint">Minimum 8 characters</div>

        <label for="passwordConfirm">Confirm New Password</label>
        <input type="password" id="passwordConfirm" placeholder="Re-enter new password" autocomplete="new-password" required>

        <button type="submit" id="submitBtn">Reset Password</button>
        <div class="msg" id="msg"></div>
      </form>

      <div class="success-panel" id="successPanel">
        <div class="tick">✓</div>
        <h2>Password Reset Successfully</h2>
        <p>Your password has been changed.<br>Open the <strong>Cloud Adoption Summit</strong> app and log in with your new password.</p>
      </div>
    </div>
  </div>

<script>
  var TOKEN = @json($token);
  var EMAIL = @json($email);

  document.getElementById('resetForm').addEventListener('submit', async function (event) {
    event.preventDefault();
    var msg = document.getElementById('msg');
    var btn = document.getElementById('submitBtn');
    var password = document.getElementById('password').value;
    var passwordConfirm = document.getElementById('passwordConfirm').value;

    msg.className = 'msg';
    if (!EMAIL) { msg.textContent = 'This reset link is invalid — the email is missing. Please request a new link from the app.'; msg.className = 'msg error'; return; }
    if (password.length < 8) { msg.textContent = 'Password must be at least 8 characters.'; msg.className = 'msg error'; return; }
    if (password !== passwordConfirm) { msg.textContent = 'Passwords do not match.'; msg.className = 'msg error'; return; }

    btn.disabled = true;
    btn.textContent = 'Resetting...';
    try {
      var response = await fetch('/api/reset-password', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ token: TOKEN, email: EMAIL, password: password, password_confirmation: passwordConfirm })
      });
      var data = await response.json().catch(function () { return {}; });
      if (!response.ok || data.success === false) {
        var firstError = data.errors ? Object.values(data.errors)[0][0] : null;
        throw new Error(data.message || firstError || 'Reset failed. The link may have expired — request a new one from the app.');
      }
      document.getElementById('resetForm').style.display = 'none';
      document.getElementById('successPanel').style.display = 'block';
    } catch (e) {
      msg.textContent = e.message;
      msg.className = 'msg error';
      btn.disabled = false;
      btn.textContent = 'Reset Password';
    }
  });
</script>
</body>
</html>
