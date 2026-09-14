INSERT IGNORE INTO exams (id, name, exam_type, school_class_id, subject_id, exam_date, start_time, duration_minutes, total_marks, pass_marks, status, instructions, created_at, updated_at)
VALUES
(1, 'Mid-Term Examination 2024', 'midterm', 1, 1, '2024-10-15', '09:00:00', 90, 100, 40, 'completed', 'Standard written examination covering chapters 1 to 5.', NOW(), NOW()),
(2, 'Mathematics Quiz #1', 'quiz', 1, 1, '2024-11-01', '10:00:00', 45, 50, 20, 'completed', 'Short answer and problem solving test.', NOW(), NOW()),
(3, 'Final Term Examination 2024', 'final', 1, 1, '2024-12-10', '09:00:00', 120, 100, 40, 'scheduled', 'Comprehensive end-of-year examination.', NOW(), NOW());

INSERT IGNORE INTO exam_marks (id, exam_id, student_id, marks_obtained, grade, is_absent, remarks, created_at, updated_at)
VALUES
(1, 1, 1, 88.5, 'A', 0, 'Excellent problem solving skills', NOW(), NOW()),
(2, 1, 2, 74.0, 'B+', 0, 'Good effort, review algebra section', NOW(), NOW()),
(3, 1, 3, 92.0, 'A+', 0, 'Outstanding performance, top of class', NOW(), NOW()),
(4, 1, 4, 61.5, 'B', 0, 'Satisfactory progress', NOW(), NOW()),
(5, 1, 5, 45.0, 'C', 0, 'Need extra tutoring in calculus', NOW(), NOW()),
(6, 2, 1, 46.0, 'A+', 0, 'Quick and accurate work', NOW(), NOW()),
(7, 2, 2, 38.5, 'B+', 0, 'Well done', NOW(), NOW()),
(8, 2, 3, 49.0, 'A+', 0, 'Near perfect score', NOW(), NOW());
