export type JobType = 'full-time' | 'part-time' | 'contract' | 'internship';
export type WorkMode = 'remote' | 'hybrid' | 'onsite';
export type JobSource = 'LinkedIn' | 'Indeed' | 'Glassdoor' | 'Monster' | 'ZipRecruiter';
export type JobStatus = 'saved' | 'applied' | 'hidden' | null;

export interface Job {
  id: string;
  title: string;
  company: string;
  location: string;
  salary: {
    min: number;
    max: number;
    currency: string;
  };
  type: JobType;
  workMode: WorkMode;
  sources: JobSource[];
  isDuplicate: boolean;
  duplicateCount: number;
  description: string;
  requirements: string[];
  postedDate: string;
  status: JobStatus;
}

export interface SearchFilters {
  query: string;
  location: string;
  types: JobType[];
  modes: WorkMode[];
  salaryRange: {
    min: number;
    max: number;
  };
  sources: JobSource[];
}

export interface AIFilter {
  query: string;
  isActive: boolean;
}
