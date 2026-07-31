<?php

namespace App\Support;

use App\Models\JobSeekerCertification;
use App\Models\JobSeekerLanguage;
use App\Models\JobSeekerSkill;

class ScoopNestedPresenter
{
    public static function skill(JobSeekerSkill $skill): array
    {
        return [
            'id' => $skill->id,
            'name' => $skill->skill_name,
            'level' => $skill->proficiency_level,
            'skill_name' => $skill->skill_name,
            'proficiency_level' => $skill->proficiency_level,
        ];
    }

    public static function language(JobSeekerLanguage $language): array
    {
        return [
            'id' => $language->id,
            'name' => $language->language,
            'level' => $language->proficiency_level,
            'language' => $language->language,
            'proficiency_level' => $language->proficiency_level,
        ];
    }

    public static function certification(JobSeekerCertification $cert): array
    {
        return [
            'id' => $cert->id,
            'name' => $cert->certification_name,
            'issuer' => $cert->issuing_organization,
            'issued_at' => optional($cert->issue_date)?->format('Y-m-d'),
            'expires' => $cert->expiry_date !== null,
            'expires_at' => optional($cert->expiry_date)?->format('Y-m-d'),
            'document_url' => $cert->certificate_file_path,
            'certification_name' => $cert->certification_name,
            'issuing_organization' => $cert->issuing_organization,
            'issue_date' => optional($cert->issue_date)?->format('Y-m-d'),
            'expiry_date' => optional($cert->expiry_date)?->format('Y-m-d'),
        ];
    }
}
