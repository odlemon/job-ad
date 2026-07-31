<?php

namespace Tests\Unit;

use App\Support\RegisterInputNormalizer;
use PHPUnit\Framework\TestCase;

class RegisterInputNormalizerTest extends TestCase
{
    public function test_normalizes_scoop_ui_labels_and_dob(): void
    {
        $out = RegisterInputNormalizer::normalize([
            'date_of_birth' => '15 / 05 / 1995',
            'gender' => 'Male',
            'employment_status' => 'Employed full-time',
            'highest_education' => "Bachelor's degree",
            'job_preferences' => ['Full-time', 'Part-time'],
        ]);

        $this->assertSame('1995-05-15', $out['date_of_birth']);
        $this->assertSame('male', $out['gender']);
        $this->assertSame('currently_employed', $out['employment_status']);
        $this->assertSame('bachelor', $out['highest_education']);
        $this->assertSame(['full-time', 'part-time'], $out['job_preferences']);
    }
}
