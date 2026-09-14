<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Academic Report Card — {{ $student->user->name }} — Tima-Ade University</title>
    <!-- Google Fonts & Bootstrap -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #F5F7FA;
            color: #1F2937;
            padding: 2rem 0;
        }
        .report-card-container {
            max-width: 850px;
            margin: 0 auto;
            background: #FFFFFF;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            padding: 2.5rem;
            border: 1px solid #E5E7EB;
        }
        .report-header {
            border-bottom: 3px double #016ED5;
            padding-bottom: 1.5rem;
            margin-bottom: 2rem;
        }
        .school-logo-icon {
            width: 70px;
            height: 70px;
            background: #016ED5;
            color: #FFFFFF;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.2rem;
        }
        .school-name {
            color: #016ED5;
            font-weight: 800;
            font-size: 1.8rem;
            line-height: 1.2;
        }
        .school-tagline {
            color: #FB8B01;
            font-weight: 600;
            font-size: 0.95rem;
            letter-spacing: 0.5px;
        }
        .grade-badge {
            font-size: 1.1rem;
            padding: 0.4rem 0.8rem;
            font-weight: 700;
        }
        .summary-box {
            background-color: #F9FAFB;
            border: 1px solid #E5E7EB;
            border-radius: 8px;
            padding: 1rem;
        }
        .signature-line {
            border-top: 1px solid #9CA3AF;
            width: 80%;
            margin: 2.5rem auto 0.5rem auto;
        }
        @media print {
            body {
                background: #FFFFFF;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            .report-card-container {
                box-shadow: none;
                border: none;
                padding: 0;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>

    <!-- Action Toolbar (Hidden during Print) -->
    <div class="container mb-4 no-print text-center">
        <div class="d-inline-flex gap-2">
            <button onclick="window.print()" class="btn btn-primary px-4 shadow-sm" style="background-color: #016ED5; border-color: #016ED5;">
                <i class="bi bi-printer-fill me-2"></i> Print Official Report Card
            </button>
            <a href="javascript:history.back()" class="btn btn-outline-secondary px-4">
                <i class="bi bi-arrow-left me-1"></i> Return Back
            </a>
        </div>
    </div>

    <!-- Official Report Card Sheet -->
    <div class="report-card-container">
        
        <!-- Header -->
        <div class="report-header">
            <div class="row align-items-center">
                <div class="col-auto">
                    <div class="school-logo-icon">
                        <i class="bi bi-mortarboard-fill"></i>
                    </div>
                </div>
                <div class="col text-center">
                    <h1 class="school-name mb-0">TIMA-ADE UNIVERSITY</h1>
                    <div class="small text-muted mt-1">Official Academic Progress Report &bull; Academic Session {{ date('Y') }}</div>
                </div>
                <div class="col-auto text-end">
                    <div class="badge bg-light text-dark border px-3 py-2">
                        <strong>{{ strtoupper($exam->name) }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scholar Bio Grid -->
        <div class="row g-3 mb-4 bg-light p-3 rounded-3 border">
            <div class="col-md-6">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted small fw-semibold" style="width: 130px;">Scholar Name:</td>
                        <td class="fw-bold text-dark">{{ $student->user->name }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted small fw-semibold">Roll Number:</td>
                        <td class="fw-semibold">{{ $student->roll_number }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted small fw-semibold">Admission No:</td>
                        <td class="fw-semibold">{{ $student->admission_number }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted small fw-semibold" style="width: 130px;">Class & Section:</td>
                        <td class="fw-bold text-dark">{{ $student->schoolClass->name ?? 'Class' }} ({{ $student->section->name ?? 'A' }})</td>
                    </tr>
                    <tr>
                        <td class="text-muted small fw-semibold">Assessment:</td>
                        <td class="fw-semibold">{{ $exam->name }} ({{ ucfirst($exam->exam_type) }})</td>
                    </tr>
                    <tr>
                        <td class="text-muted small fw-semibold">Issue Date:</td>
                        <td class="fw-semibold">{{ now()->format('F d, Y') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Academic Performance Table -->
        <div class="table-responsive mb-4">
            <table class="table table-bordered align-middle text-center mb-0">
                <thead class="table-dark" style="background-color: #1F2937;">
                    <tr>
                        <th class="text-start ps-3">Subject / Course</th>
                        <th>Max Marks</th>
                        <th>Pass Marks</th>
                        <th>Marks Obtained</th>
                        <th>Grade</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($marks as $m)
                        <tr>
                            <td class="text-start ps-3 fw-semibold text-dark">
                                {{ $m->exam->subject->name ?? 'Subject' }}
                                <div class="small text-muted">{{ $m->exam->subject->code ?? '' }}</div>
                            </td>
                            <td>{{ $m->exam->total_marks ?? 100 }}</td>
                            <td>{{ $m->exam->pass_marks ?? 40 }}</td>
                            <td class="fw-bold fs-6">
                                @if($m->is_absent)
                                    <span class="text-danger">ABSENT</span>
                                @else
                                    {{ $m->marks_obtained }}
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-primary px-2 py-1">{{ $m->grade }}</span>
                            </td>
                            <td>
                                @if(!$m->is_absent && $m->marks_obtained >= ($m->exam->pass_marks ?? 40))
                                    <span class="text-success fw-bold"><i class="bi bi-check-circle-fill me-1"></i>Pass</span>
                                @else
                                    <span class="text-danger fw-bold"><i class="bi bi-x-circle-fill me-1"></i>Fail</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-4 text-muted">No marks recorded for this assessment.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="table-light fw-bold">
                    <tr>
                        <th class="text-start ps-3">Grand Aggregate</th>
                        <th>{{ $totalMaxMarks }}</th>
                        <th>—</th>
                        <th class="text-primary fs-6">{{ $totalMarksObtained }}</th>
                        <th><span class="badge bg-success grade-badge">{{ $overallGrade }}</span></th>
                        <th>
                            @if($overallPercentage >= 40)
                                <span class="text-success fw-bold">PROMOTED / PASS</span>
                            @else
                                <span class="text-danger fw-bold">NEEDS IMPROVEMENT</span>
                            @endif
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Performance Summary & Attendance -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="summary-box text-center">
                    <div class="small text-muted fw-semibold">OVERALL PERCENTAGE</div>
                    <div class="fs-3 fw-bold text-primary">{{ $overallPercentage }}%</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="summary-box text-center">
                    <div class="small text-muted fw-semibold">FINAL GRADE</div>
                    <div class="fs-3 fw-bold text-success">{{ $overallGrade }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="summary-box text-center">
                    <div class="small text-muted fw-semibold">ATTENDANCE RECORD</div>
                    <div class="fs-3 fw-bold text-dark">{{ $attendancePct }}%</div>
                    <div class="small text-muted">{{ $presentDays }} / {{ $totalAttendance }} days present</div>
                </div>
            </div>
        </div>

        <!-- Remarks & Grading Key -->
        <div class="border rounded-3 p-3 mb-5 bg-white">
            <h6 class="fw-bold text-dark mb-1 small">Grading Benchmark Standard:</h6>
            <div class="small text-muted d-flex flex-wrap gap-3">
                <span><strong>A+</strong>: 90%–100% (Outstanding)</span>
                <span><strong>A</strong>: 80%–89% (Excellent)</span>
                <span><strong>B</strong>: 70%–79% (Very Good)</span>
                <span><strong>C</strong>: 60%–69% (Good)</span>
                <span><strong>D</strong>: 50%–59% (Satisfactory)</span>
                <span><strong>F</strong>: &lt;50% (Fail)</span>
            </div>
        </div>

        <!-- Signatures -->
        <div class="row text-center pt-3">
            <div class="col-4">
                <div class="signature-line"></div>
                <div class="small fw-bold text-dark">Class Teacher</div>
                <div class="small text-muted">Academic Faculty</div>
            </div>
            <div class="col-4">
                <div class="signature-line"></div>
                <div class="small fw-bold text-dark">Principal / Dean</div>
                <div class="small text-muted">Tima-Ade University Administration</div>
            </div>
            <div class="col-4">
                <div class="signature-line"></div>
                <div class="small fw-bold text-dark">Parent / Guardian Signature</div>
                <div class="small text-muted">Acknowledgement</div>
            </div>
        </div>

    </div>

</body>
</html>
