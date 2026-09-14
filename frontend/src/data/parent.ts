export type ParentChild = {
  id: string
  name: string
  studentId: string
  grade: string
  yearLevel: string
  advisor: string
  status: 'On track' | 'Needs support' | 'Excellent'
  attendance: string
  gpa: string
  currentTerm: string
  profile: {
    classTeacher: string
    department: string
    school: string
    nextMeeting: string
  }
  overviewStats: Array<{ title: string; value: string; trend: string; tone: 'sky' | 'violet' | 'emerald' | 'amber'; icon: 'GraduationCap' | 'ClipboardCheck' | 'BookOpen' | 'Wallet' }>
  performanceTrend: Array<{ month: string; score: number }>
  attendanceTrend: Array<{ week: string; rate: number }>
  courses: Array<{ title: string; code: string; teacher: string; progress: number; next: string }>
  timetable: Array<{ day: string; subject: string; time: string; venue: string; teacher: string }>
  assignments: Array<{ title: string; course: string; due: string; status: 'Pending' | 'Submitted' | 'Late'; score: string }>
  exams: Array<{ title: string; course: string; date: string; status: 'Scheduled' | 'Ready' | 'Completed' }>
  results: Array<{ course: string; score: string; grade: string; term: string }>
  materials: Array<{ title: string; type: string; date: string }>
  announcements: Array<{ title: string; audience: string; date: string }>
  notifications: Array<{ title: string; time: string }>
  messages: Array<{ from: string; subject: string; time: string }>
  fees: Array<{ item: string; due: string; paid: string; balance: string; status: 'Paid' | 'Pending' | 'Partial' }>
  calendar: Array<{ title: string; date: string; type: string }>
  events: Array<{ title: string; date: string; audience: string }>
  settings: Array<{ module: string; status: string }>
}

export const parentChildren: ParentChild[] = [
  {
    id: 'mia-mensah',
    name: 'Mia Mensah',
    studentId: 'ST-2091',
    grade: 'Grade 10A',
    yearLevel: 'Year 10',
    advisor: 'Mrs. Adjoa Yeboah',
    status: 'On track',
    attendance: '96%',
    gpa: '3.81',
    currentTerm: 'Term 2',
    profile: {
      classTeacher: 'Mrs. Adjoa Yeboah',
      department: 'Science',
      school: 'Tima-Ade University Senior School',
      nextMeeting: '2026-08-24',
    },
    overviewStats: [
      { title: 'Current GPA', value: '3.81', trend: '+0.17', tone: 'sky', icon: 'GraduationCap' },
      { title: 'Attendance', value: '96%', trend: '+2.4%', tone: 'emerald', icon: 'ClipboardCheck' },
      { title: 'Courses', value: '7', trend: '+1', tone: 'violet', icon: 'BookOpen' },
      { title: 'Fee balance', value: '$840', trend: '-$180', tone: 'amber', icon: 'Wallet' },
    ],
    performanceTrend: [
      { month: 'Jan', score: 3.3 },
      { month: 'Feb', score: 3.4 },
      { month: 'Mar', score: 3.5 },
      { month: 'Apr', score: 3.7 },
      { month: 'May', score: 3.8 },
      { month: 'Jun', score: 3.81 },
    ],
    attendanceTrend: [
      { week: 'W1', rate: 91 },
      { week: 'W2', rate: 93 },
      { week: 'W3', rate: 95 },
      { week: 'W4', rate: 94 },
      { week: 'W5', rate: 97 },
      { week: 'W6', rate: 96 },
    ],
    courses: [
      { title: 'Mathematics', code: 'MTH210', teacher: 'Mr. Adu Bonsu', progress: 86, next: 'Mon 09:00' },
      { title: 'Physics', code: 'PHY205', teacher: 'Mrs. Sarah Owusu', progress: 82, next: 'Tue 10:30' },
      { title: 'Biology', code: 'BIO214', teacher: 'Dr. Linda Eshun', progress: 89, next: 'Wed 11:00' },
      { title: 'English Literature', code: 'ENG208', teacher: 'Mr. Daniel Kwarteng', progress: 78, next: 'Thu 12:30' },
    ],
    timetable: [
      { day: 'Monday', subject: 'Mathematics', time: '09:00 - 10:30', venue: 'Sci-204', teacher: 'Mr. Adu Bonsu' },
      { day: 'Tuesday', subject: 'Physics', time: '10:30 - 12:00', venue: 'Lab 1', teacher: 'Mrs. Sarah Owusu' },
      { day: 'Wednesday', subject: 'Biology', time: '11:00 - 12:15', venue: 'Bio-101', teacher: 'Dr. Linda Eshun' },
      { day: 'Thursday', subject: 'English Literature', time: '12:30 - 14:00', venue: 'Arts-102', teacher: 'Mr. Daniel Kwarteng' },
    ],
    assignments: [
      { title: 'Quadratic Functions', course: 'Mathematics', due: '2026-08-22', status: 'Pending', score: '—' },
      { title: 'Lab Report', course: 'Physics', due: '2026-08-25', status: 'Submitted', score: '94/100' },
      { title: 'Research Notes', course: 'Biology', due: '2026-08-27', status: 'Late', score: '80/100' },
    ],
    exams: [
      { title: 'Unit Test 4', course: 'Mathematics', date: '2026-08-28', status: 'Scheduled' },
      { title: 'Practical Exam', course: 'Physics', date: '2026-09-01', status: 'Ready' },
      { title: 'Mid-Term Quiz', course: 'Biology', date: '2026-09-03', status: 'Scheduled' },
    ],
    results: [
      { course: 'Mathematics', score: '92%', grade: 'A', term: 'Term 1' },
      { course: 'Physics', score: '88%', grade: 'A-', term: 'Term 1' },
      { course: 'Biology', score: '95%', grade: 'A', term: 'Term 1' },
      { course: 'English Literature', score: '84%', grade: 'B+', term: 'Term 1' },
    ],
    materials: [
      { title: 'Algebra revision pack', type: 'PDF', date: '2026-08-20' },
      { title: 'Motion lab slides', type: 'Slides', date: '2026-08-18' },
      { title: 'Cell structure notes', type: 'Document', date: '2026-08-17' },
    ],
    announcements: [
      { title: 'Science expo registration', audience: 'Year 10 science students', date: '2026-08-23' },
      { title: 'Career week talk', audience: 'All students', date: '2026-08-27' },
      { title: 'Parent-teacher meeting', audience: 'Parents of Year 10', date: '2026-08-30' },
    ],
    notifications: [
      { title: 'Math assignment due tomorrow', time: '1 hour ago' },
      { title: 'Science practical schedule updated', time: 'Today' },
      { title: 'Fee reminder issued', time: '2 days ago' },
    ],
    messages: [
      { from: 'Mr. Adu Bonsu', subject: 'Math homework review', time: 'Today, 08:30' },
      { from: 'Mrs. Adjoa Yeboah', subject: 'Term 2 academic check-in', time: 'Yesterday, 17:10' },
      { from: 'School office', subject: 'Science expo volunteer sign-up', time: 'Mon, 09:20' },
    ],
    fees: [
      { item: 'Tuition', due: '$2,300', paid: '$1,460', balance: '$840', status: 'Partial' },
      { item: 'Transport', due: '$420', paid: '$420', balance: '$0', status: 'Paid' },
      { item: 'Library', due: '$120', paid: '$60', balance: '$60', status: 'Pending' },
    ],
    calendar: [
      { title: 'Parent-teacher meeting', date: '2026-08-24', type: 'Meeting' },
      { title: 'Science exhibition', date: '2026-08-30', type: 'Event' },
      { title: 'Mid-term assessment', date: '2026-09-01', type: 'Assessment' },
    ],
    events: [
      { title: 'Science Expo', date: '2026-08-30', audience: 'All families' },
      { title: 'Open Day', date: '2026-09-04', audience: 'Prospective parents' },
      { title: 'Debate finals', date: '2026-09-07', audience: 'School community' },
    ],
    settings: [
      { module: 'Academic notifications', status: 'Enabled' },
      { module: 'Fee reminders', status: 'On' },
      { module: 'Teacher messaging', status: 'Enabled' },
      { module: 'Event updates', status: 'Public' },
    ],
  },
  {
    id: 'nana-kusi',
    name: 'Nana Kusi',
    studentId: 'ST-2153',
    grade: 'Grade 8C',
    yearLevel: 'Year 8',
    advisor: 'Mr. Bernard Tetteh',
    status: 'Needs support',
    attendance: '89%',
    gpa: '3.42',
    currentTerm: 'Term 2',
    profile: {
      classTeacher: 'Mr. Bernard Tetteh',
      department: 'Humanities',
      school: 'Tima-Ade University Junior School',
      nextMeeting: '2026-08-26',
    },
    overviewStats: [
      { title: 'Current GPA', value: '3.42', trend: '+0.08', tone: 'sky', icon: 'GraduationCap' },
      { title: 'Attendance', value: '89%', trend: '+0.9%', tone: 'emerald', icon: 'ClipboardCheck' },
      { title: 'Courses', value: '6', trend: '0', tone: 'violet', icon: 'BookOpen' },
      { title: 'Fee balance', value: '$500', trend: '-$120', tone: 'amber', icon: 'Wallet' },
    ],
    performanceTrend: [
      { month: 'Jan', score: 3.1 },
      { month: 'Feb', score: 3.2 },
      { month: 'Mar', score: 3.2 },
      { month: 'Apr', score: 3.3 },
      { month: 'May', score: 3.4 },
      { month: 'Jun', score: 3.42 },
    ],
    attendanceTrend: [
      { week: 'W1', rate: 86 },
      { week: 'W2', rate: 88 },
      { week: 'W3', rate: 87 },
      { week: 'W4', rate: 90 },
      { week: 'W5', rate: 91 },
      { week: 'W6', rate: 89 },
    ],
    courses: [
      { title: 'Mathematics', code: 'MTH110', teacher: 'Mr. Bernard Tetteh', progress: 72, next: 'Mon 08:30' },
      { title: 'English', code: 'ENG105', teacher: 'Mrs. Mercy Frimpong', progress: 76, next: 'Tue 09:00' },
      { title: 'Social Studies', code: 'SOC122', teacher: 'Ms. Selina Addo', progress: 68, next: 'Wed 08:00' },
      { title: 'Art', code: 'ART101', teacher: 'Mr. Kofi Dake', progress: 83, next: 'Thu 13:00' },
    ],
    timetable: [
      { day: 'Monday', subject: 'Mathematics', time: '08:30 - 09:45', venue: 'J-205', teacher: 'Mr. Bernard Tetteh' },
      { day: 'Tuesday', subject: 'English', time: '09:00 - 10:15', venue: 'J-118', teacher: 'Mrs. Mercy Frimpong' },
      { day: 'Wednesday', subject: 'Social Studies', time: '08:00 - 09:00', venue: 'J-214', teacher: 'Ms. Selina Addo' },
      { day: 'Thursday', subject: 'Art', time: '13:00 - 14:00', venue: 'Arts Studio', teacher: 'Mr. Kofi Dake' },
    ],
    assignments: [
      { title: 'Reading Reflection', course: 'English', due: '2026-08-21', status: 'Submitted', score: '88/100' },
      { title: 'Fractions Quiz', course: 'Mathematics', due: '2026-08-24', status: 'Pending', score: '—' },
      { title: 'Current Affairs Write-up', course: 'Social Studies', due: '2026-08-26', status: 'Late', score: '74/100' },
    ],
    exams: [
      { title: 'Progress Test', course: 'English', date: '2026-08-29', status: 'Scheduled' },
      { title: 'Math Drill', course: 'Mathematics', date: '2026-09-02', status: 'Ready' },
      { title: 'Class Presentation', course: 'Social Studies', date: '2026-09-04', status: 'Scheduled' },
    ],
    results: [
      { course: 'Mathematics', score: '78%', grade: 'B+', term: 'Term 1' },
      { course: 'English', score: '82%', grade: 'B', term: 'Term 1' },
      { course: 'Social Studies', score: '76%', grade: 'B', term: 'Term 1' },
      { course: 'Art', score: '90%', grade: 'A-', term: 'Term 1' },
    ],
    materials: [
      { title: 'Fraction practice sheet', type: 'PDF', date: '2026-08-19' },
      { title: 'Reading comprehension guide', type: 'Document', date: '2026-08-20' },
      { title: 'Social studies summary notes', type: 'Slides', date: '2026-08-21' },
    ],
    announcements: [
      { title: 'Homework support evening', audience: 'Year 8 parents', date: '2026-08-22' },
      { title: 'School library hours update', audience: 'All students', date: '2026-08-25' },
      { title: 'Sports day registration', audience: 'Year 8 students', date: '2026-08-27' },
    ],
    notifications: [
      { title: 'Fractions quiz reminder', time: '30 minutes ago' },
      { title: 'Reading support session booked', time: '2 hours ago' },
      { title: 'Library fine cleared', time: '1 day ago' },
    ],
    messages: [
      { from: 'Mr. Bernard Tetteh', subject: 'Reading support plan', time: 'Today, 08:00' },
      { from: 'School counselling', subject: 'Study habits session', time: 'Yesterday, 16:40' },
      { from: 'Office admin', subject: 'Sports day forms', time: 'Mon, 10:05' },
    ],
    fees: [
      { item: 'Tuition', due: '$1,800', paid: '$1,300', balance: '$500', status: 'Partial' },
      { item: 'Uniform', due: '$180', paid: '$180', balance: '$0', status: 'Paid' },
      { item: 'Books', due: '$120', paid: '$60', balance: '$60', status: 'Pending' },
    ],
    calendar: [
      { title: 'Reading support session', date: '2026-08-22', type: 'Support' },
      { title: 'Progress test week', date: '2026-08-29', type: 'Assessment' },
      { title: 'Sports day', date: '2026-09-05', type: 'Event' },
    ],
    events: [
      { title: 'Parent Forum', date: '2026-08-26', audience: 'Parents of Year 8' },
      { title: 'Sports Day', date: '2026-09-05', audience: 'All families' },
      { title: 'Book Fair', date: '2026-09-09', audience: 'School community' },
    ],
    settings: [
      { module: 'Academic notifications', status: 'Enabled' },
      { module: 'Fee reminders', status: 'On' },
      { module: 'Teacher communication', status: 'Enabled' },
      { module: 'Event emails', status: 'Enabled' },
    ],
  },
  {
    id: 'ekow-opoku',
    name: 'Ekow Opoku',
    studentId: 'ST-2187',
    grade: 'Grade 12B',
    yearLevel: 'Year 12',
    advisor: 'Dr. Naa Korkor',
    status: 'Excellent',
    attendance: '98%',
    gpa: '4.00',
    currentTerm: 'Term 2',
    profile: {
      classTeacher: 'Dr. Naa Korkor',
      department: 'Arts & Sciences',
      school: 'Tima-Ade University Sixth Form',
      nextMeeting: '2026-08-28',
    },
    overviewStats: [
      { title: 'Current GPA', value: '4.00', trend: '+0.12', tone: 'sky', icon: 'GraduationCap' },
      { title: 'Attendance', value: '98%', trend: '+1.8%', tone: 'emerald', icon: 'ClipboardCheck' },
      { title: 'Courses', value: '5', trend: '+0', tone: 'violet', icon: 'BookOpen' },
      { title: 'Fee balance', value: '$220', trend: '-$90', tone: 'amber', icon: 'Wallet' },
    ],
    performanceTrend: [
      { month: 'Jan', score: 3.8 },
      { month: 'Feb', score: 3.9 },
      { month: 'Mar', score: 3.95 },
      { month: 'Apr', score: 3.98 },
      { month: 'May', score: 4.0 },
      { month: 'Jun', score: 4.0 },
    ],
    attendanceTrend: [
      { week: 'W1', rate: 96 },
      { week: 'W2', rate: 97 },
      { week: 'W3', rate: 98 },
      { week: 'W4', rate: 99 },
      { week: 'W5', rate: 98 },
      { week: 'W6', rate: 98 },
    ],
    courses: [
      { title: 'Advanced Mathematics', code: 'MTH312', teacher: 'Dr. Naa Korkor', progress: 94, next: 'Mon 11:00' },
      { title: 'Economics', code: 'ECO301', teacher: 'Prof. Daniel Sackey', progress: 91, next: 'Tue 09:00' },
      { title: 'Psychology', code: 'PSY302', teacher: 'Dr. Ama Kofi', progress: 88, next: 'Wed 12:00' },
      { title: 'Literature', code: 'LIT300', teacher: 'Mrs. Fiona Mensah', progress: 90, next: 'Thu 14:00' },
    ],
    timetable: [
      { day: 'Monday', subject: 'Advanced Mathematics', time: '11:00 - 12:45', venue: 'A-204', teacher: 'Dr. Naa Korkor' },
      { day: 'Tuesday', subject: 'Economics', time: '09:00 - 10:30', venue: 'A-102', teacher: 'Prof. Daniel Sackey' },
      { day: 'Wednesday', subject: 'Psychology', time: '12:00 - 13:30', venue: 'A-301', teacher: 'Dr. Ama Kofi' },
      { day: 'Thursday', subject: 'Literature', time: '14:00 - 15:30', venue: 'LIT-201', teacher: 'Mrs. Fiona Mensah' },
    ],
    assignments: [
      { title: 'Economics Case Analysis', course: 'Economics', due: '2026-08-23', status: 'Submitted', score: '98/100' },
      { title: 'Calculus Revision Set', course: 'Advanced Mathematics', due: '2026-08-26', status: 'Pending', score: '—' },
      { title: 'Psychology Reflection', course: 'Psychology', due: '2026-08-29', status: 'Submitted', score: '96/100' },
    ],
    exams: [
      { title: 'Final Mock Exams', course: 'Advanced Mathematics', date: '2026-08-31', status: 'Ready' },
      { title: 'Economics Essay', course: 'Economics', date: '2026-09-02', status: 'Scheduled' },
      { title: 'Research Presentation', course: 'Psychology', date: '2026-09-05', status: 'Scheduled' },
    ],
    results: [
      { course: 'Advanced Mathematics', score: '97%', grade: 'A', term: 'Term 1' },
      { course: 'Economics', score: '95%', grade: 'A', term: 'Term 1' },
      { course: 'Psychology', score: '94%', grade: 'A', term: 'Term 1' },
      { course: 'Literature', score: '92%', grade: 'A', term: 'Term 1' },
    ],
    materials: [
      { title: 'Calculus summary sheet', type: 'PDF', date: '2026-08-18' },
      { title: 'Economics revision deck', type: 'Slides', date: '2026-08-19' },
      { title: 'Psychology reading pack', type: 'Document', date: '2026-08-20' },
    ],
    announcements: [
      { title: 'University application support', audience: 'Year 12 parents', date: '2026-08-24' },
      { title: 'Scholarship shortlist', audience: 'Eligible students', date: '2026-08-29' },
      { title: 'Mentor check-in', audience: 'Parents of Year 12', date: '2026-09-01' },
    ],
    notifications: [
      { title: 'Final mock exam timetable published', time: '20 minutes ago' },
      { title: 'Scholarship shortlist notice', time: '3 hours ago' },
      { title: 'University application deadline reminder', time: '1 day ago' },
    ],
    messages: [
      { from: 'Dr. Naa Korkor', subject: 'University prep check-in', time: 'Today, 09:15' },
      { from: 'School counsellor', subject: 'Scholarship guidance', time: 'Yesterday, 13:40' },
      { from: 'HOD office', subject: 'Final exam briefing', time: 'Mon, 11:25' },
    ],
    fees: [
      { item: 'Tuition', due: '$1,900', paid: '$1,680', balance: '$220', status: 'Partial' },
      { item: 'Exam fees', due: '$260', paid: '$260', balance: '$0', status: 'Paid' },
      { item: 'Transport', due: '$300', paid: '$300', balance: '$0', status: 'Paid' },
    ],
    calendar: [
      { title: 'Mentor check-in', date: '2026-08-28', type: 'Meeting' },
      { title: 'Mock exam period', date: '2026-08-31', type: 'Assessment' },
      { title: 'Scholarship briefing', date: '2026-09-02', type: 'Event' },
    ],
    events: [
      { title: 'University Application Workshop', date: '2026-08-28', audience: 'Year 12 families' },
      { title: 'Scholarship Ceremony', date: '2026-09-06', audience: 'School community' },
      { title: 'Graduation Preview', date: '2026-09-10', audience: 'All families' },
    ],
    settings: [
      { module: 'Academic notifications', status: 'Enabled' },
      { module: 'Fee reminders', status: 'On' },
      { module: 'Teacher communication', status: 'Enabled' },
      { module: 'Application updates', status: 'Enabled' },
    ],
  },
]
