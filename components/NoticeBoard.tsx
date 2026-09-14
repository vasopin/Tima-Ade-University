export default function NoticeBoard(){
  const notices = [
    { id: 1, title: 'Final Exams Schedule Released', date: '2026-08-30', category: 'Exams', urgent: true },
    { id: 2, title: 'Library Closed for Maintenance', date: '2026-09-05', category: 'Facilities', urgent: false },
    { id: 3, title: 'Scholarship Application Deadline', date: '2026-09-15', category: 'Admissions', urgent: true },
  ]

  return (
    <div className="bg-white p-4 rounded-xl shadow-card">
      <h4 className="font-semibold mb-3">Notice Board</h4>
      <ul className="space-y-3">
        {notices.map(n => (
          <li key={n.id} className="flex items-start gap-3">
            <div className={`w-2 h-8 rounded ${n.urgent ? 'bg-red-500' : 'bg-slate-200'}`} />
            <div>
              <div className="font-semibold">{n.title}</div>
              <div className="text-sm text-slate-500">{n.category} • {new Date(n.date).toLocaleDateString()}</div>
            </div>
          </li>
        ))}
      </ul>
      <div className="mt-4">
        <a className="text-primary-700">View All Notices →</a>
      </div>
    </div>
  )
}
