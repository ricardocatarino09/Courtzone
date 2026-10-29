<?php

session_start();

require_once '../config/database.php';

$pageTitle = "Iniciar Sessão - CourtZone";

$erro = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? "");
    $password = $_POST['password'] ?? "";

    if ($email === "" || $password === "") {
        $erro = "Preenche todos os campos.";
    } else {

        $stmt = $pdo->prepare("
            SELECT
                id,
                nome,
                email,
                password
            FROM utilizadores
            WHERE email = ?
        ");

        $stmt->execute([$email]);

        $utilizador = $stmt->fetch();

        if ($utilizador && password_verify($password, $utilizador['password'])) {

            $_SESSION['utilizador_id'] = $utilizador['id'];

            header("Location: ../pages/perfil.php");
            exit;

        } else {

            $erro = "Email ou palavra-passe incorretos.";

        }
    }
}

?>

<!DOCTYPE html>
<html lang="pt-PT">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= $pageTitle ?></title>

    <link rel="stylesheet" href="../css/style.css">

</head>

<body class="auth-page">

<header>

    <div class="logo">
        🏀 CourtZone
    </div>

    <nav>
        <a href="../index.php">Início</a>
        <a href="login.php">Iniciar Sessão</a>
        <a href="registo.php">Registar</a>
    </nav>

</header>


<main class="auth-main">

    <section class="login-card">

        <div class="login-icon">
            🏀
        </div>

        <div class="login-header">

            <span class="login-label">
                COURTZONE
            </span>

            <h1>
                Iniciar Sessão
            </h1>

            <p>
                Entra na tua conta para acompanhares
                os teus favoritos.
            </p>

        </div>


        <?php if ($erro): ?>

            <div class="login-error">
                ⚠️
                <?= htmlspecialchars($erro) ?>
            </div>

        <?php endif; ?>


        <form method="POST" class="login-form">

            <div class="form-group">

                <label for="email">
                    Email
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="O teu email"
                    value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                    autocomplete="email"
                    required
                >

            </div>


            <div class="form-group">

                <label for="password">
                    Palavra-passe
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="A tua palavra-passe"
                    autocomplete="current-password"
                    required
                >

            </div>


            <button
                type="submit"
                class="login-button"
            >
                Iniciar Sessão
            </button>

        </form>


        <div class="login-register">

            <span>
                Ainda não tens conta?
            </span>

            <a href="registo.php">
                Criar conta
            </a>

        </div>


        <a
            href="../index.php"
            class="login-back"
        >
            ← Voltar ao CourtZone
        </a>

    </section>

</main>


<footer>

    <p>
        &copy; <?= date('Y') ?> CourtZone
    </p>

</footer>

</body>

</html>