<?php
$pageTitle = 'Dashboard';
$isAdmin = true;
require_once '../includes/config.php';
requireAdmin();

$stats = [
    'tournaments' => $conn->query("SELECT COUNT(*) as c FROM tournaments")->fetch_assoc()['c'],
    'teams'       => $conn->query("SELECT COUNT(*) as c FROM teams")->fetch_assoc()['c'],
    'matches'     => $conn->query("SELECT COUNT(*) as c FROM matches")->fetch_assoc()['c'],
    'players'     => $conn->query("SELECT COUNT(*) as c FROM players")->fetch_assoc()['c'],
    'live'        => $conn->query("SELECT COUNT(*) as c FROM matches WHERE status='live'")->fetch_assoc()['c'],
    'upcoming'    => $conn->query("SELECT COUNT(*) as c FROM tournaments WHERE status='upcoming'")->fetch_assoc()['c'],
    'ongoing'     => $conn->query("SELECT COUNT(*) as c FROM tournaments WHERE status='ongoing'")->fetch_assoc()['c'],
];

$recent_matches = $conn->query("
    SELECT m.*, t1.name as t1, t2.name as t2, tr.name as tname
    FROM matches m JOIN teams t1 ON m.team1_id=t1.id JOIN teams t2 ON m.team2_id=t2.id
    JOIN tournaments tr ON m.tournament_id=tr.id
    ORDER BY m.created_at DESC LIMIT 5
");

$recent_tournaments = $conn->query("SELECT t.*, s.icon FROM tournaments t LEFT JOIN sports s ON t.sport_id=s.id ORDER BY t.created_at DESC LIMIT 4");
?>
<?php include '../includes/header.php'; ?>
<div class="container">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:2rem;flex-wrap:wrap;gap:1rem;">
        <div>
            <h1 class="page-title">DASHBOARD</h1>
            <p style="color:var(--muted);">Welcome back, <strong style="color:var(--accent)"><?= htmlspecialchars($_SESSION['admin_name']) ?></strong></p>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <a href="tournaments.php?action=add" class="btn btn-primary">+ Tournament</a>
            <a href="teams.php?action=add" class="btn btn-secondary">+ Team</a>
            <a href="matches.php?action=add" class="btn btn-secondary">+ Match</a>
        </div>
    </div>

    <!-- STATS ROW -->
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:1rem;margin-bottom:2rem;">
        <div class="stat-card" style="border-top:3px solid var(--accent);">
            <div class="stat-icon">🏆</div>
            <div class="stat-value" style="color:var(--accent)"><?=$stats['tournaments']?></div>
            <div class="stat-label">Tournaments</div>
        </div>
        <div class="stat-card" style="border-top:3px solid var(--blue);">
            <div class="stat-icon">👥</div>
            <div class="stat-value" style="color:var(--blue)"><?=$stats['teams']?></div>
            <div class="stat-label">Teams</div>
        </div>
        <div class="stat-card" style="border-top:3px solid var(--green);">
            <div class="stat-icon">⚽</div>
            <div class="stat-value" style="color:var(--green)"><?=$stats['matches']?></div>
            <div class="stat-label">Matches</div>
        </div>
        <div class="stat-card" style="border-top:3px solid #a78bfa;">
            <div class="stat-icon">🏃</div>
            <div class="stat-value" style="color:#a78bfa"><?=$stats['players']?></div>
            <div class="stat-label">Players</div>
        </div>
        <div class="stat-card" style="border-top:3px solid var(--red);">
            <div class="stat-icon">🔴</div>
            <div class="stat-value" style="color:var(--red)"><?=$stats['live']?></div>
            <div class="stat-label">Live Now</div>
        </div>
        <div class="stat-card" style="border-top:3px solid var(--accent2);">
            <div class="stat-icon">🔄</div>
            <div class="stat-value" style="color:var(--accent2)"><?=$stats['ongoing']?></div>
            <div class="stat-label">Ongoing</div>
        </div>
    </div>

    <div class="grid-2">
        <!-- RECENT MATCHES -->
        <div class="card">
            <div class="card-header">
                <span class="card-title">⚡ Recent Matches</span>
                <a href="matches.php" class="btn btn-secondary btn-sm">View All</a>
            </div>
            <?php if($recent_matches->num_rows === 0): ?>
                <p style="color:var(--muted);text-align:center;padding:2rem;">No matches yet</p>
            <?php else: ?>
            <div style="display:flex;flex-direction:column;gap:8px;">
            <?php while($m=$recent_matches->fetch_assoc()): ?>
                <?php $sc=['scheduled'=>'badge-yellow','live'=>'badge-red','completed'=>'badge-gray','cancelled'=>'badge-gray'][$m['status']]??'badge-gray'; ?>
                <div style="background:var(--dark);border-radius:8px;padding:10px;display:flex;align-items:center;gap:10px;">
                    <span class="badge <?=$sc?>" style="flex-shrink:0"><?=$m['status']==='live'?'🔴':''?><?=ucfirst($m['status'])?></span>
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:0.85rem;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?=htmlspecialchars($m['t1'])?> vs <?=htmlspecialchars($m['t2'])?></div>
                        <div style="font-size:0.75rem;color:var(--muted);"><?=htmlspecialchars($m['tname'])?></div>
                    </div>
                    <div style="font-family:'Bebas Neue',sans-serif;font-size:1.2rem;color:var(--accent);flex-shrink:0;"><?=$m['team1_score']?>–<?=$m['team2_score']?></div>
                    <a href="matches.php?edit=<?=$m['id']?>" class="btn btn-icon btn-sm" title="Edit">✏️</a>
                </div>
            <?php endwhile; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- RECENT TOURNAMENTS -->
        <div class="card">
            <div class="card-header">
                <span class="card-title">🏆 Recent Tournaments</span>
                <a href="tournaments.php" class="btn btn-secondary btn-sm">View All</a>
            </div>
            <?php if($recent_tournaments->num_rows === 0): ?>
                <p style="color:var(--muted);text-align:center;padding:2rem;">No tournaments yet</p>
            <?php else: ?>
            <div style="display:flex;flex-direction:column;gap:8px;">
            <?php while($t=$recent_tournaments->fetch_assoc()): ?>
                <?php $sc=['upcoming'=>'badge-yellow','ongoing'=>'badge-green','completed'=>'badge-gray'][$t['status']]??'badge-gray'; ?>
                <div style="background:var(--dark);border-radius:8px;padding:10px;display:flex;align-items:center;gap:10px;">
                    <div style="font-size:1.5rem;"><?=$t['icon']??'🏆'?></div>
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:0.88rem;font-weight:600;"><?=htmlspecialchars($t['name'])?></div>
                        <div style="font-size:0.75rem;color:var(--muted);"><?=date('d M Y',strtotime($t['start_date']))?> – <?=date('d M Y',strtotime($t['end_date']))?></div>
                    </div>
                    <span class="badge <?=$sc?>"><?=ucfirst($t['status'])?></span>
                    <a href="tournaments.php?edit=<?=$t['id']?>" class="btn btn-icon btn-sm" title="Edit">✏️</a>
                </div>
            <?php endwhile; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- QUICK ACTIONS -->
    <div class="card" style="margin-top:1.5rem;">
        <div class="card-header"><span class="card-title">⚡ Quick Actions</span></div>
        <div style="display:flex;gap:1rem;flex-wrap:wrap;">
            <a href="tournaments.php?action=add" class="btn btn-primary">🏆 New Tournament</a>
            <a href="teams.php?action=add" class="btn btn-secondary">👥 Register Team</a>
            <a href="matches.php?action=add" class="btn btn-secondary">⚽ Schedule Match</a>
            <a href="players.php?action=add" class="btn btn-secondary">🏃 Add Player</a>
            <a href="../index.php" target="_blank" class="btn btn-secondary">🌐 View Public Site</a>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
