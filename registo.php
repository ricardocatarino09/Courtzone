<?php

session_start();

require_once '../config/database.php';

$pageTitle = "Criar Conta - CourtZone";

$erro = "";
$sucesso = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome = trim($_POST['nome'] ?? "");
    $email = trim($_POST['email'] ?? "");
    $password = $_POST['password'] ?? "";
    $confirmarPassword = $_POST['confirmar_password'] ?? "";

    if (
        $nome === "" ||
        $email === "" ||
        $password === "" ||
        $confirmarPassword === ""
    ) {
        $erro = "Preenche todos os campos.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = "Introduz um email válido.";

    } elseif ($password !== $confirmarPassword) {
        $erro = "As palavras-passe não coincidem.";

    } elseif (strlen($password) < 6) {
        $erro = "A palavra-passe deve ter pelo menos 6 caracteres.";

    } else {

        $stmt = $pdo->prepare("
            SELECT id
            FROM utilizadores
            WHERE email = ?
        ");

        $stmt->execute([$email]);

        if ($stmt->fetch()) {

            $erro = "Já existe uma conta com esse email.";

        } else {

            $passwordHash = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $stmt = $pdo->prepare("
                INSERT INTO utilizadores
                (nome, email, password)
                VALUES (?, ?, ?)
            ");

            $stmt->execute([
                $nome,
                $email,
                $passwordHash
            ]);

            $sucesso = "Conta criada com sucesso!";
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
                Criar Conta
            </h1>

            <p>
                Cria a tua conta para guardares
                as tuas equipas e jogadores favoritos.
            </p>

        </div>


        <?php if ($erro): ?>

            <div class="login-error">
                ⚠️
                <?= htmlspecialchars($erro) ?>
            </div>

        <?php endif; ?>


        <?php if ($sucesso): ?>

            <div class="login-success">
                ✓
                <?= htmlspecialchars($sucesso) ?>
            </div>


            <div class="registo-sucesso">

                <p>
                    A tua conta já está pronta.
                </p>

                <a
                    href="login.php"
                    class="login-button"
                >
                    Iniciar Sessão
                </a>

            </div>


        <?php else: ?>


            <form method="POST" class="login-form">

                <div class="form-group">

                    <label for="nome">
                        Nome
                    </label>

                    <input
                        type="text"
                        id="nome"
                        name="nome"
                        placeholder="O teu nome"
                        value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>"
                        autocomplete="name"
                        required
                    >

                </div>


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
                        placeholder="Mínimo de 6 caracteres"
                        autocomplete="new-password"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="confirmar_password">
                        Confirmar palavra-passe
                    </label>

                    <input
                        type="password"
                        id="confirmar_password"
                        name="confirmar_password"
                        placeholder="Repete a palavra-passe"
                        autocomplete="new-password"
                        required
                    >

                </div>


                <button
                    type="submit"
                    class="login-button"
                >
                    Criar Conta
                </button>

            </form>


            <div class="login-register">

                <span>
                    Já tens conta?
                </span>

                <a href="login.php">
                    Iniciar Sessão
                </a>

            </div>


        <?php endif; ?>


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