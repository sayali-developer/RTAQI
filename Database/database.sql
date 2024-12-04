-- Create users table
use rtaqi;

CREATE TABLE users (
                       user_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                       email VARCHAR(128) NOT NULL UNIQUE,
                       fullname VARCHAR(128) NOT NULL,
                       password VARCHAR(256) DEFAULT NULL,
                       is_enabled BOOLEAN NOT NULL DEFAULT TRUE,
                       created_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create password_reset_link table
CREATE TABLE password_reset_link (
                                     link_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                                     user_id INT UNSIGNED NOT NULL,
                                     reset_code VARCHAR(36) NOT NULL UNIQUE, -- UUID length is 36 characters
                                     created_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                     used BOOLEAN NOT NULL DEFAULT FALSE,
                                     FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);
CREATE TABLE stations (
                          station_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                          country VARCHAR(64) NOT NULL,
                          state VARCHAR(64) NOT NULL,
                          city VARCHAR(128) NOT NULL,
                          station VARCHAR(256) NOT NULL,
                          latitude DECIMAL(10, 6) NOT NULL,
                          longitude DECIMAL(10, 6) NOT NULL,
                          inserted_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
ALTER TABLE `stations` ADD UNIQUE(`country`, `state`, `city`, `station`, `latitude`, `longitude`);

CREATE TABLE pollutant_readings (
                                    entry_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                                    station_id INT UNSIGNED NOT NULL,
                                    pollutant_id VARCHAR(32) NOT NULL,
                                    pollutant_min FLOAT DEFAULT NULL,
                                    pollutant_max FLOAT DEFAULT NULL,
                                    pollutant_avg FLOAT DEFAULT NULL,
                                    update_date DATETIME NOT NULL,
                                    FOREIGN KEY (station_id) REFERENCES stations(station_id)
                                        ON DELETE CASCADE
);
ALTER TABLE pollutant_readings
    ADD UNIQUE (station_id, pollutant_id, pollutant_min, pollutant_max, pollutant_avg, update_date);

