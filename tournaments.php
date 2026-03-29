<?php
$pageTitle = 'Tournaments';
require_once 'includes/config.php';

$sport_filter = isset($_GET['sport']) ? (int)$_GET['sport'] : 0;
$status_filter = isset($_GET['status']) ? sanitize($conn, $_GET['status']) : '';

$where = "WHERE 1=1";
if ($sport_filter) $where .= " AND t.sport_id = $sport_filter";
if ($status_filter) $where .= " AND t.status = '$status_filter'";

$tournaments = $conn->query("
    SELECT t.*, s.name as sport_name, s.icon as sport_icon,
           (SELECT COUNT(*) FROM tournament_teams tt WHERE tt.tournament_id = t.id) as team_count,
           (SELECT COUNT(*) FROM matches m WHERE m.tournament_id = t.id) as match_count
    FROM tournaments t
    LEFT JOIN sports s ON t.sport_id = s.id
    $where
    ORDER BY t.start_date DESC
");

$sports = $conn->query("SELECT * FROM sports ORDER BY name");
?>
<?php include 'includes/header.php'; ?>
<div class="container">
    <div class="page-header" style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
        <div>
            <h1 class="page-title">ALL TOURNAMENTS</h1>
            <p class="page-subtitle">Browse and follow all active tournaments</p>
        </div>
    </div>

    <!-- FILTERS -->
    <form method="GET" style="display:flex;gap:1rem;flex-wrap:wrap;margin-bottom:2rem;">
        <select name="sport" class="form-control" style="width:auto;min-width:160px;" onchange="this.form.submit()">
            <option value="">All Sports</option>
            <?php while($s = $sports->fetch_assoc()): ?>
            <option value="<?=$s['id']?>" <?= $sport_filter==$s['id']?'selected':''?>>
                <?=$s['icon']?> <?=htmlspecialchars($s['name'])?>
            </option>
            <?php endwhile; ?>
        </select>
        <select name="status" class="form-control" style="width:auto;" onchange="this.form.submit()">
            <option value="">All Status</option>
            <option value="upcoming" <?=$status_filter=='upcoming'?'selected':''?>>Upcoming</option>
            <option value="ongoing" <?=$status_filter=='ongoing'?'selected':''?>>Ongoing</option>
            <option value="completed" <?=$status_filter=='completed'?'selected':''?>>Completed</option>
        </select>
        <?php if($sport_filter || $status_filter): ?>
        <a href="tournaments.php" class="btn btn-secondary">✕ Clear</a>
        <?php endif; ?>
    </form>

    <?php if ($tournaments->num_rows === 0): ?>
        <div class="empty-state">
            <div class="empty-state-icon">🏆</div>
            <h3>No tournaments found</h3>
            <p>Try a different filter or check back later.</p>
        </div>
    <?php else: ?>
    <div class="grid-3">
    <?php while ($t = $tournaments->fetch_assoc()): ?>
        <?php $statusClass = ['upcoming'=>'badge-yellow','ongoing'=>'badge-green','completed'=>'badge-gray'][$t['status']] ?? 'badge-gray'; ?>
        <div class="card" style="transition:all 0.2s;" onmouseover="this.style.borderColor='var(--accent)'" onmouseout="this.style.borderColor='var(--border)'">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:1rem;">
                <div style="font-size:2.5rem;"><?= $t['sport_icon'] ?? '🏆' ?></div>
                <div style="display:flex;gap:6px;flex-direction:column;align-items:flex-end;">
                    <span class="badge <?= $statusClass ?>"><?= ucfirst($t['status']) ?></span>
                    <span class="badge badge-blue" style="text-transform:capitalize;"><?= $t['format'] ?></span>
                </div>
            </div>
            <h3 style="font-size:1.1rem;font-weight:700;margin-bottom:10px;"><?= htmlspecialchars($t['name']) ?></h3>

            <?php if($t['description']): ?>
            <p style="font-size:0.85rem;color:var(--muted);margin-bottom:10px;line-height:1.5;"><?= htmlspecialchars(substr($t['description'],0,100)) ?>...</p>
            <?php endif; ?>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:1rem;">
                <div style="background:var(--dark);border-radius:8px;padding:10px;text-align:center;">
                    <div style="font-family:'Bebas Neue',sans-serif;font-size:1.4rem;color:var(--blue)"><?= $t['team_count'] ?></div>
                    <div style="font-size:0.7rem;color:var(--muted);text-transform:uppercase">Teams</div>
                </div>
                <div style="background:var(--dark);border-radius:8px;padding:10px;text-align:center;">
                    <div style="font-family:'Bebas Neue',sans-serif;font-size:1.4rem;color:var(--green)"><?= $t['match_count'] ?></div>
                    <div style="font-size:0.7rem;color:var(--muted);text-transform:uppercase">Matches</div>
                </div>
            </div>

            <div style="font-size:0.82rem;color:var(--muted);margin-bottom:1rem;display:flex;flex-direction:column;gap:3px;">
                <span>📍 <?= htmlspecialchars($t['location'] ?: 'TBA') ?></span>
                <span>📅 <?= date('d M Y', strtotime($t['start_date'])) ?> → <?= date('d M Y', strtotime($t['end_date'])) ?></span>
            </div>

            <a href="tournament_detail.php?id=<?= $t['id'] ?>" class="btn btn-primary" style="width:100%;justify-content:center;">View Details →</a>
        </div>
    <?php endwhile; ?>
    </div>
    <?php endif; ?>
</div>
<?php include 'includes/footer.php'; ?>
