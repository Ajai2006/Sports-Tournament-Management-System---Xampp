# 🏆 TourneyPro — Sports Tournament Management System
## Built with PHP + MySQL + XAMPP

---

## 📋 FEATURES

### Public Pages
- **Home** — Dashboard with stats, live matches, tournament listings
- **Tournaments** — Browse all tournaments with sport/status filters
- **Tournament Detail** — Teams, match schedule, standings (round-robin)
- **Teams** — View all registered teams
- **Standings** — Live leaderboards per tournament

### Admin Panel
- **Dashboard** — Overview stats, quick actions, recent activity
- **Tournaments** — Full CRUD: Create, edit, delete tournaments; manage team registrations
- **Teams** — Register/edit/delete teams
- **Matches** — Schedule matches, update live scores, set winners
- **Players** — Add players to teams with jersey numbers and positions

---

## 🚀 SETUP INSTRUCTIONS

### Step 1 — Start XAMPP
1. Open XAMPP Control Panel
2. Click **Start** for **Apache**
3. Click **Start** for **MySQL**

### Step 2 — Create the Database
1. Open your browser → `http://localhost/phpmyadmin`
2. Click **Import** tab
3. Click **Choose File** → select `database.sql`
4. Click **Go**

### Step 3 — Copy Project Files
Copy the entire `sports_tournament` folder to:
```
C:\xampp\htdocs\sports_tournament\
```

### Step 4 — Open the App
- **Public Site:** `http://localhost/sports_tournament/`
- **Admin Panel:** `http://localhost/sports_tournament/admin/login.php`

### Default Admin Login
```
Username: admin
Password: admin123
```

---

## 📁 FILE STRUCTURE

```
sports_tournament/
├── index.php              ← Public home page
├── tournaments.php        ← Browse tournaments
├── tournament_detail.php  ← Individual tournament view
├── teams.php              ← Browse teams
├── standings.php          ← Leaderboards
├── database.sql           ← Database setup (import this first!)
│
├── includes/
│   ├── config.php         ← DB connection & utilities
│   ├── header.php         ← Navigation + CSS
│   └── footer.php         ← Footer HTML
│
└── admin/
    ├── login.php           ← Admin login
    ├── logout.php          ← Session destroy
    ├── dashboard.php       ← Admin home
    ├── tournaments.php     ← Manage tournaments
    ├── teams.php           ← Manage teams
    ├── matches.php         ← Schedule & score matches
    ├── players.php         ← Manage players
    └── ajax_teams.php      ← AJAX: get teams by tournament
```

---

## 🎮 HOW TO USE

### Creating a Tournament
1. Admin Panel → Tournaments → **+ New Tournament**
2. Fill in name, sport, format (knockout/round-robin/group stage), dates, location
3. Click **Create Tournament**

### Registering Teams
1. On Tournaments page, click **👥 Teams** for any tournament
2. Select team from dropdown → **+ Add**

### Scheduling Matches
1. Admin → Matches → **+ Schedule Match**
2. Select tournament (teams auto-load from that tournament)
3. Pick teams, date, venue, round name

### Updating Scores
1. Matches page → Click **⚽ Score** button on any match
2. Enter scores, set status (Live / Completed)
3. Winner is auto-determined when marked Completed

---

## 🔒 SECURITY NOTES
- Change admin password after setup via phpMyAdmin
- Uses PHP `password_verify()` for password checking
- All inputs sanitized before DB queries
- Session-based admin authentication
