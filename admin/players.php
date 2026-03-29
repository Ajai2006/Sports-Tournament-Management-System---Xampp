<?php
$pageTitle = 'Manage Players';
$isAdmin = true;
require_once '../includes/config.php';
requireAdmin();

$msg = $err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'add' || $action === 'edit') {
        $team_id = (int)$_POST['team_id'];
        $name    = sanitize($conn, $_POST['full_name']);
        $jersey  = (int)$_POST['jersey_number'];
        $pos     = sanitize($conn, $_POST['position']);
        $dob     = sanitize($conn, $_POST['date_of_birth']);
        $nat     = sanitize($conn, $_POST['nationality']);
        if (!$name || !$team_id) { $err = 'Player name and team are required.'; }
        else {
            if ($action === 'add') {
                $conn->query("INSERT INTO players (team_id,full_name,jersey_number,position,date_of_birth,nationality) VALUES ($team_id,'$name',$jersey,'$pos','$dob','$nat')");
                $msg = 'Player added!';
            } else {
                $id = (int)$_POST['id'];
                $conn->query("UPDATE players SET team_id=$team_id,full_name='$name',jersey_number=$jersey,position='$pos',date_of_birth='$dob',nationality='$nat' WHERE id=$id");
                $msg = 'Player updated!';
            }
        }
    }
    if ($action === 'delete') {
        $conn->query("DELETE FROM players WHERE id=".(int)$_POST['id']);
        $msg = 'Player removed.';
    }
}

$edit_player = null;
if (isset($_GET['edit'])) $edit_player = $conn->query("SELECT * FROM players WHERE id=".(int)$_GET['edit'])->fetch_assoc();

$filter_team = isset($_GET['team']) ? (int)$_GET['team'] : 0;
$where = $filter_team ? "WHERE p.team_id=$filter_team" : "";

$players = $conn->query("SELECT p.*, t.name as team_name FROM players p JOIN teams t ON p.team_id=t.id $where ORDER BY t.name, p.full_name");
$teams = $conn->query("SELECT * FROM teams ORDER BY name");
?>
<?php include '../includes/header.php'; ?>
<div class="container">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem;">
        <div>
            <h1 class="page-title">PLAYERS</h1>
            <p class="page-subtitle">Manage team rosters</p>
        </div>
        <a href="?action=add" class="btn btn-primary">+ Add Player</a>
    </div>

    <?php if($msg): ?><div class="alert alert-success">✅ <?=$msg?></div><?php endif; ?>
    <?php if($err): ?><div class="alert alert-error">⚠️ <?=$err?></div><?php endif; ?>

    <?php if(isset($_GET['action']) || $edit_player): ?>
    <div class="card" style="margin-bottom:2rem;">
        <div class="card-header"><span class="card-title"><?=$edit_player?'✏️ Edit':'➕ Add'?> Player</span><a href="players.php" class="btn btn-secondary btn-sm">✕</a></div>
        <form method="POST">
            <input type="hidden" name="action" value="<?=$edit_player?'edit':'add'?>">
            <?php if($edit_player): ?><input type="hidden" name="id" value="<?=$edit_player['id']?>"><?php endif; ?>
            <div class="grid-2">
                <div class="form-group" style="grid-column:span 2">
                    <label class="form-label">Team *</label>
                    <select name="team_id" class="form-control" required>
                        <option value="">Select Team</option>
                        <?php $teams->data_seek(0); while($t=$teams->fetch_assoc()): ?>
                        <option value="<?=$t['id']?>" <?=($edit_player['team_id']??$filter_team)==$t['id']?'selected':''?>><?=htmlspecialchars($t['name'])?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Full Name *</label>
                    <input type="text" name="full_name" class="form-control" required placeholder="John Smith" value="<?=htmlspecialchars($edit_player['full_name']??'')?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Jersey Number</label>
                    <input type="number" name="jersey_number" class="form-control" min="1" max="99" placeholder="10" value="<?=$edit_player['jersey_number']??''?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Position</label>
                    <input type="text" name="position" class="form-control" placeholder="Forward, Goalkeeper, etc." value="<?=htmlspecialchars($edit_player['position']??'')?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Date of Birth</label>
                    <input type="date" name="date_of_birth" class="form-control" value="<?=$edit_player['date_of_birth']??''?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Nationality</label>
                    <input type="text" name="nationality" class="form-control" placeholder="Indian" value="<?=htmlspecialchars($edit_player['nationality']??'')?>">
                </div>
            </div>
            <button type="submit" class="btn btn-primary"><?=$edit_player?'💾 Update':'➕ Add'?> Player</button>
        </form>
    </div>
    <?php endif; ?>

    <!-- FILTER -->
    <form method="GET" style="display:flex;gap:1rem;margin-bottom:1.5rem;flex-wrap:wrap;">
        <select name="team" class="form-control" style="width:auto;" onchange="this.form.submit()">
            <option value="">All Teams</option>
            <?php $teams->data_seek(0); while($t=$teams->fetch_assoc()): ?>
            <option value="<?=$t['id']?>" <?=$filter_team==$t['id']?'selected':''?>><?=htmlspecialchars($t['name'])?></option>
            <?php endwhile; ?>
        </select>
        <?php if($filter_team): ?><a href="players.php" class="btn btn-secondary">✕ Clear</a><?php endif; ?>
    </form>

    <div class="card">
        <div class="table-wrap">
        <table>
            <thead><tr><th>#</th><th>Player</th><th>Team</th><th>Position</th><th>Jersey</th><th>Nationality</th><th>Actions</th></tr></thead>
            <tbody>
            <?php $i=1; if($players->num_rows === 0): ?>
                <tr><td colspan="7" style="text-align:center;padding:3rem;color:var(--muted);">No players found.</td></tr>
            <?php else: while($p=$players->fetch_assoc()): ?>
                <tr>
                    <td style="color:var(--muted)"><?=$i++?></td>
                    <td><strong>🏃 <?=htmlspecialchars($p['full_name'])?></strong></td>
                    <td><?=htmlspecialchars($p['team_name'])?></td>
                    <td><span class="badge badge-blue"><?=htmlspecialchars($p['position']??'—')?></span></td>
                    <td style="font-family:'Bebas Neue',sans-serif;font-size:1.2rem;color:var(--accent)"><?=$p['jersey_number']?:'—'?></td>
                    <td><?=htmlspecialchars($p['nationality']??'—')?></td>
                    <td>
                        <div style="display:flex;gap:6px;">
                            <a href="?edit=<?=$p['id']?>" class="btn btn-secondary btn-sm">✏️</a>
                            <form method="POST" style="margin:0;" onsubmit="return confirm('Remove player?')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?=$p['id']?>">
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
