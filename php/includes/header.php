<?php
/**
 * ==============================================================================
 * TRABALHO PRÁTICO - BANCO DE DADOS 2 (IFMG CAMPUS OURO PRETO)
 * CURSO: Análise e Desenvolvimento de Sistemas (ADS)
 * CENÁRIO: Congresso Acadêmico de ADS (CONADS 2026)
 * ARQUIVO: php/includes/header.php
 * OBJETIVO: Cabeçalho do portal oficial do congresso voltado para o usuário final.
 * ==============================================================================
 */

require_once __DIR__ . '/../config/database.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="CONADS 2026 - I Congresso Nacional de Análise e Desenvolvimento de Sistemas do IFMG Campus Ouro Preto. Palestras, oficinas e minicursos.">
    <title>CONADS 2026 | I Congresso Nacional de Análise e Dev. de Sistemas</title>
    
    <!-- Google Fonts: Plus Jakarta Sans & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --brand-navy: #0B132B;
            --brand-dark: #1C2541;
            --brand-blue: #2563EB;
            --brand-indigo: #4F46E5;
            --brand-cyan: #06B6D4;
            --brand-teal: #10B981;
            --bg-main: #F8FAFC;
            --bg-card: #FFFFFF;
            --text-main: #0F172A;
            --text-muted: #64748B;
            --border-color: #E2E8F0;
            --border-hover: #CBD5E1;
            
            --status-success: #10B981;
            --status-success-bg: #ECFDF5;
            --status-warning: #F59E0B;
            --status-warning-bg: #FFFBEB;
            --status-danger: #EF4444;
            --status-danger-bg: #FEF2F2;
            
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.06), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
            --shadow-lg: 0 10px 25px -5px rgba(15, 23, 42, 0.08), 0 8px 10px -6px rgba(15, 23, 42, 0.04);
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
            --radius-full: 9999px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }

        h1, h2, h3, h4, h5, .font-heading {
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            letter-spacing: -0.02em;
        }

        body {
            background-color: var(--bg-main);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }

        /* Topbar Superior */
        .topbar {
            background: linear-gradient(135deg, #0F172A 0%, #1E1B4B 50%, #0F172A 100%);
            color: white;
            padding: 0.6rem 1.5rem;
            font-size: 0.8rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .topbar-container {
            max-width: 1240px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .topbar-info {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            color: #94A3B8;
        }

        .topbar-item {
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .topbar-badge {
            background: rgba(59, 130, 246, 0.2);
            color: #60A5FA;
            border: 1px solid rgba(96, 165, 250, 0.3);
            padding: 0.2rem 0.6rem;
            border-radius: var(--radius-full);
            font-size: 0.72rem;
            font-weight: 600;
        }

        /* Header Navegação */
        header {
            background-color: #0F172A;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.25);
            border-bottom: 1px solid #1E293B;
        }

        .header-container {
            max-width: 1240px;
            margin: 0 auto;
            padding: 0.9rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1.5rem;
        }

        .brand-link {
            text-decoration: none;
            color: white;
            display: flex;
            align-items: center;
            gap: 0.85rem;
        }

        .brand-logo {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, #2563EB 0%, #4F46E5 100%);
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35);
        }

        .brand-text-wrapper {
            display: flex;
            flex-direction: column;
        }

        .brand-title {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 1.25rem;
            font-weight: 800;
            color: white;
            letter-spacing: -0.3px;
            line-height: 1.2;
        }

        .brand-sub {
            font-size: 0.72rem;
            color: #94A3B8;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.6px;
        }

        nav ul {
            display: flex;
            list-style: none;
            gap: 0.5rem;
            align-items: center;
        }

        nav a {
            color: #94A3B8;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.88rem;
            padding: 0.55rem 1rem;
            border-radius: var(--radius-sm);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        nav a:hover {
            color: white;
            background-color: rgba(255, 255, 255, 0.08);
        }

        nav a.active {
            color: white;
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.25) 0%, rgba(79, 70, 229, 0.25) 100%);
            border: 1px solid rgba(59, 130, 246, 0.3);
            box-shadow: 0 2px 8px rgba(37, 99, 235, 0.15);
        }

        /* Container Principal */
        main {
            max-width: 1240px;
            width: 100%;
            margin: 2rem auto;
            padding: 0 1.5rem;
            flex: 1;
        }

        /* Banner Hero Estilizado */
        .hero-banner {
            background: linear-gradient(135deg, #0F172A 0%, #1E3A8A 50%, #312E81 100%);
            border-radius: var(--radius-lg);
            padding: 2.5rem;
            color: white;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-lg);
            margin-bottom: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .hero-banner::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 450px;
            height: 450px;
            background: radial-gradient(circle, rgba(6, 182, 212, 0.25) 0%, rgba(0, 0, 0, 0) 70%);
            pointer-events: none;
        }

        .hero-badge-tag {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 0.35rem 0.85rem;
            border-radius: var(--radius-full);
            font-size: 0.78rem;
            font-weight: 700;
            color: #60A5FA;
            margin-bottom: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        .hero-title {
            font-size: 2.1rem;
            font-weight: 800;
            line-height: 1.25;
            margin-bottom: 0.75rem;
            color: white;
        }

        .hero-desc {
            font-size: 1.02rem;
            color: #CBD5E1;
            max-width: 820px;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }

        .hero-metrics {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1.25rem;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.12);
        }

        .metric-box {
            background: rgba(255, 255, 255, 0.06);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 1rem 1.2rem;
            border-radius: var(--radius-md);
        }

        .metric-value {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 1.75rem;
            font-weight: 800;
            color: white;
        }

        .metric-label {
            font-size: 0.78rem;
            color: #94A3B8;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        /* Cards & Grid Layout */
        .card {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 1.75rem;
            box-shadow: var(--shadow-md);
            margin-bottom: 1.75rem;
            transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
        }

        .card:hover {
            border-color: var(--border-hover);
        }

        .grid-2 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));
            gap: 1.75rem;
        }

        .grid-3 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.75rem;
        }

        /* Alertas e Mensagens Flash */
        .alert {
            padding: 1.1rem 1.3rem;
            border-radius: var(--radius-md);
            margin-bottom: 1.75rem;
            font-weight: 600;
            font-size: 0.93rem;
            display: flex;
            align-items: center;
            gap: 0.65rem;
            box-shadow: var(--shadow-sm);
        }
        .alert-success { background-color: #ECFDF5; color: #065F46; border: 1px solid #A7F3D0; }
        .alert-warning { background-color: #FFFBEB; color: #92400E; border: 1px solid #FDE68A; }
        .alert-danger { background-color: #FEF2F2; color: #991B1B; border: 1px solid #FCA5A5; }

        /* Botões */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.7rem 1.35rem;
            border-radius: var(--radius-md);
            font-weight: 700;
            font-size: 0.88rem;
            text-decoration: none;
            cursor: pointer;
            border: 1px solid transparent;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            line-height: 1.2;
        }

        .btn-primary {
            background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #1D4ED8 0%, #1E40AF 100%);
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(37, 99, 235, 0.35);
        }

        .btn-secondary {
            background-color: #F1F5F9;
            color: #334155;
            border-color: #E2E8F0;
        }
        .btn-secondary:hover {
            background-color: #E2E8F0;
            color: #0F172A;
        }

        .btn-sm {
            padding: 0.45rem 0.85rem;
            font-size: 0.8rem;
            border-radius: var(--radius-sm);
        }

        /* Tabelas Modernas */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            border-radius: var(--radius-md);
            border: 1px solid var(--border-color);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
        }

        th {
            background-color: #F8FAFC;
            color: #475569;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.6px;
            padding: 0.9rem 1.1rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        td {
            padding: 1rem 1.1rem;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-main);
            vertical-align: middle;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        tbody tr:hover {
            background-color: #F8FAFC;
        }

        /* Formulários Elegantes */
        .form-group {
            margin-bottom: 1.4rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.45rem;
            font-weight: 600;
            font-size: 0.88rem;
            color: #334155;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1.5px solid var(--border-color);
            border-radius: var(--radius-md);
            font-size: 0.95rem;
            color: var(--text-main);
            background-color: white;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--brand-blue);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
        }

        /* Layout de Seções */
        .section-header {
            margin-bottom: 1.75rem;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .section-title {
            font-size: 1.6rem;
            font-weight: 800;
            color: var(--brand-navy);
        }

        .section-subtitle {
            font-size: 0.92rem;
            color: var(--text-muted);
            margin-top: 0.2rem;
        }

        /* Badges de Status de Vagas */
        .badge-available {
            background-color: #ECFDF5;
            color: #047857;
            border: 1px solid #6EE7B7;
            padding: 0.4rem 0.85rem;
            border-radius: var(--radius-full);
            font-weight: 700;
            font-size: 0.78rem;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }

        .badge-soldout {
            background-color: #FEF2F2;
            color: #B91C1C;
            border: 1px solid #FCA5A5;
            padding: 0.4rem 0.85rem;
            border-radius: var(--radius-full);
            font-weight: 700;
            font-size: 0.78rem;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }
    </style>
</head>
<body>

    <!-- Topbar Informativa -->
    <div class="topbar">
        <div class="topbar-container">
            <div class="topbar-info">
                <div class="topbar-item">
                    <span>🏛️</span>
                    <span>IFMG Campus Ouro Preto</span>
                </div>
                <div class="topbar-item">
                    <span>📅</span>
                    <span>CONADS 2026 — 10 a 14 de Agosto</span>
                </div>
            </div>
            <div class="topbar-badge">
                <span>Inscrições Abertas</span>
            </div>
        </div>
    </div>

    <!-- Navegação Principal -->
    <header>
        <div class="header-container">
            <a href="index.php" class="brand-link">
                <div class="brand-logo">🎓</div>
                <div class="brand-text-wrapper">
                    <span class="brand-title">CONADS 2026</span>
                    <span class="brand-sub">I Congresso Nacional de ADS</span>
                </div>
            </a>
            
            <?php
            $currentPage = basename($_SERVER['PHP_SELF']);
            ?>
            <nav>
                <ul>
                    <li>
                        <a href="index.php" class="<?= $currentPage === 'index.php' ? 'active' : '' ?>">
                            <span>🏠</span> Início
                        </a>
                    </li>
                    <li>
                        <a href="atividades.php" class="<?= $currentPage === 'atividades.php' ? 'active' : '' ?>">
                            <span>🎯</span> Programação
                        </a>
                    </li>
                    <li>
                        <a href="participante_cadastrar.php" class="<?= $currentPage === 'participante_cadastrar.php' ? 'active' : '' ?>">
                            <span>✍️</span> Credenciamento
                        </a>
                    </li>
                    <li>
                        <a href="ranking.php" class="<?= $currentPage === 'ranking.php' ? 'active' : '' ?>">
                            <span>🏆</span> Atividades Populares
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <?php if (isset($_SESSION['mensagem_sucesso'])): ?>
            <div class="alert alert-success">
                <span>✅</span>
                <div><?= htmlspecialchars($_SESSION['mensagem_sucesso']) ?></div>
                <?php unset($_SESSION['mensagem_sucesso']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['mensagem_erro'])): ?>
            <div class="alert alert-danger">
                <span>⚠️</span>
                <div><?= htmlspecialchars($_SESSION['mensagem_erro']) ?></div>
                <?php unset($_SESSION['mensagem_erro']); ?>
            </div>
        <?php endif; ?>
