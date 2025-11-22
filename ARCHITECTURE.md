# JobsFusionHub - System Architecture & Database Design

**Version:** 1.0
**Date:** November 22, 2025
**Project:** Job Aggregation Platform with AI-Powered Matching

---

## Table of Contents

1. [Project Overview](#project-overview)
2. [System Architecture](#system-architecture)
3. [Database Schema](#database-schema)
4. [Vector Database Design](#vector-database-design)
5. [Technology Stack](#technology-stack)
6. [AI/ML Pipeline](#aiml-pipeline)
7. [Data Flow Diagrams](#data-flow-diagrams)
8. [Key Features Implementation](#key-features-implementation)
9. [Scalability & Performance](#scalability--performance)
10. [Security Considerations](#security-considerations)
11. [Deployment Architecture](#deployment-architecture)

---

## 1. Project Overview

### 1.1 Purpose
JobsFusionHub is a comprehensive job aggregation platform that collects job listings from 50+ global and local job portals, detects duplicates using AI, and provides intelligent resume matching for candidates and recruiters.

### 1.2 Core Features
- Multi-source job aggregation (50+ portals)
- AI-powered duplicate detection using embeddings
- Intelligent resume-to-job matching with percentage scores
- Advanced filtering (country, date, keywords, salary, work mode)
- Dual portal system (Candidate & Recruiter)
- Real-time job classification and categorization
- Spam/fake job detection
- Job application tracking

### 1.3 Target Users
- **Candidates**: Job seekers looking for opportunities
- **Recruiters**: Companies posting jobs and finding candidates
- **Administrators**: Platform managers

---

## 2. System Architecture

### 2.1 High-Level Architecture Diagram

```
┌─────────────────────────────────────────────────────────────────────┐
│                         CLIENT LAYER                                 │
├─────────────────────────────────────────────────────────────────────┤
│  Vue 3 Frontend (Candidate Portal)  │  Vue 3 Frontend (Recruiter)   │
│  - Job Search & Filters              │  - Job Posting                │
│  - Resume Upload & Matching          │  - Candidate Management       │
│  - Application Tracking              │  - Analytics Dashboard        │
└────────────────┬─────────────────────┴───────────────┬───────────────┘
                 │                                     │
                 └──────────────┬──────────────────────┘
                                │ HTTPS/REST API
                 ┌──────────────▼──────────────────────┐
                 │      API Gateway / Load Balancer    │
                 │      (Rate Limiting, Auth)          │
                 └──────────────┬──────────────────────┘
                                │
        ┌───────────────────────┼───────────────────────┐
        │                       │                       │
┌───────▼────────┐   ┌─────────▼──────────┐   ┌───────▼────────┐
│   Symfony      │   │   AI/ML Services   │   │  Job Scraper   │
│   Backend      │   │     (Python)       │   │    Service     │
│   API          │◄──┤                    │   │   (Python)     │
│                │   │  - Embeddings      │   │                │
│  - Auth/Users  │   │  - Similarity      │   │  - Scrapers    │
│  - Jobs API    │   │  - Classification  │   │  - Schedulers  │
│  - Applications│   │  - Resume Matching │   │  - Proxies     │
│  - Analytics   │   └────────────────────┘   └────────────────┘
└───────┬────────┘            │                       │
        │                     │                       │
        │     ┌───────────────┼───────────────────────┤
        │     │               │                       │
┌───────▼─────▼───────────────▼───────────────────────▼───────┐
│                    DATA LAYER                                │
├──────────────────────────────────────────────────────────────┤
│  PostgreSQL         │  Vector DB        │  Cache Layer       │
│  (Primary)          │  (FAISS/Milvus)   │  (Redis)          │
│                     │                   │                    │
│  - Users            │  - Job Embeddings │  - Job Cache       │
│  - Jobs             │  - Resume Vectors │  - Session Data    │
│  - Applications     │  - Similarity     │  - Rate Limits     │
│  - Companies        │    Search         │                    │
└──────────────────────────────────────────────────────────────┘
        │                     │                       │
┌───────▼─────────────────────▼───────────────────────▼───────┐
│                    QUEUE/MESSAGE BROKER                      │
│                    (RabbitMQ / Redis)                        │
│                                                              │
│  - Scraping Jobs Queue                                       │
│  - AI Processing Queue                                       │
│  - Email Notifications Queue                                 │
└──────────────────────────────────────────────────────────────┘
```

### 2.2 Microservices Architecture

#### 2.2.1 Frontend Service (Vue 3)
- **Technology**: Vue 3 + TypeScript + Tailwind CSS
- **Responsibilities**:
  - User interface rendering
  - Client-side routing
  - State management (Pinia)
  - API communication
- **Deployment**: Static hosting (S3 + CloudFront / Netlify / Vercel)

#### 2.2.2 API Service (Symfony)
- **Technology**: Symfony 7.3 + API Platform
- **Responsibilities**:
  - RESTful API endpoints
  - Authentication & authorization (JWT)
  - Business logic orchestration
  - Database operations (via Doctrine ORM)
  - Request validation
- **Scaling**: Horizontal with load balancer

#### 2.2.3 AI/ML Service (Python FastAPI)
- **Technology**: Python 3.11 + FastAPI
- **Responsibilities**:
  - Job embedding generation (Sentence-BERT)
  - Resume embedding generation
  - Similarity calculations
  - Job classification (category, seniority, tech stack)
  - Keyword extraction (spaCy NLP)
  - Duplicate detection
  - Spam detection
  - Resume parsing
  - Match score calculation
- **Scaling**: Horizontal with GPU support for embeddings

#### 2.2.4 Job Scraper Service (Python)
- **Technology**: Python + Scrapy/Playwright
- **Responsibilities**:
  - Web scraping from 50+ job portals
  - API integration with job platforms
  - Scheduling scraping jobs
  - Proxy rotation
  - Rate limiting compliance
  - Data extraction and normalization
- **Scaling**: Distributed workers via Celery

---

## 3. Database Schema

### 3.1 PostgreSQL Database Structure

#### 3.1.1 User Management Tables

**Table: users**
```sql
CREATE TABLE users (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    user_type ENUM('candidate', 'recruiter', 'admin') NOT NULL,
    email_verified BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login_at TIMESTAMP,

    INDEX idx_email (email),
    INDEX idx_user_type (user_type),
    INDEX idx_created_at (created_at)
);
```

**Purpose**: Store all user accounts (candidates, recruiters, admins)

**Key Fields**:
- `user_type`: Distinguishes between candidate, recruiter, and admin roles
- `email_verified`: Email verification status for security
- `is_active`: Soft delete flag

---

**Table: candidate_profiles**
```sql
CREATE TABLE candidate_profiles (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id UUID NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    phone VARCHAR(20),
    location VARCHAR(255),
    country_code VARCHAR(2),
    desired_job_title VARCHAR(255),
    desired_salary_min DECIMAL(12, 2),
    desired_salary_max DECIMAL(12, 2),
    desired_work_mode ENUM('remote', 'hybrid', 'onsite'),
    years_of_experience INT,
    current_company VARCHAR(255),
    linkedin_url VARCHAR(500),
    github_url VARCHAR(500),
    portfolio_url VARCHAR(500),
    bio TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    UNIQUE(user_id),
    INDEX idx_location (location),
    INDEX idx_country (country_code)
);
```

**Purpose**: Extended profile information for job seekers

**Key Fields**:
- `desired_*`: Preferences for job matching algorithm
- `years_of_experience`: Used in experience matching
- Social links for recruiter reference

---

**Table: candidate_skills**
```sql
CREATE TABLE candidate_skills (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    candidate_profile_id UUID NOT NULL REFERENCES candidate_profiles(id) ON DELETE CASCADE,
    skill_name VARCHAR(100) NOT NULL,
    proficiency_level ENUM('beginner', 'intermediate', 'advanced', 'expert'),
    years_of_experience DECIMAL(3, 1),

    INDEX idx_candidate (candidate_profile_id),
    INDEX idx_skill (skill_name)
);
```

**Purpose**: Track candidate skills for matching

**Use Case**: Skills matching in resume-to-job algorithm (30% weight)

---

**Table: resumes**
```sql
CREATE TABLE resumes (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    candidate_profile_id UUID NOT NULL REFERENCES candidate_profiles(id) ON DELETE CASCADE,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_size INT NOT NULL,
    mime_type VARCHAR(100),
    parsed_text TEXT,
    is_primary BOOLEAN DEFAULT FALSE,
    embedding_vector_id VARCHAR(255), -- Reference to vector DB
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_candidate (candidate_profile_id),
    INDEX idx_is_primary (is_primary)
);
```

**Purpose**: Store uploaded resumes and parsed content

**Key Fields**:
- `parsed_text`: Extracted text from PDF/DOCX for analysis
- `embedding_vector_id`: Links to vector database for similarity search
- `is_primary`: Indicates which resume to use for auto-matching

---

**Table: recruiter_profiles**
```sql
CREATE TABLE recruiter_profiles (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id UUID NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    company_id UUID REFERENCES companies(id) ON DELETE SET NULL,
    phone VARCHAR(20),
    job_title VARCHAR(255),
    linkedin_url VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    UNIQUE(user_id),
    INDEX idx_company (company_id)
);
```

**Purpose**: Recruiter-specific profile information

---

**Table: companies**
```sql
CREATE TABLE companies (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    website VARCHAR(500),
    logo_url VARCHAR(500),
    description TEXT,
    industry VARCHAR(100),
    company_size ENUM('1-10', '11-50', '51-200', '201-500', '501-1000', '1001-5000', '5000+'),
    headquarters_location VARCHAR(255),
    founded_year INT,
    is_verified BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_name (name),
    INDEX idx_slug (slug),
    INDEX idx_industry (industry)
);
```

**Purpose**: Company information for both scraped jobs and recruiter postings

**Key Fields**:
- `slug`: URL-friendly identifier
- `is_verified`: Indicates official company accounts
- Denormalized in jobs table to avoid dependency on company existing

---

#### 3.1.2 Job Tables

**Table: jobs**
```sql
CREATE TABLE jobs (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    canonical_id UUID, -- For grouping duplicates, points to the "master" job
    title VARCHAR(500) NOT NULL,
    company_id UUID REFERENCES companies(id) ON DELETE SET NULL,
    company_name VARCHAR(255) NOT NULL, -- Denormalized for scraped jobs
    description TEXT NOT NULL,
    requirements TEXT,
    responsibilities TEXT,

    -- Location
    location VARCHAR(255),
    city VARCHAR(100),
    state_province VARCHAR(100),
    country VARCHAR(100),
    country_code VARCHAR(2),
    is_location_remote BOOLEAN DEFAULT FALSE,

    -- Job Details
    job_type ENUM('full-time', 'part-time', 'contract', 'internship', 'temporary'),
    work_mode ENUM('remote', 'hybrid', 'onsite'),
    experience_level ENUM('entry', 'mid', 'senior', 'lead', 'executive'),

    -- Salary
    salary_min DECIMAL(12, 2),
    salary_max DECIMAL(12, 2),
    salary_currency VARCHAR(3) DEFAULT 'USD',
    salary_period ENUM('hourly', 'monthly', 'yearly'),

    -- Application
    application_url VARCHAR(1000),
    application_email VARCHAR(255),
    external_apply_url VARCHAR(1000),

    -- Status
    is_active BOOLEAN DEFAULT TRUE,
    is_duplicate BOOLEAN DEFAULT FALSE,
    is_spam BOOLEAN DEFAULT FALSE,
    spam_confidence_score DECIMAL(3, 2), -- 0.00 to 1.00

    -- AI/ML Fields
    embedding_vector_id VARCHAR(255), -- Reference to vector DB
    classification_tags JSONB, -- e.g., {"tech": ["react", "nodejs"], "seniority": "senior"}
    extracted_keywords JSONB,

    -- Metadata
    posted_date TIMESTAMP,
    expires_at TIMESTAMP,
    scraped_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_canonical (canonical_id),
    INDEX idx_company (company_id),
    INDEX idx_location (country_code, city),
    INDEX idx_job_type (job_type),
    INDEX idx_work_mode (work_mode),
    INDEX idx_posted_date (posted_date),
    INDEX idx_active (is_active),
    INDEX idx_duplicate (is_duplicate),
    INDEX idx_classification (classification_tags) USING GIN,
    FULLTEXT INDEX idx_search (title, description, requirements)
);
```

**Purpose**: Core job listings table

**Key Design Decisions**:
- `canonical_id`: Self-referencing FK to group duplicate jobs
- Location denormalized into city/state/country for easier filtering
- `is_duplicate`: Flag for quick filtering in queries
- JSONB fields for flexible AI-generated metadata
- Full-text index for keyword search
- GIN index on JSONB for fast JSON queries

**Example canonical_id Usage**:
```
Job 1 (LinkedIn): id=abc, canonical_id=abc (master)
Job 2 (Indeed): id=def, canonical_id=abc (duplicate)
Job 3 (Monster): id=ghi, canonical_id=abc (duplicate)
```

---

**Table: job_sources**
```sql
CREATE TABLE job_sources (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    job_id UUID NOT NULL REFERENCES jobs(id) ON DELETE CASCADE,
    source_platform ENUM('LinkedIn', 'Indeed', 'Glassdoor', 'Monster', 'ZipRecruiter',
                         'AngelList', 'Dice', 'CareerBuilder', 'SimplyHired', 'Wellfound',
                         'RemoteOK', 'WeWorkRemotely', 'GitHub Jobs', 'StackOverflow Jobs',
                         'Naukri', 'Shine', 'TimesJobs', 'Instahyre', 'Cutshort',
                         'Hirect', 'Upwork', 'Fiverr', 'Toptal', 'Gun.io',
                         'Hired', 'Vettery', 'Triplebyte', 'Underdog', 'YCombinator',
                         'CrunchBoard', 'ProductHunt Jobs', 'FlexJobs', 'Remote.co',
                         'JustRemote', 'Remotive', 'Jobspresso', 'PowerToFly', 'Fairygodboss',
                         'The Muse', 'Mediabistro', 'Idealist', 'ReliefWeb', 'Devsnap',
                         'Authentic Jobs', 'Krop', 'Dribbble Jobs', 'Behance', 'Coroflot',
                         'CoolWorks', 'Seasonal Jobs', 'Other'),
    source_job_id VARCHAR(255), -- ID from the source platform
    source_url VARCHAR(1000) NOT NULL,
    source_posted_date TIMESTAMP,
    last_scraped_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    UNIQUE(source_platform, source_job_id),
    INDEX idx_job (job_id),
    INDEX idx_source (source_platform),
    INDEX idx_scraped (last_scraped_at)
);
```

**Purpose**: Track which platforms each job appears on

**Use Case**:
- Display "Found on 3 platforms" badge
- Track scraping history
- Avoid re-scraping same job

---

**Table: job_duplicates**
```sql
CREATE TABLE job_duplicates (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    canonical_job_id UUID NOT NULL REFERENCES jobs(id) ON DELETE CASCADE,
    duplicate_job_id UUID NOT NULL REFERENCES jobs(id) ON DELETE CASCADE,
    similarity_score DECIMAL(5, 4) NOT NULL, -- 0.0000 to 1.0000
    detection_method ENUM('embedding', 'fuzzy_match', 'manual') NOT NULL,
    detected_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    UNIQUE(canonical_job_id, duplicate_job_id),
    INDEX idx_canonical (canonical_job_id),
    INDEX idx_duplicate (duplicate_job_id),
    INDEX idx_similarity (similarity_score)
);
```

**Purpose**: Track duplicate relationships and their similarity scores

**Detection Methods**:
- `embedding`: Vector similarity (cosine similarity > 0.95)
- `fuzzy_match`: Title + company fuzzy matching
- `manual`: Admin-marked duplicates

---

**Table: job_skills**
```sql
CREATE TABLE job_skills (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    job_id UUID NOT NULL REFERENCES jobs(id) ON DELETE CASCADE,
    skill_name VARCHAR(100) NOT NULL,
    is_required BOOLEAN DEFAULT TRUE,
    proficiency_level ENUM('beginner', 'intermediate', 'advanced', 'expert'),

    INDEX idx_job (job_id),
    INDEX idx_skill (skill_name)
);
```

**Purpose**: Structured skill requirements for precise matching

**Use Case**: Skills matching component of resume-to-job algorithm

---

#### 3.1.3 Application & Interaction Tables

**Table: applications**
```sql
CREATE TABLE applications (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    candidate_profile_id UUID NOT NULL REFERENCES candidate_profiles(id) ON DELETE CASCADE,
    job_id UUID NOT NULL REFERENCES jobs(id) ON DELETE CASCADE,
    resume_id UUID REFERENCES resumes(id) ON DELETE SET NULL,

    status ENUM('draft', 'applied', 'viewed', 'shortlisted', 'interviewing',
                'offered', 'accepted', 'rejected', 'withdrawn') DEFAULT 'applied',

    cover_letter TEXT,
    match_score DECIMAL(5, 2), -- 0.00 to 100.00 percentage
    match_details JSONB, -- Detailed breakdown of matching skills, experience, etc.

    applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    UNIQUE(candidate_profile_id, job_id),
    INDEX idx_candidate (candidate_profile_id),
    INDEX idx_job (job_id),
    INDEX idx_status (status),
    INDEX idx_match_score (match_score),
    INDEX idx_applied_date (applied_at)
);
```

**Purpose**: Track job applications and their lifecycle

**Status Flow**:
```
draft → applied → viewed → shortlisted → interviewing → offered → accepted
                     ↓         ↓            ↓            ↓
                 rejected  rejected     rejected     rejected
                     ↓         ↓            ↓
                 withdrawn withdrawn   withdrawn
```

---

**Table: saved_jobs**
```sql
CREATE TABLE saved_jobs (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    candidate_profile_id UUID NOT NULL REFERENCES candidate_profiles(id) ON DELETE CASCADE,
    job_id UUID NOT NULL REFERENCES jobs(id) ON DELETE CASCADE,
    notes TEXT,
    saved_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    UNIQUE(candidate_profile_id, job_id),
    INDEX idx_candidate (candidate_profile_id),
    INDEX idx_job (job_id),
    INDEX idx_saved_date (saved_at)
);
```

**Purpose**: Bookmark jobs for later review

---

**Table: hidden_jobs**
```sql
CREATE TABLE hidden_jobs (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    candidate_profile_id UUID NOT NULL REFERENCES candidate_profiles(id) ON DELETE CASCADE,
    job_id UUID NOT NULL REFERENCES jobs(id) ON DELETE CASCADE,
    reason VARCHAR(255),
    hidden_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    UNIQUE(candidate_profile_id, job_id),
    INDEX idx_candidate (candidate_profile_id),
    INDEX idx_job (job_id)
);
```

**Purpose**: Allow candidates to hide irrelevant jobs

**Use Case**: Improves user experience by not showing dismissed jobs

---

#### 3.1.4 Scraper Management Tables

**Table: scraper_configs**
```sql
CREATE TABLE scraper_configs (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    platform_name VARCHAR(100) UNIQUE NOT NULL,
    base_url VARCHAR(500) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    scrape_frequency_minutes INT DEFAULT 60,
    last_successful_scrape TIMESTAMP,
    last_error_at TIMESTAMP,
    last_error_message TEXT,
    total_jobs_scraped INT DEFAULT 0,
    config_json JSONB, -- Platform-specific configs (selectors, API keys, etc.)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_platform (platform_name),
    INDEX idx_active (is_active)
);
```

**Purpose**: Configuration for each job portal scraper

**config_json Example**:
```json
{
  "scraper_type": "api",
  "api_key": "encrypted_key",
  "selectors": {
    "job_title": ".job-title",
    "company": ".company-name",
    "description": "#job-description"
  },
  "rate_limit": {
    "requests_per_minute": 30
  },
  "proxy_required": true
}
```

---

**Table: scraper_runs**
```sql
CREATE TABLE scraper_runs (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    scraper_config_id UUID NOT NULL REFERENCES scraper_configs(id) ON DELETE CASCADE,
    status ENUM('pending', 'running', 'completed', 'failed') NOT NULL,
    jobs_found INT DEFAULT 0,
    jobs_created INT DEFAULT 0,
    jobs_updated INT DEFAULT 0,
    duplicates_detected INT DEFAULT 0,
    errors_count INT DEFAULT 0,
    error_details JSONB,
    started_at TIMESTAMP,
    completed_at TIMESTAMP,
    duration_seconds INT,

    INDEX idx_scraper (scraper_config_id),
    INDEX idx_status (status),
    INDEX idx_started (started_at)
);
```

**Purpose**: Log each scraping run for monitoring and debugging

**Use Case**: Analytics dashboard showing scraper performance

---

#### 3.1.5 AI/ML Tables

**Table: ai_job_classifications**
```sql
CREATE TABLE ai_job_classifications (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    job_id UUID NOT NULL REFERENCES jobs(id) ON DELETE CASCADE,
    category VARCHAR(100) NOT NULL, -- e.g., 'engineering', 'sales', 'marketing'
    subcategory VARCHAR(100),
    tech_stack JSONB, -- ["react", "nodejs", "aws"]
    seniority_level VARCHAR(50),
    confidence_score DECIMAL(5, 4) NOT NULL,
    model_version VARCHAR(50),
    classified_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_job (job_id),
    INDEX idx_category (category),
    INDEX idx_tech_stack (tech_stack) USING GIN
);
```

**Purpose**: Store AI-generated job classifications

**Example Classification**:
```json
{
  "category": "Engineering",
  "subcategory": "Backend Development",
  "tech_stack": ["Python", "Django", "PostgreSQL", "Redis", "Docker"],
  "seniority_level": "Senior",
  "confidence_score": 0.9234
}
```

---

**Table: resume_job_matches**
```sql
CREATE TABLE resume_job_matches (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    resume_id UUID NOT NULL REFERENCES resumes(id) ON DELETE CASCADE,
    job_id UUID NOT NULL REFERENCES jobs(id) ON DELETE CASCADE,
    overall_match_score DECIMAL(5, 2) NOT NULL, -- 0.00 to 100.00
    skills_match_score DECIMAL(5, 2),
    experience_match_score DECIMAL(5, 2),
    education_match_score DECIMAL(5, 2),
    location_match_score DECIMAL(5, 2),
    matching_skills JSONB,
    missing_skills JSONB,
    match_details JSONB,
    calculated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    UNIQUE(resume_id, job_id),
    INDEX idx_resume (resume_id),
    INDEX idx_job (job_id),
    INDEX idx_score (overall_match_score)
);
```

**Purpose**: Pre-calculated matching scores for resume-job pairs

**Performance Optimization**:
- Calculate once, query many times
- Recalculate when resume updated or job modified
- Enable sorting by match score

**match_details Example**:
```json
{
  "matching_skills": ["React", "TypeScript", "Node.js"],
  "missing_skills": ["AWS", "Kubernetes"],
  "experience_years_required": 5,
  "experience_years_candidate": 4,
  "location_match": "Same city",
  "salary_expectation_met": true
}
```

---

#### 3.1.6 Notification & Analytics Tables

**Table: notifications**
```sql
CREATE TABLE notifications (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id UUID NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    type ENUM('job_match', 'application_update', 'new_message', 'system') NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    link_url VARCHAR(500),
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    read_at TIMESTAMP,

    INDEX idx_user (user_id),
    INDEX idx_read (is_read),
    INDEX idx_created (created_at)
);
```

**Purpose**: In-app notifications for users

**Notification Types**:
- `job_match`: "New job matches your resume (95% match)"
- `application_update`: "Your application status changed to 'interviewing'"
- `new_message`: Recruiter messages
- `system`: Platform announcements

---

**Table: search_history**
```sql
CREATE TABLE search_history (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id UUID REFERENCES users(id) ON DELETE CASCADE,
    search_query VARCHAR(500),
    filters_json JSONB,
    results_count INT,
    clicked_job_ids UUID[],
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_user (user_id),
    INDEX idx_created (created_at)
);
```

**Purpose**: Track user search patterns for analytics and personalization

**Use Case**:
- Show recent searches
- Suggest common searches
- Analyze user behavior
- Improve search algorithm

---

### 3.2 Database Relationships Diagram

```
users (1) ──────< (1) candidate_profiles (1) ──────< (N) candidate_skills
  │                        │
  │                        ├──────────< (N) resumes (1) ──────< (N) resume_job_matches >─────── (1) jobs
  │                        │                                                                         │
  │                        ├──────────< (N) applications >──────────────────────────────────────────┤
  │                        │                                                                         │
  │                        ├──────────< (N) saved_jobs >────────────────────────────────────────────┤
  │                        │                                                                         │
  │                        └──────────< (N) hidden_jobs >───────────────────────────────────────────┤
  │                                                                                                  │
  ├─ (1) recruiter_profiles (N) ────> (1) companies (1) ──────< (N) jobs                          │
  │                                                                    │                             │
  └─ (1) notifications                                                 ├──────< (N) job_sources      │
                                                                       │                             │
                                                                       ├──────< (N) job_skills       │
                                                                       │                             │
                                                                       ├──────< (N) ai_job_classifications
                                                                       │
                                                                       └──────< (N) job_duplicates (self-referencing)

scraper_configs (1) ──────< (N) scraper_runs
```

---

## 4. Vector Database Design

### 4.1 Why Vector Database?

**Use Cases**:
1. **Duplicate Detection**: Find similar job descriptions (cosine similarity > 0.95)
2. **Semantic Search**: "remote python jobs for AI" → find relevant jobs
3. **Resume Matching**: Calculate similarity between resume and job descriptions

### 4.2 Technology Choice

**Options**:
| Technology | Pros | Cons | Recommendation |
|------------|------|------|----------------|
| FAISS | Fast, in-memory, free | No persistence, single-node | Development/MVP |
| Milvus | Scalable, distributed, open-source | Complex setup | Production (recommended) |
| Qdrant | Easy to use, Rust-based, fast | Smaller community | Alternative |
| Pinecone | Managed, easy | Expensive, vendor lock-in | If budget allows |
| Weaviate | GraphQL API, hybrid search | Learning curve | Advanced use cases |

**Recommendation**: **Milvus** for production (scalable, open-source, mature)

---

### 4.3 Milvus Collections Schema

#### Collection: job_embeddings

```python
from pymilvus import Collection, FieldSchema, CollectionSchema, DataType

job_embedding_fields = [
    FieldSchema(name="id", dtype=DataType.VARCHAR, max_length=36, is_primary=True),
    FieldSchema(name="job_id", dtype=DataType.VARCHAR, max_length=36),
    FieldSchema(name="embedding", dtype=DataType.FLOAT_VECTOR, dim=768),
    FieldSchema(name="title", dtype=DataType.VARCHAR, max_length=500),
    FieldSchema(name="company_name", dtype=DataType.VARCHAR, max_length=255),
    FieldSchema(name="created_at", dtype=DataType.INT64),
]

job_embedding_schema = CollectionSchema(
    fields=job_embedding_fields,
    description="Job description embeddings for similarity search"
)

job_embeddings_collection = Collection(
    name="job_embeddings",
    schema=job_embedding_schema
)

# Create IVF_FLAT index for fast similarity search
index_params = {
    "metric_type": "COSINE",  # Cosine similarity for text
    "index_type": "IVF_FLAT",
    "params": {"nlist": 1024}  # Number of clusters
}

job_embeddings_collection.create_index(
    field_name="embedding",
    index_params=index_params
)
```

**Embedding Model**: `sentence-transformers/all-MiniLM-L6-v2` (384 dim) or `all-mpnet-base-v2` (768 dim)

**Why 768 dimensions**:
- Balance between accuracy and performance
- Standard for BERT-based models
- Good for semantic similarity tasks

---

#### Collection: resume_embeddings

```python
resume_embedding_fields = [
    FieldSchema(name="id", dtype=DataType.VARCHAR, max_length=36, is_primary=True),
    FieldSchema(name="resume_id", dtype=DataType.VARCHAR, max_length=36),
    FieldSchema(name="candidate_id", dtype=DataType.VARCHAR, max_length=36),
    FieldSchema(name="embedding", dtype=DataType.FLOAT_VECTOR, dim=768),
    FieldSchema(name="created_at", dtype=DataType.INT64),
]

resume_embedding_schema = CollectionSchema(
    fields=resume_embedding_fields,
    description="Resume embeddings for job matching"
)

resume_embeddings_collection = Collection(
    name="resume_embeddings",
    schema=resume_embedding_schema
)

index_params = {
    "metric_type": "COSINE",
    "index_type": "IVF_FLAT",
    "params": {"nlist": 512}
}

resume_embeddings_collection.create_index(
    field_name="embedding",
    index_params=index_params
)
```

---

### 4.4 Vector Search Example

**Finding Similar Jobs (Duplicate Detection)**:
```python
from sentence_transformers import SentenceTransformer

# Initialize model
model = SentenceTransformer('all-mpnet-base-v2')

# New job description
new_job_text = f"{job.title} {job.company_name} {job.description}"

# Generate embedding
embedding = model.encode(new_job_text)

# Search for similar jobs
search_params = {"metric_type": "COSINE", "params": {"nprobe": 10}}

results = job_embeddings_collection.search(
    data=[embedding],
    anns_field="embedding",
    param=search_params,
    limit=10,
    expr=None,
    output_fields=["job_id", "title", "company_name"]
)

# Check for duplicates (similarity > 0.95)
for hit in results[0]:
    if hit.distance > 0.95:
        print(f"Potential duplicate: {hit.entity.get('title')} (similarity: {hit.distance})")
```

---

## 5. Technology Stack

### 5.1 Frontend Stack

**Framework**: Vue 3.5
- **Why**: Reactive, component-based, excellent TypeScript support
- **Build Tool**: Vite 7.1 (fast HMR, optimized builds)
- **State Management**: Pinia 3.0 (official Vue store)
- **Routing**: Vue Router 4.6
- **Language**: TypeScript 5.9 (type safety)
- **Styling**: Tailwind CSS 4.1 (utility-first CSS)
- **HTTP Client**: Axios
- **Form Handling**: VeeValidate
- **Charts**: Chart.js / Apache ECharts (for analytics)

**Testing**:
- **Unit Tests**: Vitest 3.2
- **E2E Tests**: Playwright 1.56
- **Code Quality**: ESLint 9.37 + Prettier 3.6

---

### 5.2 Backend Stack

**Framework**: Symfony 7.3
- **Why**: Enterprise-grade, excellent ORM, built-in security
- **API**: API Platform 4.2 (auto-generates REST API, OpenAPI docs)
- **ORM**: Doctrine ORM 3.5
- **Database**: PostgreSQL 16
- **Authentication**: Lexik JWT Authentication Bundle
- **Security**: Symfony Security Bundle
- **CORS**: Nelmio CORS Bundle
- **Validation**: Symfony Validator
- **Serialization**: Symfony Serializer
- **Queue**: Symfony Messenger (with RabbitMQ/Redis transport)

**Additional Libraries**:
- `ramsey/uuid`: UUID generation
- `guzzlehttp/guzzle`: HTTP client for API calls
- `symfony/mailer`: Email notifications
- `symfony/cache`: Caching layer

---

### 5.3 AI/ML Service Stack

**Framework**: Python 3.11 + FastAPI
- **Why**: Fast, async, automatic API docs, great for ML

**ML/NLP Libraries**:
```python
# Core ML
transformers==4.36.0           # HuggingFace transformers
sentence-transformers==2.2.2   # Sentence embeddings
torch==2.1.0                   # PyTorch (or tensorflow)

# Vector DB
pymilvus==2.3.4               # Milvus client
faiss-cpu==1.7.4              # FAISS (development)

# NLP
spacy==3.7.0                  # NLP, keyword extraction
en_core_web_sm                # English model

# Text Processing
nltk==3.8.1                   # Text preprocessing
python-docx==1.1.0            # DOCX parsing
PyPDF2==3.0.1                 # PDF parsing
pdfplumber==0.10.3            # Better PDF extraction

# Machine Learning
scikit-learn==1.3.2           # Classification, clustering
numpy==1.26.0
pandas==2.1.3

# Fuzzy Matching
fuzzywuzzy==0.18.0
python-Levenshtein==0.23.0

# API
fastapi==0.108.0
uvicorn==0.25.0
pydantic==2.5.0
```

**Embedding Models**:
- **Job/Resume Matching**: `sentence-transformers/all-mpnet-base-v2` (768 dim, high quality)
- **Fast Search**: `sentence-transformers/all-MiniLM-L6-v2` (384 dim, faster)

**Classification Models**:
- **Option 1**: Fine-tune BERT on job categories
- **Option 2**: Use zero-shot classification (facebook/bart-large-mnli)
- **Option 3**: Train custom classifier (sklearn)

---

### 5.4 Job Scraper Stack

**Framework**: Python 3.11

**Scraping Libraries**:
```python
# Web Scraping
scrapy==2.11.0                # Framework for web scraping
playwright==1.40.0            # Browser automation (JS-heavy sites)
beautifulsoup4==4.12.2        # HTML parsing
lxml==4.9.3                   # Fast XML/HTML parser
selenium==4.16.0              # Alternative browser automation

# HTTP
requests==2.31.0
httpx==0.25.2                 # Async HTTP client

# Proxy & Anti-Detection
scrapy-rotating-proxies==0.6.2
fake-useragent==1.4.0
scrapy-user-agents==0.1.1

# Task Queue
celery==5.3.4                 # Distributed task queue
redis==5.0.1                  # Celery broker

# Scheduling
APScheduler==3.10.4           # Job scheduling

# Data Processing
pandas==2.1.3
```

**Scraping Strategies**:
1. **API-based**: LinkedIn API, Indeed API (if available)
2. **HTML Scraping**: BeautifulSoup + requests
3. **JavaScript Sites**: Playwright (headless browser)
4. **Anti-Bot Sites**: Rotating proxies + user agents

---

### 5.5 Infrastructure Stack

**Databases**:
- **PostgreSQL 16**: Primary relational database
- **Redis 7**: Caching, sessions, rate limiting, Celery broker
- **Milvus 2.3**: Vector database for embeddings

**Message Queue**:
- **RabbitMQ 3.12** or **Redis** (Symfony Messenger + Celery)

**Containerization**:
- **Docker 24+**: Containerization
- **Docker Compose**: Local development
- **Kubernetes**: Production orchestration

**Cloud Services** (AWS example):
- **Compute**: EKS (Kubernetes), EC2, Lambda
- **Database**: RDS (PostgreSQL), ElastiCache (Redis)
- **Storage**: S3 (resumes, logos)
- **CDN**: CloudFront
- **Load Balancer**: ALB (Application Load Balancer)

---

### 5.6 Monitoring & DevOps

**Monitoring**:
- **Prometheus**: Metrics collection
- **Grafana**: Dashboards
- **Sentry**: Error tracking
- **ELK Stack**: Logs (Elasticsearch, Logstash, Kibana)

**CI/CD**:
- **GitHub Actions** / **GitLab CI** / **Jenkins**
- **Automated Testing**: Run on every PR
- **Deployment**: Blue-green or rolling updates

---

## 6. AI/ML Pipeline

### 6.1 Job Processing Pipeline

```
┌─────────────────────────────────────────────────────────────┐
│                    JOB PROCESSING PIPELINE                   │
└─────────────────────────────────────────────────────────────┘

Step 1: Job Scraped
  ↓ (Scraper Service pushes to queue)

Step 2: Text Preprocessing
  ↓ (AI Service)
  - Remove HTML tags
  - Normalize whitespace
  - Remove special characters
  - Lowercase conversion

Step 3: Keyword Extraction
  ↓ (spaCy NLP)
  - Extract named entities (technologies, tools)
  - Identify key phrases
  - Extract salary info if in description

Step 4: Job Classification
  ↓ (BERT or zero-shot classifier)
  Output:
    - Category: "Engineering", "Sales", "Marketing", etc.
    - Subcategory: "Backend Development", "Frontend", etc.
    - Tech Stack: ["Python", "Django", "PostgreSQL", "Docker"]
    - Seniority: "Senior", "Mid-level", etc.
    - Confidence Score: 0.92

Step 5: Embedding Generation
  ↓ (sentence-transformers)
  - Combine: title + company + description + requirements
  - Generate 768-dimensional vector
  - Store in Milvus vector DB
  - Save embedding_vector_id in PostgreSQL

Step 6: Duplicate Detection
  ↓ (Vector similarity search)
  Algorithm:
    a) Search Milvus for similar embeddings (cosine similarity)
    b) If similarity > 0.95:
       - Check if company name matches (fuzzy)
       - Check if location matches
       - If yes → Mark as duplicate, set canonical_id

Step 7: Spam Detection
  ↓ (Trained classifier)
  Features:
    - Too many emojis
    - Suspicious keywords ("get rich quick", "work from home easy")
    - No company information
    - Unrealistic salary
  Output:
    - is_spam: true/false
    - spam_confidence_score: 0.85

Step 8: Store in PostgreSQL
  ↓
  - Insert into jobs table
  - Insert into job_sources table
  - Insert into job_skills table
  - Insert into ai_job_classifications table
  - If duplicate, insert into job_duplicates table

Step 9: Trigger Matching (if enabled)
  ↓
  - For all active resumes, calculate match scores
  - Send notifications to candidates with high matches
```

---

### 6.2 Resume Processing Pipeline

```
┌─────────────────────────────────────────────────────────────┐
│                  RESUME PROCESSING PIPELINE                  │
└─────────────────────────────────────────────────────────────┘

Step 1: Resume Upload
  ↓ (Frontend → API)
  - Upload file (PDF/DOCX) to S3 or local storage
  - Create record in resumes table

Step 2: Text Extraction
  ↓ (AI Service)
  - PDF: Use PyPDF2 or pdfplumber
  - DOCX: Use python-docx
  - Store parsed_text in database

Step 3: Information Extraction
  ↓ (NLP + Regex)
  Extract:
    a) Contact Info (email, phone)
    b) Skills:
       - Use spaCy NER
       - Match against known skill database
       - Example: ["Python", "React", "AWS", "Docker"]
    c) Work Experience:
       - Parse company names and dates
       - Calculate total years of experience
    d) Education:
       - Degree, institution, year
    e) Certifications

Step 4: Skill Normalization
  ↓
  - "ReactJS" → "React"
  - "Node" → "Node.js"
  - Use synonym mapping dictionary

Step 5: Embedding Generation
  ↓ (sentence-transformers)
  - Generate 768-dim vector from resume text
  - Store in Milvus vector DB
  - Save embedding_vector_id in PostgreSQL

Step 6: Match Calculation
  ↓ (For all active jobs)
  For each job:
    Calculate match score (see algorithm below)
    Store in resume_job_matches table

Step 7: Notify Candidate
  ↓
  - Send notification: "We found 15 jobs matching your resume!"
  - Email top 5 matches
```

---

### 6.3 Resume-to-Job Matching Algorithm

**Weighted Scoring System**:

```python
def calculate_match_score(resume, job):
    """
    Calculate overall match score (0-100%)

    Weights:
    - Vector Similarity: 40%
    - Skills Match: 30%
    - Experience Match: 20%
    - Preferences Match: 10%
    """

    # 1. Vector Similarity (40% weight)
    # Semantic similarity between resume and job description
    resume_embedding = get_embedding(resume.id, 'resume')
    job_embedding = get_embedding(job.id, 'job')

    cosine_sim = cosine_similarity(resume_embedding, job_embedding)
    vector_score = cosine_sim  # Already 0-1

    # 2. Skills Match (30% weight)
    resume_skills = set(resume.skills)  # ["Python", "Django", "React"]
    job_required_skills = set(job.required_skills)  # ["Python", "Django", "PostgreSQL"]
    job_preferred_skills = set(job.preferred_skills)  # ["Docker", "AWS"]

    # Required skills matching (80% of skill score)
    if len(job_required_skills) > 0:
        required_match = len(resume_skills & job_required_skills) / len(job_required_skills)
    else:
        required_match = 1.0

    # Preferred skills matching (20% of skill score)
    if len(job_preferred_skills) > 0:
        preferred_match = len(resume_skills & job_preferred_skills) / len(job_preferred_skills)
    else:
        preferred_match = 0.0

    skills_score = (required_match * 0.8) + (preferred_match * 0.2)

    # 3. Experience Match (20% weight)
    resume_years = resume.years_of_experience  # 5
    job_min_years = job.minimum_experience  # 3
    job_max_years = job.maximum_experience  # 7

    if resume_years >= job_min_years and resume_years <= job_max_years:
        experience_score = 1.0  # Perfect match
    elif resume_years >= job_min_years:
        # Over-qualified (still good)
        experience_score = 0.9
    else:
        # Under-qualified (proportional)
        experience_score = resume_years / job_min_years

    # 4. Preferences Match (10% weight)
    preferences_score = 0.0

    # Location match (40% of preferences)
    if job.is_remote or resume.desired_location == job.location:
        preferences_score += 0.4
    elif resume.country == job.country:
        preferences_score += 0.2  # Same country, different city

    # Work mode match (30% of preferences)
    if resume.desired_work_mode == job.work_mode:
        preferences_score += 0.3
    elif job.work_mode == 'hybrid':
        preferences_score += 0.15  # Hybrid is flexible

    # Salary match (30% of preferences)
    if resume.desired_salary_min <= job.salary_max and resume.desired_salary_max >= job.salary_min:
        preferences_score += 0.3

    # Calculate overall score
    overall_score = (
        (vector_score * 0.40) +
        (skills_score * 0.30) +
        (experience_score * 0.20) +
        (preferences_score * 0.10)
    )

    # Convert to percentage
    overall_percentage = overall_score * 100

    # Store detailed breakdown
    match_details = {
        "vector_similarity": round(vector_score * 100, 2),
        "skills_match": round(skills_score * 100, 2),
        "experience_match": round(experience_score * 100, 2),
        "preferences_match": round(preferences_score * 100, 2),
        "matching_skills": list(resume_skills & job_required_skills),
        "missing_skills": list(job_required_skills - resume_skills),
        "extra_skills": list(resume_skills - job_required_skills),
        "experience_years_required": f"{job_min_years}-{job_max_years}",
        "experience_years_candidate": resume_years
    }

    return {
        "overall_match_score": round(overall_percentage, 2),
        "skills_match_score": round(skills_score * 100, 2),
        "experience_match_score": round(experience_score * 100, 2),
        "match_details": match_details
    }
```

**Example Output**:
```json
{
  "overall_match_score": 87.50,
  "skills_match_score": 85.00,
  "experience_match_score": 100.00,
  "match_details": {
    "vector_similarity": 88.34,
    "skills_match": 85.00,
    "experience_match": 100.00,
    "preferences_match": 70.00,
    "matching_skills": ["Python", "Django", "PostgreSQL", "Docker"],
    "missing_skills": ["Kubernetes"],
    "extra_skills": ["React", "Vue.js"],
    "experience_years_required": "3-5",
    "experience_years_candidate": 4
  }
}
```

---

### 6.4 Duplicate Detection Algorithm

**Multi-Stage Approach**:

```python
def detect_duplicate(new_job):
    """
    Detect if new job is a duplicate of existing job

    Returns:
        canonical_job_id if duplicate, None otherwise
    """

    # Stage 1: Vector Similarity (Primary method)
    # Generate embedding for new job
    new_job_text = f"{new_job.title} {new_job.company_name} {new_job.description}"
    new_embedding = generate_embedding(new_job_text)

    # Search Milvus for similar jobs
    similar_jobs = vector_search(
        embedding=new_embedding,
        collection="job_embeddings",
        limit=20,
        threshold=0.90  # Get jobs with >90% similarity
    )

    for similar_job in similar_jobs:
        similarity_score = similar_job.distance

        # Stage 2: Title Fuzzy Match
        from fuzzywuzzy import fuzz
        title_similarity = fuzz.ratio(
            new_job.title.lower(),
            similar_job.title.lower()
        )

        # Stage 3: Company Match
        company_match = (
            new_job.company_name.lower() == similar_job.company_name.lower()
        )

        # Stage 4: Location Match
        location_match = (
            new_job.city == similar_job.city and
            new_job.country_code == similar_job.country_code
        )

        # Duplicate Detection Rules
        if similarity_score >= 0.95 and company_match:
            # Very high similarity + same company = duplicate
            return {
                "canonical_id": similar_job.id,
                "similarity_score": similarity_score,
                "method": "embedding"
            }

        elif title_similarity >= 90 and company_match and location_match:
            # Exact title + company + location = duplicate
            return {
                "canonical_id": similar_job.id,
                "similarity_score": similarity_score,
                "method": "fuzzy_match"
            }

        elif similarity_score >= 0.98:
            # Extremely high similarity (even different sources)
            # Likely same job, different posting
            return {
                "canonical_id": similar_job.id,
                "similarity_score": similarity_score,
                "method": "embedding"
            }

    # No duplicate found
    return None


# Usage Example
def process_scraped_job(job_data, source_platform):
    """Process a newly scraped job"""

    # Check for duplicate
    duplicate_info = detect_duplicate(job_data)

    if duplicate_info:
        # It's a duplicate
        canonical_job_id = duplicate_info['canonical_id']

        # Create job_sources entry (link to canonical job)
        create_job_source(
            job_id=canonical_job_id,
            source_platform=source_platform,
            source_url=job_data.url,
            source_job_id=job_data.external_id
        )

        # Record duplicate relationship
        create_job_duplicate(
            canonical_job_id=canonical_job_id,
            duplicate_job_id=job_data.id,
            similarity_score=duplicate_info['similarity_score'],
            detection_method=duplicate_info['method']
        )

        # Update duplicate count
        increment_duplicate_count(canonical_job_id)

        print(f"Duplicate detected! Linked to job {canonical_job_id}")

    else:
        # New unique job
        job_id = create_job(job_data)

        # This job is its own canonical version
        update_job_canonical_id(job_id, job_id)

        # Create job_sources entry
        create_job_source(
            job_id=job_id,
            source_platform=source_platform,
            source_url=job_data.url,
            source_job_id=job_data.external_id
        )

        print(f"New job created: {job_id}")
```

---

### 6.5 Keyword Extraction & Synonym Matching

**Skill Extraction with spaCy**:

```python
import spacy
from spacy.matcher import PhraseMatcher

# Load spaCy model
nlp = spacy.load("en_core_web_sm")

# Known skills database (can be stored in DB)
KNOWN_SKILLS = [
    "Python", "Java", "JavaScript", "TypeScript", "React", "Vue.js", "Angular",
    "Node.js", "Django", "Flask", "FastAPI", "Express.js",
    "PostgreSQL", "MySQL", "MongoDB", "Redis", "Elasticsearch",
    "Docker", "Kubernetes", "AWS", "Azure", "GCP",
    "Git", "CI/CD", "Jenkins", "GitHub Actions",
    "Machine Learning", "Deep Learning", "TensorFlow", "PyTorch",
    "REST API", "GraphQL", "Microservices",
    # ... add 500+ more skills
]

# Skill synonym mapping
SKILL_SYNONYMS = {
    "ReactJS": "React",
    "React.js": "React",
    "NodeJS": "Node.js",
    "Node": "Node.js",
    "Postgres": "PostgreSQL",
    "Mongo": "MongoDB",
    "K8s": "Kubernetes",
    "ML": "Machine Learning",
    "DL": "Deep Learning",
    # ... more synonyms
}


def extract_skills(text):
    """Extract technical skills from job description or resume"""

    # Normalize text
    text = text.lower()

    # Use PhraseMatcher for exact skill matching
    matcher = PhraseMatcher(nlp.vocab, attr="LOWER")
    patterns = [nlp.make_doc(skill.lower()) for skill in KNOWN_SKILLS]
    matcher.add("SKILLS", patterns)

    doc = nlp(text)
    matches = matcher(doc)

    extracted_skills = set()
    for match_id, start, end in matches:
        skill = doc[start:end].text
        # Normalize using synonyms
        normalized_skill = SKILL_SYNONYMS.get(skill, skill)
        extracted_skills.add(normalized_skill)

    # Also extract using NER (Named Entity Recognition)
    for ent in doc.ents:
        if ent.label_ in ["PRODUCT", "ORG"]:  # Technologies often tagged as PRODUCT
            skill_candidate = ent.text
            if skill_candidate in KNOWN_SKILLS or skill_candidate in SKILL_SYNONYMS:
                normalized_skill = SKILL_SYNONYMS.get(skill_candidate, skill_candidate)
                extracted_skills.add(normalized_skill)

    return list(extracted_skills)


# Example usage
job_description = """
We are looking for a Senior Backend Developer with expertise in Python and Django.
You should have experience with PostgreSQL, Redis, and Docker. Knowledge of
Kubernetes and AWS is a plus. Experience with RESTful APIs and microservices
architecture is required.
"""

skills = extract_skills(job_description)
print(skills)
# Output: ['Python', 'Django', 'PostgreSQL', 'Redis', 'Docker', 'Kubernetes', 'AWS', 'REST API', 'Microservices']
```

---

### 6.6 Spam Detection

**Machine Learning Classifier**:

```python
from sklearn.ensemble import RandomForestClassifier
from sklearn.feature_extraction.text import TfidfVectorizer
import re

class JobSpamDetector:
    def __init__(self):
        self.vectorizer = TfidfVectorizer(max_features=1000)
        self.classifier = RandomForestClassifier(n_estimators=100)
        self.is_trained = False

    def extract_features(self, job):
        """Extract features for spam detection"""

        features = {}

        # Text features
        text = f"{job.title} {job.description}"

        # 1. Emoji count
        emoji_pattern = re.compile("["
            u"\U0001F600-\U0001F64F"  # emoticons
            u"\U0001F300-\U0001F5FF"  # symbols & pictographs
            "]+", flags=re.UNICODE)
        features['emoji_count'] = len(emoji_pattern.findall(text))

        # 2. Excessive punctuation
        features['exclamation_count'] = text.count('!')
        features['question_count'] = text.count('?')

        # 3. Suspicious keywords
        spam_keywords = [
            'get rich quick', 'work from home easy', 'no experience needed',
            'earn money fast', 'unlimited earning potential', 'be your own boss',
            'investment opportunity', 'multi-level marketing', 'mlm'
        ]
        features['spam_keyword_count'] = sum(
            1 for keyword in spam_keywords if keyword in text.lower()
        )

        # 4. Missing information
        features['missing_company'] = 1 if not job.company_name else 0
        features['missing_location'] = 1 if not job.location else 0
        features['missing_salary'] = 1 if not job.salary_min else 0

        # 5. Unrealistic salary
        if job.salary_max and job.salary_max > 500000:  # $500k+ is suspicious
            features['unrealistic_salary'] = 1
        else:
            features['unrealistic_salary'] = 0

        # 6. Description length
        features['description_length'] = len(job.description)
        features['too_short'] = 1 if len(job.description) < 100 else 0

        # 7. URL count (too many external links)
        url_pattern = re.compile(r'http[s]?://(?:[a-zA-Z]|[0-9]|[$-_@.&+]|[!*\\(\\),]|(?:%[0-9a-fA-F][0-9a-fA-F]))+')
        features['url_count'] = len(url_pattern.findall(text))

        return features

    def predict_spam(self, job):
        """Predict if job is spam"""

        if not self.is_trained:
            # Load pre-trained model
            self.load_model()

        features = self.extract_features(job)

        # Simple rule-based detection (fallback)
        spam_score = 0.0

        if features['spam_keyword_count'] > 2:
            spam_score += 0.4
        if features['missing_company']:
            spam_score += 0.2
        if features['emoji_count'] > 5:
            spam_score += 0.2
        if features['unrealistic_salary']:
            spam_score += 0.15
        if features['too_short']:
            spam_score += 0.1

        is_spam = spam_score > 0.5

        return {
            'is_spam': is_spam,
            'confidence_score': spam_score,
            'features': features
        }
```

---

## 7. Data Flow Diagrams

### 7.1 Job Scraping Flow

```
┌─────────────────────────────────────────────────────────────┐
│                     JOB SCRAPING FLOW                        │
└─────────────────────────────────────────────────────────────┘

1. Scheduler (APScheduler / Cron)
   - Runs every hour (configurable per platform)
   - Checks scraper_configs table for active platforms
   ↓

2. Scraper Service (Python)
   - For each platform:
     a) Load config from scraper_configs
     b) Create scraper_runs record (status: 'running')
     c) Scrape jobs (API or web scraping)
     d) Parse HTML/JSON to extract job data
   ↓

3. Data Normalization
   - Standardize fields (job_type, work_mode, etc.)
   - Parse salary from various formats
   - Extract location (city, country)
   - Clean HTML from description
   ↓

4. Message Queue (RabbitMQ)
   - Push job data to 'job_processing_queue'
   - Each job as separate message
   ↓

5. AI Service Worker (Celery/Symfony Messenger)
   - Consume messages from queue
   - For each job:
     a) Generate embedding (Sentence-BERT)
     b) Store in Milvus vector DB
     c) Classify job (category, tech stack, seniority)
     d) Extract keywords (spaCy)
     e) Detect duplicates (vector similarity search)
     f) Check for spam
   ↓

6. Database Storage (PostgreSQL)
   - Insert into jobs table
   - Insert into job_sources table
   - Insert into job_skills table
   - Insert into ai_job_classifications table
   - If duplicate:
     - Insert into job_duplicates table
     - Update canonical_id
   ↓

7. Update Scraper Stats
   - Update scraper_runs record
     - status: 'completed'
     - jobs_found, jobs_created, duplicates_detected
     - completed_at, duration_seconds
   - Update scraper_configs
     - last_successful_scrape
     - total_jobs_scraped
   ↓

8. Trigger Notifications (Optional)
   - For candidates with matching preferences:
     - Calculate match score
     - Send notification if score > 70%
```

---

### 7.2 Resume Upload & Matching Flow

```
┌─────────────────────────────────────────────────────────────┐
│                RESUME UPLOAD & MATCHING FLOW                 │
└─────────────────────────────────────────────────────────────┘

1. Frontend Upload
   - User selects PDF/DOCX file
   - Frontend validates file (type, size < 5MB)
   - POST /api/resumes (multipart/form-data)
   ↓

2. API (Symfony)
   - Validate file
   - Save to storage (S3 or /storage/resumes/)
   - Create record in resumes table
     - file_path, file_name, mime_type
     - is_primary (if first resume)
   - Return resume_id
   ↓

3. Message Queue
   - Push to 'resume_processing_queue'
   - Message: { resume_id, candidate_id }
   ↓

4. AI Service Worker
   - Fetch file from storage
   - Extract text:
     - PDF: pdfplumber.open(file).extract_text()
     - DOCX: docx.Document(file).paragraphs
   - Store parsed_text in database
   ↓

5. Information Extraction (NLP)
   - Extract skills (spaCy + keyword matching)
   - Parse work experience (regex + NER)
   - Calculate total years of experience
   - Extract education, certifications
   - Store in candidate_skills table
   ↓

6. Embedding Generation
   - Generate 768-dim vector (Sentence-BERT)
   - Store in Milvus resume_embeddings collection
   - Update embedding_vector_id in resumes table
   ↓

7. Match Calculation
   - Fetch all active jobs (is_active=true, not expired)
   - For each job:
     a) Calculate match score (vector + skills + experience + preferences)
     b) Insert into resume_job_matches table
   - This may take 10-30 seconds for 10,000 jobs
   ↓

8. Response to Frontend
   - WebSocket or polling:
     - "Processing... 25% complete"
     - "Processing... 75% complete"
     - "Done! Found 147 matching jobs"
   ↓

9. Notifications
   - Create notification: "Your resume has been processed!"
   - Email: "Top 10 jobs matching your resume (85%+ match)"
   - Update frontend UI
```

---

### 7.3 Job Search Flow

```
┌─────────────────────────────────────────────────────────────┐
│                      JOB SEARCH FLOW                         │
└─────────────────────────────────────────────────────────────┘

1. User Input (Frontend)
   - Search query: "remote python developer"
   - Filters:
     - Location: "United States"
     - Job Type: ["full-time"]
     - Work Mode: ["remote"]
     - Salary: $80k - $150k
     - Posted: Last 7 days
   ↓

2. API Request
   - GET /api/jobs?q=remote+python+developer&location=US&...
   ↓

3. Search Strategy Decision (Backend)

   IF query is semantic (e.g., "AI jobs for beginners"):
     ↓
   3a. Semantic Search (Vector DB)
     - Generate query embedding
     - Search Milvus: similarity search
     - Get top 1000 job IDs (by relevance)

   ELSE (keyword search):
     ↓
   3b. Full-Text Search (PostgreSQL)
     - Use FULLTEXT index on title, description
     - OR use Elasticsearch if available
   ↓

4. Apply Filters (PostgreSQL)
   - Filter by location (country_code, city)
   - Filter by job_type, work_mode
   - Filter by salary range
   - Filter by posted_date
   - Filter by sources (if specified)
   - Exclude hidden jobs (LEFT JOIN hidden_jobs WHERE hidden_jobs.id IS NULL)
   - Exclude inactive jobs (is_active = true)
   ↓

5. Duplicate Handling
   - GROUP BY canonical_id
   - For each group:
     - Show canonical job (main card)
     - Join with job_sources to show "Found on 3 platforms"
     - Show platform badges (LinkedIn, Indeed, Glassdoor)
   ↓

6. Match Score Enhancement (If user has resume)
   - LEFT JOIN resume_job_matches ON (resume_job_matches.job_id = jobs.id)
   - Include overall_match_score in results
   - Sort by match_score DESC (if available), else by posted_date DESC
   ↓

7. Pagination
   - LIMIT 20 OFFSET 0 (first page)
   - Return total count for pagination
   ↓

8. Response Format
   {
     "total": 1247,
     "page": 1,
     "per_page": 20,
     "jobs": [
       {
         "id": "uuid",
         "title": "Senior Python Developer",
         "company": "TechCorp",
         "location": "San Francisco, CA",
         "salary": { "min": 120000, "max": 180000, "currency": "USD" },
         "job_type": "full-time",
         "work_mode": "remote",
         "posted_date": "2025-11-20T10:30:00Z",
         "is_duplicate": false,
         "duplicate_count": 3,
         "sources": ["LinkedIn", "Indeed", "Glassdoor"],
         "match_score": 87.5,  // If user has resume
         "status": null  // "saved", "applied", or null
       },
       // ... more jobs
     ]
   }
   ↓

9. Frontend Rendering
   - Display job cards
   - Show match score badges (if available)
   - Show source badges
   - Show "View 2 more sources" button
   - Apply saved/applied/hidden states
```

---

### 7.4 Application Flow

```
┌─────────────────────────────────────────────────────────────┐
│                      APPLICATION FLOW                        │
└─────────────────────────────────────────────────────────────┘

1. User Clicks "Apply"
   - Frontend opens application modal
   - User selects resume (if multiple)
   - User writes cover letter (optional)
   ↓

2. Submit Application
   - POST /api/applications
   - Body: { job_id, resume_id, cover_letter }
   ↓

3. API Validation (Symfony)
   - Check if already applied (UNIQUE constraint)
   - Validate job is active
   - Calculate match score (if not already calculated)
   ↓

4. Create Application Record
   - Insert into applications table
     - status: 'applied'
     - applied_at: NOW()
     - match_score: from resume_job_matches
   ↓

5. External Application (if applicable)
   - If job has external_apply_url:
     - Return redirect URL to frontend
     - Frontend opens in new tab
   - If job has application_email:
     - Send email with resume attachment
   ↓

6. Notifications
   - Candidate: "Application submitted successfully!"
   - Recruiter (if internal job): "New application received"
   ↓

7. Track Application
   - User can view in "My Applications" section
   - Recruiter can view in "Candidates" section
   - Status updates: viewed → shortlisted → interviewing → offered
```

---

## 8. Key Features Implementation

### 8.1 Grouping Duplicate Jobs

**Database Query**:
```sql
-- Get all jobs with their duplicate counts
SELECT
    j.*,
    COUNT(jd.duplicate_job_id) as duplicate_count,
    JSON_AGG(
        JSON_BUILD_OBJECT(
            'source', js.source_platform,
            'url', js.source_url,
            'posted_date', js.source_posted_date
        )
    ) as sources
FROM jobs j
LEFT JOIN job_duplicates jd ON j.id = jd.canonical_job_id
LEFT JOIN job_sources js ON j.id = js.job_id
WHERE j.canonical_id = j.id  -- Only show canonical jobs
    AND j.is_active = true
GROUP BY j.id
ORDER BY j.posted_date DESC;
```

**API Response**:
```json
{
  "id": "abc-123",
  "title": "Senior Backend Engineer",
  "company": "Google",
  "duplicate_count": 4,
  "sources": [
    {
      "source": "LinkedIn",
      "url": "https://linkedin.com/jobs/...",
      "posted_date": "2025-11-20"
    },
    {
      "source": "Indeed",
      "url": "https://indeed.com/viewjob?jk=...",
      "posted_date": "2025-11-20"
    },
    {
      "source": "Glassdoor",
      "url": "https://glassdoor.com/job/...",
      "posted_date": "2025-11-21"
    }
  ]
}
```

**Frontend Display**:
```
┌─────────────────────────────────────────────────────────────┐
│ Senior Backend Engineer                     🔥 87% Match    │
│ Google • San Francisco, CA • Remote                         │
│                                                             │
│ $150k - $200k/year                                          │
│                                                             │
│ Posted 2 days ago                                           │
│                                                             │
│ Found on 3 platforms:                                       │
│ [LinkedIn] [Indeed] [Glassdoor]                             │
│                                                             │
│ [View Details] [Save] [Apply]                               │
└─────────────────────────────────────────────────────────────┘
```

---

### 8.2 Advanced Filtering

**Multi-Criteria Filter API**:

```php
// Symfony API Platform Filter

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\RangeFilter;

#[ApiResource(
    operations: [
        new GetCollection(),
    ],
    paginationEnabled: true,
    paginationItemsPerPage: 20
)]
#[ApiFilter(SearchFilter::class, properties: [
    'title' => 'partial',
    'company_name' => 'partial',
    'location' => 'partial',
    'country_code' => 'exact',
    'job_type' => 'exact',
    'work_mode' => 'exact',
    'experience_level' => 'exact'
])]
#[ApiFilter(RangeFilter::class, properties: [
    'salary_min',
    'salary_max',
    'posted_date'
])]
#[ApiFilter(DateFilter::class, properties: ['posted_date'])]
class Job
{
    // ...
}
```

**Frontend Filter Component**:
```vue
<template>
  <div class="filters">
    <!-- Keyword Search -->
    <input v-model="filters.query" placeholder="Search jobs..." />

    <!-- Location -->
    <select v-model="filters.country">
      <option value="">All Countries</option>
      <option value="US">United States</option>
      <option value="CA">Canada</option>
      <option value="UK">United Kingdom</option>
      <!-- ... -->
    </select>

    <!-- Date Posted -->
    <select v-model="filters.datePosted">
      <option value="">Any time</option>
      <option value="1">Last 24 hours</option>
      <option value="7">Last 7 days</option>
      <option value="30">Last 30 days</option>
    </select>

    <!-- Job Type -->
    <div class="checkbox-group">
      <label>
        <input type="checkbox" value="full-time" v-model="filters.types" />
        Full-time
      </label>
      <label>
        <input type="checkbox" value="contract" v-model="filters.types" />
        Contract
      </label>
      <!-- ... -->
    </div>

    <!-- Salary Range -->
    <input
      type="range"
      v-model="filters.salaryMin"
      min="0"
      max="300000"
      step="10000"
    />
    <span>${{ filters.salaryMin }}k - ${{ filters.salaryMax }}k</span>

    <!-- Sources -->
    <div class="checkbox-group">
      <label v-for="source in availableSources" :key="source">
        <input type="checkbox" :value="source" v-model="filters.sources" />
        {{ source }}
      </label>
    </div>
  </div>
</template>
```

---

### 8.3 AI-Powered Search

**Semantic Search Implementation**:

```python
from fastapi import FastAPI, HTTPException
from sentence_transformers import SentenceTransformer
from pymilvus import Collection, connections

app = FastAPI()

# Initialize model
model = SentenceTransformer('all-mpnet-base-v2')

# Connect to Milvus
connections.connect(host='localhost', port='19530')
job_collection = Collection('job_embeddings')
job_collection.load()


@app.get("/api/ai/search")
async def semantic_search(
    query: str,
    limit: int = 100,
    min_similarity: float = 0.7
):
    """
    Semantic job search using embeddings

    Example: "Entry-level AI jobs that don't require PhD"
    """

    # Generate query embedding
    query_embedding = model.encode(query)

    # Search Milvus
    search_params = {
        "metric_type": "COSINE",
        "params": {"nprobe": 10}
    }

    results = job_collection.search(
        data=[query_embedding],
        anns_field="embedding",
        param=search_params,
        limit=limit,
        output_fields=["job_id", "title", "company_name"]
    )

    # Filter by minimum similarity
    job_ids = []
    for hit in results[0]:
        if hit.distance >= min_similarity:
            job_ids.append(hit.entity.get('job_id'))

    return {
        "job_ids": job_ids,
        "count": len(job_ids),
        "query": query
    }
```

**Frontend Integration**:
```typescript
// When user types semantic query
async function searchJobs(query: string) {
  // First, try semantic search
  const aiResults = await fetch(`/api/ai/search?query=${encodeURIComponent(query)}`);
  const { job_ids } = await aiResults.json();

  // Then fetch full job details from main API
  const jobs = await fetch(`/api/jobs?ids=${job_ids.join(',')}`);

  return jobs.json();
}
```

---

## 9. Scalability & Performance

### 9.1 Horizontal Scaling Strategy

**Component-wise Scaling**:

| Component | Scaling Strategy | Instances (MVP) | Instances (Production) |
|-----------|------------------|-----------------|------------------------|
| Frontend | CDN + Static hosting | 1 | Auto-scale |
| API (Symfony) | Load balanced | 2 | 5-10 |
| AI Service | GPU-enabled instances | 1 | 3-5 |
| Scraper Workers | Celery distributed workers | 3 | 10-20 |
| PostgreSQL | Master + read replicas | 1 | 1 master + 2 replicas |
| Vector DB (Milvus) | Cluster mode | 1 | 3-node cluster |
| Redis | Cluster mode | 1 | 3-node cluster |
| RabbitMQ | Cluster | 1 | 3-node cluster |

---

### 9.2 Caching Strategy

**Redis Caching Layers**:

```python
# Cache job listings (short TTL for fresh results)
CACHE_JOBS_LIST = "jobs:list:{filters_hash}"  # TTL: 5 minutes

# Cache job details (longer TTL)
CACHE_JOB_DETAIL = "job:{job_id}"  # TTL: 1 hour

# Cache search results
CACHE_SEARCH = "search:{query_hash}"  # TTL: 10 minutes

# Cache resume match scores (recalculate daily)
CACHE_RESUME_MATCHES = "resume:{resume_id}:matches"  # TTL: 24 hours

# Cache user sessions
CACHE_SESSION = "session:{session_id}"  # TTL: 2 hours
```

**Symfony Cache Implementation**:
```php
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class JobService
{
    public function __construct(
        private CacheInterface $cache,
        private JobRepository $jobRepository
    ) {}

    public function getJobs(array $filters): array
    {
        $cacheKey = 'jobs:list:' . md5(json_encode($filters));

        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($filters) {
            $item->expiresAfter(300); // 5 minutes

            return $this->jobRepository->findByFilters($filters);
        });
    }
}
```

---

### 9.3 Database Optimization

**Indexing Strategy**:

```sql
-- Full-text search index
CREATE INDEX idx_job_fulltext ON jobs USING GIN (
    to_tsvector('english', title || ' ' || description)
);

-- Composite indexes for common queries
CREATE INDEX idx_job_search ON jobs (country_code, job_type, work_mode, is_active, posted_date DESC);

-- Partial indexes for active jobs only
CREATE INDEX idx_active_jobs ON jobs (posted_date DESC) WHERE is_active = true;

-- JSONB GIN indexes
CREATE INDEX idx_classification_tags ON jobs USING GIN (classification_tags);

-- Covering index for job list queries
CREATE INDEX idx_job_list_covering ON jobs (id, title, company_name, location, salary_min, salary_max, job_type, work_mode, posted_date) WHERE is_active = true;
```

**Query Optimization**:
```sql
-- Bad: N+1 query problem
SELECT * FROM jobs WHERE is_active = true;
-- Then for each job: SELECT * FROM job_sources WHERE job_id = ?

-- Good: Single query with JOIN
SELECT
    j.*,
    JSON_AGG(js.*) as sources
FROM jobs j
LEFT JOIN job_sources js ON j.id = js.job_id
WHERE j.is_active = true
GROUP BY j.id;
```

**Materialized Views** (for analytics):
```sql
-- Daily job statistics
CREATE MATERIALIZED VIEW daily_job_stats AS
SELECT
    DATE(created_at) as date,
    COUNT(*) as total_jobs,
    COUNT(CASE WHEN is_duplicate THEN 1 END) as duplicates,
    COUNT(CASE WHEN is_spam THEN 1 END) as spam_jobs,
    AVG(salary_max) as avg_salary
FROM jobs
GROUP BY DATE(created_at);

-- Refresh daily
REFRESH MATERIALIZED VIEW daily_job_stats;
```

---

### 9.4 Rate Limiting

**API Rate Limiting** (per user):
```yaml
# config/packages/api_platform.yaml
api_platform:
    defaults:
        rate_limit:
            enabled: true
            max_requests: 100
            interval: '1 hour'
```

**Scraper Rate Limiting**:
```python
from ratelimit import limits, sleep_and_retry

# LinkedIn: 30 requests per minute
@sleep_and_retry
@limits(calls=30, period=60)
def scrape_linkedin_job(url):
    response = requests.get(url)
    return response.json()
```

---

### 9.5 Background Job Processing

**Queue Architecture**:

```
┌─────────────────────────────────────────────────────────────┐
│                         QUEUES                               │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  1. job_scraping_queue                                      │
│     - Priority: High                                        │
│     - Workers: 10                                           │
│     - Task: Scrape jobs from portals                        │
│                                                             │
│  2. job_processing_queue                                    │
│     - Priority: High                                        │
│     - Workers: 5                                            │
│     - Task: AI processing (embeddings, classification)      │
│                                                             │
│  3. resume_processing_queue                                 │
│     - Priority: Medium                                      │
│     - Workers: 3                                            │
│     - Task: Parse resume, generate embeddings               │
│                                                             │
│  4. matching_queue                                          │
│     - Priority: Medium                                      │
│     - Workers: 3                                            │
│     - Task: Calculate resume-job match scores               │
│                                                             │
│  5. notification_queue                                      │
│     - Priority: Low                                         │
│     - Workers: 2                                            │
│     - Task: Send emails, push notifications                 │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

---

## 10. Security Considerations

### 10.1 Authentication & Authorization

**JWT Authentication Flow**:

```
1. User Login
   POST /api/auth/login
   { "email": "user@example.com", "password": "..." }
   ↓
2. Server validates credentials
   ↓
3. Generate JWT tokens
   {
     "access_token": "eyJhbGciOiJIUzI1...",  // Valid for 1 hour
     "refresh_token": "eyJhbGciOiJIUzI1...", // Valid for 7 days
     "token_type": "Bearer"
   }
   ↓
4. Client stores tokens (localStorage/cookie)
   ↓
5. Subsequent requests include token
   Authorization: Bearer eyJhbGciOiJIUzI1...
   ↓
6. Server validates token
   - Check signature
   - Check expiration
   - Extract user_id and roles
```

**Role-Based Access Control (RBAC)**:

```php
// Symfony Security Voter
class JobVoter extends Voter
{
    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, ['VIEW', 'EDIT', 'DELETE'])
            && $subject instanceof Job;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        $job = $subject;

        switch ($attribute) {
            case 'VIEW':
                // Everyone can view active jobs
                return $job->isActive();

            case 'EDIT':
            case 'DELETE':
                // Only recruiter who posted can edit/delete
                return $user->isRecruiter()
                    && $job->getRecruiter()->getId() === $user->getId();
        }

        return false;
    }
}
```

---

### 10.2 Data Security

**Encryption**:
- **Passwords**: Bcrypt hashing (Symfony default)
- **Sensitive Data**: AES-256 encryption for stored API keys
- **In Transit**: TLS 1.3 (HTTPS only)
- **At Rest**: Database encryption (AWS RDS encryption)

**Resume Security**:
```php
// Secure file upload
class ResumeUploadService
{
    public function uploadResume(UploadedFile $file, User $user): Resume
    {
        // 1. Validate file type
        $allowedMimes = ['application/pdf', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            throw new InvalidFileException('Only PDF and DOCX files allowed');
        }

        // 2. Validate file size (max 5MB)
        if ($file->getSize() > 5 * 1024 * 1024) {
            throw new InvalidFileException('File too large (max 5MB)');
        }

        // 3. Scan for viruses (using ClamAV)
        if (!$this->virusScanner->isSafe($file)) {
            throw new SecurityException('File failed virus scan');
        }

        // 4. Generate secure filename
        $filename = Uuid::uuid4() . '.' . $file->guessExtension();

        // 5. Store with user-specific path
        $path = sprintf('resumes/%s/%s', $user->getId(), $filename);
        $this->storage->put($path, file_get_contents($file->getPathname()));

        // 6. Create database record
        $resume = new Resume();
        $resume->setFilePath($path);
        $resume->setFileName($file->getClientOriginalName());
        $resume->setCandidate($user->getCandidateProfile());

        return $resume;
    }
}
```

---

### 10.3 Input Validation

**API Input Validation** (Symfony):
```php
use Symfony\Component\Validator\Constraints as Assert;

class CreateJobDTO
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 5, max: 500)]
    public string $title;

    #[Assert\NotBlank]
    #[Assert\Length(min: 50, max: 10000)]
    public string $description;

    #[Assert\Choice(choices: ['full-time', 'part-time', 'contract', 'internship'])]
    public string $jobType;

    #[Assert\Range(min: 0, max: 1000000)]
    public ?int $salaryMin = null;

    #[Assert\GreaterThan(propertyPath: 'salaryMin')]
    public ?int $salaryMax = null;
}
```

**XSS Prevention**:
```php
// Auto-escape output (Twig)
{{ job.description|e }}

// Sanitize HTML input
use HTMLPurifier;

$config = HTMLPurifier_Config::createDefault();
$purifier = new HTMLPurifier($config);
$cleanDescription = $purifier->purify($input['description']);
```

**SQL Injection Prevention**:
```php
// Use Doctrine ORM (parameterized queries)
$jobs = $this->entityManager
    ->createQuery('SELECT j FROM App\Entity\Job j WHERE j.title LIKE :title')
    ->setParameter('title', '%' . $searchTerm . '%')
    ->getResult();
```

---

### 10.4 GDPR Compliance

**User Data Management**:

```php
class GDPRService
{
    /**
     * Export all user data (GDPR Right to Access)
     */
    public function exportUserData(User $user): array
    {
        return [
            'personal_info' => [
                'email' => $user->getEmail(),
                'name' => $user->getFirstName() . ' ' . $user->getLastName(),
                'created_at' => $user->getCreatedAt(),
            ],
            'profile' => $user->getCandidateProfile()?->toArray(),
            'resumes' => $user->getResumes()->map(fn($r) => $r->toArray())->toArray(),
            'applications' => $user->getApplications()->map(fn($a) => $a->toArray())->toArray(),
            'saved_jobs' => $user->getSavedJobs()->map(fn($j) => $j->toArray())->toArray(),
        ];
    }

    /**
     * Delete all user data (GDPR Right to Erasure)
     */
    public function deleteUserData(User $user): void
    {
        // 1. Delete resumes from storage
        foreach ($user->getResumes() as $resume) {
            $this->storage->delete($resume->getFilePath());
        }

        // 2. Delete from vector DB
        $this->milvusClient->delete(
            collection_name='resume_embeddings',
            expr=f'candidate_id == "{$user->getId()}"'
        );

        // 3. Cascade delete from PostgreSQL (configured in entities)
        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }
}
```

---

## 11. Deployment Architecture

### 11.1 Development Environment

```yaml
# docker-compose.yml
version: '3.8'

services:
  # Frontend
  frontend:
    build: ./frontend
    ports:
      - "5173:5173"
    volumes:
      - ./frontend:/app
    environment:
      - VITE_API_URL=http://localhost:8000

  # Backend API
  api:
    build: ./backend
    ports:
      - "8000:8000"
    volumes:
      - ./backend:/var/www/html
    environment:
      - DATABASE_URL=postgresql://postgres:password@db:5432/jobsfusionhub
      - REDIS_URL=redis://redis:6379
    depends_on:
      - db
      - redis

  # AI/ML Service
  ai-service:
    build: ./ai-service
    ports:
      - "8001:8000"
    volumes:
      - ./ai-service:/app
    environment:
      - DATABASE_URL=postgresql://postgres:password@db:5432/jobsfusionhub
      - MILVUS_HOST=milvus
    depends_on:
      - db
      - milvus

  # PostgreSQL
  db:
    image: postgres:16-alpine
    ports:
      - "5432:5432"
    environment:
      - POSTGRES_DB=jobsfusionhub
      - POSTGRES_USER=postgres
      - POSTGRES_PASSWORD=password
    volumes:
      - postgres_data:/var/lib/postgresql/data

  # Redis
  redis:
    image: redis:7-alpine
    ports:
      - "6379:6379"

  # Milvus (Vector DB)
  milvus:
    image: milvusdb/milvus:latest
    ports:
      - "19530:19530"
    environment:
      - ETCD_ENDPOINTS=etcd:2379
      - MINIO_ADDRESS=minio:9000
    depends_on:
      - etcd
      - minio

  # Supporting services for Milvus
  etcd:
    image: quay.io/coreos/etcd:latest
    environment:
      - ETCD_AUTO_COMPACTION_MODE=revision
      - ETCD_AUTO_COMPACTION_RETENTION=1000

  minio:
    image: minio/minio:latest
    environment:
      - MINIO_ACCESS_KEY=minioadmin
      - MINIO_SECRET_KEY=minioadmin
    command: server /minio_data

volumes:
  postgres_data:
```

---

### 11.2 Production Architecture (AWS)

```
┌─────────────────────────────────────────────────────────────┐
│                       PRODUCTION AWS                         │
└─────────────────────────────────────────────────────────────┘

Internet
  │
  ↓
CloudFront (CDN)
  │
  ├─→ S3 Bucket (Frontend static files)
  │
  └─→ Application Load Balancer (ALB)
      │
      ├─→ EKS Cluster (Kubernetes)
      │   │
      │   ├─→ API Pods (Symfony) - 5 replicas
      │   │   - Auto-scaling: 5-20 pods
      │   │   - Resources: 2 CPU, 4GB RAM each
      │   │
      │   ├─→ AI Service Pods (FastAPI) - 3 replicas
      │   │   - GPU-enabled instances (g4dn.xlarge)
      │   │   - Resources: 4 CPU, 16GB RAM, 1 GPU each
      │   │
      │   └─→ Scraper Worker Pods (Celery) - 10 replicas
      │       - Resources: 2 CPU, 4GB RAM each
      │
      ├─→ RDS PostgreSQL (Multi-AZ)
      │   - Primary: db.r6g.xlarge (4 vCPU, 32GB RAM)
      │   - Read Replica: db.r6g.large (2 vCPU, 16GB RAM)
      │   - Backup: Daily snapshots, 7-day retention
      │
      ├─→ ElastiCache Redis (Cluster mode)
      │   - Node type: cache.r6g.large
      │   - Nodes: 3 (1 primary, 2 replicas)
      │
      ├─→ Milvus on EC2 (or managed alternative)
      │   - Instance: c6g.4xlarge (16 vCPU, 32GB RAM)
      │   - Storage: EBS gp3 (1TB)
      │
      ├─→ RabbitMQ on EC2 (or Amazon MQ)
      │   - Instance: t3.medium
      │   - Cluster: 3 nodes
      │
      └─→ S3 Buckets
          - Resumes (private, encrypted)
          - Company logos (public)
          - Backups

Monitoring:
  - CloudWatch (logs, metrics)
  - Prometheus + Grafana (custom metrics)
  - Sentry (error tracking)

Security:
  - WAF (Web Application Firewall)
  - Shield (DDoS protection)
  - Secrets Manager (API keys, credentials)
  - VPC with private subnets
  - Security groups (firewall rules)
```

---

### 11.3 Kubernetes Deployment

**API Deployment**:
```yaml
# k8s/api-deployment.yaml
apiVersion: apps/v1
kind: Deployment
metadata:
  name: api
spec:
  replicas: 5
  selector:
    matchLabels:
      app: api
  template:
    metadata:
      labels:
        app: api
    spec:
      containers:
      - name: api
        image: jobsfusionhub/api:latest
        ports:
        - containerPort: 8000
        env:
        - name: DATABASE_URL
          valueFrom:
            secretKeyRef:
              name: db-credentials
              key: url
        - name: REDIS_URL
          value: "redis://redis-service:6379"
        resources:
          requests:
            memory: "2Gi"
            cpu: "1000m"
          limits:
            memory: "4Gi"
            cpu: "2000m"
        livenessProbe:
          httpGet:
            path: /health
            port: 8000
          initialDelaySeconds: 30
          periodSeconds: 10
        readinessProbe:
          httpGet:
            path: /ready
            port: 8000
          initialDelaySeconds: 5
          periodSeconds: 5
---
apiVersion: v1
kind: Service
metadata:
  name: api-service
spec:
  selector:
    app: api
  ports:
  - port: 80
    targetPort: 8000
  type: LoadBalancer
---
apiVersion: autoscaling/v2
kind: HorizontalPodAutoscaler
metadata:
  name: api-hpa
spec:
  scaleTargetRef:
    apiVersion: apps/v1
    kind: Deployment
    name: api
  minReplicas: 5
  maxReplicas: 20
  metrics:
  - type: Resource
    resource:
      name: cpu
      target:
        type: Utilization
        averageUtilization: 70
```

---

### 11.4 CI/CD Pipeline

**GitHub Actions Workflow**:

```yaml
# .github/workflows/deploy.yml
name: Deploy to Production

on:
  push:
    branches: [main]

jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3

      - name: Run Backend Tests
        run: |
          cd backend
          composer install
          php bin/phpunit

      - name: Run Frontend Tests
        run: |
          cd frontend
          npm install
          npm run test:unit

  build:
    needs: test
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3

      - name: Build Docker Images
        run: |
          docker build -t ${{ secrets.ECR_REGISTRY }}/api:${{ github.sha }} ./backend
          docker build -t ${{ secrets.ECR_REGISTRY }}/ai-service:${{ github.sha }} ./ai-service

      - name: Push to ECR
        run: |
          aws ecr get-login-password | docker login --username AWS --password-stdin ${{ secrets.ECR_REGISTRY }}
          docker push ${{ secrets.ECR_REGISTRY }}/api:${{ github.sha }}
          docker push ${{ secrets.ECR_REGISTRY }}/ai-service:${{ github.sha }}

  deploy:
    needs: build
    runs-on: ubuntu-latest
    steps:
      - name: Deploy to Kubernetes
        run: |
          kubectl set image deployment/api api=${{ secrets.ECR_REGISTRY }}/api:${{ github.sha }}
          kubectl set image deployment/ai-service ai-service=${{ secrets.ECR_REGISTRY }}/ai-service:${{ github.sha }}
          kubectl rollout status deployment/api
          kubectl rollout status deployment/ai-service
```

---

## 12. Cost Estimation

### 12.1 AWS Monthly Cost (Production - 10k MAU)

| Service | Instance/Type | Quantity | Monthly Cost |
|---------|---------------|----------|--------------|
| **Compute** |
| EKS Cluster | - | 1 | $73 |
| EC2 (API) | t3.large | 5 | $375 |
| EC2 (AI - GPU) | g4dn.xlarge | 3 | $1,494 |
| EC2 (Scrapers) | t3.medium | 10 | $416 |
| **Database** |
| RDS PostgreSQL | db.r6g.xlarge | 1 primary + 1 replica | $658 |
| ElastiCache Redis | cache.r6g.large | 3 nodes | $459 |
| Milvus (EC2) | c6g.4xlarge | 1 | $497 |
| **Storage** |
| S3 (Resumes) | 100GB | - | $2.30 |
| S3 (Backups) | 500GB | - | $11.50 |
| EBS (Milvus) | gp3 1TB | - | $80 |
| **Networking** |
| Data Transfer | 1TB/month | - | $90 |
| CloudFront | 1TB/month | - | $85 |
| Load Balancer | ALB | 1 | $23 |
| **Monitoring** |
| CloudWatch | - | - | $50 |
| **Total** | | | **~$4,313/month** |

**Cost Optimization Tips**:
- Use Spot Instances for scraper workers (-70% cost)
- Reserved Instances for predictable workloads (-40% for 1-year commitment)
- S3 Intelligent-Tiering for old resumes
- CloudFront caching to reduce API calls

---

## 13. Conclusion

This architecture provides a scalable, AI-powered job aggregation platform capable of:

✅ **Aggregating jobs** from 50+ portals daily
✅ **Detecting duplicates** with 95%+ accuracy using embeddings
✅ **Matching resumes** to jobs with detailed scoring (0-100%)
✅ **Filtering** by country, date, keywords, salary, work mode
✅ **Supporting** both candidates and recruiters
✅ **Scaling** to millions of jobs and users
✅ **Processing** thousands of resumes per hour
✅ **Detecting** spam/fake jobs automatically

### Next Steps for Implementation:

**Phase 1: MVP (Months 1-2)**
1. Set up core infrastructure (Docker, databases)
2. Implement basic job scraping (5-10 portals)
3. Build job listing API and frontend
4. Implement basic search and filters
5. User authentication (candidate only)

**Phase 2: AI Integration (Months 3-4)**
1. Set up Vector DB (Milvus/FAISS)
2. Implement embedding generation
3. Build duplicate detection
4. Resume upload and parsing
5. Basic match scoring

**Phase 3: Advanced Features (Months 5-6)**
1. AI classification (categories, tech stack)
2. Semantic search
3. Spam detection
4. Recruiter portal
5. Advanced analytics
6. Scale to 50 portals

**Phase 4: Production (Month 7+)**
1. Performance optimization
2. Load testing
3. Security audit
4. Production deployment
5. Monitoring and alerts
6. User feedback and iteration

---

**Document Version**: 1.0
**Last Updated**: November 22, 2025
**Author**: JobsFusionHub Team
