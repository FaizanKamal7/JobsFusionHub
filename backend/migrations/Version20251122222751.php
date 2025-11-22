<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251122222751 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ai_job_classifications (id CHAR(36) NOT NULL, category VARCHAR(100) NOT NULL, subcategory VARCHAR(100) DEFAULT NULL, tech_stack JSON DEFAULT NULL, seniority_level VARCHAR(50) DEFAULT NULL, confidence_score NUMERIC(5, 4) NOT NULL, model_version VARCHAR(50) DEFAULT NULL, classified_at DATETIME NOT NULL, job_id CHAR(36) NOT NULL, INDEX idx_job (job_id), INDEX idx_category (category), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE applications (id CHAR(36) NOT NULL, status VARCHAR(20) DEFAULT \'applied\' NOT NULL, cover_letter LONGTEXT DEFAULT NULL, match_score NUMERIC(5, 2) DEFAULT NULL, match_details JSON DEFAULT NULL, applied_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, candidate_profile_id CHAR(36) NOT NULL, job_id CHAR(36) NOT NULL, resume_id CHAR(36) DEFAULT NULL, INDEX IDX_F7C966F0D262AF09 (resume_id), INDEX idx_candidate (candidate_profile_id), INDEX idx_job (job_id), INDEX idx_status (status), INDEX idx_match_score (match_score), INDEX idx_applied_date (applied_at), UNIQUE INDEX UNIQ_CANDIDATE_JOB (candidate_profile_id, job_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE candidate_profiles (id CHAR(36) NOT NULL, phone VARCHAR(20) DEFAULT NULL, location VARCHAR(255) DEFAULT NULL, country_code VARCHAR(2) DEFAULT NULL, desired_job_title VARCHAR(255) DEFAULT NULL, desired_salary_min NUMERIC(12, 2) DEFAULT NULL, desired_salary_max NUMERIC(12, 2) DEFAULT NULL, desired_work_mode VARCHAR(20) DEFAULT NULL, years_of_experience INT DEFAULT NULL, current_company VARCHAR(255) DEFAULT NULL, linkedin_url VARCHAR(500) DEFAULT NULL, github_url VARCHAR(500) DEFAULT NULL, portfolio_url VARCHAR(500) DEFAULT NULL, bio LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, user_id CHAR(36) NOT NULL, UNIQUE INDEX UNIQ_2A6EC7E3A76ED395 (user_id), INDEX idx_location (location), INDEX idx_country (country_code), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE candidate_skills (id CHAR(36) NOT NULL, skill_name VARCHAR(100) NOT NULL, proficiency_level VARCHAR(20) DEFAULT NULL, years_of_experience NUMERIC(3, 1) DEFAULT NULL, candidate_profile_id CHAR(36) NOT NULL, INDEX idx_candidate (candidate_profile_id), INDEX idx_skill (skill_name), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE companies (id CHAR(36) NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, website VARCHAR(500) DEFAULT NULL, logo_url VARCHAR(500) DEFAULT NULL, description LONGTEXT DEFAULT NULL, industry VARCHAR(100) DEFAULT NULL, company_size VARCHAR(20) DEFAULT NULL, headquarters_location VARCHAR(255) DEFAULT NULL, founded_year INT DEFAULT NULL, is_verified TINYINT(1) DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX idx_name (name), INDEX idx_industry (industry), UNIQUE INDEX UNIQ_SLUG (slug), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE hidden_jobs (id CHAR(36) NOT NULL, reason VARCHAR(255) DEFAULT NULL, hidden_at DATETIME NOT NULL, candidate_profile_id CHAR(36) NOT NULL, job_id CHAR(36) NOT NULL, INDEX idx_candidate (candidate_profile_id), INDEX idx_job (job_id), UNIQUE INDEX UNIQ_HIDDEN_JOB (candidate_profile_id, job_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE job_duplicates (id CHAR(36) NOT NULL, similarity_score NUMERIC(5, 4) NOT NULL, detection_method VARCHAR(20) NOT NULL, detected_at DATETIME NOT NULL, canonical_job_id CHAR(36) NOT NULL, duplicate_job_id CHAR(36) NOT NULL, INDEX idx_canonical (canonical_job_id), INDEX idx_duplicate (duplicate_job_id), INDEX idx_similarity (similarity_score), UNIQUE INDEX UNIQ_DUPLICATE_PAIR (canonical_job_id, duplicate_job_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE job_skills (id CHAR(36) NOT NULL, skill_name VARCHAR(100) NOT NULL, is_required TINYINT(1) DEFAULT 1 NOT NULL, proficiency_level VARCHAR(20) DEFAULT NULL, job_id CHAR(36) NOT NULL, INDEX idx_job (job_id), INDEX idx_skill (skill_name), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE job_sources (id CHAR(36) NOT NULL, source_platform VARCHAR(50) NOT NULL, source_job_id VARCHAR(255) DEFAULT NULL, source_url VARCHAR(1000) NOT NULL, source_posted_date DATETIME DEFAULT NULL, last_scraped_at DATETIME NOT NULL, job_id CHAR(36) NOT NULL, INDEX idx_job (job_id), INDEX idx_source (source_platform), INDEX idx_scraped (last_scraped_at), UNIQUE INDEX UNIQ_SOURCE_JOB (source_platform, source_job_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE jobs (id CHAR(36) NOT NULL, canonical_id CHAR(36) DEFAULT NULL, title VARCHAR(500) NOT NULL, company_name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, requirements LONGTEXT DEFAULT NULL, responsibilities LONGTEXT DEFAULT NULL, location VARCHAR(255) DEFAULT NULL, city VARCHAR(100) DEFAULT NULL, state_province VARCHAR(100) DEFAULT NULL, country VARCHAR(100) DEFAULT NULL, country_code VARCHAR(2) DEFAULT NULL, is_location_remote TINYINT(1) DEFAULT 0 NOT NULL, job_type VARCHAR(20) DEFAULT NULL, work_mode VARCHAR(20) DEFAULT NULL, experience_level VARCHAR(20) DEFAULT NULL, salary_min NUMERIC(12, 2) DEFAULT NULL, salary_max NUMERIC(12, 2) DEFAULT NULL, salary_currency VARCHAR(3) DEFAULT \'USD\' NOT NULL, salary_period VARCHAR(20) DEFAULT NULL, application_url VARCHAR(1000) DEFAULT NULL, application_email VARCHAR(255) DEFAULT NULL, external_apply_url VARCHAR(1000) DEFAULT NULL, is_active TINYINT(1) DEFAULT 1 NOT NULL, is_duplicate TINYINT(1) DEFAULT 0 NOT NULL, is_spam TINYINT(1) DEFAULT 0 NOT NULL, spam_confidence_score NUMERIC(3, 2) DEFAULT NULL, embedding_vector_id VARCHAR(255) DEFAULT NULL, classification_tags JSON DEFAULT NULL, extracted_keywords JSON DEFAULT NULL, posted_date DATETIME DEFAULT NULL, expires_at DATETIME DEFAULT NULL, scraped_at DATETIME NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, company_id CHAR(36) DEFAULT NULL, INDEX idx_canonical (canonical_id), INDEX idx_company (company_id), INDEX idx_location (country_code, city), INDEX idx_job_type (job_type), INDEX idx_work_mode (work_mode), INDEX idx_posted_date (posted_date), INDEX idx_active (is_active), INDEX idx_duplicate (is_duplicate), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE notifications (id CHAR(36) NOT NULL, type VARCHAR(30) NOT NULL, title VARCHAR(255) NOT NULL, message LONGTEXT NOT NULL, link_url VARCHAR(500) DEFAULT NULL, is_read TINYINT(1) DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL, read_at DATETIME DEFAULT NULL, user_id CHAR(36) NOT NULL, INDEX idx_user (user_id), INDEX idx_read (is_read), INDEX idx_created (created_at), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE recruiter_profiles (id CHAR(36) NOT NULL, phone VARCHAR(20) DEFAULT NULL, job_title VARCHAR(255) DEFAULT NULL, linkedin_url VARCHAR(500) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, user_id CHAR(36) NOT NULL, company_id CHAR(36) DEFAULT NULL, UNIQUE INDEX UNIQ_C29FD578A76ED395 (user_id), INDEX idx_company (company_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE resume_job_matches (id CHAR(36) NOT NULL, overall_match_score NUMERIC(5, 2) NOT NULL, skills_match_score NUMERIC(5, 2) DEFAULT NULL, experience_match_score NUMERIC(5, 2) DEFAULT NULL, education_match_score NUMERIC(5, 2) DEFAULT NULL, location_match_score NUMERIC(5, 2) DEFAULT NULL, matching_skills JSON DEFAULT NULL, missing_skills JSON DEFAULT NULL, match_details JSON DEFAULT NULL, calculated_at DATETIME NOT NULL, resume_id CHAR(36) NOT NULL, job_id CHAR(36) NOT NULL, INDEX idx_resume (resume_id), INDEX idx_job (job_id), INDEX idx_score (overall_match_score), UNIQUE INDEX UNIQ_RESUME_JOB (resume_id, job_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE resumes (id CHAR(36) NOT NULL, file_name VARCHAR(255) NOT NULL, file_path VARCHAR(500) NOT NULL, file_size INT NOT NULL, mime_type VARCHAR(100) DEFAULT NULL, parsed_text LONGTEXT DEFAULT NULL, is_primary TINYINT(1) DEFAULT 0 NOT NULL, embedding_vector_id VARCHAR(255) DEFAULT NULL, uploaded_at DATETIME NOT NULL, candidate_profile_id CHAR(36) NOT NULL, INDEX idx_candidate (candidate_profile_id), INDEX idx_is_primary (is_primary), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE saved_jobs (id CHAR(36) NOT NULL, notes LONGTEXT DEFAULT NULL, saved_at DATETIME NOT NULL, candidate_profile_id CHAR(36) NOT NULL, job_id CHAR(36) NOT NULL, INDEX idx_candidate (candidate_profile_id), INDEX idx_job (job_id), INDEX idx_saved_date (saved_at), UNIQUE INDEX UNIQ_SAVED_JOB (candidate_profile_id, job_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE scraper_configs (id CHAR(36) NOT NULL, platform_name VARCHAR(100) NOT NULL, base_url VARCHAR(500) NOT NULL, is_active TINYINT(1) DEFAULT 1 NOT NULL, scrape_frequency_minutes INT DEFAULT 60 NOT NULL, last_successful_scrape DATETIME DEFAULT NULL, last_error_at DATETIME DEFAULT NULL, last_error_message LONGTEXT DEFAULT NULL, total_jobs_scraped INT DEFAULT 0 NOT NULL, config_json JSON DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX idx_active (is_active), UNIQUE INDEX UNIQ_PLATFORM (platform_name), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE scraper_runs (id CHAR(36) NOT NULL, status VARCHAR(20) NOT NULL, jobs_found INT DEFAULT 0 NOT NULL, jobs_created INT DEFAULT 0 NOT NULL, jobs_updated INT DEFAULT 0 NOT NULL, duplicates_detected INT DEFAULT 0 NOT NULL, errors_count INT DEFAULT 0 NOT NULL, error_details JSON DEFAULT NULL, started_at DATETIME DEFAULT NULL, completed_at DATETIME DEFAULT NULL, duration_seconds INT DEFAULT NULL, scraper_config_id CHAR(36) NOT NULL, INDEX idx_scraper (scraper_config_id), INDEX idx_status (status), INDEX idx_started (started_at), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE search_history (id CHAR(36) NOT NULL, search_query VARCHAR(500) DEFAULT NULL, filters_json JSON DEFAULT NULL, results_count INT DEFAULT NULL, clicked_job_ids JSON DEFAULT NULL, created_at DATETIME NOT NULL, user_id CHAR(36) DEFAULT NULL, INDEX idx_user (user_id), INDEX idx_created (created_at), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE users (id CHAR(36) NOT NULL, email VARCHAR(255) NOT NULL, password_hash VARCHAR(255) NOT NULL, first_name VARCHAR(100) NOT NULL, last_name VARCHAR(100) NOT NULL, user_type VARCHAR(20) NOT NULL, email_verified TINYINT(1) DEFAULT 0 NOT NULL, is_active TINYINT(1) DEFAULT 1 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, last_login_at DATETIME DEFAULT NULL, INDEX idx_user_type (user_type), INDEX idx_created_at (created_at), UNIQUE INDEX UNIQ_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE ai_job_classifications ADD CONSTRAINT FK_F671DF14BE04EA9 FOREIGN KEY (job_id) REFERENCES jobs (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE applications ADD CONSTRAINT FK_F7C966F0FE3D0586 FOREIGN KEY (candidate_profile_id) REFERENCES candidate_profiles (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE applications ADD CONSTRAINT FK_F7C966F0BE04EA9 FOREIGN KEY (job_id) REFERENCES jobs (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE applications ADD CONSTRAINT FK_F7C966F0D262AF09 FOREIGN KEY (resume_id) REFERENCES resumes (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE candidate_profiles ADD CONSTRAINT FK_2A6EC7E3A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE candidate_skills ADD CONSTRAINT FK_610248ACFE3D0586 FOREIGN KEY (candidate_profile_id) REFERENCES candidate_profiles (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE hidden_jobs ADD CONSTRAINT FK_21A87069FE3D0586 FOREIGN KEY (candidate_profile_id) REFERENCES candidate_profiles (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE hidden_jobs ADD CONSTRAINT FK_21A87069BE04EA9 FOREIGN KEY (job_id) REFERENCES jobs (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE job_duplicates ADD CONSTRAINT FK_FB0ED17B1AFBC776 FOREIGN KEY (canonical_job_id) REFERENCES jobs (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE job_duplicates ADD CONSTRAINT FK_FB0ED17B5225BD2E FOREIGN KEY (duplicate_job_id) REFERENCES jobs (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE job_skills ADD CONSTRAINT FK_85353BF1BE04EA9 FOREIGN KEY (job_id) REFERENCES jobs (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE job_sources ADD CONSTRAINT FK_48B2D269BE04EA9 FOREIGN KEY (job_id) REFERENCES jobs (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE jobs ADD CONSTRAINT FK_A8936DC5979B1AD6 FOREIGN KEY (company_id) REFERENCES companies (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE notifications ADD CONSTRAINT FK_6000B0D3A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE recruiter_profiles ADD CONSTRAINT FK_C29FD578A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE recruiter_profiles ADD CONSTRAINT FK_C29FD578979B1AD6 FOREIGN KEY (company_id) REFERENCES companies (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE resume_job_matches ADD CONSTRAINT FK_7EC3A847D262AF09 FOREIGN KEY (resume_id) REFERENCES resumes (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE resume_job_matches ADD CONSTRAINT FK_7EC3A847BE04EA9 FOREIGN KEY (job_id) REFERENCES jobs (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE resumes ADD CONSTRAINT FK_CDB8AD33FE3D0586 FOREIGN KEY (candidate_profile_id) REFERENCES candidate_profiles (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE saved_jobs ADD CONSTRAINT FK_A6AC9FD0FE3D0586 FOREIGN KEY (candidate_profile_id) REFERENCES candidate_profiles (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE saved_jobs ADD CONSTRAINT FK_A6AC9FD0BE04EA9 FOREIGN KEY (job_id) REFERENCES jobs (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE scraper_runs ADD CONSTRAINT FK_709A071177C61B1B FOREIGN KEY (scraper_config_id) REFERENCES scraper_configs (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE search_history ADD CONSTRAINT FK_AA6B9FD1A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ai_job_classifications DROP FOREIGN KEY FK_F671DF14BE04EA9');
        $this->addSql('ALTER TABLE applications DROP FOREIGN KEY FK_F7C966F0FE3D0586');
        $this->addSql('ALTER TABLE applications DROP FOREIGN KEY FK_F7C966F0BE04EA9');
        $this->addSql('ALTER TABLE applications DROP FOREIGN KEY FK_F7C966F0D262AF09');
        $this->addSql('ALTER TABLE candidate_profiles DROP FOREIGN KEY FK_2A6EC7E3A76ED395');
        $this->addSql('ALTER TABLE candidate_skills DROP FOREIGN KEY FK_610248ACFE3D0586');
        $this->addSql('ALTER TABLE hidden_jobs DROP FOREIGN KEY FK_21A87069FE3D0586');
        $this->addSql('ALTER TABLE hidden_jobs DROP FOREIGN KEY FK_21A87069BE04EA9');
        $this->addSql('ALTER TABLE job_duplicates DROP FOREIGN KEY FK_FB0ED17B1AFBC776');
        $this->addSql('ALTER TABLE job_duplicates DROP FOREIGN KEY FK_FB0ED17B5225BD2E');
        $this->addSql('ALTER TABLE job_skills DROP FOREIGN KEY FK_85353BF1BE04EA9');
        $this->addSql('ALTER TABLE job_sources DROP FOREIGN KEY FK_48B2D269BE04EA9');
        $this->addSql('ALTER TABLE jobs DROP FOREIGN KEY FK_A8936DC5979B1AD6');
        $this->addSql('ALTER TABLE notifications DROP FOREIGN KEY FK_6000B0D3A76ED395');
        $this->addSql('ALTER TABLE recruiter_profiles DROP FOREIGN KEY FK_C29FD578A76ED395');
        $this->addSql('ALTER TABLE recruiter_profiles DROP FOREIGN KEY FK_C29FD578979B1AD6');
        $this->addSql('ALTER TABLE resume_job_matches DROP FOREIGN KEY FK_7EC3A847D262AF09');
        $this->addSql('ALTER TABLE resume_job_matches DROP FOREIGN KEY FK_7EC3A847BE04EA9');
        $this->addSql('ALTER TABLE resumes DROP FOREIGN KEY FK_CDB8AD33FE3D0586');
        $this->addSql('ALTER TABLE saved_jobs DROP FOREIGN KEY FK_A6AC9FD0FE3D0586');
        $this->addSql('ALTER TABLE saved_jobs DROP FOREIGN KEY FK_A6AC9FD0BE04EA9');
        $this->addSql('ALTER TABLE scraper_runs DROP FOREIGN KEY FK_709A071177C61B1B');
        $this->addSql('ALTER TABLE search_history DROP FOREIGN KEY FK_AA6B9FD1A76ED395');
        $this->addSql('DROP TABLE ai_job_classifications');
        $this->addSql('DROP TABLE applications');
        $this->addSql('DROP TABLE candidate_profiles');
        $this->addSql('DROP TABLE candidate_skills');
        $this->addSql('DROP TABLE companies');
        $this->addSql('DROP TABLE hidden_jobs');
        $this->addSql('DROP TABLE job_duplicates');
        $this->addSql('DROP TABLE job_skills');
        $this->addSql('DROP TABLE job_sources');
        $this->addSql('DROP TABLE jobs');
        $this->addSql('DROP TABLE notifications');
        $this->addSql('DROP TABLE recruiter_profiles');
        $this->addSql('DROP TABLE resume_job_matches');
        $this->addSql('DROP TABLE resumes');
        $this->addSql('DROP TABLE saved_jobs');
        $this->addSql('DROP TABLE scraper_configs');
        $this->addSql('DROP TABLE scraper_runs');
        $this->addSql('DROP TABLE search_history');
        $this->addSql('DROP TABLE users');
    }
}
