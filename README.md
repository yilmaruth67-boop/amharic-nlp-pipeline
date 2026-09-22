# Amharic NLP Normalization & Phonetic Transliteration Pipeline

A high-performance backend API built for enterprise-grade Amharic text processing, offering robust normalization rules, automated phonetic transliteration, secure token-based authentication, and comprehensive MySQL request logging.

---

## Features

- **Text Normalization:** Cleans and standardizes raw Amharic Unicode strings by stripping control characters and normalizing whitespace.
- **Phonetic Transliteration:** Automatically converts Amharic Fidel characters into phonetic Latin equivalents using an optimized mapping engine.
- **RESTful JSON API:** Accepts single-string or batch corpus payloads via standard HTTP `POST` requests.
- **Bearer Token Authentication:** Secures endpoints against unauthorized access.
- **Database Request Logging:** Automatically records API usage metrics, tokens used, and timestamps via PDO into MySQL.

---

## Tech Stack

- **Backend:** PHP 8+ (Object-Oriented Programming)
- **Database:** MySQL / MariaDB (via PDO)
- **Client Testing:** Python (`requests`) / PowerShell / cURL
- **Architecture:** Micro-service / API Gateway

---

## Installation & Setup

### 1. Clone or Copy the Repository
Place the project folder inside your local server root directory (e.g., `xampp/htdocs/amharic_nlp_pipeline`).

### 2. Configure the Database
Run the following SQL script in your MySQL environment (via phpMyAdmin or command line) to create the logging table:

```sql
CREATE DATABASE IF NOT EXISTS amharic_nlp_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE amharic_nlp_db;

CREATE TABLE IF NOT EXISTS api_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    token_used VARCHAR(50) NOT NULL,
    endpoint_hit VARCHAR(255) NOT NULL,
    request_count INT NOT NULL,
    created_at DATETIME NOT NULL
);
## License
This project is open-source and available under the [MIT License](LICENSE).