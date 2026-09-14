INSERT IGNORE INTO school_settings (school_settings.key, school_settings.value, school_settings.type, school_settings.group, school_settings.label, school_settings.description, school_settings.created_at, school_settings.updated_at)
VALUES
('school_name', 'Tima-Ade University', 'text', 'general', 'School Name', 'Official school name', NOW(), NOW()),
('school_tagline', 'Excellence in Education Since 1990', 'text', 'general', 'Tagline', 'School motto', NOW(), NOW()),
('school_email', 'info@timaade.edu', 'text', 'contact', 'Email Address', 'Contact email', NOW(), NOW()),
('school_phone', '+1 (555) 123-4567', 'text', 'contact', 'Phone Number', 'Contact phone', NOW(), NOW()),
('school_address', 'Tima-Ade University Campus, Main Road, Tima-Ade City', 'text', 'contact', 'Address', 'Physical address', NOW(), NOW()),
('academic_year', '2024-2025', 'text', 'academic', 'Academic Year', 'Active academic year', NOW(), NOW()),
('passing_marks', '40', 'number', 'academic', 'Pass Marks', 'Minimum pass percentage', NOW(), NOW()),
('school_website', 'http://127.0.0.1:8001/', 'text', 'general', 'Website URL', 'Public website URL', NOW(), NOW());
