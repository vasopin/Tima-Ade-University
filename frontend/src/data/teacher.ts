export const teacherStats = [
  { title: 'Classes taught', value: '6', trend: '+2', tone: 'sky', icon: 'Users' },
  { title: 'Students under supervision', value: '248', trend: '+18', tone: 'violet', icon: 'GraduationCap' },
  { title: 'Attendance rate', value: '94.3%', trend: '+1.9%', tone: 'emerald', icon: 'ClipboardCheck' },
  { title: 'Assignments pending', value: '38', trend: '-11', tone: 'amber', icon: 'FileText' },
]

export const classRows = [
  { name: 'CS 201 - Data Structures', students: 42, attendance: '92%', next: 'Mon 09:00' },
  { name: 'CS 301 - Operating Systems', students: 35, attendance: '89%', next: 'Tue 11:00' },
  { name: 'BBA 210 - Business Analytics', students: 48, attendance: '96%', next: 'Wed 10:30' },
  { name: 'ENG 112 - Circuit Design', students: 31, attendance: '90%', next: 'Thu 13:00' },
]

export const courseRows = [
  { title: 'Data Structures', code: 'CS201', term: 'Semester 2', students: 42, completion: 78 },
  { title: 'Operating Systems', code: 'CS301', term: 'Semester 2', students: 35, completion: 66 },
  { title: 'Business Analytics', code: 'BBA210', term: 'Semester 1', students: 48, completion: 84 },
  { title: 'Circuit Design', code: 'ENG112', term: 'Semester 1', students: 31, completion: 72 },
]

export const studentRows = [
  { name: 'Mabel Kusi', id: 'ST-2041', performance: 'A', attendance: '96%', actions: 'View profile' },
  { name: 'Kwame Armah', id: 'ST-2048', performance: 'B+', attendance: '88%', actions: 'Needs support' },
  { name: 'Ada Mensah', id: 'ST-2055', performance: 'A-', attendance: '93%', actions: 'View profile' },
  { name: 'Nelson Darko', id: 'ST-2072', performance: 'C+', attendance: '81%', actions: 'At risk' },
  { name: 'Rosemary Tetteh', id: 'ST-2095', performance: 'A', attendance: '97%', actions: 'View profile' },
]

export const assignmentRows = [
  { title: 'Algorithm Design Exercise', course: 'CS201', due: '2026-08-22', submissions: 32, status: 'Open' },
  { title: 'Linux Process Assignment', course: 'CS301', due: '2026-08-25', submissions: 24, status: 'In review' },
  { title: 'Market Trends Case Study', course: 'BBA210', due: '2026-08-28', submissions: 41, status: 'Open' },
]

export const submissionRows = [
  { student: 'Mabel Kusi', assignment: 'Algorithm Design Exercise', status: 'Submitted', score: '92/100' },
  { student: 'Kwame Armah', assignment: 'Linux Process Assignment', status: 'Late', score: '78/100' },
  { student: 'Ada Mensah', assignment: 'Market Trends Case Study', status: 'Submitted', score: '95/100' },
  { student: 'Nelson Darko', assignment: 'Algorithm Design Exercise', status: 'Pending', score: '—' },
]

export const examRows = [
  { title: 'Mid-Term Exam', course: 'CS201', date: '2026-08-30', status: 'Scheduled' },
  { title: 'Lab Practical', course: 'CS301', date: '2026-09-02', status: 'Ready' },
  { title: 'Case Analysis', course: 'BBA210', date: '2026-09-05', status: 'Scheduled' },
]

export const gradeRows = [
  { student: 'Mabel Kusi', course: 'CS201', score: 92, grade: 'A', status: 'Submitted' },
  { student: 'Kwame Armah', course: 'CS301', score: 78, grade: 'B+', status: 'Draft' },
  { student: 'Ada Mensah', course: 'BBA210', score: 95, grade: 'A', status: 'Submitted' },
  { student: 'Nelson Darko', course: 'CS201', score: 67, grade: 'C+', status: 'Pending' },
]

export const timetableRows = [
  { day: 'Monday', course: 'CS201', time: '09:00 - 11:00', venue: 'CS-204' },
  { day: 'Tuesday', course: 'CS301', time: '11:00 - 13:00', venue: 'Lab 2' },
  { day: 'Wednesday', course: 'BBA210', time: '10:30 - 12:00', venue: 'BUS-103' },
  { day: 'Thursday', course: 'ENG112', time: '13:00 - 15:00', venue: 'ENG-305' },
]

export const resourceRows = [
  { title: 'Sorting Algorithms Handout', type: 'PDF', date: '2026-08-20' },
  { title: 'Linux Shell Lab Guide', type: 'Document', date: '2026-08-21' },
  { title: 'Business Strategy Slides', type: 'Slides', date: '2026-08-23' },
]

export const announcementRows = [
  { title: 'Research methodology workshop', audience: 'All CS students', date: '2026-08-24' },
  { title: 'Career webinar registration', audience: 'BBA students', date: '2026-08-29' },
  { title: 'Online lab briefing', audience: 'ENG112 cohort', date: '2026-09-01' },
]

export const messageRows = [
  { from: 'Academic office', subject: 'Exam timetable update', time: 'Today, 08:20' },
  { from: 'Mabel Kusi', subject: 'Question about coursework', time: 'Yesterday, 17:12' },
  { from: 'Department admin', subject: 'Guest lecture invite', time: 'Mon, 09:15' },
]

export const notificationRows = [
  { title: 'Three assignments need grading', time: '10 minutes ago' },
  { title: 'Attendance summary published', time: '1 hour ago' },
  { title: 'Lab schedule updated', time: '2 days ago' },
]

export const attendanceTrend = [
  { day: 'Mon', present: 96, expected: 90 },
  { day: 'Tue', present: 94, expected: 90 },
  { day: 'Wed', present: 92, expected: 90 },
  { day: 'Thu', present: 95, expected: 90 },
  { day: 'Fri', present: 97, expected: 90 },
  { day: 'Sat', present: 88, expected: 85 },
]

export const performanceTrend = [
  { month: 'Jan', score: 78 },
  { month: 'Feb', score: 81 },
  { month: 'Mar', score: 83 },
  { month: 'Apr', score: 86 },
  { month: 'May', score: 88 },
  { month: 'Jun', score: 90 },
  { month: 'Jul', score: 92 },
]
