<?php

namespace Tests\Unit;

use App\Models\ExamMark;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Tests\TestCase;

class ExamMarkTest extends TestCase
{
    public function test_student_and_user_parent_relationships_are_configured(): void
    {
        $studentRelation = (new Student())->examMarks();
        $this->assertInstanceOf(HasMany::class, $studentRelation);
        $this->assertSame('student_id', $studentRelation->getForeignKeyName());

        $parentRelation = (new User())->parent();
        $this->assertInstanceOf(HasOne::class, $parentRelation);
        $this->assertSame('user_id', $parentRelation->getForeignKeyName());
    }

    public function test_compute_grade_boundaries(): void
    {
        $this->assertSame('A+', ExamMark::computeGrade(90, 100));
        $this->assertSame('A+', ExamMark::computeGrade(95.5, 100));
        $this->assertSame('A+', ExamMark::computeGrade(100, 100));
        $this->assertSame('A', ExamMark::computeGrade(80, 100));
        $this->assertSame('A', ExamMark::computeGrade(89.99, 100));
        $this->assertSame('B+', ExamMark::computeGrade(70, 100));
        $this->assertSame('B', ExamMark::computeGrade(60, 100));
        $this->assertSame('C+', ExamMark::computeGrade(50, 100));
        $this->assertSame('C', ExamMark::computeGrade(40, 100));
        $this->assertSame('D', ExamMark::computeGrade(33, 100));
        $this->assertSame('F', ExamMark::computeGrade(32.99, 100));
        $this->assertSame('F', ExamMark::computeGrade(0, 100));
        $this->assertSame('F', ExamMark::computeGrade(-1, 100));
        $this->assertSame('A+', ExamMark::computeGrade(180, 200));
        $this->assertSame('A+', ExamMark::computeGrade(120, 100));
        $this->assertSame('F', ExamMark::computeGrade(null, 0));
        $this->assertSame('F', ExamMark::computeGrade('abc', 'def'));
        $this->assertSame('F', ExamMark::computeGrade(50, 0));
    }
}
