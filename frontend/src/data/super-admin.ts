export const kpiCards = [
  {
    title: 'Total enrollments',
    value: '24,860',
    trend: '+12.4%',
    icon: 'Users',
    tone: 'sky',
  },
  {
    title: 'Active faculties',
    value: '1,248',
    trend: '+4.8%',
    icon: 'GraduationCap',
    tone: 'violet',
  },
  {
    title: 'Revenue collected',
    value: '$1.68M',
    trend: '+8.2%',
    icon: 'Wallet',
    tone: 'emerald',
  },
  {
    title: 'Retention rate',
    value: '94.6%',
    trend: '+1.7%',
    icon: 'TrendingUp',
    tone: 'amber',
  },
]

export const attendanceTrend = [
  { day: 'Mon', attendance: 88, target: 82 },
  { day: 'Tue', attendance: 90, target: 86 },
  { day: 'Wed', attendance: 84, target: 85 },
  { day: 'Thu', attendance: 92, target: 87 },
  { day: 'Fri', attendance: 95, target: 90 },
  { day: 'Sat', attendance: 82, target: 83 },
  { day: 'Sun', attendance: 89, target: 84 },
]

export const enrollmentMix = [
  { name: 'Undergraduate', value: 58, fill: '#0f172a' },
  { name: 'Postgraduate', value: 22, fill: '#9f1239' },
  { name: 'Diploma', value: 12, fill: '#e11d48' },
  { name: 'Certificate', value: 8, fill: '#64748b' },
]

export const userRows = [
  { name: 'Aisha Okafor', email: 'aisha.okafor@timaade.edu', role: 'Super Admin', status: 'Active', lastLogin: '2 hours ago' },
  { name: 'Daniel Mensah', email: 'daniel.mensah@timaade.edu', role: 'University Admin', status: 'Pending', lastLogin: '1 day ago' },
  { name: 'Grace Lawson', email: 'grace.lawson@timaade.edu', role: 'Teacher', status: 'Active', lastLogin: '5 hours ago' },
  { name: 'Olivia Johnson', email: 'olivia.johnson@timaade.edu', role: 'Student', status: 'Suspended', lastLogin: '3 days ago' },
  { name: 'Michael Scott', email: 'michael.scott@timaade.edu', role: 'Parent', status: 'Active', lastLogin: '9 hours ago' },
]

export const universityRows = [
  { name: 'Tima-Ade University Main', location: 'Accra, Ghana', students: 18420, teachers: 840, status: 'Operational' },
  { name: 'Tima-Ade University East', location: 'Lagos, Nigeria', students: 9470, teachers: 420, status: 'Review' },
  { name: 'Tima-Ade University North', location: 'Kampala, Uganda', students: 6120, teachers: 310, status: 'Operational' },
  { name: 'Tima-Ade University West', location: 'Nairobi, Kenya', students: 4310, teachers: 220, status: 'Maintenance' },
]

export const roleRows = [
  { name: 'Super Admin', users: 12, permissions: 24 },
  { name: 'University Admin', users: 18, permissions: 18 },
  { name: 'Teacher', users: 140, permissions: 9 },
  { name: 'Student', users: 12400, permissions: 7 },
  { name: 'Parent', users: 4600, permissions: 5 },
]

export const auditRows = [
  { actor: 'System', action: 'Created new fee schedule', timestamp: '2026-08-16 08:42', ip: '10.0.0.18', status: 'Success' },
  { actor: 'Aisha Okafor', action: 'Updated role permissions: Teacher', timestamp: '2026-08-16 07:18', ip: '10.0.0.11', status: 'Success' },
  { actor: 'Daniel Mensah', action: 'Approved university onboarding', timestamp: '2026-08-16 06:09', ip: '10.0.0.27', status: 'Warning' },
  { actor: 'Grace Lawson', action: 'Submitted grade override request', timestamp: '2026-08-15 17:52', ip: '10.0.0.35', status: 'Pending' },
]

export const reportCards = [
  { title: 'Academic performance', value: '88.4%', summary: '+3.1% vs last term' },
  { title: 'Fee collection', value: '$642K', summary: '96.2% on target' },
  { title: 'Attendance compliance', value: '91.7%', summary: '1.2% above average' },
  { title: 'Operational risk', value: 'Low', summary: '3 alerts only' },
]
