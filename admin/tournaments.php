<?php
$pageTitle = 'Manage Tournaments';
$isAdmin = true;
require_once '../includes/config.php';
requireAdmin();

$msg = $err = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add' || $action === 'edit') {
        $name       = sanitize($conn, $_POST['name']);
        $sport_id   = (int)$_POST['sport_id'];
        $format     = sanitize($conn, $_POST['format']);
        $start_date = sanitize($conn, $_POST['start_date']);
        $end_date   = sanitize($conn, $_POST['end_date']);
        $location   = sanitize($conn, $_POST['location']);
        $max_teams  = (int)$_POST['max_teams'];
        $status     = sanitize($conn, $_POST['status']);
        $description= sanitize($conn, $_POST['description']);

        if (!$name || !$start_date || !$end_date) { $err = 'Name and dates are required.'; }
        else {
            if ($action === 'add') {
                $conn->query("INSERT INTO tournaments (name,sport_id,format,start_date,end_date,location,max_teams,status,description) VALUES ('$name',$sport_id,'$format','$start_date','$end_date','$location',$max_teams,'$status','$description')");
                $msg = 'Tournament created successfully!';
            } else {
                $id = (int)$_POST['id'];
                $conn->query("UPDATE tournaments SET name='$name',sport_id=$sport_id,format='$format',start_date='$start_date',end_date='$end_date',location='$location',max_teams=$max_teams,status='$status',description='$description' WHERE id=$id");
                $msg = 'Tournament updated!';
            }
        }
    }

    if ($action === 'delete') {
        $id = (int)$_POST['id'];
        $conn->query("DELETE FROM tournaments WHERE id=$id");
        $msg = 'Tournament deleted.';
    }

    if ($action === 'add_team') {
        $tid = (int)$_POST['tournament_id'];
        $team_id = (int)$_POST['team_id'];
        $conn->query("INSERT IGNORE INTO tournament_teams (tournament_id, team_id) VALUES ($tid, $team_id)");
        $msg = 'Team added to tournament!';
    }

    if ($action === 'remove_team') {
        $tid = (int)$_POST['tournament_id'];
        $team_id = (int)$_POST['team_id'];
        $conn->query("DELETE FROM tournament_teams WHERE tournament_id=$tid AND team_id=$team_id");
        $msg = 'Team removed.';
    }
}

$edit_tournament = null;
if (isset($_GET['edit'])) {
    $edit_tournament = $conn->query("SELECT * FROM tournaments WHERE id=".(int)$_GET['edit'])->fetch_assoc();
}

$manage_teams_tournament = null;
if (isset($_GET['manage_teams'])) {
    $manage_teams_tournament = $conn->query("SELECT t.*,s.name as sport_name FROM tournaments t LEFT JOIN sports s ON t.sport_id=s.id WHERE t.id=".(int)$_GET['manage_teams'])->fetch_assoc();
}

$tournaments = $conn->query("SELECT t.*,s.name as sport_name,s.icon,(SELECT COUNT(*) FROM tournament_teams tt WHERE tt.tournament_id=t.id) as team_count FROM tournaments t LEFT JOIN sports s ON t.sport_id=s.id ORDER BY t.start_date DESC");
$sports = $conn->query("SELECT * FROM sports ORDER BY name");
$all_teams = $conn->query("SELECT * FROM teams ORDER BY name");
?>
<?php include '../includes/header.php'; ?>
<div class="container">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem;">
        <div>
            <h1 class="page-title">TOURNAMENTS</h1>
            <p class="page-subtitle">Create and manage all tournaments</p>
        </div>
        <a href="?action=add" class="btn btn-primary">+ New Tournament</a>
    </div>

    <?php if($msg): ?><div class="alert alert-success">✅ <?=$msg?></div><?php endif; ?>
    <?php if($err): ?><div class="alert alert-error">⚠️ <?=$err?></div><?php endif; ?>

    <!-- ADD/EDIT FORM -->
    <?php if(isset($_GET['action']) || $edit_tournament): ?>
    <div class="card" style="margin-bottom:2rem;">
        <div class="card-header">
            <span class="card-title"><?=$edit_tournament?'✏️ Edit':'➕ New'?> Tournament</span>
            <a href="tournaments.php" class="btn btn-secondary btn-sm">✕ Cancel</a>
        </div>
        <form method="POST">
            <input type="hidden" name="action" value="<?=$edit_tournament?'edit':'add'?>">
            <?php if($edit_tournament): ?><input type="hidden" name="id" value="<?=$edit_tournament['id']?>"><?php endif; ?>
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Tournament Name *</label>
                    <input type="text" name="name" class="form-control" required placeholder="City Championship 2025" value="<?=htmlspecialchars($edit_tournament['name']??'')?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Sport</label>
                    <select name="sport_id" class="form-control">
                        <option value="0">Select Sport</option>
                        <?php $sports->data_seek(0); while($s=$sports->fetch_assoc()): ?>
                        <option value="<?=$s['id']?>" <?=($edit_tournament['sport_id']??'')==$s['id']?'selected':''?>><?=$s['icon']?> <?=htmlspecialchars($s['name'])?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Format</label>
                    <select name="format" class="form-control">
                        <?php foreach(['knockout','round_robin','group_stage'] as $f): ?>
                        <option value="<?=$f?>" <?=($edit_tournament['format']??'')===$f?'selected':''?>><?=ucfirst(str_replace('_',' ',$f))?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <?php foreach(['upcoming','ongoing','completed'] as $st): ?>
                        <option value="<?=$st?>" <?=($edit_tournament['status']??'')===$st?'selected':''?>><?=ucfirst($st)?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Start Date *</label>
                    <input type="date" name="start_date" class="form-control" required value="<?=$edit_tournament['start_date']??''?>">
                </div>
                <div class="form-group">
                    <label class="form-label">End Date *</label>
                    <input type="date" name="end_date" class="form-control" required value="<?=$edit_tournament['end_date']??''?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Location</label>
                    <input type="text" name="location" class="form-control" placeholder="City Stadium" value="<?=htmlspecialchars($edit_tournament['location']??'')?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Max Teams</label>
                    <input type="number" name="max_teams" class="form-control" min="2" max="256" value="<?=$edit_tournament['max_teams']??16?>">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" placeholder="Tournament details..."><?=htmlspecialchars($edit_tournament['description']??'')?></textarea>
            </div>
            <button type="submit" class="btn btn-primary"><?=$edit_tournament?'💾 Update':'➕ Create'?> Tournament</button>
        </form>
    </div>
    <?php endif; ?>

    <!-- MANAGE TEAMS PANEL -->
    <?php if($manage_teams_tournament): ?>
    <div class="card" style="margin-bottom:2rem;">
        <div class="card-header">
            <span class="card-title">👥 Teams in: <?=htmlspecialchars($manage_teams_tournament['name'])?></span>
            <a href="tournaments.php" class="btn btn-secondary btn-sm">✕ Close</a>
        </div>
        <?php
        $tid = $manage_teams_tournament['id'];
        $registered_teams = $conn->query("SELECT te.* FROM teams te JOIN tournament_teams tt ON te.id=tt.team_id WHERE tt.tournament_id=$tid ORDER BY te.name");
        $registered_ids = [];
        $rteams_data = [];
        while($rt = $registered_teams->fetch_assoc()) { $registered_ids[] = $rt['id']; $rteams_data[] = $rt; }
        ?>
        <div style="display:flex;gap:1.5rem;flex-wrap:wrap;">
            <div style="flex:1;min-width:240px;">
                <h4 style="font-size:0.85rem;color:var(--muted);margin-bottom:10px;text-transform:uppercase;letter-spacing:1px;">Registered (<?=count($rteams_data)?>)</h4>
                <?php foreach($rteams_data as $rt): ?>
                <div style="display:flex;align-items:center;gap:8px;padding:8px;background:var(--dark);border-radius:8px;margin-bottom:6px;">
                    <span style="flex:1;font-size:0.9rem;font-weight:500;">👕 <?=htmlspecialchars($rt['name'])?></span>
                    <form method="POST" style="margin:0;">
                        <input type="hidden" name="action" value="remove_team">
                        <input type="hidden" name="tournament_id" value="<?=$tid?>">
                        <input type="hidden" name="team_id" value="<?=$rt['id']?>">
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Remove team?')">✕</button>
                    </form>
                </div>
                <?php endforeach; ?>
            </div>
            <div style="flex:1;min-width:240px;">
                <h4 style="font-size:0.85rem;color:var(--muted);margin-bottom:10px;text-transform:uppercase;letter-spacing:1px;">Add Team</h4>
                <form method="POST" style="display:flex;gap:8px;">
                    <input type="hidden" name="action" value="add_team">
                    <input type="hidden" name="tournament_id" value="<?=$tid?>">
                    <select name="team_id" class="form-control" required>
                        <option value="">Select team...</option>
                        <?php $all_teams->data_seek(0); while($t=$all_teams->fetch_assoc()): ?>
                        <?php if(!in_array($t['id'],$registered_ids)): ?>
                        <option value="<?=$t['id']?>"><?=htmlspecialchars($t['name'])?></option>
                        <?php endif; ?>
                        <?php endwhile; ?>
                    </select>
                    <button type="submit" class="btn btn-primary" style="white-space:nowrap;">+ Add</button>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- TOURNAMENTS TABLE -->
    <div class="card">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>Sport</th><th>Tournament</th><th>Dates</th><th>Format</th><th>Teams</th><th>Status</th><th>Actions</th></tr>
                </thead>
                <tbody>
                <?php if($tournaments->num_rows === 0): ?>
                    <tr><td colspan="7" style="text-align:center;padding:3rem;color:var(--muted);">No tournaments yet. Create one above.</td></tr>
                <?php else: while($t=$tournaments->fetch_assoc()): ?>
                    <?php $sc=['upcoming'=>'badge-yellow','ongoing'=>'badge-green','completed'=>'badge-gray'][$t['status']]??'badge-gray'; ?>
                    <tr>
                        <td style="font-size:1.3rem;"><?=$t['icon']??'🏆'?></td>
                        <td><strong><?=htmlspecialchars($t['name'])?></strong><div style="font-size:0.78rem;color:var(--muted);"><?=htmlspecialchars($t['location'])?></div></td>
                        <td style="font-size:0.82rem;"><?=date('d M Y',strtotime($t['start_date']))?><br><span style="color:var(--muted);">→ <?=date('d M Y',strtotime($t['end_date']))?></span></td>
                        <td><span class="badge badge-blue" style="text-transform:capitalize;"><?=str_replace('_',' ',$t['format'])?></span></td>
                        <td style="text-align:center;"><strong style="color:var(--blue)"><?=$t['team_count']?></strong><span style="color:var(--muted);font-size:0.8rem;"> / <?=$t['max_teams']?></span></td>
                        <td><span class="badge <?=$sc?>"><?=ucfirst($t['status'])?></span></td>
                        <td>
                            <div style="display:flex;gap:6px;flex-wrap:wrap;">
                                <a href="?manage_teams=<?=$t['id']?>" class="btn btn-secondary btn-sm">👥 Teams</a>
                                <a href="?edit=<?=$t['id']?>" class="btn btn-secondary btn-sm">✏️</a>
                                <form method="POST" style="margin:0;" onsubmit="return confirm('Delete this tournament?')">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?=$t['id']?>">
                                    <button class="btn btn-danger btn-sm" type="submit">🗑️</button>
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
