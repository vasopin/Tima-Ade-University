export const overviewStats = [
  { title: 'Total students', value: '18,420', trend: '+8.2%', tone: 'sky', icon: 'Users' },
  { title: 'Active teachers', value: '842', trend: '+3.6%', tone: 'violet', icon: 'GraduationCap' },
  { title: 'Admissions', value: '1,408', trend: '+14.1%', tone: 'emerald', icon: 'ClipboardCheck' },
  { title: 'Revenue', value: '$924K', trend: '+9.8%', tone: 'amber', icon: 'Wallet' },
]

export const attendanceTrend = [
  { day: 'Mon', attendance: 86, target: 82 },
  { day: 'Tue', attendance: 89, target: 84 },
  { day: 'Wed', attendance: 92, target: 86 },
  { day: 'Thu', attendance: 90, target: 85 },
  { day: 'Fri', attendance: 94, target: 88 },
  { day: 'Sat', attendance: 81, target: 80 },
  { day: 'Sun', attendance: 87, target: 82 },
]

export const enrollmentTrend = [
  { month: 'Jan', students: 420 },
  { month: 'Feb', students: 460 },
  { month: 'Mar', students: 520 },
  { month: 'Apr', students: 490 },
  { month: 'May', students: 560 },
  { month: 'Jun', students: 610 },
  { month: 'Jul', students: 660 },
  { month: 'Aug', students: 640 },
  { month: 'Sep', students: 690 },
]

export const studentRows = [
  { name: 'Mabel Kusi', id: 'ST-2041', faculty: 'Computing', year: 'Year 3', status: 'Active' },
  { name: 'Kwame Armah', id: 'ST-2048', faculty: 'Engineering', year: 'Year 2', status: 'On Leave' },
  { name: 'Ada Mensah', id: 'ST-2055', faculty: 'Business', year: 'Year 1', status: 'Active' },
  { name: 'Nelson Darko', id: 'ST-2072', faculty: 'Health Sciences', year: 'Year 4', status: 'Probation' },
  { name: 'Rosemary Tetteh', id: 'ST-2095', faculty: 'Arts', year: 'Year 2', status: 'Active' },
]

export const teacherRows = [
  { name: 'Dr. Evelyn Nkrumah', course: 'Data Structures', department: 'Computer Science', status: 'Assigned' },
  { name: 'Prof. Isaac Boateng', course: 'Financial Management', department: 'Business', status: 'On Duty' },
  { name: 'Dr. Fatiha Khan', course: 'Human Anatomy', department: 'Health Sciences', status: 'Assigned' },
  { name: 'Mr. Samuel Addo', course: 'Circuit Design', department: 'Engineering', status: 'On Leave' },
]

export const departmentRows = [
  { name: 'Computer Science', head: 'Dr. Evelyn Nkrumah', strength: 160, programs: 7, status: 'Stable' },
  { name: 'Business Administration', head: 'Prof. Isaac Boateng', strength: 122, programs: 5, status: 'Stable' },
  { name: 'Engineering', head: 'Mr. Samuel Addo', strength: 186, programs: 9, status: 'Review' },
  { name: 'Health Sciences', head: 'Dr. Fatiha Khan', strength: 104, programs: 6, status: 'Stable' },
]

export const programRows = [
  { title: 'BSc Computer Science', level: 'Undergraduate', duration: '4 years', students: 1185 },
  { title: 'BBA Finance', level: 'Undergraduate', duration: '4 years', students: 932 },
  { title: 'MSc Data Analytics', level: 'Postgraduate', duration: '2 years', students: 387 },
  { title: 'Diploma in Graphic Design', level: 'Diploma', duration: '2 years', students: 411 },
]

export const admissionsRows = [
  { applicant: 'Esi Quaye', program: 'BSc Computer Science', stage: 'Interview', date: '2026-08-18', status: 'In Progress' },
  { applicant: 'Kofi Djebar', program: 'BBA Finance', stage: 'Document review', date: '2026-08-20', status: 'Pending' },
  { applicant: 'Ama Lawson', program: 'MSc Data Analytics', stage: 'Offer letter', date: '2026-08-17', status: 'Approved' },
  { applicant: 'Tariq Mensah', program: 'Diploma in Graphic Design', stage: 'Awaiting response', date: '2026-08-19', status: 'Pending' },
]

export const financeRows = [
  { type: 'Tuition', collected: '$412K', target: '$450K', progress: 91 },
  { type: 'Hostel fees', collected: '$184K', target: '$210K', progress: 88 },
  { type: 'Transport', collected: '$98K', target: '$120K', progress: 82 },
  { type: 'Library fines', collected: '$17K', target: '$28K', progress: 61 },
]

export const timetableRows = [
  { day: 'Monday', course: 'Data Structures', venue: 'CS-204', start: '09:00', end: '11:00', teacher: 'Dr. Evelyn' },
  { day: 'Tuesday', course: 'Business Strategy', venue: 'BUS-103', start: '11:00', end: '13:00', teacher: 'Prof. Isaac' },
  { day: 'Wednesday', course: 'Human Anatomy', venue: 'HS-201', start: '08:00', end: '10:00', teacher: 'Dr. Fatiha' },
  { day: 'Thursday', course: 'Circuit Analysis', venue: 'ENG-305', start: '13:00', end: '15:00', teacher: 'Mr. Samuel' },
]

export const examRows = [
  { exam: 'Mid-semester exam', course: 'Data Structures', date: '2026-08-30', status: 'Scheduled' },
  { exam: 'Semester practicals', course: 'Business Strategy', date: '2026-09-02', status: 'Ready' },
  { exam: 'Lab viva', course: 'Circuit Analysis', date: '2026-09-06', status: 'Scheduled' },
]

export const resultsSummary = [
  { label: 'Pass rate', value: '91.4%', delta: '+2.1%' },
  { label: 'Distinctions', value: '1,786', delta: '+8.4%' },
  { label: 'At-risk students', value: '214', delta: '-6.2%' },
  { label: 'GPA average', value: '3.68', delta: '+0.09' },
]

export const libraryRows = [
  { title: 'Operating systems', issue: '3 pending', status: 'Healthy' },
  { title: 'Business analytics', issue: '8 overdue', status: 'Review' },
  { title: 'Medical ethics', issue: '1 pending', status: 'Healthy' },
]

export const hostelRows = [
  { block: 'North Hall', occupancy: '86%', beds: 160, status: 'Full' },
  { block: 'East Hall', occupancy: '62%', beds: 180, status: 'Available' },
  { block: 'West Hall', occupancy: '71%', beds: 140, status: 'Available' },
]

export const transportRows = [
  { route: 'City Loop', buses: 8, passengers: 312, status: 'On schedule' },
  { route: 'North Campus', buses: 5, passengers: 188, status: 'Delayed' },
  { route: 'West District', buses: 6, passengers: 242, status: 'On schedule' },
]

export const eventRows = [
  { title: 'Open day week', date: '2026-08-23', audience: 'Prospective students' },
  { title: 'Faculty symposium', date: '2026-08-27', audience: 'Academic staff' },
  { title: 'Career fair', date: '2026-09-05', audience: 'All students' },
]

export const reportRows = [
  { name: 'Term attendance report', type: 'Academic', updated: '2 days ago' },
  { name: 'Fee collection snapshot', type: 'Finance', updated: '1 day ago' },
  { name: 'Campus compliance review', type: 'Operations', updated: 'Today' },
]

export const settingsRows = [
  { module: 'Academic calendar', status: 'Synced' },
  { module: 'Admission workflow', status: 'Live' },
  { module: 'Finance rules', status: 'Locked' },
  { module: 'Emergency alerts', status: 'Enabled' },
]
