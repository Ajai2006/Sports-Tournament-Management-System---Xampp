<?php
$pageTitle = 'Teams';
require_once 'includes/config.php';

$teams = $conn->query("
    SELECT t.*, 
           COUNT(DISTINCT p.id) as player_count,
           COUNT(DISTINCT tt.tournament_id) as tournament_count
    FROM teams t
    LEFT JOIN players p ON t.id = p.team_id
    LEFT JOIN tournament_teams tt ON t.id = tt.team_id
    GROUP BY t.id ORDER BY t.name
");
?>
<?php include 'includes/header.php'; ?>
<div class="container">
    <div class="page-header">
        <h1 class="page-title">ALL TEAMS</h1>
        <p class="page-subtitle">Browse registered teams and their details</p>
    </div>

    <?php if($teams->num_rows === 0): ?>
        <div class="empty-state"><div class="empty-state-icon">👥</div><h3>No teams registered yet</h3></div>
    <?php else: ?>
    <div class="grid-3">
    <?php while($t = $teams->fetch_assoc()): ?>
        <div class="card" style="transition:all 0.2s;" onmouseover="this.style.borderColor='var(--blue)'" onmouseout="this.style.borderColor='var(--border)'">
            <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1rem;">
                <div style="width:52px;height:52px;border-radius:50%;background:var(--border);display:flex;align-items:center;justify-content:center;font-size:1.5rem;flex-shrink:0;">👕</div>
                <div>
                    <h3 style="font-weight:700;font-size:1.05rem;"><?= htmlspecialchars($t['name']) ?></h3>
                    <div style="color:var(--muted);font-size:0.82rem;">📍 <?= htmlspecialchars($t['city'] ?: 'Unknown') ?></div>
                </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:1rem;">
                <div style="background:var(--dark);border-radius:8px;padding:10px;text-align:center;">
                    <div style="font-family:'Bebas Neue',sans-serif;font-size:1.4rem;color:var(--blue)"><?= $t['player_count'] ?></div>
                    <div style="font-size:0.7rem;color:var(--muted);text-transform:uppercase">Players</div>
                </div>
                <div style="background:var(--dark);border-radius:8px;padding:10px;text-align:center;">
                    <div style="font-family:'Bebas Neue',sans-serif;font-size:1.4rem;color:var(--accent)"><?= $t['tournament_count'] ?></div>
                    <div style="font-size:0.7rem;color:var(--muted);text-transform:uppercase">Tournaments</div>
                </div>
            </div>
            <?php if($t['coach']): ?>
            <div style="font-size:0.82rem;color:var(--muted);">🎓 Coach: <?= htmlspecialchars($t['coach']) ?></div>
            <?php endif; ?>
        </div>
    <?php endwhile; ?>
    </div>
    <?php endif; ?>
</div>
<?php include 'includes/footer.php'; ?>
