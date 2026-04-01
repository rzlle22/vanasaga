<?php
session_start();

// --- PENGATURAN USERNAME & PASSWORD PATEN ---
$admin_user = "vanaadmin22"; 
$admin_pass = "adminvana22_6524";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_input = $_POST['username'];
    $pass_input = $_POST['password'];

    if ($user_input === $admin_user && $pass_input === $admin_pass) {
        $_SESSION['admin_logged_in'] = true;
        header("Location: dashboard.php"); 
        exit;
    } else {
        $error = "Username atau Password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Vanasaga ID</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            margin: 0; padding: 0; background: #030303; 
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(168, 85, 247, 0.2) 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(126, 34, 206, 0.15) 0%, transparent 40%),
                radial-gradient(circle at 50% 50%, rgba(76, 29, 149, 0.2) 0%, transparent 50%);
            background-size: cover;
            color: #ffffff;
            font-family: 'Plus Jakarta Sans', sans-serif;
            height: 100vh;
            overflow: hidden;
        }

        .login-container {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .login-card {
            width: 100%;
            max-width: 400px;
            background: rgba(15, 15, 15, 0.5); 
            border: 1px solid rgba(168, 85, 247, 0.4); 
            backdrop-filter: blur(20px); 
            border-radius: 28px; 
            padding: 40px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
            animation: slideUp 0.8s ease;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .brand-title {
            font-weight: 800;
            font-size: 1.8rem;
            letter-spacing: 1px;
            margin-bottom: 5px;
            text-align: center;
        }

        .brand-title span { color: #a855f7; text-shadow: 0 0 15px rgba(168, 85, 247, 0.5); }

        .subtitle {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.85rem;
            text-align: center;
            margin-bottom: 35px;
        }

        .form-group { margin-bottom: 20px; }

        label {
            display: block;
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            color: #a855f7;
            margin-bottom: 8px;
            margin-left: 5px;
        }

        .form-control {
            width: 100%;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(168, 85, 247, 0.2);
            border-radius: 15px;
            padding: 14px 20px;
            color: #fff;
            font-family: inherit;
            font-size: 0.95rem;
            outline: none;
            transition: 0.3s;
            box-sizing: border-box;
        }

        .form-control:focus {
            border-color: #a855f7;
            background: rgba(168, 85, 247, 0.1);
            box-shadow: 0 0 15px rgba(168, 85, 247, 0.2);
        }

        .btn-primary {
            width: 100%;
            background: linear-gradient(135deg, #a855f7 0%, #7e22ce 100%);
            border: none;
            border-radius: 15px;
            padding: 15px;
            color: #fff;
            font-weight: 800;
            font-size: 0.9rem;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 10px 20px rgba(126, 34, 206, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 25px rgba(126, 34, 206, 0.4);
            filter: brightness(1.1);
        }

        .error-box {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            color: #ff4444;
            padding: 12px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
            text-align: center;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="brand-title">VANASAGA <span>PANEL</span></div>
            <p class="subtitle">Login khusus pengelola Vanasaga ID</p>

            <?php if($error): ?>
                <div class="error-box">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" placeholder="Admin username..." required autocomplete="off">
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-sign-in-alt"></i> LOGIN SYSTEM
                </button>
            </form>
        </div>
    </div>
</body>
</html>