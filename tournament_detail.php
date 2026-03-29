<?php
require_once 'includes/config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$tournament = $conn->query("SELECT t.*, s.name as sport_name, s.icon as sport_icon FROM tournaments t LEFT JOIN sports s ON t.sport_id=s.id WHERE t.id=$id")->fetch_assoc();

if (!$tournament) { header('Location: tournaments.php'); exit; }

$pageTitle = $tournament['name'];

$teams = $conn->query("
    SELECT te.*, tt.registered_at FROM teams te
    JOIN tournament_teams tt ON te.id = tt.team_id
    WHERE tt.tournament_id = $id ORDER BY te.name
");

$matches = $conn->query("
    SELECT m.*, t1.name as team1_name, t2.name as team2_name, w.name as winner_name
    FROM matches m
    JOIN teams t1 ON m.team1_id = t1.id
    JOIN teams t2 ON m.team2_id = t2.id
    LEFT JOIN teams w ON m.winner_id = w.id
    WHERE m.tournament_id = $id ORDER BY m.match_date
");

// Standings calculation for round-robin
$standings = [];
if ($tournament['format'] === 'round_robin') {
    $all_matches = $conn->query("SELECT * FROM matches WHERE tournament_id=$id AND status='completed'");
    while($m = $all_matches->fetch_assoc()){
        foreach([$m['team1_id'],$m['team2_id']] as $tid){
            if(!isset($standings[$tid])) $standings[$tid]=['p'=>0,'w'=>0,'d'=>0,'l'=>0,'gf'=>0,'ga'=>0,'pts'=>0];
            $standings[$tid]['p']++;
        }
        if($m['winner_id']==$m['team1_id']){
            $standings[$m['team1_id']]['w']++; $standings[$m['team1_id']]['pts']+=3;
            $standings[$m['team2_id']]['l']++;
        } elseif($m['winner_id']==$m['team2_id']){
            $standings[$m['team2_id']]['w']++; $standings[$m['team2_id']]['pts']+=3;
            $standings[$m['team1_id']]['l']++;
        } else {
            $standings[$m['team1_id']]['d']++; $standings[$m['team1_id']]['pts']++;
            $standings[$m['team2_id']]['d']++; $standings[$m['team2_id']]['pts']++;
        }
        $standings[$m['team1_id']]['gf']+=$m['team1_score']; $standings[$m['team1_id']]['ga']+=$m['team2_score'];
        $standings[$m['team2_id']]['gf']+=$m['team2_score']; $standings[$m['team2_id']]['ga']+=$m['team1_score'];
    }
    usort($standings, fn($a,$b) => $b['pts'] - $a['pts']);
}
?>
<?php include 'includes/header.php'; ?>
<div class="container">
    <!-- BACK + TITLE -->
    <div style="margin-bottom:1.5rem;">
        <a href="tournaments.php" style="color:var(--muted);text-decoration:none;font-size:0.85rem;">← Back to Tournaments</a>
    </div>

    <div style="display:flex;align-items:flex-start;gap:1.5rem;flex-wrap:wrap;margin-bottom:2rem;">
        <div style="font-size:4rem;"><?= $tournament['sport_icon'] ?></div>
        <div style="flex:1">
            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:8px;">
                <h1 style="font-family:'Bebas Neue',sans-serif;font-size:2.5rem;letter-spacing:2px;"><?= htmlspecialchars($tournament['name']) ?></h1>
                <?php $sc = ['upcoming'=>'badge-yellow','ongoing'=>'badge-green','completed'=>'badge-gray'][$tournament['status']] ?? 'badge-gray'; ?>
                <span class="badge <?=$sc?>" style="font-size:0.85rem;"><?= ucfirst($tournament['status']) ?></span>
            </div>
            <div style="display:flex;gap:1.5rem;flex-wrap:wrap;color:var(--muted);font-size:0.9rem;">
                <span>🏅 <?= htmlspecialchars($tournament['sport_name']) ?></span>
                <span>📍 <?= htmlspecialchars($tournament['location'] ?: 'TBA') ?></span>
                <span>📅 <?= date('d M Y', strtotime($tournament['start_date'])) ?> – <?= date('d M Y', strtotime($tournament['end_date'])) ?></span>
                <span>📋 <?= ucfirst(str_replace('_',' ',$tournament['format'])) ?></span>
            </div>
            <?php if($tournament['description']): ?>
            <p style="margin-top:10px;color:var(--muted);font-size:0.9rem;line-height:1.6;"><?= htmlspecialchars($tournament['description']) ?></p>
            <?php endif; ?>
        </div>
    </div>

    <div class="grid-2">
        <!-- TEAMS -->
        <div class="card">
            <div class="card-header">
                <span class="card-title">👥 Participating Teams</span>
                <span class="badge badge-blue"><?= $teams->num_rows ?> Teams</span>
            </div>
            <?php if($teams->num_rows === 0): ?>
                <p style="color:var(--muted);text-align:center;padding:2rem;">No teams registered yet.</p>
            <?php else: ?>
            <div style="display:flex;flex-direction:column;gap:8px;">
            <?php while($team = $teams->fetch_assoc()): ?>
                <div style="display:flex;align-items:center;gap:12px;padding:10px;background:var(--dark);border-radius:8px;">
                    <div style="width:36px;height:36px;border-radius:50%;background:var(--border);display:flex;align-items:center;justify-content:center;font-size:1rem;">👕</div>
                    <div>
                        <div style="font-weight:600;font-size:0.9rem;"><?= htmlspecialchars($team['name']) ?></div>
                        <div style="font-size:0.75rem;color:var(--muted);">📍 <?= htmlspecialchars($team['city'] ?: 'Unknown') ?></div>
                    </div>
                </div>
            <?php endwhile; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- MATCHES -->
        <div class="card">
            <div class="card-header">
                <span class="card-title">⚡ Match Schedule</span>
                <span class="badge badge-green"><?= $matches->num_rows ?> Matches</span>
            </div>
            <?php if($matches->num_rows === 0): ?>
                <p style="color:var(--muted);text-align:center;padding:2rem;">No matches scheduled yet.</p>
            <?php else: ?>
            <div style="display:flex;flex-direction:column;gap:8px;">
            <?php while($m = $matches->fetch_assoc()): ?>
                <?php $sc2 = ['scheduled'=>'badge-yellow','live'=>'badge-red','completed'=>'badge-gray','cancelled'=>'badge-gray'][$m['status']] ?? 'badge-gray'; ?>
                <div style="background:var(--dark);border-radius:8px;padding:12px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
                        <span style="font-size:0.75rem;color:var(--muted);"><?= $m['round_name'] ?></span>
                        <span class="badge <?=$sc2?>"><?= $m['status']==='live'?'🔴 LIVE':ucfirst($m['status']) ?></span>
                    </div>
                    <div style="display:flex;align-items:center;gap:10px;justify-content:space-between;">
                        <span style="font-weight:600;font-size:0.85rem;flex:1;"><?= htmlspecialchars($m['team1_name']) ?></span>
                        <span style="font-family:'Bebas Neue',sans-serif;font-size:1.3rem;color:var(--accent)"><?= $m['team1_score'] ?> – <?= $m['team2_score'] ?></span>
                        <span style="font-weight:600;font-size:0.85rem;flex:1;text-align:right;"><?= htmlspecialchars($m['team2_name']) ?></span>
                    </div>
                    <?php if($m['match_date']): ?>
                    <div style="font-size:0.75rem;color:var(--muted);margin-top:4px;">📅 <?= date('d M, g:i A', strtotime($m['match_date'])) ?></div>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- STANDINGS (Round Robin only) -->
    <?php if(!empty($standings)): ?>
    <div class="card" style="margin-top:1.5rem;">
        <div class="card-header"><span class="card-title">📊 Standings</span></div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>#</th><th>Team</th><th>P</th><th>W</th><th>D</th><th>L</th><th>GF</th><th>GA</th><th>PTS</th></tr>
                </thead>
                <tbody>
                <?php $rank=1; foreach($standings as $tid => $s): ?>
                    <?php $tname = $conn->query("SELECT name FROM teams WHERE id=$tid")->fetch_assoc()['name'] ?? ''; ?>
                    <tr>
                        <td><?=$rank++?></td>
                        <td style="font-weight:600;"><?=htmlspecialchars($tname)?></td>
                        <td><?=$s['p']?></td><td><?=$s['w']?></td><td><?=$s['d']?></td><td><?=$s['l']?></td>
                        <td><?=$s['gf']?></td><td><?=$s['ga']?></td>
                        <td><strong style="color:var(--accent)"><?=$s['pts']?></strong></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php include 'includes/footer.php'; ?>
