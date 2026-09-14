export interface FacultyMember {
  id: string
  name: string
  title: string
  department: string
  qualification: string
  email: string
  phone: string
  avatar: string
  bio: string
  classTeacherOf?: string
  specialization: string
}

export interface AcademicProgram {
  id: string
  grade: string
  name: string
  description: string
  leadTeacher: string
  sections: string[]
  subjectsCount: number
  subjects: Array<{ name: string; code: string; credits: number }>
  featured: boolean
}

export interface UniversityNotice {
  id: string
  title: string
  category: 'Academic' | 'Exams' | 'Events' | 'Admissions' | 'General'
  publishedDate: string
  summary: string
  content: string
  author: string
  priority: 'high' | 'normal' | 'low'
  attachment?: string
}

export interface UniversityStat {
  label: string
  value: string
  change: string
  subtext: string
}

export const universityStats: UniversityStat[] = [
  { label: 'Enrolled Scholars', value: '2,850+', change: '+14%', subtext: 'Across 4 grade tiers' },
  { label: 'Distinguished Faculty', value: '140+', change: '12:1', subtext: 'Student-to-faculty ratio' },
  { label: 'Academic Programs', value: '38', change: '100%', subtext: 'Accredited STEM & Arts courses' },
  { label: 'University Acceptance', value: '99.4%', change: 'Top Tier', subtext: 'Ivy League & Global admissions' },
]

export const academicPrograms: AcademicProgram[] = [
  {
    id: 'grade-9',
    grade: 'Grade 9',
    name: 'Freshman Academic Foundation',
    description: 'Core discovery curriculum establishing rigorous foundations in algebra, biological sciences, humanities, and classical world literature.',
    leadTeacher: 'Dr. Sarah Jenkins, M.Ed.',
    sections: ['Section 9-A (STEM Honors)', 'Section 9-B (Arts & Humanities)', 'Section 9-C (General Science)'],
    subjectsCount: 8,
    subjects: [
      { name: 'Mathematics I (Algebra & Geometry)', code: 'MTH101', credits: 4 },
      { name: 'General Biology & Lab Sciences', code: 'BIO101', credits: 4 },
      { name: 'English Literature & Composition', code: 'ENG101', credits: 3 },
      { name: 'World History & Civilizations', code: 'HIS101', credits: 3 },
      { name: 'Foundations of Computer Science', code: 'CSC101', credits: 3 },
      { name: 'Introductory Physics', code: 'PHY101', credits: 4 },
      { name: 'Foreign Language (French / Spanish)', code: 'LAN101', credits: 3 },
      { name: 'Physical Education & Health', code: 'PED101', credits: 2 },
    ],
    featured: true,
  },
  {
    id: 'grade-10',
    grade: 'Grade 10',
    name: 'Sophomore STEM & Humanities Track',
    description: 'Intermediate analytical disciplines featuring laboratory research, advanced geometry, world geography, and modern languages.',
    leadTeacher: 'Prof. Marcus Vance, Ph.D.',
    sections: ['Section 10-A (Advanced STEM)', 'Section 10-B (Social Sciences)', 'Section 10-C (Business Studies)'],
    subjectsCount: 9,
    subjects: [
      { name: 'Advanced Algebra & Trigonometry', code: 'MTH201', credits: 4 },
      { name: 'Chemistry & Molecular Sciences', code: 'CHM201', credits: 4 },
      { name: 'Physics (Mechanics & Waves)', code: 'PHY201', credits: 4 },
      { name: 'World Literature & Rhetoric', code: 'ENG201', credits: 3 },
      { name: 'Economics & Financial Principles', code: 'ECO201', credits: 3 },
      { name: 'Data Structures & Python Coding', code: 'CSC201', credits: 3 },
      { name: 'Civics & Political Systems', code: 'POL201', credits: 3 },
      { name: 'Modern Language II', code: 'LAN201', credits: 3 },
      { name: 'Studio Fine Arts & Design', code: 'ART201', credits: 2 },
    ],
    featured: true,
  },
  {
    id: 'grade-11',
    grade: 'Grade 11',
    name: 'Junior College Preparatory Stream',
    description: 'Pre-university specialization focusing on calculus, organic chemistry, microeconomics, and global debate forums.',
    leadTeacher: 'Dr. Elena Rostova, Ph.D.',
    sections: ['Section 11-A (Pre-Engineering)', 'Section 11-B (Pre-Medical)', 'Section 11-C (Business & Law)'],
    subjectsCount: 10,
    subjects: [
      { name: 'Pre-Calculus & Differential Math', code: 'MTH301', credits: 4 },
      { name: 'Organic & Analytical Chemistry', code: 'CHM301', credits: 4 },
      { name: 'Electromagnetism & Quantum Physics', code: 'PHY301', credits: 4 },
      { name: 'Micro & Macro Economics', code: 'ECO301', credits: 3 },
      { name: 'Advanced English Research Seminar', code: 'ENG301', credits: 3 },
      { name: 'Algorithms & Software Engineering', code: 'CSC301', credits: 4 },
      { name: 'Cellular & Molecular Biology', code: 'BIO301', credits: 4 },
      { name: 'Statistics & Probability Modeling', code: 'STA301', credits: 3 },
    ],
    featured: true,
  },
  {
    id: 'grade-12',
    grade: 'Grade 12',
    name: 'Senior Honors & University Capstone',
    description: 'Advanced placement capstone research, university thesis mentoring, AP Calculus BC, and professional internships.',
    leadTeacher: 'Prof. David Sterling, Ed.D.',
    sections: ['Section 12-A (STEM Honors Capstone)', 'Section 12-B (Liberal Arts Fellowship)'],
    subjectsCount: 8,
    subjects: [
      { name: 'AP Calculus BC & Linear Algebra', code: 'MTH401', credits: 5 },
      { name: 'Senior Capstone Research Thesis', code: 'RES401', credits: 4 },
      { name: 'Advanced Biochemistry Seminar', code: 'BIO401', credits: 4 },
      { name: 'Modern World Political Philosophy', code: 'POL401', credits: 3 },
      { name: 'Artificial Intelligence & Data Systems', code: 'CSC401', credits: 4 },
      { name: 'Corporate Finance & Global Markets', code: 'FIN401', credits: 3 },
    ],
    featured: true,
  },
]

export const facultyDirectory: FacultyMember[] = [
  {
    id: 'fac-1',
    name: 'Dr. Sarah Jenkins',
    title: 'Dean of Sciences & Senior Biology Chair',
    department: 'Biological & Life Sciences',
    qualification: 'Ph.D. in Molecular Biology (Stanford University), M.Ed. (Harvard)',
    email: 's.jenkins@timaade.edu',
    phone: '+1 (555) 234-5681',
    avatar: 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=400&auto=format&fit=crop&q=80',
    bio: 'Pioneered undergraduate cellular genomics curriculum. Recipient of the 2024 National Science Educator of the Year.',
    classTeacherOf: 'Grade 9-A',
    specialization: 'Genomics, Molecular Genetics & AP Biology',
  },
  {
    id: 'fac-2',
    name: 'Prof. Marcus Vance',
    title: 'Department Head, Mathematics & Applied Computing',
    department: 'Mathematics & Computing',
    qualification: 'Ph.D. in Applied Mathematics (MIT), B.S. in Computer Science',
    email: 'm.vance@timaade.edu',
    phone: '+1 (555) 234-5682',
    avatar: 'https://images.unsplash.com/photo-1560250097-0b93528c311a?w=400&auto=format&fit=crop&q=80',
    bio: 'Lead author of Advanced High School Calculus textbooks. Former research fellow at CERN and Google Brain mentor.',
    classTeacherOf: 'Grade 10-A',
    specialization: 'Differential Equations, AP Calculus & Algorithms',
  },
  {
    id: 'fac-3',
    name: 'Dr. Elena Rostova',
    title: 'Senior Fellow in Chemical Sciences',
    department: 'Chemistry & Physical Sciences',
    qualification: 'Ph.D. in Physical Chemistry (Oxford University), M.Sc. (ETH Zurich)',
    email: 'e.rostova@timaade.edu',
    phone: '+1 (555) 234-5683',
    avatar: 'https://images.unsplash.com/photo-1580489944761-15a19d654956?w=400&auto=format&fit=crop&q=80',
    bio: 'Specialist in green chemistry synthesis and modern spectroscopy. Guides international chemistry Olympiad medalists.',
    classTeacherOf: 'Grade 11-A',
    specialization: 'Organic Synthesis, Thermodynamics & Lab Safety',
  },
  {
    id: 'fac-4',
    name: 'Prof. David Sterling',
    title: 'Dean of Humanities & Academic Dean',
    department: 'Humanities & Social Sciences',
    qualification: 'Ed.D. in Curriculum Design (Columbia), M.A. in History (Yale)',
    email: 'd.sterling@timaade.edu',
    phone: '+1 (555) 234-5684',
    avatar: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=400&auto=format&fit=crop&q=80',
    bio: 'Over 20 years guiding high school scholars to premier university placements and competitive research fellowships.',
    classTeacherOf: 'Grade 12-A',
    specialization: 'World History, Global Diplomacy & Rhetoric',
  },
  {
    id: 'fac-5',
    name: 'Dr. Amina Yusuf',
    title: 'Lead Instructor in Modern Physics & Astrophysics',
    department: 'Physics & Space Sciences',
    qualification: 'Ph.D. in Theoretical Astrophysics (Cambridge), B.Sc. First Class Honors',
    email: 'a.yusuf@timaade.edu',
    phone: '+1 (555) 234-5685',
    avatar: 'https://images.unsplash.com/photo-1573497019940-1c28c88b4f3e?w=400&auto=format&fit=crop&q=80',
    bio: 'Director of the Tima-Ade University Observatory project. Passionate about empowering women in STEM and orbital mechanics.',
    classTeacherOf: 'Grade 11-B',
    specialization: 'Electromagnetism, Quantum Mechanics & Astronomy',
  },
  {
    id: 'fac-6',
    name: 'Prof. Robert Chen',
    title: 'Chair of Computer Science & Artificial Intelligence',
    department: 'Computer Science',
    qualification: 'M.S. in Computer Science (Carnegie Mellon University)',
    email: 'r.chen@timaade.edu',
    phone: '+1 (555) 234-5686',
    avatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&auto=format&fit=crop&q=80',
    bio: 'Former senior Silicon Valley engineer. Created the robotics lab and AI research elective at Tima-Ade University.',
    classTeacherOf: 'Grade 10-B',
    specialization: 'Machine Learning, Full-Stack Development & Cyber Security',
  },
]

export const universityNotices: UniversityNotice[] = [
  {
    id: 'notice-1',
    title: 'Admissions Open for Academic Year 2026-2027 Across All Grade Tiers',
    category: 'Admissions',
    publishedDate: 'August 15, 2026',
    summary: 'Online registration and merit scholarship applications are now formally open for prospective Grades 9, 10, 11, and 12 scholars.',
    content: 'Tima-Ade University is proud to invite applications from high-achieving, curious scholars worldwide for the 2026-2027 academic session. Our admissions process is holistic, evaluating academic potential, personal character, extracurricular leadership, and intellectual curiosity. Early decision deadline is November 15, 2026, with regular decision extending through January 30, 2027. Full and partial merit scholarships are available for eligible candidates in STEM, Arts, and Community Leadership tracks.',
    author: 'Admissions & Registrar Directorate',
    priority: 'high',
  },
  {
    id: 'notice-2',
    title: 'Second-Semester Mid-Term Examination Timetable & Diagnostic Guidelines',
    category: 'Exams',
    publishedDate: 'August 12, 2026',
    summary: 'Detailed examination schedules, hall allocations, and laboratory practical guidelines are now published on the student and parent portals.',
    content: 'The Academic Council has approved the official timetable for the upcoming Mid-Term Examination cycle commencing October 15, 2026. All students in Grades 9 through 12 must review their personal examination docket through the student dashboard. Laboratory sessions will precede written papers by one week. Parents are encouraged to consult attendance thresholds and academic preparation resources provided in the parent portal.',
    author: 'Office of Academic Affairs',
    priority: 'high',
  },
  {
    id: 'notice-3',
    title: 'Inauguration of the New Advanced Robotics & Quantum STEM Innovation Wing',
    category: 'Events',
    publishedDate: 'August 08, 2026',
    summary: 'A 15,000 sq. ft. multi-disciplinary research facility featuring AI robotics stations and laser optics laboratories is opening this fall.',
    content: 'We are thrilled to announce the ribbon-cutting ceremony for the Tima-Ade University Quantum Innovation Wing on September 18, 2026. Generously supported by university alumni and corporate research partners, this new facility provides every enrolled student with hands-on access to 3D rapid prototyping, automated robotics arrays, clean-room optics stations, and high-performance computing clusters.',
    author: 'Institutional Development & Campus Planning',
    priority: 'normal',
  },
  {
    id: 'notice-4',
    title: 'Annual Parent-Faculty Symposium & College Guidance Forum',
    category: 'Academic',
    publishedDate: 'August 04, 2026',
    summary: 'Join us for a campus-wide conference featuring 1-on-1 counselor check-ins, university admission forecasts, and curriculum briefings.',
    content: 'The Annual Parent-Faculty Symposium will take place on Saturday, August 30, 2026 in the Main Auditorium and Virtual Live Stream. Parents will have dedicated 15-minute consultations with core subject teachers and receive personalized progress reports covering GPA trends, classroom participation, attendance statistics, and university application roadmaps.',
    author: 'Dean of Student Affairs',
    priority: 'normal',
  },
  {
    id: 'notice-5',
    title: 'Global High School Mathematics Olympiad: Tima-Ade University Team Wins Gold',
    category: 'General',
    publishedDate: 'July 28, 2026',
    summary: 'Our 6-student delegation achieved 1st place in the International Inter-Collegiate Mathematics Championship in Geneva.',
    content: 'Congratulations to our Mathematics Olympiad delegation led by Prof. Marcus Vance! The 6-member student team demonstrated extraordinary mastery in combinatorics, number theory, and advanced analytical geometry, bringing home 4 Gold Medals and the overall championship trophy among 120 participating international institutions.',
    author: 'Department of Mathematics',
    priority: 'low',
  },
  {
    id: 'notice-6',
    title: 'Tuition Billing, Payment Plans, and Financial Aid Portal Upgrades',
    category: 'Admissions',
    publishedDate: 'July 20, 2026',
    summary: 'Families can now manage quarterly installment schedules, automated bank transfers, and scholarship credit balances online.',
    content: 'The Bursary and Finance Office has launched a secure, streamlined online fee management module accessible via the Parent and University Admin dashboards. Parents can view invoice breakdowns, download official tax receipts, and set up interest-free flexible payment schedules directly through the portal.',
    author: 'Bursar & Finance Office',
    priority: 'normal',
  },
]

export const universityTestimonials = [
  {
    quote: 'Tima-Ade University provided an environment where my passion for quantum physics was not just nurtured, but challenged at a university research level. My teachers became mentors who shaped my trajectory toward MIT.',
    author: 'Alexander Thorne',
    role: 'Alumnus, Class of 2024 · MIT Physics Scholar',
    avatar: 'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?w=200&auto=format&fit=crop&q=80',
  },
  {
    quote: 'As a parent, the transparency of the parent portal and the dedication of the faculty gave us complete peace of mind. We always knew our daughter’s academic standing, attendance, and well-being.',
    author: 'Mrs. Victoria Kingsley',
    role: 'Parent of Grade 11 Honors Scholar',
    avatar: 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=200&auto=format&fit=crop&q=80',
  },
  {
    quote: 'Teaching at Tima-Ade University is extraordinary. The student body is exceptionally motivated, curious, and collaborative. Our classrooms are hubs of intellectual discovery every single day.',
    author: 'Prof. Marcus Vance, Ph.D.',
    role: 'Department Head, Mathematics & Applied Computing',
    avatar: 'https://images.unsplash.com/photo-1560250097-0b93528c311a?w=200&auto=format&fit=crop&q=80',
  },
]

export const campusPillars = [
  {
    icon: 'Brain',
    title: 'Rigorous Academic Mastery',
    description: 'Accredited STEM, Arts, and Humanities curricula designed to foster deep analytical thinking, scholarly writing, and problem-solving.',
    color: 'from-crimson-600 to-rose-700',
    accent: 'text-rose-600 bg-rose-50 border-rose-200',
  },
  {
    icon: 'Atom',
    title: 'World-Class STEM Labs',
    description: 'Hands-on inquiry across modern molecular biology suites, laser optics chambers, high-speed robotics bays, and computing nodes.',
    color: 'from-blue-600 to-indigo-700',
    accent: 'text-blue-600 bg-blue-50 border-blue-200',
  },
  {
    icon: 'ShieldCheck',
    title: 'Character & Ethical Leadership',
    description: 'Holistic mentorship encouraging integrity, empathy, civic responsibility, global cultural fluency, and collaborative teamwork.',
    color: 'from-emerald-600 to-teal-700',
    accent: 'text-emerald-600 bg-emerald-50 border-emerald-200',
  },
  {
    icon: 'Compass',
    title: 'Dedicated University Counseling',
    description: '1-on-1 college admissions mentorship starting in Grade 9, guiding essay writing, portfolio curation, and Ivy League applications.',
    color: 'from-amber-600 to-orange-700',
    accent: 'text-amber-600 bg-amber-50 border-amber-200',
  },
]
