<?php

require_once 'config/database.php';

require_once 'config/logos.php';


/* =========================================================
   LOGOS G LEAGUE
   ========================================================= */

function obterLogoGLeagueHome($nomeEquipa)
{
    $logosGLeague = [

        'Swarm' => 'LogosGLeague/GreensBoroSwarm.png',
        'Charge' => 'LogosGLeague/ClevelandCharge.png',
        'Go-Go' => 'LogosGLeague/CapitalCityGoGo.png',
        'Gold' => 'LogosGLeague/GrandRapidsGold.png',
        'Squadron' => 'LogosGLeague/BirmingamSquadron.png',
        'Nets' => 'LogosGLeague/LongIslandNets.png',
        'Herd' => 'LogosGLeague/WisconsinHerd.png',
        'Skyhawks' => 'LogosGLeague/CollegeParkSkyHawks.png',
        'Blue Coats' => 'LogosGLeague/DelawareBlueCoats.png',
        'Celtics' => 'LogosGLeague/MaineCeltics.png',
        'Boom' => 'LogosGLeague/NoblesVilleBoom.png',
        'Knicks' => 'LogosGLeague/Westchesterknicks.png',
        'Cruise' => 'LogosGLeague/MotorCityCruise.png',
        'Bulls' => 'LogosGLeague/WindyCityBulls.png',
        '905' => 'LogosGLeague/Raptors905.png',
        'Magic' => 'LogosGLeague/OsceolaMagic.png',
        'Stars' => 'LogosGLeague/SaltLakeCityStars.png',
        'Skyforce' => 'LogosGLeague/SiouxFallsSkyForce.png',
        'Suns' => 'LogosGLeague/ValleySuns.png',
        'Legends' => 'LogosGLeague/TexasLegends.png',
        'Kings' => 'LogosGLeague/Stockton Kings.png',
        'Warriors' => 'LogosGLeague/SantaCruzWarriors.png',
        'Vipers' => 'LogosGLeague/RioGrandVipers.png',
        'Clippers' => 'LogosGLeague/SanDiegoClippers.png',
        'Hustle' => 'LogosGLeague/MemphisHustle.png',
        'Wolves' => 'LogosGLeague/IowaWolves.png',
        'Remix' => 'LogosGLeague/RipCityRemix.png',
        'Capitanes' => 'LogosGLeague/MexicoCityCapitanes.png',
        'Spurs' => 'LogosGLeague/AustinSpurs.png',
        'Lakers' => 'LogosGLeague/SouthBayLakers.png',
        'Blue' => 'LogosGLeague/OklahomaCityBlue.png',
        'Coachella Valley Lakers' => 'LogosGLeague/SouthBayLakers.png',
        'Laketown Squadron' => 'LogosGLeague/BirmingamSquadron.png'

    ];

    return $logosGLeague[$nomeEquipa] ?? null;
}


function obterLogoJogoHome($nomeEquipa, $liga)
{
    if ($liga === 'G LEAGUE') {
        return obterLogoGLeagueHome($nomeEquipa);
    }

    return obterLogoEquipa($nomeEquipa);
}


/* =========================================================
   DATA ATUAL
   ========================================================= */

$dataHoje = date('Y-m-d');


/* =========================================================
   JOGOS DE HOJE
   ========================================================= */

$stmt = $pdo->prepare("

    SELECT
        jogos.id,
        jogos.hora,
        jogos.pontos_casa,
        jogos.pontos_fora,
        jogos.estado,
        equipa_casa.nome AS equipa_casa,
        equipa_fora.nome AS equipa_fora,
        ligas.sigla AS liga

    FROM jogos

    INNER JOIN equipas AS equipa_casa
        ON jogos.equipa_casa_id = equipa_casa.id

    INNER JOIN equipas AS equipa_fora
        ON jogos.equipa_fora_id = equipa_fora.id

    INNER JOIN ligas
        ON equipa_casa.liga_id = ligas.id

    WHERE jogos.data = ?

    ORDER BY jogos.hora

");

$stmt->execute([$dataHoje]);

$jogosHoje = $stmt->fetchAll();


/* =========================================================
   PRÓXIMOS JOGOS
   ========================================================= */

$stmt = $pdo->prepare("

    SELECT
        jogos.id,
        jogos.data,
        jogos.hora,
        jogos.estado,
        equipa_casa.nome AS equipa_casa,
        equipa_fora.nome AS equipa_fora,
        ligas.sigla AS liga

    FROM jogos

    INNER JOIN equipas AS equipa_casa
        ON jogos.equipa_casa_id = equipa_casa.id

    INNER JOIN equipas AS equipa_fora
        ON jogos.equipa_fora_id = equipa_fora.id

    INNER JOIN ligas
        ON equipa_casa.liga_id = ligas.id

    WHERE jogos.data > ?

    ORDER BY jogos.data, jogos.hora

    LIMIT 3

");

$stmt->execute([$dataHoje]);

$proximosJogos = $stmt->fetchAll();


/* =========================================================
   NOTÍCIAS EM DESTAQUE
   ========================================================= */

$stmt = $pdo->query("

    SELECT
        id,
        titulo,
        categoria,
        data_publicacao,
        imagem,
        conteudo

    FROM noticias

    ORDER BY data_publicacao DESC

    LIMIT 3

");

$noticias = $stmt->fetchAll();

?>

<!DOCTYPE html>

<html lang="pt-PT">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>CourtZone</title>

    <link rel="stylesheet" href="css/style.css">

    <style>

        /* =====================================================
           LOGOS DOS JOGOS NA HOMEPAGE
           ===================================================== */

        .home-team {

            display: flex;
            align-items: center;
            gap: 10px;

        }

        .home-team-logo {

            width: 42px;
            height: 42px;

            object-fit: contain;

            flex-shrink: 0;

        }

        .home-team-info {

            display: flex;
            align-items: center;
            gap: 8px;

            min-width: 0;

        }

        .home-team-info strong {

            font-size: 15px;

        }


        /* =====================================================
           LOGOS DOS PRÓXIMOS JOGOS
           ===================================================== */

        .upcoming-teams {

            display: flex;
            align-items: center;

            gap: 10px;

        }

        .upcoming-team {

            display: flex;
            align-items: center;

            gap: 8px;

        }

        .upcoming-team-logo {

            width: 34px;
            height: 34px;

            object-fit: contain;

            flex-shrink: 0;

        }


        /* =====================================================
           CHATBOT
           ===================================================== */

        .chatbot-button {

            position: fixed;

            right: 24px;
            bottom: 24px;

            width: 58px;
            height: 58px;

            border: none;
            border-radius: 50%;

            background: #ff6b35;
            color: white;

            font-size: 26px;

            cursor: pointer;

            box-shadow:
                0 8px 25px rgba(0, 0, 0, 0.25);

            z-index: 9999;

            transition:
                transform 0.2s ease;

        }

        .chatbot-button:hover {

            transform: scale(1.08);

        }

        .chatbot-window {

            position: fixed;

            right: 24px;
            bottom: 94px;

            width: 360px;

            max-width: calc(100vw - 30px);

            height: 500px;

            max-height: calc(100vh - 120px);

            background: white;

            border-radius: 16px;

            box-shadow:
                0 15px 45px rgba(0, 0, 0, 0.25);

            display: none;

            flex-direction: column;

            overflow: hidden;

            z-index: 9998;

        }

        .chatbot-window.open {

            display: flex;

        }

        .chatbot-header {

            background: #101a33;
            color: white;

            padding: 16px;

            display: flex;

            align-items: center;
            justify-content: space-between;

        }

        .chatbot-header-info {

            display: flex;

            align-items: center;

            gap: 10px;

        }

        .chatbot-header-icon {

            width: 38px;
            height: 38px;

            display: flex;

            align-items: center;
            justify-content: center;

            background: #ff6b35;

            border-radius: 50%;

            font-size: 20px;

        }

        .chatbot-header-text strong {

            display: block;

            font-size: 15px;

        }

        .chatbot-header-text span {

            display: block;

            margin-top: 2px;

            font-size: 11px;

            opacity: 0.75;

        }

        .chatbot-close {

            border: none;

            background: transparent;

            color: white;

            font-size: 22px;

            cursor: pointer;

            padding: 4px;

        }

        .chatbot-messages {

            flex: 1;

            padding: 16px;

            overflow-y: auto;

            background: #f4f6f8;

            display: flex;

            flex-direction: column;

            gap: 10px;

        }

        .chatbot-message {

            max-width: 82%;

            padding: 10px 13px;

            border-radius: 12px;

            font-size: 14px;

            line-height: 1.4;

        }

        .chatbot-message.bot {

            align-self: flex-start;

            background: white;

            color: #172033;

            border: 1px solid #e1e5eb;

            border-bottom-left-radius: 4px;

        }

        .chatbot-message.user {

            align-self: flex-end;

            background: #ff6b35;

            color: white;

            border-bottom-right-radius: 4px;

        }

        .chatbot-input-area {

            padding: 12px;

            background: white;

            border-top: 1px solid #e1e5eb;

            display: flex;

            gap: 8px;

        }

        .chatbot-input {

            flex: 1;

            border: 1px solid #d7dce3;

            border-radius: 10px;

            padding: 10px 12px;

            font-family: inherit;

            font-size: 14px;

            outline: none;

        }

        .chatbot-input:focus {

            border-color: #ff6b35;

        }

        .chatbot-send {

            width: 42px;
            height: 42px;

            border: none;

            border-radius: 10px;

            background: #ff6b35;

            color: white;

            font-size: 18px;

            cursor: pointer;

        }

        .chatbot-send:hover {

            background: #e95d2d;

        }


        /* =====================================================
           RESPONSIVO
           ===================================================== */

        @media (max-width: 700px) {

            .home-team-logo {

                width: 36px;
                height: 36px;

            }

            .home-team-info strong {

                font-size: 13px;

            }

            .upcoming-team-logo {

                width: 28px;
                height: 28px;

            }

            .upcoming-teams {

                gap: 6px;

            }

            .upcoming-team {

                gap: 5px;

            }

            .chatbot-button {

                right: 16px;
                bottom: 16px;

                width: 54px;
                height: 54px;

            }

            .chatbot-window {

                right: 15px;
                bottom: 82px;

                width: calc(100vw - 30px);

                height: 470px;

            }

        }

    </style>

</head>


<body>


<header>

    <div class="logo">

        🏀 CourtZone

    </div>


    <nav>

        <a href="index.php" class="active">
            Início
        </a>

        <a href="pages/jogos.php">
            Jogos
        </a>

        <a href="pages/equipas.php">
            Equipas
        </a>

        <a href="pages/jogadores.php">
            Jogadores
        </a>

        <a href="pages/classificacoes.php">
            Classificações
        </a>

        <a href="pages/noticias.php">
            Notícias
        </a>

        <a href="pages/pesquisa.php">
            🔎
        </a>

        <a href="pages/perfil.php">
            Perfil
        </a>

    </nav>

</header>


<main>


    <!-- =========================================================
         HERO
         ========================================================= -->

    <section class="home-hero">

        <div class="home-hero-content">

            <span class="home-hero-label">

                NBA • WNBA • G LEAGUE

            </span>


            <h1>

                O basquetebol

                <br>

                num só lugar.

            </h1>


            <p>

                Acompanha jogos, resultados, equipas,
                jogadores, classificações e notícias.

            </p>


            <div class="home-hero-buttons">

                <a
                    href="pages/jogos.php"
                    class="hero-button primary"
                >

                    Ver Jogos →

                </a>


                <a
                    href="pages/noticias.php"
                    class="hero-button secondary"
                >

                    Ver Notícias

                </a>

            </div>

        </div>


        <div class="home-hero-ball">

            🏀

        </div>

    </section>


    <!-- =========================================================
         RESUMO
         ========================================================= -->

    <section class="home-overview">


        <div class="overview-card">

            <div class="overview-icon">

                🏀

            </div>


            <div>

                <span>
                    JOGOS HOJE
                </span>

                <strong>
                    <?= count($jogosHoje) ?>
                </strong>

            </div>

        </div>


        <div class="overview-card">

            <div class="overview-icon">

                📰

            </div>


            <div>

                <span>
                    NOTÍCIAS
                </span>

                <strong>
                    <?= count($noticias) ?>
                </strong>

            </div>

        </div>


        <div class="overview-card">

            <div class="overview-icon">

                📅

            </div>


            <div>

                <span>
                    PRÓXIMOS JOGOS
                </span>

                <strong>
                    <?= count($proximosJogos) ?>
                </strong>

            </div>

        </div>


    </section>


    <!-- =========================================================
         JOGOS DE HOJE
         ========================================================= -->

    <section class="home-section home-games-section">


        <div class="section-title">

            <div>

                <span class="home-section-label">

                    AO VIVO NO COURTZONE

                </span>


                <h2>

                    Jogos de Hoje

                </h2>

            </div>


            <a href="pages/jogos.php">

                Ver todos →

            </a>

        </div>


        <?php if (!empty($jogosHoje)): ?>


            <div class="home-games">


                <?php foreach ($jogosHoje as $jogo): ?>


                    <?php

                    $logoCasa = obterLogoJogoHome(
                        $jogo['equipa_casa'],
                        $jogo['liga']
                    );

                    $logoFora = obterLogoJogoHome(
                        $jogo['equipa_fora'],
                        $jogo['liga']
                    );

                    ?>


                    <a
                        href="pages/jogo.php?id=<?= $jogo['id'] ?>"
                        class="home-game-card"
                    >


                        <div class="home-team">


                            <?php if ($logoCasa): ?>

                                <img
                                    src="images/<?= htmlspecialchars($logoCasa) ?>"
                                    alt="<?= htmlspecialchars($jogo['equipa_casa']) ?>"
                                    class="home-team-logo"
                                >

                            <?php endif; ?>


                            <div class="home-team-info">

                                <strong>

                                    <?= htmlspecialchars(
                                        $jogo['equipa_casa']
                                    ) ?>

                                </strong>


                                <?php if ($jogo['pontos_casa'] !== null): ?>

                                    <span class="home-score">

                                        <?= htmlspecialchars(
                                            $jogo['pontos_casa']
                                        ) ?>

                                    </span>

                                <?php endif; ?>

                            </div>

                        </div>


                        <div class="home-game-status">


                            <?php if ($jogo['estado'] === 'FINAL'): ?>

                                <span class="status-final">

                                    FINAL

                                </span>


                            <?php elseif ($jogo['hora']): ?>

                                <span class="status-time">

                                    <?= date(
                                        'H:i',
                                        strtotime($jogo['hora'])
                                    ) ?>

                                </span>


                            <?php else: ?>

                                <span>

                                    <?= htmlspecialchars(
                                        $jogo['estado']
                                    ) ?>

                                </span>

                            <?php endif; ?>


                        </div>


                        <div class="home-team">


                            <?php if ($logoFora): ?>

                                <img
                                    src="images/<?= htmlspecialchars($logoFora) ?>"
                                    alt="<?= htmlspecialchars($jogo['equipa_fora']) ?>"
                                    class="home-team-logo"
                                >

                            <?php endif; ?>


                            <div class="home-team-info">

                                <strong>

                                    <?= htmlspecialchars(
                                        $jogo['equipa_fora']
                                    ) ?>

                                </strong>


                                <?php if ($jogo['pontos_fora'] !== null): ?>

                                    <span class="home-score">

                                        <?= htmlspecialchars(
                                            $jogo['pontos_fora']
                                        ) ?>

                                    </span>

                                <?php endif; ?>

                            </div>


                        </div>


                    </a>


                <?php endforeach; ?>


            </div>


        <?php else: ?>


            <div class="home-empty">

                <span>
                    🏀
                </span>

                <p>

                    Não existem jogos agendados para hoje.

                </p>

            </div>


        <?php endif; ?>


    </section>


    <!-- =========================================================
         PRÓXIMOS JOGOS
         ========================================================= -->

    <?php if (!empty($proximosJogos)): ?>


        <section class="home-section">


            <div class="section-title">

                <div>

                    <span class="home-section-label">

                        CALENDÁRIO

                    </span>


                    <h2>

                        Próximos Jogos

                    </h2>

                </div>


                <a href="pages/jogos.php">

                    Calendário completo →

                </a>

            </div>


            <div class="upcoming-games">


                <?php foreach ($proximosJogos as $jogo): ?>


                    <?php

                    $logoCasa = obterLogoJogoHome(
                        $jogo['equipa_casa'],
                        $jogo['liga']
                    );

                    $logoFora = obterLogoJogoHome(
                        $jogo['equipa_fora'],
                        $jogo['liga']
                    );

                    ?>


                    <a
                        href="pages/jogo.php?id=<?= $jogo['id'] ?>"
                        class="upcoming-game"
                    >


                        <div class="upcoming-date">

                            <span>

                                <?= date(
                                    'd',
                                    strtotime($jogo['data'])
                                ) ?>

                            </span>


                            <small>

                                <?= date(
                                    'M',
                                    strtotime($jogo['data'])
                                ) ?>

                            </small>

                        </div>


                        <div class="upcoming-teams">


                            <div class="upcoming-team">


                                <?php if ($logoCasa): ?>

                                    <img
                                        src="images/<?= htmlspecialchars($logoCasa) ?>"
                                        alt="<?= htmlspecialchars($jogo['equipa_casa']) ?>"
                                        class="upcoming-team-logo"
                                    >

                                <?php endif; ?>


                                <strong>

                                    <?= htmlspecialchars(
                                        $jogo['equipa_casa']
                                    ) ?>

                                </strong>


                            </div>


                            <span>

                                vs

                            </span>


                            <div class="upcoming-team">


                                <?php if ($logoFora): ?>

                                    <img
                                        src="images/<?= htmlspecialchars($logoFora) ?>"
                                        alt="<?= htmlspecialchars($jogo['equipa_fora']) ?>"
                                        class="upcoming-team-logo"
                                    >

                                <?php endif; ?>


                                <strong>

                                    <?= htmlspecialchars(
                                        $jogo['equipa_fora']
                                    ) ?>

                                </strong>


                            </div>


                        </div>


                        <div class="upcoming-time">


                            <?php if ($jogo['hora']): ?>

                                <?= date(
                                    'H:i',
                                    strtotime($jogo['hora'])
                                ) ?>


                            <?php else: ?>

                                <?= htmlspecialchars(
                                    $jogo['estado']
                                ) ?>

                            <?php endif; ?>


                        </div>


                        <span class="upcoming-arrow">

                            →

                        </span>


                    </a>


                <?php endforeach; ?>


            </div>


        </section>


    <?php endif; ?>


    <!-- =========================================================
         NOTÍCIAS
         ========================================================= -->

    <section class="home-section">


        <div class="section-title">

            <div>

                <span class="home-section-label">

                    ÚLTIMA HORA

                </span>


                <h2>

                    Notícias em Destaque

                </h2>

            </div>


            <a href="pages/noticias.php">

                Ver todas →

            </a>

        </div>


        <?php if (!empty($noticias)): ?>


            <div class="home-news-grid">


                <?php foreach ($noticias as $noticia): ?>


                    <a
                        href="pages/noticia.php?id=<?= $noticia['id'] ?>"
                        class="home-news-card"
                    >


                        <div class="home-news-image">

                            <span>

                                <?= htmlspecialchars(
                                    $noticia['imagem'] ?? '🏀'
                                ) ?>

                            </span>


                            <small>

                                <?= htmlspecialchars(
                                    $noticia['categoria']
                                ) ?>

                            </small>

                        </div>


                        <div class="home-news-content">


                            <time>

                                <?= date(
                                    'd \d\e F \d\e Y',
                                    strtotime(
                                        $noticia['data_publicacao']
                                    )
                                ) ?>

                            </time>


                            <h3>

                                <?= htmlspecialchars(
                                    $noticia['titulo']
                                ) ?>

                            </h3>


                            <p>

                                <?= htmlspecialchars(
                                    mb_substr(
                                        $noticia['conteudo'],
                                        0,
                                        120
                                    )
                                ) ?>...

                            </p>


                            <span class="home-news-link">

                                Ler notícia →

                            </span>


                        </div>


                    </a>


                <?php endforeach; ?>


            </div>


        <?php endif; ?>


    </section>


    <!-- =========================================================
         ACESSO RÁPIDO
         ========================================================= -->

    <section class="home-section">


        <div class="section-title">

            <div>

                <span class="home-section-label">

                    EXPLORA

                </span>


                <h2>

                    O que procuras?

                </h2>

            </div>

        </div>


        <div class="quick-links">


            <a
                href="pages/equipas.php"
                class="quick-link"
            >

                <span>
                    🏀
                </span>


                <div>

                    <h3>
                        Equipas
                    </h3>

                    <p>
                        Consulta as principais equipas.
                    </p>

                </div>


                <b>
                    →
                </b>

            </a>


            <a
                href="pages/jogadores.php"
                class="quick-link"
            >

                <span>
                    ⭐
                </span>


                <div>

                    <h3>
                        Jogadores
                    </h3>

                    <p>
                        Consulta informações dos jogadores.
                    </p>

                </div>


                <b>
                    →
                </b>

            </a>


            <a
                href="pages/classificacoes.php"
                class="quick-link"
            >

                <span>
                    📊
                </span>


                <div>

                    <h3>
                        Classificações
                    </h3>

                    <p>
                        Consulta as classificações das ligas.
                    </p>

                </div>


                <b>
                    →
                </b>

            </a>


            <a
                href="pages/pesquisa.php"
                class="quick-link"
            >

                <span>
                    🔎
                </span>


                <div>

                    <h3>
                        Pesquisa
                    </h3>

                    <p>
                        Encontra rapidamente o que procuras.
                    </p>

                </div>


                <b>
                    →
                </b>

            </a>


        </div>


    </section>


</main>


<!-- =========================================================
     CHATBOT
     ========================================================= -->

<button
    type="button"
    class="chatbot-button"
    id="chatbotButton"
    aria-label="Abrir assistente CourtZone"
>

    💬

</button>


<div
    class="chatbot-window"
    id="chatbotWindow"
>


    <div class="chatbot-header">


        <div class="chatbot-header-info">


            <div class="chatbot-header-icon">

                🏀

            </div>


            <div class="chatbot-header-text">

                <strong>

                    CourtZone Assistant

                </strong>


                <span>

                    Assistente do CourtZone

                </span>

            </div>


        </div>


        <button
            type="button"
            class="chatbot-close"
            id="chatbotClose"
            aria-label="Fechar chatbot"
        >

            ×

        </button>


    </div>


    <div
        class="chatbot-messages"
        id="chatbotMessages"
    >

        <div class="chatbot-message bot">

            Olá! 👋 Sou o assistente do CourtZone.
            Posso ajudar-te a encontrar informações sobre
            jogos, equipas, jogadores e classificações.

        </div>

    </div>


    <form
        class="chatbot-input-area"
        id="chatbotForm"
    >


        <input
            type="text"
            class="chatbot-input"
            id="chatbotInput"
            placeholder="Escreve a tua mensagem..."
            autocomplete="off"
        >


        <button
            type="submit"
            class="chatbot-send"
            aria-label="Enviar mensagem"
        >

            ➤

        </button>


    </form>


</div>


<footer>

    <p>

        &copy; <?= date('Y') ?> CourtZone.

        Todos os direitos reservados.

    </p>

</footer>


<script>

    const chatbotButton =
        document.getElementById('chatbotButton');

    const chatbotWindow =
        document.getElementById('chatbotWindow');

    const chatbotClose =
        document.getElementById('chatbotClose');

    const chatbotForm =
        document.getElementById('chatbotForm');

    const chatbotInput =
        document.getElementById('chatbotInput');

    const chatbotMessages =
        document.getElementById('chatbotMessages');


    /* =========================================================
       ABRIR / FECHAR CHATBOT
       ========================================================= */

    chatbotButton.addEventListener('click', function () {

        chatbotWindow.classList.toggle('open');

        if (chatbotWindow.classList.contains('open')) {

            chatbotInput.focus();

        }

    });


    chatbotClose.addEventListener('click', function () {

        chatbotWindow.classList.remove('open');

    });


    /* =========================================================
       ENVIAR MENSAGEM
       ========================================================= */

    chatbotForm.addEventListener(
        'submit',
        async function (event) {

            event.preventDefault();


            const mensagem =
                chatbotInput.value.trim();


            if (!mensagem) {

                return;

            }


            /* =================================================
               MENSAGEM DO UTILIZADOR
               ================================================= */

            const mensagemUtilizador =
                document.createElement('div');

            mensagemUtilizador.className =
                'chatbot-message user';

            mensagemUtilizador.textContent =
                mensagem;

            chatbotMessages.appendChild(
                mensagemUtilizador
            );


            chatbotInput.value = '';


            chatbotMessages.scrollTop =
                chatbotMessages.scrollHeight;


            /* =================================================
               MENSAGEM DE CARREGAMENTO
               ================================================= */

            const mensagemLoading =
                document.createElement('div');

            mensagemLoading.className =
                'chatbot-message bot';

            mensagemLoading.textContent =
                'A pesquisar...';

            chatbotMessages.appendChild(
                mensagemLoading
            );


            chatbotMessages.scrollTop =
                chatbotMessages.scrollHeight;


            /* =================================================
               PEDIDO AO BACKEND
               ================================================= */

            try {

                const resposta = await fetch(
                    'api/chatbot.php',
                    {
                        method: 'POST',

                        headers: {
                            'Content-Type':
                                'application/json'
                        },

                        body: JSON.stringify({
                            mensagem: mensagem
                        })
                    }
                );


                const dados =
                    await resposta.json();


                /* =============================================
                   REMOVER LOADING
                   ============================================= */

                mensagemLoading.remove();


                /* =============================================
                   RESPOSTA DO BOT
                   ============================================= */

                const mensagemBot =
                    document.createElement('div');

                mensagemBot.className =
                    'chatbot-message bot';


                if (dados.resposta) {

                    mensagemBot.textContent =
                        dados.resposta;

                } else {

                    mensagemBot.textContent =
                        'Não foi possível obter uma resposta.';

                }


                chatbotMessages.appendChild(
                    mensagemBot
                );


                chatbotMessages.scrollTop =
                    chatbotMessages.scrollHeight;


            } catch (erro) {


                /* =============================================
                   ERRO
                   ============================================= */

                mensagemLoading.remove();


                const mensagemErro =
                    document.createElement('div');

                mensagemErro.className =
                    'chatbot-message bot';

                mensagemErro.textContent =
                    'Ocorreu um erro ao contactar o assistente.';


                chatbotMessages.appendChild(
                    mensagemErro
                );


                chatbotMessages.scrollTop =
                    chatbotMessages.scrollHeight;


                console.error(
                    'Erro no chatbot:',
                    erro
                );

            }

        }
    );

</script>


</body>

</html>
