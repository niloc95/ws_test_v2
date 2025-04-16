<?php
session_start();
define('ENV_PATH', __DIR__ . '/.env');
define('ENV_EXAMPLE_PATH', __DIR__ . '/.env.example');

function env_exists() {
    return file_exists(ENV_PATH) && strpos(file_get_contents(ENV_PATH), 'DB_DATABASE=') !== false;
}

// Detect the app subfolder from the current script path
$script_name = $_SERVER['SCRIPT_NAME'] ?? '';
$parts = explode('/', trim($script_name, '/'));
$subfolder = count($parts) > 1 ? $parts[0] : 'build'; // fallback to 'build' if not found

// And also update the earlier check:
if (env_exists()) {
    header('Location: /' . $subfolder . '/');
    exit;
}

$error = '';
$test_success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $base_url = rtrim($_POST['base_url'] ?? '', '/');
    $db_host = $_POST['db_host'] ?? '';
    $db_user = $_POST['db_user'] ?? '';
    $db_pass = $_POST['db_pass'] ?? '';
    $db_name = $_POST['db_name'] ?? '';
    $db_driver = $_POST['db_driver'] ?? 'mysqli';

    if (isset($_POST['test_connect'])) {
        // Only test connection, do not write .env
        try {
            if ($db_driver === 'mysqli') {
                $mysqli = @new mysqli($db_host, $db_user, $db_pass, $db_name);
                if ($mysqli->connect_error) throw new Exception($mysqli->connect_error);
                $mysqli->close();
            }
            $test_success = true;
        } catch (Exception $e) {
            $error = 'Database connection failed: ' . htmlspecialchars($e->getMessage());
        }
    } else {
        // Normal install flow
        try {
            if ($db_driver === 'mysqli') {
                $mysqli = @new mysqli($db_host, $db_user, $db_pass, $db_name);
                if ($mysqli->connect_error) throw new Exception($mysqli->connect_error);
                $mysqli->close();
            }
            $env = file_exists(ENV_EXAMPLE_PATH) ? file_get_contents(ENV_EXAMPLE_PATH) : '';
            $env .= "\nAPP_ENV=production";
            $env .= "\nBASE_URL={$base_url}";
            $env .= "\nDB_HOSTNAME={$db_host}";
            $env .= "\nDB_USERNAME={$db_user}";
            $env .= "\nDB_PASSWORD={$db_pass}";
            $env .= "\nDB_DATABASE={$db_name}";
            $env .= "\nDB_DRIVER={$db_driver}";
            $env .= "\nENCRYPTION_KEY=" . base64_encode(random_bytes(32));
            file_put_contents(ENV_PATH, $env);
            // When redirecting after setup:
            header('Location: /' . $subfolder . '/');
            exit;
        } catch (Exception $e) {
            $error = 'Database connection failed: ' . htmlspecialchars($e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>webSchedulr Setup</title>
    <link rel="stylesheet" href="build/assets/vendor/bootstrap/bootstrap.min.css">
    <style>body{background:#f8f9fa}.container{max-width:500px;margin-top:60px}</style>
</head>
<body>
<div class="container">
    <h2 class="mb-4">Welcome to webSchedulr</h2>
    <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
    <?php if ($test_success): ?><div class="alert alert-success">Database connection successful!</div><?php endif; ?>
    <form method="post" class="card card-body">
        <div class="mb-3">
            <label>Base URL</label>
            <input
                type="url"
                name="base_url"
                class="form-control"
                required
                value="<?= htmlspecialchars((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']), ENT_QUOTES) ?>"
                readonly
            >
        </div>
        <div class="mb-3">
            <label>DB Host</label>
            <input type="text" name="db_host" class="form-control" required value="<?= htmlspecialchars($_POST['db_host'] ?? 'localhost') ?>">
        </div>
        <div class="mb-3">
            <label>DB Name</label>
            <input type="text" name="db_name" class="form-control" required value="<?= htmlspecialchars($_POST['db_name'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label>DB User</label>
            <input type="text" name="db_user" class="form-control" required value="<?= htmlspecialchars($_POST['db_user'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label>DB Password</label>
            <input type="password" name="db_pass" class="form-control" value="<?= htmlspecialchars($_POST['db_pass'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label>DB Driver</label>
            <select name="db_driver" class="form-select">
                <option value="mysqli" <?= (($_POST['db_driver'] ?? '') === 'mysqli' ? 'selected' : '') ?>>MySQLi</option>
            </select>
        </div>
        <div class="d-flex gap-2">
            <button type="submit" name="test_connect" class="btn btn-secondary w-50">Test Connect</button>
            <button type="submit" class="btn btn-primary w-50">Save & Start</button>
        </div>
    </form>
</div>
</body>
</html>