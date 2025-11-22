import type { Job } from '@/types/job';

export const mockJobs: Job[] = [
  {
    id: '1',
    title: 'Senior Frontend Developer',
    company: 'TechCorp Inc',
    location: 'San Francisco, CA',
    salary: {
      min: 120000,
      max: 180000,
      currency: 'USD'
    },
    type: 'full-time',
    workMode: 'hybrid',
    sources: ['LinkedIn', 'Indeed'],
    isDuplicate: true,
    duplicateCount: 2,
    description: 'We are seeking an experienced Senior Frontend Developer to join our team. You will be responsible for building and maintaining our web applications using modern frameworks and best practices.',
    requirements: [
      '5+ years of experience with JavaScript/TypeScript',
      'Strong experience with Vue.js or React',
      'Experience with state management (Vuex/Pinia or Redux)',
      'Proficiency in HTML5, CSS3, and responsive design',
      'Experience with RESTful APIs and GraphQL',
      'Strong problem-solving and communication skills'
    ],
    postedDate: '2025-11-20',
    status: null
  },
  {
    id: '2',
    title: 'Full Stack Engineer',
    company: 'StartupXYZ',
    location: 'Remote',
    salary: {
      min: 90000,
      max: 140000,
      currency: 'USD'
    },
    type: 'full-time',
    workMode: 'remote',
    sources: ['LinkedIn'],
    isDuplicate: false,
    duplicateCount: 0,
    description: 'Join our fast-growing startup as a Full Stack Engineer. You will work on both frontend and backend technologies to build innovative solutions.',
    requirements: [
      '3+ years of full-stack development experience',
      'Proficiency in Node.js and Express',
      'Experience with Vue.js or React',
      'Knowledge of SQL and NoSQL databases',
      'Familiarity with cloud platforms (AWS, Azure, or GCP)',
      'Strong understanding of RESTful API design'
    ],
    postedDate: '2025-11-19',
    status: null
  },
  {
    id: '3',
    title: 'Junior Frontend Developer',
    company: 'Digital Solutions Ltd',
    location: 'New York, NY',
    salary: {
      min: 60000,
      max: 85000,
      currency: 'USD'
    },
    type: 'full-time',
    workMode: 'onsite',
    sources: ['Indeed', 'Glassdoor'],
    isDuplicate: true,
    duplicateCount: 2,
    description: 'Looking for a passionate Junior Frontend Developer to join our growing team. This is an excellent opportunity to learn and grow your skills.',
    requirements: [
      '1-2 years of experience with JavaScript',
      'Basic knowledge of Vue.js or React',
      'Understanding of HTML5 and CSS3',
      'Familiarity with version control (Git)',
      'Good communication skills',
      'Eagerness to learn and grow'
    ],
    postedDate: '2025-11-21',
    status: null
  },
  {
    id: '4',
    title: 'React Developer',
    company: 'Innovation Labs',
    location: 'Austin, TX',
    salary: {
      min: 95000,
      max: 130000,
      currency: 'USD'
    },
    type: 'full-time',
    workMode: 'hybrid',
    sources: ['LinkedIn', 'Monster'],
    isDuplicate: true,
    duplicateCount: 2,
    description: 'We are looking for an experienced React Developer to build cutting-edge web applications for our clients.',
    requirements: [
      '4+ years of React development experience',
      'Strong knowledge of JavaScript ES6+',
      'Experience with state management libraries (Redux, MobX)',
      'Proficiency in testing frameworks (Jest, React Testing Library)',
      'Understanding of modern build tools (Webpack, Vite)',
      'Experience with TypeScript is a plus'
    ],
    postedDate: '2025-11-18',
    status: null
  },
  {
    id: '5',
    title: 'Frontend Engineer Intern',
    company: 'MegaSoft Corporation',
    location: 'Seattle, WA',
    salary: {
      min: 25000,
      max: 35000,
      currency: 'USD'
    },
    type: 'internship',
    workMode: 'hybrid',
    sources: ['Glassdoor'],
    isDuplicate: false,
    duplicateCount: 0,
    description: 'Join our team as a Frontend Engineer Intern and gain hands-on experience building real-world applications.',
    requirements: [
      'Currently pursuing a degree in Computer Science or related field',
      'Basic understanding of HTML, CSS, and JavaScript',
      'Familiarity with at least one modern framework (React, Vue, Angular)',
      'Strong problem-solving skills',
      'Good communication and teamwork abilities',
      'Passion for learning new technologies'
    ],
    postedDate: '2025-11-22',
    status: null
  },
  {
    id: '6',
    title: 'Senior UI/UX Developer',
    company: 'DesignFirst Inc',
    location: 'Los Angeles, CA',
    salary: {
      min: 110000,
      max: 160000,
      currency: 'USD'
    },
    type: 'full-time',
    workMode: 'remote',
    sources: ['Indeed', 'LinkedIn', 'Glassdoor'],
    isDuplicate: true,
    duplicateCount: 3,
    description: 'We are seeking a Senior UI/UX Developer who can bridge the gap between design and development to create beautiful, user-friendly interfaces.',
    requirements: [
      '5+ years of UI/UX development experience',
      'Strong proficiency in CSS3, SASS/LESS',
      'Experience with design systems and component libraries',
      'Proficiency in Figma, Sketch, or Adobe XD',
      'Knowledge of accessibility standards (WCAG)',
      'Experience with animation libraries (GSAP, Framer Motion)'
    ],
    postedDate: '2025-11-17',
    status: null
  },
  {
    id: '7',
    title: 'Vue.js Developer',
    company: 'CodeCrafters',
    location: 'Boston, MA',
    salary: {
      min: 85000,
      max: 120000,
      currency: 'USD'
    },
    type: 'contract',
    workMode: 'remote',
    sources: ['LinkedIn'],
    isDuplicate: false,
    duplicateCount: 0,
    description: '6-month contract position for an experienced Vue.js developer to work on a large-scale enterprise application.',
    requirements: [
      '3+ years of Vue.js development experience',
      'Strong understanding of Vue 3 Composition API',
      'Experience with Pinia or Vuex',
      'Knowledge of Vue Router and Vite',
      'Experience with component testing',
      'Ability to work independently'
    ],
    postedDate: '2025-11-16',
    status: null
  },
  {
    id: '8',
    title: 'Frontend Developer (Part-Time)',
    company: 'FlexWork Solutions',
    location: 'Chicago, IL',
    salary: {
      min: 40000,
      max: 60000,
      currency: 'USD'
    },
    type: 'part-time',
    workMode: 'remote',
    sources: ['Indeed', 'ZipRecruiter'],
    isDuplicate: true,
    duplicateCount: 2,
    description: 'Part-time frontend developer needed to maintain and update existing web applications. Flexible hours available.',
    requirements: [
      '2+ years of frontend development experience',
      'Proficiency in JavaScript and modern frameworks',
      'Experience with responsive design',
      'Good time management skills',
      'Ability to work 20-30 hours per week',
      'Strong communication skills'
    ],
    postedDate: '2025-11-15',
    status: null
  },
  {
    id: '9',
    title: 'Web Developer',
    company: 'E-Commerce Giants',
    location: 'Miami, FL',
    salary: {
      min: 75000,
      max: 105000,
      currency: 'USD'
    },
    type: 'full-time',
    workMode: 'onsite',
    sources: ['Monster', 'Indeed'],
    isDuplicate: true,
    duplicateCount: 2,
    description: 'Join our e-commerce platform team as a Web Developer. You will work on improving user experience and implementing new features.',
    requirements: [
      '3+ years of web development experience',
      'Strong JavaScript, HTML, CSS skills',
      'Experience with e-commerce platforms',
      'Knowledge of payment gateway integration',
      'Familiarity with SEO best practices',
      'Experience with performance optimization'
    ],
    postedDate: '2025-11-14',
    status: null
  },
  {
    id: '10',
    title: 'JavaScript Engineer',
    company: 'Tech Innovators',
    location: 'Denver, CO',
    salary: {
      min: 100000,
      max: 145000,
      currency: 'USD'
    },
    type: 'full-time',
    workMode: 'hybrid',
    sources: ['LinkedIn', 'Glassdoor'],
    isDuplicate: true,
    duplicateCount: 2,
    description: 'We are looking for a talented JavaScript Engineer to build scalable web applications and contribute to our growing codebase.',
    requirements: [
      '4+ years of JavaScript development experience',
      'Deep understanding of JavaScript fundamentals',
      'Experience with modern frameworks (React, Vue, Angular)',
      'Knowledge of Node.js and backend development',
      'Familiarity with testing and CI/CD',
      'Strong architectural and design skills'
    ],
    postedDate: '2025-11-13',
    status: null
  }
];
