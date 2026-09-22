# Amharic NLP & Phonetic Transliteration Pipeline

A high-performance pipeline for Amharic text normalization and phonetic transliteration, built with PHP, JavaScript, and MySQL.

## Features
- Full Amharic Fidel (ፊደል) mapping dictionary.
- Clean JSON REST API endpoint.
- Modern dark-themed frontend UI (`index.html`).
- Request logging and database tracking.

## Installation & Setup
1. **Clone or Download** the project into your local server directory (e.g., `htdocs/amharic_nlp_pipeline/` for XAMPP).
2. **Database Setup**:
   - Create a MySQL database named `amharic_nlp_db`.
   - Run your table creation scripts for storing pipeline logs.
3. **Configure Settings**:
   - Update `config.php` with your local database credentials and desired API secret token.
4. **Run**:
   - Open `http://localhost/amharic_nlp_pipeline/index.html` in your browser.