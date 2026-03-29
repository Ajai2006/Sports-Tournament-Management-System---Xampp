<?php
$pageTitle = 'Home';
require_once 'includes/config.php';

// Stats
$total_tournaments = $conn->query("SELECT COUNT(*) as c FROM tournaments")->fetch_assoc()['c'];
$total_teams = $conn->query("SELECT COUNT(*) as c FROM teams")->fetch_assoc()['c'];
$total_matches = $conn->query("SELECT COUNT(*) as c FROM matches")->fetch_assoc()['c'];
$live_matches = $conn->query("SELECT COUNT(*) as c FROM matches WHERE status='live'")->fetch_assoc()['c'];

// Recent/upcoming tournaments
$tournaments = $conn->query("
    SELECT t.*, s.name as sport_name, s.icon as sport_icon,
           (SELECT COUNT(*) FROM tournament_teams tt WHERE tt.tournament_id = t.id) as team_count
    FROM tournaments t
    LEFT JOIN sports s ON t.sport_id = s.id
    ORDER BY t.start_date DESC LIMIT 6
");

// Recent matches
$recent_matches = $conn->query("
    SELECT m.*, 
           t1.name as team1_name, t2.name as team2_name,
           w.name as winner_name,
           tr.name as tournament_name
    FROM matches m
    JOIN teams t1 ON m.team1_id = t1.id
    JOIN teams t2 ON m.team2_id = t2.id
    JOIN tournaments tr ON m.tournament_id = tr.id
    LEFT JOIN teams w ON m.winner_id = w.id
    ORDER BY m.match_date DESC LIMIT 4
");
?>
<?php include 'includes/header.php'; ?>

<!-- HERO -->
<div style="background: linear-gradient(135deg, #12121a 0%, #1a1a2e 50%, #12121a 100%); border-bottom: 1px solid var(--border); padding: 4rem 2rem; text-align: center; position: relative; overflow: hidden;">
    <div style="position:absolute;inset:0;background:radial-gradient(ellipse at 50% 0%, rgba(245,197,24,0.08) 0%, transparent 70%);pointer-events:none;"></div>
    <div style="position:relative;">
        <div style="font-size:0.8rem;letter-spacing:3px;text-transform:uppercase;color:var(--accent);margin-bottom:1rem;font-weight:600;">Sports Tournament Management</div>
        <h1 style="font-family:'Bebas Neue',sans-serif;font-size:clamp(3rem,8vw,6rem);letter-spacing:4px;line-height:1;margin-bottom:1.5rem;">
            MANAGE YOUR<br><span style="color:var(--accent)">TOURNAMENTS</span><br>LIKE A PRO
        </h1>
        <p style="color:var(--muted);max-width:500px;margin:0 auto 2rem;font-size:1.1rem;">Track matches, manage teams, and run tournaments seamlessly — all in one powerful platform.</p>
        <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;">
            <a href="tournaments.php" class="btn btn-primary" style="font-size:1rem;padding:12px 28px;">View Tournaments →</a>
            <a href="admin/login.php" class="btn btn-secondary" style="font-size:1rem;padding:12px 28px;">Admin Panel</a>
        </div>
    </div>
</div>

<div class="container">
    <!-- STATS -->
    <div class="grid-4" style="margin-bottom:2.5rem;margin-top:2rem;">
        <div class="stat-card" style="border-left:3px solid var(--accent);">
            <div class="stat-icon">🏆</div>
            <div class="stat-value" style="color:var(--accent)"><?= $total_tournaments ?></div>
            <div class="stat-label">Tournaments</div>
        </div>
        <div class="stat-card" style="border-left:3px solid var(--blue);">
            <div class="stat-icon">👥</div>
            <div class="stat-value" style="color:var(--blue)"><?= $total_teams ?></div>
            <div class="stat-label">Teams</div>
        </div>
        <div class="stat-card" style="border-left:3px solid var(--green);">
            <div class="stat-icon">⚽</div>
            <div class="stat-value" style="color:var(--green)"><?= $total_matches ?></div>
            <div class="stat-label">Matches Played</div>
        </div>
        <div class="stat-card" style="border-left:3px solid var(--red);">
            <div class="stat-icon" style="position:relative;">
                🔴
                <?php if($live_matches > 0): ?>
                <span style="position:absolute;top:-2px;right:-2px;width:8px;height:8px;background:var(--red);border-radius:50%;animation:pulse 1.5s infinite;"></span>
                <?php endif; ?>
            </div>
            <div class="stat-value" style="color:var(--red)"><?= $live_matches ?></div>
            <div class="stat-label">Live Matches</div>
        </div>
    </div>

    <!-- TOURNAMENTS -->
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.25rem;">
        <h2 style="font-family:'Bebas Neue',sans-serif;font-size:1.8rem;letter-spacing:2px;">TOURNAMENTS</h2>
        <a href="tournaments.php" class="btn btn-secondary btn-sm">View All →</a>
    </div>

    <?php if ($tournaments->num_rows === 0): ?>
        <div class="empty-state">
            <div class="empty-state-icon">🏆</div>
            <h3>No tournaments yet</h3>
            <p>Ask the admin to create tournaments.</p>
        </div>
    <?php else: ?>
        <div class="grid-3" style="margin-bottom:3rem;">
        <?php while ($t = $tournaments->fetch_assoc()): ?>
            <?php
            $statusClass = ['upcoming'=>'badge-yellow','ongoing'=>'badge-green','completed'=>'badge-gray'][$t['status']] ?? 'badge-gray';
            ?>
            <div class="card" style="transition:transform 0.2s,box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 12px 40px rgba(0,0,0,0.4)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:1rem;">
                    <div style="font-size:2rem;"><?= $t['sport_icon'] ?? '🏆' ?></div>
                    <span class="badge <?= $statusClass ?>"><?= ucfirst($t['status']) ?></span>
                </div>
                <h3 style="font-size:1.1rem;font-weight:700;margin-bottom:8px;"><?= htmlspecialchars($t['name']) ?></h3>
                <div style="color:var(--muted);font-size:0.85rem;display:flex;flex-direction:column;gap:4px;">
                    <span>🏅 <?= htmlspecialchars($t['sport_name'] ?? 'Unknown') ?></span>
                    <span>📍 <?= htmlspecialchars($t['location'] ?: 'TBA') ?></span>
                    <span>📅 <?= date('M d', strtotime($t['start_date'])) ?> – <?= date('M d, Y', strtotime($t['end_date'])) ?></span>
                    <span>👥 <?= $t['team_count'] ?> / <?= $t['max_teams'] ?> Teams</span>
                </div>
                <div style="margin-top:1rem;padding-top:1rem;border-top:1px solid var(--border);">
                    <a href="tournament_detail.php?id=<?= $t['id'] ?>" class="btn btn-secondary btn-sm" style="width:100%;justify-content:center;">View Details →</a>
                </div>
            </div>
        <?php endwhile; ?>
        </div>
    <?php endif; ?>

    <!-- RECENT MATCHES -->
    <?php if ($recent_matches->num_rows > 0): ?>
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.25rem;">
        <h2 style="font-family:'Bebas Neue',sans-serif;font-size:1.8rem;letter-spacing:2px;">RECENT MATCHES</h2>
    </div>
    <div style="display:flex;flex-direction:column;gap:1rem;margin-bottom:3rem;">
    <?php while ($m = $recent_matches->fetch_assoc()): ?>
        <div class="match-card">
            <div class="match-team">
                <div class="match-team-name"><?= htmlspecialchars($m['team1_name']) ?></div>
            </div>
            <div style="text-align:center;">
                <div class="match-score"><?= $m['team1_score'] ?> — <?= $m['team2_score'] ?></div>
                <div style="font-size:0.75rem;color:var(--muted);margin-top:2px;"><?= htmlspecialchars($m['tournament_name']) ?></div>
                <?php if($m['status'] === 'live'): ?>
                    <span class="badge badge-red" style="margin-top:4px;">🔴 LIVE</span>
                <?php elseif($m['status'] === 'completed'): ?>
                    <span class="badge badge-gray" style="margin-top:4px;">FT</span>
                <?php endif; ?>
            </div>
            <div class="match-team">
                <div class="match-team-name"><?= htmlspecialchars($m['team2_name']) ?></div>
            </div>
        </div>
    <?php endwhile; ?>
    </div>
    <?php endif; ?>
</div>

<style>
@keyframes pulse { 0%,100%{opacity:1}50%{opacity:0.3} }
</style>

<?php include 'includes/footer.php'; ?>
