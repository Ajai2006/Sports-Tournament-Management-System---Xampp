<?php
require_once '../includes/config.php';

if (isAdmin()) redirect('dashboard.php');

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($conn, $_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $user = $conn->query("SELECT * FROM admin_users WHERE username='$username' LIMIT 1")->fetch_assoc();

    $login_ok = false;

    // Always allow admin/admin - fix DB if needed
    if ($username === 'admin' && $password === 'admin') {
        if (!$user) {
            $conn->query("INSERT INTO admin_users (username, password, full_name, email) VALUES ('admin','admin','Administrator','admin@sports.com')");
            $user = $conn->query("SELECT * FROM admin_users WHERE username='admin' LIMIT 1")->fetch_assoc();
        } else {
            $conn->query("UPDATE admin_users SET password='admin' WHERE username='admin'");
            $user['password'] = 'admin';
        }
        $login_ok = true;
    }
    // Plain text match
    elseif ($user && $password === $user['password']) {
        $login_ok = true;
    }
    // Bcrypt match (legacy) - auto-migrate to plain text
    elseif ($user && password_verify($password, $user['password'])) {
        $conn->query("UPDATE admin_users SET password='".mysqli_real_escape_string($conn,$password)."' WHERE username='$username'");
        $login_ok = true;
    }

    if ($login_ok && $user) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $user['id'] ?? 1;
        $_SESSION['admin_name'] = $user['full_name'] ?? 'Admin';
        redirect('dashboard.php');
    } else {
        $error = 'Invalid username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login — TourneyPro</title>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        :root{--black:#0a0a0f;--dark:#12121a;--card:#1a1a26;--border:#2a2a3d;--accent:#f5c518;--red:#e84545;--text:#e8e8f0;--muted:#7878a0;}
        body{font-family:'DM Sans',sans-serif;background:var(--black);color:var(--text);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:1rem;}
        .login-box{background:var(--card);border:1px solid var(--border);border-radius:20px;padding:2.5rem;width:100%;max-width:420px;}
        .logo{font-family:'Bebas Neue',sans-serif;font-size:2rem;color:var(--accent);letter-spacing:3px;text-align:center;margin-bottom:8px;}
        h2{text-align:center;font-size:1.1rem;color:var(--muted);font-weight:400;margin-bottom:2rem;}
        .form-group{margin-bottom:1.2rem;}
        label{display:block;font-size:0.82rem;font-weight:500;color:var(--muted);margin-bottom:6px;}
        input{width:100%;background:var(--dark);border:1px solid var(--border);border-radius:8px;padding:11px 14px;color:var(--text);font-size:0.9rem;font-family:inherit;outline:none;transition:border-color 0.2s;}
        input:focus{border-color:var(--accent);}
        .btn{width:100%;background:var(--accent);color:var(--black);border:none;border-radius:8px;padding:12px;font-size:1rem;font-weight:700;cursor:pointer;font-family:inherit;transition:background 0.2s;}
        .btn:hover{background:#e0b015;}
        .error{background:rgba(232,69,69,0.1);border:1px solid rgba(232,69,69,0.3);color:var(--red);padding:10px 14px;border-radius:8px;font-size:0.88rem;margin-bottom:1rem;}
        .hint{text-align:center;margin-top:1.5rem;font-size:0.8rem;color:var(--muted);}
        .back{display:block;text-align:center;margin-top:1rem;color:var(--muted);text-decoration:none;font-size:0.85rem;}
        .back:hover{color:var(--text);}
    </style>
</head>
<body>
<div class="login-box">
    <div class="logo">🏆 TourneyPro</div>
    <h2>Admin Panel Login</h2>
    <?php if($error): ?><div class="error">⚠️ <?= $error ?></div><?php endif; ?>
    <form method="POST">
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" placeholder="admin" required autofocus value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="••••••••" required>
        </div>
        <button type="submit" class="btn">Sign In →</button>
    </form>
    <div class="hint">Login: <strong>admin</strong> / <strong>admin</strong></div>
    <a href="../index.php" class="back">← Back to Public Site</a>
</div>
</body>
</html>
