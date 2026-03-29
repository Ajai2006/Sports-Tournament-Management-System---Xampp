<?php
$pageTitle = 'Standings';
require_once 'includes/config.php';

$tournaments = $conn->query("SELECT t.*, s.name as sport_name, s.icon FROM tournaments t LEFT JOIN sports s ON t.sport_id=s.id ORDER BY t.start_date DESC");
$selected_tid = isset($_GET['tournament']) ? (int)$_GET['tournament'] : 0;

$standings = [];
$tournament_info = null;

if ($selected_tid) {
    $tournament_info = $conn->query("SELECT t.*,s.name as sport_name,s.icon FROM tournaments t LEFT JOIN sports s ON t.sport_id=s.id WHERE t.id=$selected_tid")->fetch_assoc();
    
    $all_matches = $conn->query("SELECT * FROM matches WHERE tournament_id=$selected_tid AND status='completed'");
    $team_ids = $conn->query("SELECT team_id FROM tournament_teams WHERE tournament_id=$selected_tid");
    
    while ($tr = $team_ids->fetch_assoc()) {
        $tid = $tr['team_id'];
        $standings[$tid] = ['p'=>0,'w'=>0,'d'=>0,'l'=>0,'gf'=>0,'ga'=>0,'pts'=>0];
    }
    
    while($m = $all_matches->fetch_assoc()){
        foreach([$m['team1_id'],$m['team2_id']] as $tid){
            if(isset($standings[$tid])) $standings[$tid]['p']++;
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
        if(isset($standings[$m['team1_id']])){$standings[$m['team1_id']]['gf']+=$m['team1_score']; $standings[$m['team1_id']]['ga']+=$m['team2_score'];}
        if(isset($standings[$m['team2_id']])){$standings[$m['team2_id']]['gf']+=$m['team2_score']; $standings[$m['team2_id']]['ga']+=$m['team1_score'];}
    }
    
    uasort($standings, fn($a,$b) => $b['pts']!=$a['pts'] ? $b['pts']-$a['pts'] : ($b['gf']-$b['ga'])-($a['gf']-$a['ga']));
}
?>
<?php include 'includes/header.php'; ?>
<div class="container">
    <div class="page-header">
        <h1 class="page-title">STANDINGS</h1>
        <p class="page-subtitle">Tournament leaderboards and rankings</p>
    </div>

    <form method="GET" style="margin-bottom:2rem;">
        <select name="tournament" class="form-control" style="max-width:400px;" onchange="this.form.submit()">
            <option value="">Select a Tournament...</option>
            <?php while($t=$tournaments->fetch_assoc()): ?>
            <option value="<?=$t['id']?>" <?=$selected_tid==$t['id']?'selected':''?>><?=$t['icon']?> <?=htmlspecialchars($t['name'])?></option>
            <?php endwhile; ?>
        </select>
    </form>

    <?php if($tournament_info): ?>
    <div style="margin-bottom:1.5rem;">
        <h2 style="font-family:'Bebas Neue',sans-serif;font-size:1.8rem;letter-spacing:2px;"><?=$tournament_info['icon']?> <?=htmlspecialchars($tournament_info['name'])?></h2>
        <p style="color:var(--muted);">📅 <?=date('d M Y',strtotime($tournament_info['start_date']))?> – <?=date('d M Y',strtotime($tournament_info['end_date']))?></p>
    </div>

    <?php if(empty($standings)): ?>
        <div class="empty-state"><div class="empty-state-icon">📊</div><h3>No data yet</h3><p>Standings will appear once matches are completed.</p></div>
    <?php else: ?>
    <div class="card">
        <div class="table-wrap">
        <table>
            <thead>
                <tr><th style="width:40px">#</th><th>Team</th><th>P</th><th>W</th><th>D</th><th>L</th><th>GF</th><th>GA</th><th>GD</th><th>PTS</th></tr>
            </thead>
            <tbody>
            <?php $rank=1; foreach($standings as $tid => $s):
                $tname = $conn->query("SELECT name FROM teams WHERE id=$tid")->fetch_assoc()['name'] ?? 'Unknown';
                $gd = $s['gf'] - $s['ga'];
                $rowBg = $rank===1 ? 'rgba(245,197,24,0.05)' : ($rank<=3 ? 'rgba(41,201,110,0.03)' : '');
            ?>
            <tr style="background:<?=$rowBg?>">
                <td>
                    <?php if($rank===1): ?>🥇<?php elseif($rank===2): ?>🥈<?php elseif($rank===3): ?>🥉
                    <?php else: ?><span style="color:var(--muted)"><?=$rank?></span><?php endif; ?>
                </td>
                <td style="font-weight:<?=$rank<=3?'700':'400'?>;"><?=htmlspecialchars($tname)?></td>
                <td><?=$s['p']?></td>
                <td><strong style="color:var(--green)"><?=$s['w']?></strong></td>
                <td><?=$s['d']?></td>
                <td><span style="color:var(--red)"><?=$s['l']?></span></td>
                <td><?=$s['gf']?></td>
                <td><?=$s['ga']?></td>
                <td style="color:<?=$gd>0?'var(--green)':($gd<0?'var(--red)':'var(--muted)')?>"><?=$gd>0?'+':''?><?=$gd?></td>
                <td><strong style="font-family:'Bebas Neue',sans-serif;font-size:1.3rem;color:var(--accent)"><?=$s['pts']?></strong></td>
            </tr>
            <?php $rank++; endforeach; ?>
            </tbody>
        </table>
        </div>
    </div>
    <?php endif; ?>
    <?php else: ?>
        <div class="empty-state"><div class="empty-state-icon">📊</div><h3>Select a tournament</h3><p>Choose a tournament above to see standings.</p></div>
    <?php endif; ?>
</div>
<?php include 'includes/footer.php'; ?>
