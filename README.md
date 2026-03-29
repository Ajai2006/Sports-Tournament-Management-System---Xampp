# 🏆 Sports Tournament Management System

A full-stack web application built with **PHP**, **MySQL**, and **XAMPP** for managing sports tournaments, teams, players, and match results — with a public-facing site and a secure admin panel.

---

## 📸 Preview

| Home Page | Admin Dashboard | Standings |
|-----------|----------------|-----------|
| Tournament cards with live status badges | Stats overview, recent matches & tournaments | Auto-calculated leaderboard with gold/silver/bronze |

---

## ✨ Features

### 🌐 Public Site
- **Home Page** — Live stats (tournaments, teams, matches, live count) and tournament cards
- **Tournaments** — Browse all tournaments filtered by sport and status
- **Tournament Detail** — Team roster, match schedule, and live standings
- **Teams** — View all registered teams with player counts
- **Standings** — Leaderboard with points, wins, draws, losses, and goal difference

### 🔐 Admin Panel
- **Dashboard** — Quick stats and recent activity overview
- **Tournament Management** — Create, edit, delete tournaments; manage team registrations
- **Team Management** — Register teams with coach and contact details
- **Match Scheduling** — Schedule matches, update live scores, auto-detect winners
- **Player Management** — Add players with jersey numbers, positions, and personal info
- **Secure Login** — Session-based authentication (username: `admin`, password: `admin`)

---

## 🛠️ Tech Stack

| Layer | Technology |
|-------|-----------|
| Frontend | HTML, CSS |
| Backend | PHP 8 |
| Database | MySQL |
| Server | Apache (via XAMPP) |
| Auth | PHP Sessions |

---

## 📁 Project Structure

```
sports_tournament/
├── index.php                  # Public home page
├── tournaments.php            # Browse all tournaments
├── tournament_detail.php      # Individual tournament view
├── teams.php                  # Browse all teams
├── standings.php              # Live leaderboards
├── database.sql               # Database setup (import this first!)
│
├── includes/
│   ├── config.php             # DB connection & utility functions
│   ├── header.php             # Navigation + global CSS
│   └── footer.php             # Footer HTML
│
└── admin/
    ├── login.php              # Admin login
    ├── logout.php             # Session destroy
    ├── dashboard.php          # Admin home
    ├── tournaments.php        # Manage tournaments + team registration
    ├── teams.php              # Manage teams
    ├── matches.php            # Schedule & score matches
    ├── players.php            # Manage players
    └── ajax_teams.php         # AJAX: fetch teams by tournament
```

---

## 🗄️ Database Schema

```
sports            → id, name, icon
tournaments       → id, name, sport_id, format, start_date, end_date, location, max_teams, status
teams             → id, name, coach, contact_email, contact_phone, city
tournament_teams  → id, tournament_id (FK), team_id (FK)
players           → id, team_id (FK), full_name, jersey_number, position, date_of_birth, nationality
matches           → id, tournament_id (FK), team1_id (FK), team2_id (FK), match_date, venue, round_name, team1_score, team2_score, winner_id (FK), status
admin_users       → id, username, password
```

---

## 🚀 Getting Started

### Prerequisites
- [XAMPP](https://www.apachefriends.org/) installed (Apache + MySQL)

### Installation

**1. Clone the repository**
```bash
git clone https://github.com/your-username/sports-tournament-management.git
```

**2. Copy to XAMPP's web root**
```
Copy the sports_tournament/ folder to:
C:\xampp\htdocs\sports_tournament\
```

**3. Import the database**
- Start XAMPP → Start **Apache** and **MySQL**
- Open [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
- Click **Import** → Choose `database.sql` → Click **Go**

**4. Open the app**

| Page | URL |
|------|-----|
| Public Site | http://localhost/sports_tournament/ |
| Admin Panel | http://localhost/sports_tournament/admin/login.php |

**5. Login**
```
Username: admin
Password: admin
```

---

## 📦 Sample Data Included

The `database.sql` file comes pre-loaded with:

- **8 Sports** — Football, Cricket, Basketball, Tennis, Volleyball, Badminton, Table Tennis, Chess
- **8 Teams** — Thunder Eagles, Royal Warriors, Storm United, Iron Lions, Blaze FC, Phoenix Rising, Silver Sharks, Golden Hawks
- **66 Players** — 11 players per team with jersey numbers and positions
- **3 Tournaments**
  - ⚽ City Football Championship 2025 *(Round Robin — Ongoing)*
  - 🏏 State Cricket Cup 2025 *(Knockout — Upcoming)*
  - 🏀 Inter-City Basketball League *(Round Robin — Completed)*
- **31 Matches** — Including completed results, a live match, and scheduled fixtures

---

## 📋 Tournament Formats Supported

| Format | Description |
|--------|-------------|
| **Knockout** | Single elimination — loser is out |
| **Round Robin** | Every team plays every other team; standings auto-calculated |
| **Group Stage** | Teams divided into groups |

---

## 🔑 Admin Credentials

```
Username : admin
Password : admin
```

> You can update the password directly in the `admin_users` table via phpMyAdmin.

---

## 📌 Usage Guide

### Creating a Tournament
1. Admin Panel → **Tournaments** → `+ New Tournament`
2. Fill in name, sport, format, dates, location, max teams
3. Click **Create Tournament**

### Registering Teams
1. Tournaments page → Click **👥 Teams** on any tournament
2. Select a team from the dropdown → Click **+ Add**

### Scheduling a Match
1. Admin → **Matches** → `+ Schedule Match`
2. Select tournament (teams auto-load)
3. Pick both teams, date, venue, round name

### Updating a Score
1. Matches page → Click **⚽ Score** on any match
2. Enter scores, set status to **Live** or **Completed**
3. Winner is auto-determined on completion

---

## 🤝 Contributing

Pull requests are welcome! For major changes, please open an issue first to discuss what you would like to change.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/new-feature`)
3. Commit your changes (`git commit -m 'Add new feature'`)
4. Push to the branch (`git push origin feature/new-feature`)
5. Open a Pull Request

---


---

## 👤 Author

**Ajai K**


---

> Built as part of **23CY205T - Database Management Systems and Security** | Assignment III | Academic Year 2025–2026
