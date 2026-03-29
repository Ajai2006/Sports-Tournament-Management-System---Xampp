<?php
$pageTitle = 'Manage Matches';
$isAdmin = true;
require_once '../includes/config.php';
requireAdmin();

$msg = $err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $tid      = (int)$_POST['tournament_id'];
        $team1    = (int)$_POST['team1_id'];
        $team2    = (int)$_POST['team2_id'];
        $date     = sanitize($conn, $_POST['match_date']);
        $venue    = sanitize($conn, $_POST['venue']);
        $round    = sanitize($conn, $_POST['round_name']);
        $status   = sanitize($conn, $_POST['status']);
        if ($team1 === $team2) $err = 'A team cannot play against itself!';
        elseif (!$tid || !$team1 || !$team2) $err = 'Select tournament and both teams.';
        else {
            $conn->query("INSERT INTO matches (tournament_id,team1_id,team2_id,match_date,venue,round_name,status) VALUES ($tid,$team1,$team2,'$date','$venue','$round','$status')");
            $msg = 'Match scheduled!';
        }
    }

    if ($action === 'update_score') {
        $id      = (int)$_POST['id'];
        $s1      = (int)$_POST['team1_score'];
        $s2      = (int)$_POST['team2_score'];
        $status  = sanitize($conn, $_POST['status']);
        $winner  = 'NULL';
        if ($status === 'completed') {
            $match = $conn->query("SELECT * FROM matches WHERE id=$id")->fetch_assoc();
            if ($s1 > $s2) $winner = $match['team1_id'];
            elseif ($s2 > $s1) $winner = $match['team2_id'];
        }
        $conn->query("UPDATE matches SET team1_score=$s1,team2_score=$s2,status='$status',winner_id=$winner WHERE id=$id");
        $msg = 'Score updated!';
    }

    if ($action === 'delete') {
        $id = (int)$_POST['id'];
        $conn->query("DELETE FROM matches WHERE id=$id");
        $msg = 'Match deleted.';
    }
}

$score_match = null;
if (isset($_GET['edit'])) $score_match = $conn->query("SELECT m.*,t1.name as t1,t2.name as t2 FROM matches m JOIN teams t1 ON m.team1_id=t1.id JOIN teams t2 ON m.team2_id=t2.id WHERE m.id=".(int)$_GET['edit'])->fetch_assoc();

$tournaments = $conn->query("SELECT * FROM tournaments ORDER BY name");
$filter_tid = isset($_GET['tournament']) ? (int)$_GET['tournament'] : 0;
$where = $filter_tid ? "WHERE m.tournament_id=$filter_tid" : "";

$matches = $conn->query("
    SELECT m.*, t1.name as t1, t2.name as t2, w.name as winner, tr.name as tname
    FROM matches m
    JOIN teams t1 ON m.team1_id=t1.id
    JOIN teams t2 ON m.team2_id=t2.id
    JOIN tournaments tr ON m.tournament_id=tr.id
    LEFT JOIN teams w ON m.winner_id=w.id
    $where
    ORDER BY m.match_date DESC, m.id DESC
");
?>
<?php include '../includes/header.php'; ?>
<div class="container">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem;">
        <div>
            <h1 class="page-title">MATCHES</h1>
            <p class="page-subtitle">Schedule and update match results</p>
        </div>
        <a href="?action=add" class="btn btn-primary">+ Schedule Match</a>
    </div>

    <?php if($msg): ?><div class="alert alert-success">✅ <?=$msg?></div><?php endif; ?>
    <?php if($err): ?><div class="alert alert-error">⚠️ <?=$err?></div><?php endif; ?>

    <!-- SCHEDULE MATCH FORM -->
    <?php if(isset($_GET['action']) && $_GET['action']==='add'): ?>
    <div class="card" style="margin-bottom:2rem;">
        <div class="card-header"><span class="card-title">➕ Schedule New Match</span><a href="matches.php" class="btn btn-secondary btn-sm">✕</a></div>
        <form method="POST">
            <input type="hidden" name="action" value="add">
            <div class="grid-2">
                <div class="form-group" style="grid-column:span 2">
                    <label class="form-label">Tournament *</label>
                    <select name="tournament_id" id="tid_sel" class="form-control" required onchange="loadTeams(this.value)">
                        <option value="">Select Tournament</option>
                        <?php $tournaments->data_seek(0); while($t=$tournaments->fetch_assoc()): ?>
                        <option value="<?=$t['id']?>"><?=htmlspecialchars($t['name'])?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Team 1 *</label>
                    <select name="team1_id" id="team1" class="form-control" required>
                        <option value="">Select tournament first</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Team 2 *</label>
                    <select name="team2_id" id="team2" class="form-control" required>
                        <option value="">Select tournament first</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Match Date & Time</label>
                    <input type="datetime-local" name="match_date" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">Venue</label>
                    <input type="text" name="venue" class="form-control" placeholder="City Stadium">
                </div>
                <div class="form-group">
                    <label class="form-label">Round</label>
                    <input type="text" name="round_name" class="form-control" placeholder="Quarter Final" value="Round 1">
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="scheduled">Scheduled</option>
                        <option value="live">Live</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">📅 Schedule Match</button>
        </form>
    </div>
    <script>
    function loadTeams(tid) {
        if(!tid) return;
        fetch('ajax_teams.php?tournament_id=' + tid)
            .then(r=>r.json())
            .then(teams => {
                const opts = '<option value="">Select team</option>' + teams.map(t=>`<option value="${t.id}">${t.name}</option>`).join('');
                document.getElementById('team1').innerHTML = opts;
                document.getElementById('team2').innerHTML = opts;
            });
    }
    </script>
    <?php endif; ?>

    <!-- UPDATE SCORE FORM -->
    <?php if($score_match): ?>
    <div class="card" style="margin-bottom:2rem;">
        <div class="card-header"><span class="card-title">⚽ Update Score: <?=htmlspecialchars($score_match['t1'])?> vs <?=htmlspecialchars($score_match['t2'])?></span><a href="matches.php" class="btn btn-secondary btn-sm">✕</a></div>
        <form method="POST">
            <input type="hidden" name="action" value="update_score">
            <input type="hidden" name="id" value="<?=$score_match['id']?>">
            <div style="display:flex;align-items:center;gap:1.5rem;flex-wrap:wrap;margin-bottom:1.5rem;">
                <div style="flex:1;min-width:120px;">
                    <label class="form-label"><?=htmlspecialchars($score_match['t1'])?> Score</label>
                    <input type="number" name="team1_score" class="form-control" min="0" value="<?=$score_match['team1_score']?>" style="font-family:'Bebas Neue',sans-serif;font-size:2rem;text-align:center;">
                </div>
                <div style="font-family:'Bebas Neue',sans-serif;font-size:2rem;color:var(--muted);margin-top:20px;">VS</div>
                <div style="flex:1;min-width:120px;">
                    <label class="form-label"><?=htmlspecialchars($score_match['t2'])?> Score</label>
                    <input type="number" name="team2_score" class="form-control" min="0" value="<?=$score_match['team2_score']?>" style="font-family:'Bebas Neue',sans-serif;font-size:2rem;text-align:center;">
                </div>
                <div style="flex:1;min-width:120px;">
                    <label class="form-label">Match Status</label>
                    <select name="status" class="form-control">
                        <?php foreach(['scheduled','live','completed','cancelled'] as $st): ?>
                        <option value="<?=$st?>" <?=$score_match['status']===$st?'selected':''?>><?=ucfirst($st)?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">💾 Update Score</button>
        </form>
    </div>
    <?php endif; ?>

    <!-- FILTER -->
    <form method="GET" style="display:flex;gap:1rem;margin-bottom:1.5rem;flex-wrap:wrap;">
        <select name="tournament" class="form-control" style="width:auto;" onchange="this.form.submit()">
            <option value="">All Tournaments</option>
            <?php $tournaments->data_seek(0); while($t=$tournaments->fetch_assoc()): ?>
            <option value="<?=$t['id']?>" <?=$filter_tid==$t['id']?'selected':''?>><?=htmlspecialchars($t['name'])?></option>
            <?php endwhile; ?>
        </select>
        <?php if($filter_tid): ?><a href="matches.php" class="btn btn-secondary">✕ Clear</a><?php endif; ?>
    </form>

    <!-- MATCHES TABLE -->
    <div class="card">
        <div class="table-wrap">
        <table>
            <thead><tr><th>Tournament</th><th>Round</th><th>Teams</th><th>Score</th><th>Date</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
            <?php if($matches->num_rows === 0): ?>
                <tr><td colspan="7" style="text-align:center;padding:3rem;color:var(--muted);">No matches. Schedule one above!</td></tr>
            <?php else: while($m=$matches->fetch_assoc()): ?>
                <?php $sc=['scheduled'=>'badge-yellow','live'=>'badge-red','completed'=>'badge-gray','cancelled'=>'badge-gray'][$m['status']]??'badge-gray'; ?>
                <tr>
                    <td style="font-size:0.82rem;"><?=htmlspecialchars($m['tname'])?></td>
                    <td><span class="badge badge-blue"><?=htmlspecialchars($m['round_name'])?></span></td>
                    <td>
                        <div style="font-weight:600;"><?=htmlspecialchars($m['t1'])?></div>
                        <div style="color:var(--muted);font-size:0.8rem;">vs <?=htmlspecialchars($m['t2'])?></div>
                    </td>
                    <td style="font-family:'Bebas Neue',sans-serif;font-size:1.4rem;color:var(--accent);"><?=$m['team1_score']?> – <?=$m['team2_score']?></td>
                    <td style="font-size:0.82rem;"><?=$m['match_date']?date('d M, g:iA',strtotime($m['match_date'])):'—'?></td>
                    <td><span class="badge <?=$sc?>"><?=$m['status']==='live'?'🔴 ':''?><?=ucfirst($m['status'])?></span></td>
                    <td>
                        <div style="display:flex;gap:6px;">
                            <a href="?edit=<?=$m['id']?>" class="btn btn-primary btn-sm">⚽ Score</a>
                            <form method="POST" style="margin:0;" onsubmit="return confirm('Delete match?')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?=$m['id']?>">
                                <button class="btn btn-danger btn-sm">🗑️</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endwhile; endif; ?>
            </tbody>
        </table>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
