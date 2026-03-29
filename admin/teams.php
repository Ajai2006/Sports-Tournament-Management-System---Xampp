<?php
$pageTitle = 'Manage Teams';
$isAdmin = true;
require_once '../includes/config.php';
requireAdmin();

$msg = $err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'add' || $action === 'edit') {
        $name    = sanitize($conn, $_POST['name']);
        $coach   = sanitize($conn, $_POST['coach']);
        $email   = sanitize($conn, $_POST['contact_email']);
        $phone   = sanitize($conn, $_POST['contact_phone']);
        $city    = sanitize($conn, $_POST['city']);
        if (!$name) { $err = 'Team name is required.'; }
        else {
            if ($action === 'add') {
                $conn->query("INSERT INTO teams (name,coach,contact_email,contact_phone,city) VALUES ('$name','$coach','$email','$phone','$city')");
                $msg = 'Team created!';
            } else {
                $id = (int)$_POST['id'];
                $conn->query("UPDATE teams SET name='$name',coach='$coach',contact_email='$email',contact_phone='$phone',city='$city' WHERE id=$id");
                $msg = 'Team updated!';
            }
        }
    }
    if ($action === 'delete') {
        $id = (int)$_POST['id'];
        $conn->query("DELETE FROM teams WHERE id=$id");
        $msg = 'Team deleted.';
    }
}

$edit_team = null;
if (isset($_GET['edit'])) $edit_team = $conn->query("SELECT * FROM teams WHERE id=".(int)$_GET['edit'])->fetch_assoc();

$teams = $conn->query("SELECT t.*, COUNT(DISTINCT p.id) as player_count, COUNT(DISTINCT tt.tournament_id) as tourney_count FROM teams t LEFT JOIN players p ON t.id=p.team_id LEFT JOIN tournament_teams tt ON t.id=tt.team_id GROUP BY t.id ORDER BY t.name");
?>
<?php include '../includes/header.php'; ?>
<div class="container">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem;">
        <div>
            <h1 class="page-title">TEAMS</h1>
            <p class="page-subtitle">Register and manage teams</p>
        </div>
        <a href="?action=add" class="btn btn-primary">+ Register Team</a>
    </div>

    <?php if($msg): ?><div class="alert alert-success">✅ <?=$msg?></div><?php endif; ?>
    <?php if($err): ?><div class="alert alert-error">⚠️ <?=$err?></div><?php endif; ?>

    <?php if(isset($_GET['action']) || $edit_team): ?>
    <div class="card" style="margin-bottom:2rem;">
        <div class="card-header">
            <span class="card-title"><?=$edit_team?'✏️ Edit':'➕ New'?> Team</span>
            <a href="teams.php" class="btn btn-secondary btn-sm">✕ Cancel</a>
        </div>
        <form method="POST">
            <input type="hidden" name="action" value="<?=$edit_team?'edit':'add'?>">
            <?php if($edit_team): ?><input type="hidden" name="id" value="<?=$edit_team['id']?>"><?php endif; ?>
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Team Name *</label>
                    <input type="text" name="name" class="form-control" required placeholder="Thunder Eagles" value="<?=htmlspecialchars($edit_team['name']??'')?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Coach Name</label>
                    <input type="text" name="coach" class="form-control" placeholder="Coach Smith" value="<?=htmlspecialchars($edit_team['coach']??'')?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Contact Email</label>
                    <input type="email" name="contact_email" class="form-control" placeholder="team@email.com" value="<?=htmlspecialchars($edit_team['contact_email']??'')?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Contact Phone</label>
                    <input type="text" name="contact_phone" class="form-control" placeholder="+91 98765 43210" value="<?=htmlspecialchars($edit_team['contact_phone']??'')?>">
                </div>
                <div class="form-group">
                    <label class="form-label">City</label>
                    <input type="text" name="city" class="form-control" placeholder="Mumbai" value="<?=htmlspecialchars($edit_team['city']??'')?>">
                </div>
            </div>
            <button type="submit" class="btn btn-primary"><?=$edit_team?'💾 Update':'➕ Add'?> Team</button>
        </form>
    </div>
    <?php endif; ?>

    <div class="card">
        <div class="table-wrap">
        <table>
            <thead>
                <tr><th>Team</th><th>Coach</th><th>City</th><th>Contact</th><th>Players</th><th>Tournaments</th><th>Actions</th></tr>
            </thead>
            <tbody>
            <?php if($teams->num_rows === 0): ?>
                <tr><td colspan="7" style="text-align:center;padding:3rem;color:var(--muted);">No teams yet.</td></tr>
            <?php else: while($t=$teams->fetch_assoc()): ?>
                <tr>
                    <td><strong>👕 <?=htmlspecialchars($t['name'])?></strong></td>
                    <td><?=htmlspecialchars($t['coach']??'—')?></td>
                    <td>📍 <?=htmlspecialchars($t['city']??'—')?></td>
                    <td style="font-size:0.8rem;"><?=htmlspecialchars($t['contact_email']??'')?><br><?=htmlspecialchars($t['contact_phone']??'')?></td>
                    <td><span class="badge badge-blue"><?=$t['player_count']?></span></td>
                    <td><span class="badge badge-yellow"><?=$t['tourney_count']?></span></td>
                    <td>
                        <div style="display:flex;gap:6px;">
                            <a href="players.php?team=<?=$t['id']?>" class="btn btn-secondary btn-sm">🏃 Players</a>
                            <a href="?edit=<?=$t['id']?>" class="btn btn-secondary btn-sm">✏️</a>
                            <form method="POST" style="margin:0;" onsubmit="return confirm('Delete team?')">
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
