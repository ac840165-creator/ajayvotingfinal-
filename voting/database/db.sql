-- Users table
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    date_of_birth DATE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    type ENUM('voter', 'admin') DEFAULT 'voter',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Candidates table
CREATE TABLE IF NOT EXISTS candidates (
    candidate_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    party_affiliation VARCHAR(100) DEFAULT NULL,
    photo_url VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Votes table
CREATE TABLE IF NOT EXISTS votes (
    vote_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    candidate_id INT NOT NULL,
    voted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(user_id, candidate_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (candidate_id) REFERENCES candidates(candidate_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Indexes for performance
CREATE INDEX idx_votes_user ON votes(user_id);
CREATE INDEX idx_votes_candidate ON votes(candidate_id);

-- Seed default admin account (password is 'admin123')
INSERT INTO users (first_name, last_name, date_of_birth, email, password_hash, type) 
VALUES ('System', 'Admin', '1990-01-01', 'ac840165@gamil.com', '$2y$10$/eaTyyi64nFlknDs8ob3QuQKWlR3JslaJ109pm2BGuKo.04jkxzhy', 'admin')
ON DUPLICATE KEY UPDATE user_id=user_id;

-- Seed default voter account (password is 'admin123')
INSERT INTO users (first_name, last_name, date_of_birth, email, password_hash, type) 
VALUES ('John', 'Doe', '2000-01-01', 'voter@voting.com', '$2y$10$/eaTyyi64nFlknDs8ob3QuQKWlR3JslaJ109pm2BGuKo.04jkxzhy', 'voter')
ON DUPLICATE KEY UPDATE user_id=user_id;