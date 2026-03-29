<?php require_once __DIR__ . '/config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle . ' — ' : '' ?>TourneyPro</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --black: #0a0a0f;
            --dark: #12121a;
            --card: #1a1a26;
            --border: #2a2a3d;
            --accent: #f5c518;
            --accent2: #e8773a;
            --green: #29c96e;
            --red: #e84545;
            --blue: #4f9cf9;
            --text: #e8e8f0;
            --muted: #7878a0;
            --radius: 12px;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--black);
            color: var(--text);
            min-height: 100vh;
        }

        /* NAV */
        nav {
            background: var(--dark);
            border-bottom: 1px solid var(--border);
            padding: 0 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 64px;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .nav-logo {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 1.8rem;
            color: var(--accent);
            letter-spacing: 2px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .nav-links { display: flex; gap: 4px; align-items: center; }
        .nav-links a {
            color: var(--muted);
            text-decoration: none;
            padding: 6px 14px;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.2s;
        }
        .nav-links a:hover, .nav-links a.active {
            color: var(--text);
            background: var(--card);
        }
        .nav-links a.btn-accent {
            background: var(--accent);
            color: var(--black);
        }
        .nav-links a.btn-accent:hover { background: #e0b015; }

        /* LAYOUT */
        .container { max-width: 1280px; margin: 0 auto; padding: 2rem; }
        .page-header {
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--border);
        }
        .page-title {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 2.5rem;
            letter-spacing: 2px;
            color: var(--text);
        }
        .page-subtitle { color: var(--muted); margin-top: 4px; }

        /* CARDS */
        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 1.5rem;
        }
        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.25rem;
        }
        .card-title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text);
        }

        /* GRID */
        .grid-3 { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.25rem; }
        .grid-4 { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 1.25rem; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; }

        /* BADGES */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .badge-yellow { background: rgba(245,197,24,0.15); color: var(--accent); }
        .badge-green { background: rgba(41,201,110,0.15); color: var(--green); }
        .badge-red { background: rgba(232,69,69,0.15); color: var(--red); }
        .badge-blue { background: rgba(79,156,249,0.15); color: var(--blue); }
        .badge-gray { background: var(--border); color: var(--muted); }

        /* BUTTONS */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 9px 18px;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            border: none;
            transition: all 0.2s;
        }
        .btn-primary { background: var(--accent); color: var(--black); }
        .btn-primary:hover { background: #e0b015; }
        .btn-secondary { background: var(--border); color: var(--text); }
        .btn-secondary:hover { background: #3a3a55; }
        .btn-danger { background: rgba(232,69,69,0.15); color: var(--red); border: 1px solid rgba(232,69,69,0.3); }
        .btn-danger:hover { background: var(--red); color: #fff; }
        .btn-sm { padding: 5px 12px; font-size: 0.8rem; }
        .btn-icon { padding: 8px; border-radius: 8px; background: var(--border); color: var(--text); cursor: pointer; border: none; transition: all 0.2s; }
        .btn-icon:hover { background: #3a3a55; }

        /* FORMS */
        .form-group { margin-bottom: 1.2rem; }
        .form-label { display: block; font-size: 0.85rem; font-weight: 500; color: var(--muted); margin-bottom: 6px; }
        .form-control {
            width: 100%;
            background: var(--dark);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 10px 14px;
            color: var(--text);
            font-size: 0.9rem;
            font-family: inherit;
            transition: border-color 0.2s;
            outline: none;
        }
        .form-control:focus { border-color: var(--accent); }
        .form-control option { background: var(--dark); }
        textarea.form-control { resize: vertical; min-height: 100px; }

        /* TABLE */
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 0.9rem; }
        th { 
            padding: 10px 16px; 
            text-align: left; 
            font-size: 0.75rem; 
            font-weight: 600; 
            text-transform: uppercase; 
            letter-spacing: 0.5px;
            color: var(--muted);
            border-bottom: 1px solid var(--border);
        }
        td { padding: 12px 16px; border-bottom: 1px solid rgba(42,42,61,0.5); }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: rgba(255,255,255,0.02); }

        /* STATS */
        .stat-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
        }
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0; right: 0;
            width: 80px; height: 80px;
            border-radius: 50%;
            opacity: 0.08;
            transform: translate(20px, -20px);
        }
        .stat-value {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 2.5rem;
            letter-spacing: 1px;
            line-height: 1;
        }
        .stat-label { font-size: 0.8rem; color: var(--muted); margin-top: 4px; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px; }
        .stat-icon { font-size: 1.8rem; margin-bottom: 12px; }

        /* ALERTS */
        .alert { padding: 12px 16px; border-radius: 8px; margin-bottom: 1.2rem; font-size: 0.9rem; }
        .alert-success { background: rgba(41,201,110,0.1); border: 1px solid rgba(41,201,110,0.3); color: var(--green); }
        .alert-error { background: rgba(232,69,69,0.1); border: 1px solid rgba(232,69,69,0.3); color: var(--red); }
        .alert-info { background: rgba(79,156,249,0.1); border: 1px solid rgba(79,156,249,0.3); color: var(--blue); }

        /* EMPTY STATE */
        .empty-state { text-align: center; padding: 4rem 2rem; color: var(--muted); }
        .empty-state-icon { font-size: 3rem; margin-bottom: 1rem; opacity: 0.5; }
        .empty-state p { margin-top: 8px; font-size: 0.9rem; }

        /* MATCH CARD */
        .match-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 1.25rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .match-team { flex: 1; text-align: center; }
        .match-team-name { font-weight: 600; font-size: 0.95rem; }
        .match-score {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 2rem;
            letter-spacing: 2px;
            color: var(--accent);
            min-width: 80px;
            text-align: center;
        }
        .match-vs { color: var(--muted); font-size: 0.8rem; font-weight: 600; }

        /* MODAL */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.7);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        .modal-overlay.open { display: flex; }
        .modal {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 2rem;
            width: 90%;
            max-width: 520px;
            max-height: 90vh;
            overflow-y: auto;
        }
        .modal-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; }
        .modal-title { font-family: 'Bebas Neue', sans-serif; font-size: 1.5rem; letter-spacing: 1px; }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            nav { padding: 0 1rem; }
            .container { padding: 1rem; }
            .grid-3, .grid-2 { grid-template-columns: 1fr; }
            .nav-links a span { display: none; }
        }
    </style>
</head>
<body>
<nav>
    <a href="<?= isset($isAdmin) ? '../index.php' : 'index.php' ?>" class="nav-logo">🏆 TourneyPro</a>
    <div class="nav-links">
        <?php if (isset($isAdmin) && $isAdmin): ?>
            <a href="dashboard.php">📊 <span>Dashboard</span></a>
            <a href="tournaments.php">🏆 <span>Tournaments</span></a>
            <a href="teams.php">👥 <span>Teams</span></a>
            <a href="matches.php">⚡ <span>Matches</span></a>
            <a href="players.php">🏃 <span>Players</span></a>
            <a href="logout.php" class="btn-accent">Logout</a>
        <?php else: ?>
            <a href="index.php">Home</a>
            <a href="tournaments.php">Tournaments</a>
            <a href="teams.php">Teams</a>
            <a href="standings.php">Standings</a>
            <a href="admin/login.php" class="btn-accent">Admin</a>
        <?php endif; ?>
    </div>
</nav>
