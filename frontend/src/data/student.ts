export const studentStats = [
  { title: 'Current GPA', value: '3.74', trend: '+0.11', tone: 'sky', icon: 'GraduationCap' },
  { title: 'Attendance rate', value: '94.8%', trend: '+1.4%', tone: 'emerald', icon: 'ClipboardCheck' },
  { title: 'Courses enrolled', value: '7', trend: '+1', tone: 'violet', icon: 'BookOpen' },
  { title: 'Fees due', value: '$1,240', trend: '-$260', tone: 'amber', icon: 'Wallet' },
]

export const courseRows = [
  { title: 'Data Structures', code: 'CS201', instructor: 'Dr. Evelyn Nkrumah', progress: 82, next: 'Mon 09:00' },
  { title: 'Database Systems', code: 'CS220', instructor: 'Dr. Amina Yusuf', progress: 74, next: 'Tue 11:00' },
  { title: 'Business Strategy', code: 'BUS210', instructor: 'Prof. Isaac Boateng', progress: 88, next: 'Wed 10:30' },
  { title: 'Research Methods', code: 'RES310', instructor: 'Dr. Kofi Addo', progress: 68, next: 'Thu 13:00' },
]

export const timetableRows = [
  { day: 'Monday', course: 'Data Structures', time: '09:00 - 11:00', venue: 'CS-204', teacher: 'Dr. Evelyn' },
  { day: 'Tuesday', course: 'Database Systems', time: '11:00 - 13:00', venue: 'Lab 2', teacher: 'Dr. Amina' },
  { day: 'Wednesday', course: 'Business Strategy', time: '10:30 - 12:00', venue: 'BUS-103', teacher: 'Prof. Isaac' },
  { day: 'Thursday', course: 'Research Methods', time: '13:00 - 15:00', venue: 'RSH-201', teacher: 'Dr. Kofi' },
]

export const attendanceTrend = [
  { week: 'Week 1', rate: 92 },
  { week: 'Week 2', rate: 90 },
  { week: 'Week 3', rate: 96 },
  { week: 'Week 4', rate: 94 },
  { week: 'Week 5', rate: 97 },
  { week: 'Week 6', rate: 95 },
]

export const assignmentRows = [
  { title: 'Algorithm Design Exercise', course: 'CS201', due: '2026-08-22', status: 'Open', score: '—' },
  { title: 'ER Diagram Report', course: 'CS220', due: '2026-08-25', status: 'Submitted', score: '91/100' },
  { title: 'Market Trends Case Study', course: 'BUS210', due: '2026-08-28', status: 'Needs review', score: 'Pending' },
]

export const submissionRows = [
  { assignment: 'ER Diagram Report', course: 'CS220', status: 'Submitted', score: '91/100', date: '2026-08-18' },
  { assignment: 'Lab Practical Record', course: 'CS220', status: 'Late', score: '82/100', date: '2026-08-15' },
  { assignment: 'Case Reflection', course: 'BUS210', status: 'Submitted', score: '94/100', date: '2026-08-17' },
]

export const examRows = [
  { title: 'Mid-Term Exam', course: 'CS201', date: '2026-08-30', status: 'Scheduled' },
  { title: 'Database Systems Quiz', course: 'CS220', date: '2026-09-02', status: 'Ready' },
  { title: 'Case Analysis', course: 'BUS210', date: '2026-09-06', status: 'Scheduled' },
]

export const resultsRows = [
  { course: 'Data Structures', score: '88%', grade: 'A-', term: 'Semester 2' },
  { course: 'Database Systems', score: '91%', grade: 'A', term: 'Semester 2' },
  { course: 'Business Strategy', score: '86%', grade: 'A-', term: 'Semester 2' },
  { course: 'Research Methods', score: '79%', grade: 'B+', term: 'Semester 2' },
]

export const gpaTrend = [
  { term: 'Year 1', gpa: 3.42 },
  { term: 'Year 2', gpa: 3.58 },
  { term: 'Year 3', gpa: 3.71 },
  { term: 'Current', gpa: 3.74 },
]

export const materialRows = [
  { title: 'Data Structures Lecture Notes', type: 'PDF', updated: '2026-08-19' },
  { title: 'ER Modeling Slides', type: 'Slides', updated: '2026-08-18' },
  { title: 'Business Strategy Reading Pack', type: 'Document', updated: '2026-08-17' },
]

export const feeRows = [
  { item: 'Tuition', due: '$1,800', paid: '$1,200', balance: '$600', status: 'Partial' },
  { item: 'Hostel', due: '$920', paid: '$920', balance: '$0', status: 'Paid' },
  { item: 'Library', due: '$180', paid: '$90', balance: '$90', status: 'Pending' },
]

export const notificationRows = [
  { title: 'Assignment deadline approaching', time: '10 minutes ago' },
  { title: 'Exam timetable published', time: '1 hour ago' },
  { title: 'Fee reminder issued', time: '2 days ago' },
]

export const messageRows = [
  { from: 'Academic office', subject: 'Scholarship application update', time: 'Today, 08:20' },
  { from: 'Dr. Evelyn', subject: 'Project review schedule', time: 'Yesterday, 17:10' },
  { from: 'Student union', subject: 'Leadership forum registration', time: 'Mon, 09:15' },
]

export const calendarRows = [
  { title: 'Database lab practical', date: '2026-08-21', type: 'Assessment' },
  { title: 'Career counselling clinic', date: '2026-08-23', type: 'Advising' },
  { title: 'Faculty seminar', date: '2026-08-27', type: 'Event' },
]

export const settingsRows = [
  { module: 'Profile visibility', status: 'Public' },
  { module: 'Academic notifications', status: 'Enabled' },
  { module: 'Payment reminder', status: 'On' },
  { module: 'Attendance alerts', status: 'Enabled' },
]
