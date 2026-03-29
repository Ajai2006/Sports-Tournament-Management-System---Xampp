-- Sports Tournament Management System Database
-- Run this in phpMyAdmin or MySQL CLI

CREATE DATABASE IF NOT EXISTS sports_tournament;
USE sports_tournament;

-- Sports types
CREATE TABLE IF NOT EXISTS sports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    icon VARCHAR(50) DEFAULT '🏆',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tournaments
CREATE TABLE IF NOT EXISTS tournaments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    sport_id INT,
    format ENUM('knockout','round_robin','group_stage') DEFAULT 'knockout',
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    location VARCHAR(200),
    max_teams INT DEFAULT 16,
    status ENUM('upcoming','ongoing','completed') DEFAULT 'upcoming',
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sport_id) REFERENCES sports(id) ON DELETE SET NULL
);

-- Teams
CREATE TABLE IF NOT EXISTS teams (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    coach VARCHAR(100),
    contact_email VARCHAR(150),
    contact_phone VARCHAR(20),
    city VARCHAR(100),
    logo_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tournament registrations (teams in tournaments)
CREATE TABLE IF NOT EXISTS tournament_teams (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tournament_id INT NOT NULL,
    team_id INT NOT NULL,
    group_name VARCHAR(10),
    registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tournament_id) REFERENCES tournaments(id) ON DELETE CASCADE,
    FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE,
    UNIQUE KEY unique_team_tournament (tournament_id, team_id)
);

-- Players
CREATE TABLE IF NOT EXISTS players (
    id INT AUTO_INCREMENT PRIMARY KEY,
    team_id INT NOT NULL,
    full_name VARCHAR(150) NOT NULL,
    jersey_number INT,
    position VARCHAR(50),
    date_of_birth DATE,
    nationality VARCHAR(80),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE
);

-- Matches
CREATE TABLE IF NOT EXISTS matches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tournament_id INT NOT NULL,
    team1_id INT NOT NULL,
    team2_id INT NOT NULL,
    match_date DATETIME,
    venue VARCHAR(200),
    round_name VARCHAR(50) DEFAULT 'Round 1',
    team1_score INT DEFAULT 0,
    team2_score INT DEFAULT 0,
    winner_id INT,
    status ENUM('scheduled','live','completed','cancelled') DEFAULT 'scheduled',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tournament_id) REFERENCES tournaments(id) ON DELETE CASCADE,
    FOREIGN KEY (team1_id) REFERENCES teams(id),
    FOREIGN KEY (team2_id) REFERENCES teams(id),
    FOREIGN KEY (winner_id) REFERENCES teams(id)
);

-- Admin users
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(80) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(150),
    email VARCHAR(150),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default sports
INSERT INTO sports (name, icon) VALUES
('Football', '⚽'),
('Cricket', '🏏'),
('Basketball', '🏀'),
('Tennis', '🎾'),
('Volleyball', '🏐'),
('Badminton', '🏸'),
('Table Tennis', '🏓'),
('Chess', '♟️');

-- Insert default admin (username: admin, password: admin)
-- Password stored as plain text for easy XAMPP setup
INSERT INTO admin_users (username, password, full_name, email) VALUES
('admin', 'admin', 'Administrator', 'admin@sports.com');

-- Sample teams (8 teams)
INSERT INTO teams (name, coach, contact_email, contact_phone, city) VALUES
('Thunder Eagles',  'John Smith',   'thunder@sports.com',  '9876543210', 'Mumbai'),
('Royal Warriors',  'Raj Patel',    'royal@sports.com',    '9876543211', 'Delhi'),
('Storm United',    'Mike Chen',    'storm@sports.com',    '9876543212', 'Bangalore'),
('Iron Lions',      'David Kumar',  'iron@sports.com',     '9876543213', 'Chennai'),
('Blaze FC',        'Arjun Mehta',  'blaze@sports.com',    '9876543214', 'Hyderabad'),
('Phoenix Rising',  'Suresh Nair',  'phoenix@sports.com',  '9876543215', 'Pune'),
('Silver Sharks',   'Karan Singh',  'sharks@sports.com',   '9876543216', 'Kolkata'),
('Golden Hawks',    'Priya Iyer',   'hawks@sports.com',    '9876543217', 'Jaipur');

-- -----------------------------------------------
-- PLAYERS — Thunder Eagles (team_id = 1)
-- -----------------------------------------------
INSERT INTO players (team_id, full_name, jersey_number, position, date_of_birth, nationality) VALUES
(1, 'Arjun Sharma',    1,  'Goalkeeper',    '1998-03-15', 'Indian'),
(1, 'Rohit Verma',     2,  'Defender',      '1999-07-22', 'Indian'),
(1, 'Karan Mehta',     3,  'Defender',      '2000-01-10', 'Indian'),
(1, 'Sunil Das',       4,  'Midfielder',    '1997-11-05', 'Indian'),
(1, 'Vikram Rao',      7,  'Midfielder',    '2001-06-18', 'Indian'),
(1, 'Deepak Nair',     9,  'Forward',       '1998-09-30', 'Indian'),
(1, 'Aman Singh',      10, 'Forward',       '2000-04-25', 'Indian'),
(1, 'Nitin Pillai',    11, 'Forward',       '1999-12-12', 'Indian'),
(1, 'Rahul Joshi',     5,  'Defender',      '2001-08-08', 'Indian'),
(1, 'Sanjay Kumar',    6,  'Midfielder',    '1997-02-14', 'Indian'),
(1, 'Manish Tiwari',   8,  'Midfielder',    '2000-10-20', 'Indian');

-- -----------------------------------------------
-- PLAYERS — Royal Warriors (team_id = 2)
-- -----------------------------------------------
INSERT INTO players (team_id, full_name, jersey_number, position, date_of_birth, nationality) VALUES
(2, 'Praveen Reddy',   1,  'Goalkeeper',    '1997-05-19', 'Indian'),
(2, 'Aditya Gupta',    2,  'Defender',      '1999-03-07', 'Indian'),
(2, 'Ravi Shankar',    3,  'Defender',      '2000-09-14', 'Indian'),
(2, 'Mohan Lal',       4,  'Midfielder',    '1998-07-29', 'Indian'),
(2, 'Tarun Bose',      5,  'Defender',      '2001-01-03', 'Indian'),
(2, 'Sourav Das',      7,  'Midfielder',    '1999-11-17', 'Indian'),
(2, 'Hrithik Roy',     9,  'Forward',       '2000-06-23', 'Indian'),
(2, 'Akash Pandey',    10, 'Forward',       '1998-04-11', 'Indian'),
(2, 'Vishal Mishra',   11, 'Forward',       '2001-02-28', 'Indian'),
(2, 'Nikhil Srivastava',6, 'Midfielder',    '1997-08-16', 'Indian'),
(2, 'Pradeep Yadav',   8,  'Midfielder',    '2000-12-05', 'Indian');

-- -----------------------------------------------
-- PLAYERS — Storm United (team_id = 3)
-- -----------------------------------------------
INSERT INTO players (team_id, full_name, jersey_number, position, date_of_birth, nationality) VALUES
(3, 'Gopal Krishnan',  1,  'Goalkeeper',    '1998-06-12', 'Indian'),
(3, 'Sachin Patil',    2,  'Defender',      '1999-10-08', 'Indian'),
(3, 'Farhan Sheikh',   3,  'Defender',      '2000-03-22', 'Indian'),
(3, 'Rajesh Pillai',   4,  'Midfielder',    '1997-12-30', 'Indian'),
(3, 'Hemant Desai',    5,  'Defender',      '2001-07-15', 'Indian'),
(3, 'Vinod Nambiar',   7,  'Midfielder',    '1999-05-04', 'Indian'),
(3, 'Chetan Bhatt',    9,  'Forward',       '2000-09-19', 'Indian'),
(3, 'Lokesh Gowda',    10, 'Forward',       '1998-01-27', 'Indian'),
(3, 'Prashant Hegde',  11, 'Forward',       '2001-04-09', 'Indian'),
(3, 'Arvind Rao',      6,  'Midfielder',    '1997-10-31', 'Indian'),
(3, 'Sudhir Kamath',   8,  'Midfielder',    '2000-07-06', 'Indian');

-- -----------------------------------------------
-- PLAYERS — Iron Lions (team_id = 4)
-- -----------------------------------------------
INSERT INTO players (team_id, full_name, jersey_number, position, date_of_birth, nationality) VALUES
(4, 'Balasubramaniam', 1,  'Goalkeeper',    '1998-02-18', 'Indian'),
(4, 'Murugan Selvam',  2,  'Defender',      '1999-08-24', 'Indian'),
(4, 'Karthik Rajan',   3,  'Defender',      '2000-05-11', 'Indian'),
(4, 'Senthil Kumar',   4,  'Midfielder',    '1997-04-07', 'Indian'),
(4, 'Durai Pandian',   5,  'Defender',      '2001-09-20', 'Indian'),
(4, 'Arun Prakash',    7,  'Midfielder',    '1999-01-16', 'Indian'),
(4, 'Dinesh Babu',     9,  'Forward',       '2000-11-03', 'Indian'),
(4, 'Vijay Anand',     10, 'Forward',       '1998-07-13', 'Indian'),
(4, 'Surya Thamizh',   11, 'Forward',       '2001-03-26', 'Indian'),
(4, 'Palani Vel',      6,  'Midfielder',    '1997-06-09', 'Indian'),
(4, 'Manoj Bharath',   8,  'Midfielder',    '2000-02-14', 'Indian');

-- -----------------------------------------------
-- PLAYERS — Blaze FC (team_id = 5)
-- -----------------------------------------------
INSERT INTO players (team_id, full_name, jersey_number, position, date_of_birth, nationality) VALUES
(5, 'Rajan Reddy',     1,  'Goalkeeper',    '1998-04-21', 'Indian'),
(5, 'Siddharth Naidu', 2,  'Defender',      '1999-09-13', 'Indian'),
(5, 'Omkar Deshmukh',  3,  'Defender',      '2000-06-28', 'Indian'),
(5, 'Varun Telang',    4,  'Midfielder',    '1997-03-04', 'Indian'),
(5, 'Ninad Kulkarni',  5,  'Defender',      '2001-10-17', 'Indian'),
(5, 'Abhijeet More',   7,  'Midfielder',    '1999-02-22', 'Indian'),
(5, 'Tejas Pawar',     9,  'Forward',       '2000-08-06', 'Indian'),
(5, 'Gaurav Thakur',   10, 'Forward',       '1998-05-30', 'Indian'),
(5, 'Pratik Jadhav',   11, 'Forward',       '2001-01-14', 'Indian'),
(5, 'Sanket Gaikwad',  6,  'Midfielder',    '1997-11-25', 'Indian'),
(5, 'Yash Kamble',     8,  'Midfielder',    '2000-04-18', 'Indian');

-- -----------------------------------------------
-- PLAYERS — Phoenix Rising (team_id = 6)
-- -----------------------------------------------
INSERT INTO players (team_id, full_name, jersey_number, position, date_of_birth, nationality) VALUES
(6, 'Girish Menon',    1,  'Goalkeeper',    '1998-07-09', 'Indian'),
(6, 'Amal Joseph',     2,  'Defender',      '1999-12-01', 'Indian'),
(6, 'Bibin Thomas',    3,  'Defender',      '2000-04-16', 'Indian'),
(6, 'Jithin Mathew',   4,  'Midfielder',    '1997-08-23', 'Indian'),
(6, 'Ashwin Pillai',   5,  'Defender',      '2001-05-30', 'Indian'),
(6, 'Midhun Raj',      7,  'Midfielder',    '1999-03-14', 'Indian'),
(6, 'Sarath Babu',     9,  'Forward',       '2000-10-27', 'Indian'),
(6, 'Adithya Nair',    10, 'Forward',       '1998-06-05', 'Indian'),
(6, 'Vishnu Dev',      11, 'Forward',       '2001-02-11', 'Indian'),
(6, 'Shyam Krishnan',  6,  'Midfielder',    '1997-09-18', 'Indian'),
(6, 'Rahul Varma',     8,  'Midfielder',    '2000-01-24', 'Indian');

-- -----------------------------------------------
-- TOURNAMENTS (3 tournaments across different sports)
-- -----------------------------------------------
INSERT INTO tournaments (name, sport_id, format, start_date, end_date, location, max_teams, status, description) VALUES
('City Football Championship 2025', 1, 'round_robin',  '2025-03-01', '2025-03-31', 'Jawaharlal Stadium, Mumbai',   8,  'ongoing',   'Premier annual football championship of the city with top 8 clubs.'),
('State Cricket Cup 2025',          2, 'knockout',     '2025-04-10', '2025-05-05', 'DY Patil Stadium, Pune',       8,  'upcoming',  'State-level cricket knockout tournament. Best of 3 format in finals.'),
('Inter-City Basketball League',    3, 'round_robin',  '2025-01-15', '2025-02-28', 'YMCA Courts, Bangalore',       8,  'completed', 'Fast-paced inter-city basketball league. Top 4 teams qualify for playoffs.');

-- -----------------------------------------------
-- REGISTER TEAMS IN TOURNAMENTS
-- -----------------------------------------------
-- Tournament 1 (Football): all 8 teams
INSERT INTO tournament_teams (tournament_id, team_id) VALUES
(1,1),(1,2),(1,3),(1,4),(1,5),(1,6),(1,7),(1,8);

-- Tournament 2 (Cricket): 4 teams
INSERT INTO tournament_teams (tournament_id, team_id) VALUES
(2,1),(2,2),(2,3),(2,4);

-- Tournament 3 (Basketball): 6 teams
INSERT INTO tournament_teams (tournament_id, team_id) VALUES
(3,1),(3,2),(3,3),(3,4),(3,5),(3,6);

-- -----------------------------------------------
-- MATCHES — Tournament 1: City Football Championship (round_robin, ongoing)
-- -----------------------------------------------
INSERT INTO matches (tournament_id, team1_id, team2_id, match_date, venue, round_name, team1_score, team2_score, winner_id, status) VALUES
(1, 1, 2, '2025-03-01 10:00:00', 'Jawaharlal Stadium', 'Round 1', 3, 1, 1, 'completed'),
(1, 3, 4, '2025-03-01 14:00:00', 'Jawaharlal Stadium', 'Round 1', 0, 2, 4, 'completed'),
(1, 5, 6, '2025-03-02 10:00:00', 'Jawaharlal Stadium', 'Round 1', 1, 1, NULL, 'completed'),
(1, 7, 8, '2025-03-02 14:00:00', 'Jawaharlal Stadium', 'Round 1', 2, 0, 7, 'completed'),
(1, 1, 3, '2025-03-05 10:00:00', 'Jawaharlal Stadium', 'Round 2', 2, 2, NULL, 'completed'),
(1, 2, 4, '2025-03-05 14:00:00', 'Jawaharlal Stadium', 'Round 2', 1, 3, 4, 'completed'),
(1, 5, 7, '2025-03-06 10:00:00', 'Jawaharlal Stadium', 'Round 2', 4, 1, 5, 'completed'),
(1, 6, 8, '2025-03-06 14:00:00', 'Jawaharlal Stadium', 'Round 2', 2, 2, NULL, 'completed'),
(1, 1, 4, '2025-03-10 10:00:00', 'Jawaharlal Stadium', 'Round 3', 1, 0, 1, 'completed'),
(1, 2, 3, '2025-03-10 14:00:00', 'Jawaharlal Stadium', 'Round 3', 0, 1, 3, 'completed'),
(1, 5, 8, '2025-03-11 10:00:00', 'Jawaharlal Stadium', 'Round 3', 3, 2, 5, 'completed'),
(1, 6, 7, '2025-03-11 14:00:00', 'Jawaharlal Stadium', 'Round 3', 1, 2, 7, 'completed'),
-- Live match today
(1, 1, 5, '2025-03-16 11:00:00', 'Jawaharlal Stadium', 'Round 4', 2, 1, NULL, 'live'),
-- Upcoming
(1, 2, 6, '2025-03-17 10:00:00', 'Jawaharlal Stadium', 'Round 4', 0, 0, NULL, 'scheduled'),
(1, 3, 7, '2025-03-18 10:00:00', 'Jawaharlal Stadium', 'Round 4', 0, 0, NULL, 'scheduled'),
(1, 4, 8, '2025-03-18 14:00:00', 'Jawaharlal Stadium', 'Round 4', 0, 0, NULL, 'scheduled');

-- -----------------------------------------------
-- MATCHES — Tournament 2: State Cricket Cup (knockout, upcoming)
-- -----------------------------------------------
INSERT INTO matches (tournament_id, team1_id, team2_id, match_date, venue, round_name, team1_score, team2_score, winner_id, status) VALUES
(2, 1, 2, '2025-04-10 09:00:00', 'DY Patil Stadium', 'Quarter Final', 0, 0, NULL, 'scheduled'),
(2, 3, 4, '2025-04-11 09:00:00', 'DY Patil Stadium', 'Quarter Final', 0, 0, NULL, 'scheduled'),
(2, 1, 3, '2025-04-20 09:00:00', 'DY Patil Stadium', 'Semi Final',    0, 0, NULL, 'scheduled'),
(2, 2, 4, '2025-04-21 09:00:00', 'DY Patil Stadium', 'Semi Final',    0, 0, NULL, 'scheduled'),
(2, 1, 2, '2025-05-05 09:00:00', 'DY Patil Stadium', 'Final',         0, 0, NULL, 'scheduled');

-- -----------------------------------------------
-- MATCHES — Tournament 3: Basketball League (completed)
-- -----------------------------------------------
INSERT INTO matches (tournament_id, team1_id, team2_id, match_date, venue, round_name, team1_score, team2_score, winner_id, status) VALUES
(3, 1, 2, '2025-01-15 10:00:00', 'YMCA Courts', 'Round 1', 78, 65, 1, 'completed'),
(3, 3, 4, '2025-01-15 13:00:00', 'YMCA Courts', 'Round 1', 55, 70, 4, 'completed'),
(3, 5, 6, '2025-01-16 10:00:00', 'YMCA Courts', 'Round 1', 88, 72, 5, 'completed'),
(3, 1, 3, '2025-01-22 10:00:00', 'YMCA Courts', 'Round 2', 91, 80, 1, 'completed'),
(3, 2, 4, '2025-01-22 13:00:00', 'YMCA Courts', 'Round 2', 67, 75, 4, 'completed'),
(3, 5, 1, '2025-01-29 10:00:00', 'YMCA Courts', 'Round 3', 82, 95, 1, 'completed'),
(3, 4, 6, '2025-01-29 13:00:00', 'YMCA Courts', 'Round 3', 73, 60, 4, 'completed'),
(3, 1, 4, '2025-02-10 10:00:00', 'YMCA Courts', 'Semi Final', 88, 76, 1, 'completed'),
(3, 5, 2, '2025-02-10 14:00:00', 'YMCA Courts', 'Semi Final', 79, 68, 5, 'completed'),
(3, 1, 5, '2025-02-28 10:00:00', 'YMCA Courts', 'Final',      102, 94, 1, 'completed');
