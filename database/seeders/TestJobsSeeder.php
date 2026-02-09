<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\JobAdvertisement;
use App\Models\JobCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TestJobsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Job Categories
        $categories = [
            [
                'name' => 'Software Development',
                'description' => 'Software development and programming jobs',
                'sort_order' => 1,
            ],
            [
                'name' => 'Hospitality / F&B',
                'description' => 'Hotel, restaurant, and food service positions',
                'sort_order' => 2,
            ],
            [
                'name' => 'Sales / Retail / Marketing',
                'description' => 'Sales, retail, and marketing opportunities',
                'sort_order' => 3,
            ],
            [
                'name' => 'Customer Service',
                'description' => 'Customer support and service roles',
                'sort_order' => 4,
            ],
            [
                'name' => 'Administrative',
                'description' => 'Administrative and office support positions',
                'sort_order' => 5,
            ],
            [
                'name' => 'Finance / Accounting',
                'description' => 'Finance, accounting, and banking jobs',
                'sort_order' => 6,
            ],
            [
                'name' => 'Healthcare',
                'description' => 'Medical and healthcare positions',
                'sort_order' => 7,
            ],
            [
                'name' => 'Education',
                'description' => 'Teaching and educational roles',
                'sort_order' => 8,
            ],
        ];

        $categoryMap = [];
        foreach ($categories as $catData) {
            $category = JobCategory::firstOrCreate(
                ['slug' => Str::slug($catData['name'])],
                [
                    'name' => $catData['name'],
                    'description' => $catData['description'],
                    'is_active' => true,
                    'sort_order' => $catData['sort_order'],
                ]
            );
            $categoryMap[$catData['name']] = $category;
        }

        // Create Companies
        $companies = [
            [
                'slug' => 'tech-solutions-ltd',
                'name' => 'Tech Solutions Ltd',
                'description' => 'Leading technology solutions provider in Seychelles',
                'website' => 'https://techsolutions.sc',
                'email' => 'info@techsolutions.sc',
                'phone' => '+248 1234567',
                'industry' => 'Technology',
                'size' => '51-200',
                'location' => 'Victoria, Mahe',
            ],
            [
                'slug' => 'digital-innovations',
                'name' => 'Digital Innovations',
                'description' => 'Innovative digital solutions and web development company',
                'website' => 'https://digitalinnovations.sc',
                'email' => 'contact@digitalinnovations.sc',
                'phone' => '+248 7654321',
                'industry' => 'Technology',
                'size' => '11-50',
                'location' => 'Beau Vallon, Mahe',
            ],
            [
                'slug' => 'paradise-resort',
                'name' => 'Paradise Resort & Spa',
                'description' => 'Luxury beachfront resort offering world-class hospitality',
                'website' => 'https://paradiseresort.sc',
                'email' => 'careers@paradiseresort.sc',
                'phone' => '+248 2345678',
                'industry' => 'Hospitality',
                'size' => '201-500',
                'location' => 'Beau Vallon, Mahe',
            ],
            [
                'slug' => 'seychelles-trading',
                'name' => 'Seychelles Trading Company',
                'description' => 'Premier retail and trading company in Seychelles',
                'website' => 'https://seychellestrading.sc',
                'email' => 'hr@seychellestrading.sc',
                'phone' => '+248 3456789',
                'industry' => 'Retail',
                'size' => '51-200',
                'location' => 'Victoria, Mahe',
            ],
            [
                'slug' => 'island-bank',
                'name' => 'Island Bank Seychelles',
                'description' => 'Leading financial institution serving Seychelles',
                'website' => 'https://islandbank.sc',
                'email' => 'recruitment@islandbank.sc',
                'phone' => '+248 4567890',
                'industry' => 'Finance',
                'size' => '201-500',
                'location' => 'Victoria, Mahe',
            ],
            [
                'slug' => 'ocean-view-restaurant',
                'name' => 'Ocean View Restaurant',
                'description' => 'Fine dining restaurant with stunning ocean views',
                'website' => 'https://oceanview.sc',
                'email' => 'info@oceanview.sc',
                'phone' => '+248 5678901',
                'industry' => 'Hospitality',
                'size' => '11-50',
                'location' => 'Anse Royale, Mahe',
            ],
            [
                'slug' => 'seychelles-medical-center',
                'name' => 'Seychelles Medical Center',
                'description' => 'Comprehensive healthcare facility',
                'website' => 'https://medicalcenter.sc',
                'email' => 'hr@medicalcenter.sc',
                'phone' => '+248 6789012',
                'industry' => 'Healthcare',
                'size' => '51-200',
                'location' => 'Victoria, Mahe',
            ],
            [
                'slug' => 'island-academy',
                'name' => 'Island Academy',
                'description' => 'Private educational institution',
                'website' => 'https://islandacademy.sc',
                'email' => 'careers@islandacademy.sc',
                'phone' => '+248 7890123',
                'industry' => 'Education',
                'size' => '11-50',
                'location' => 'Victoria, Mahe',
            ],
        ];

        $companyMap = [];
        foreach ($companies as $compData) {
            $company = Company::firstOrCreate(
                ['slug' => $compData['slug']],
                array_merge($compData, ['is_active' => true])
            );
            $companyMap[$compData['slug']] = $company;
        }

        // Create Jobs
        $jobs = [
            // Software Development Jobs
            [
                'slug' => 'senior-software-developer',
                'title' => 'Senior Software Developer',
                'company' => 'tech-solutions-ltd',
                'category' => 'Software Development',
                'description' => 'We are looking for an experienced Senior Software Developer to join our dynamic team. You will be responsible for designing, developing, and maintaining high-quality software solutions.

Key Responsibilities:
- Design and develop scalable web applications
- Write clean, maintainable code following best practices
- Collaborate with cross-functional teams
- Participate in code reviews and technical discussions
- Mentor junior developers

Requirements:
- Bachelor\'s degree in Computer Science or related field
- 5+ years of experience in software development
- Strong knowledge of PHP, Laravel, and JavaScript
- Experience with databases (MySQL, PostgreSQL)
- Excellent problem-solving skills',
                'requirements' => 'Bachelor\'s degree in Computer Science, 5+ years experience, PHP/Laravel, JavaScript, MySQL',
                'benefits' => 'Competitive salary, health insurance, flexible working hours, remote work options',
                'location' => 'Victoria, Mahe',
                'employment_type' => 'full-time',
                'experience_level' => 'senior',
                'salary_min' => 25000,
                'salary_max' => 35000,
                'currency' => 'SCR',
                'application_deadline' => now()->addDays(30),
            ],
            [
                'slug' => 'frontend-developer',
                'title' => 'Frontend Developer',
                'company' => 'digital-innovations',
                'category' => 'Software Development',
                'description' => 'Join our creative team as a Frontend Developer! We are seeking a talented individual to build beautiful and responsive user interfaces.

Key Responsibilities:
- Develop responsive web applications using modern frameworks
- Implement UI/UX designs with pixel-perfect accuracy
- Optimize applications for maximum speed and scalability
- Collaborate with designers and backend developers
- Write clean, semantic HTML, CSS, and JavaScript

Requirements:
- 3+ years of frontend development experience
- Proficiency in HTML5, CSS3, JavaScript (ES6+)
- Experience with React, Vue.js, or Angular
- Knowledge of Tailwind CSS or similar frameworks
- Strong attention to detail and design sense',
                'requirements' => '3+ years experience, HTML5/CSS3/JavaScript, React/Vue/Angular, Tailwind CSS',
                'benefits' => 'Great work environment, learning opportunities, team events, competitive package',
                'location' => 'Beau Vallon, Mahe',
                'employment_type' => 'full-time',
                'experience_level' => 'mid',
                'salary_min' => 18000,
                'salary_max' => 25000,
                'currency' => 'SCR',
                'application_deadline' => now()->addDays(25),
            ],
            [
                'slug' => 'junior-php-developer',
                'title' => 'Junior PHP Developer',
                'company' => 'tech-solutions-ltd',
                'category' => 'Software Development',
                'description' => 'Perfect opportunity for a junior developer to start their career in a supportive environment.

Key Responsibilities:
- Develop and maintain web applications using PHP
- Write clean, well-documented code
- Work with senior developers on team projects
- Learn and apply best practices

Requirements:
- Basic knowledge of PHP and MySQL
- Understanding of HTML, CSS, and JavaScript
- Willingness to learn and grow
- Good communication skills',
                'requirements' => 'Basic PHP/MySQL knowledge, HTML/CSS/JavaScript, willingness to learn',
                'benefits' => 'Mentorship program, training opportunities, career growth',
                'location' => 'Victoria, Mahe',
                'employment_type' => 'full-time',
                'experience_level' => 'entry',
                'salary_min' => 12000,
                'salary_max' => 18000,
                'currency' => 'SCR',
                'application_deadline' => now()->addDays(20),
            ],
            [
                'slug' => 'full-stack-developer',
                'title' => 'Full Stack Developer',
                'company' => 'digital-innovations',
                'category' => 'Software Development',
                'description' => 'We need a versatile Full Stack Developer to work on both frontend and backend systems.

Key Responsibilities:
- Develop end-to-end web applications
- Design and implement RESTful APIs
- Create responsive user interfaces
- Optimize database queries and application performance

Requirements:
- 4+ years of full-stack development experience
- Proficiency in PHP, Laravel, JavaScript, React/Vue
- Strong database design skills
- Experience with Git and version control',
                'requirements' => '4+ years experience, PHP/Laravel, JavaScript, React/Vue, database design',
                'benefits' => 'Flexible schedule, remote work options, competitive package',
                'location' => 'Beau Vallon, Mahe',
                'employment_type' => 'full-time',
                'experience_level' => 'mid',
                'salary_min' => 20000,
                'salary_max' => 28000,
                'currency' => 'SCR',
                'application_deadline' => now()->addDays(28),
            ],
            // Hospitality Jobs
            [
                'slug' => 'hotel-manager',
                'title' => 'Hotel Operations Manager',
                'company' => 'paradise-resort',
                'category' => 'Hospitality / F&B',
                'description' => 'Lead our hotel operations team and ensure exceptional guest experiences.

Key Responsibilities:
- Oversee daily hotel operations
- Manage staff and ensure high service standards
- Handle guest relations and resolve issues
- Coordinate with various departments
- Monitor budgets and operational costs

Requirements:
- 5+ years of hotel management experience
- Strong leadership and communication skills
- Degree in Hospitality Management preferred
- Excellent problem-solving abilities',
                'requirements' => '5+ years hotel management, leadership skills, Hospitality degree preferred',
                'benefits' => 'Accommodation allowance, health insurance, staff meals, career advancement',
                'location' => 'Beau Vallon, Mahe',
                'employment_type' => 'full-time',
                'experience_level' => 'senior',
                'salary_min' => 30000,
                'salary_max' => 40000,
                'currency' => 'SCR',
                'application_deadline' => now()->addDays(35),
            ],
            [
                'slug' => 'chef-de-cuisine',
                'title' => 'Chef de Cuisine',
                'company' => 'ocean-view-restaurant',
                'category' => 'Hospitality / F&B',
                'description' => 'Lead our kitchen team and create exceptional culinary experiences.

Key Responsibilities:
- Plan and execute menu items
- Supervise kitchen staff
- Maintain food quality and safety standards
- Manage inventory and food costs
- Create seasonal menus

Requirements:
- Culinary degree or equivalent experience
- 5+ years of experience in fine dining
- Strong leadership and organizational skills
- Knowledge of international cuisines',
                'requirements' => 'Culinary degree, 5+ years fine dining experience, leadership skills',
                'benefits' => 'Competitive salary, staff meals, health insurance, creative freedom',
                'location' => 'Anse Royale, Mahe',
                'employment_type' => 'full-time',
                'experience_level' => 'senior',
                'salary_min' => 25000,
                'salary_max' => 35000,
                'currency' => 'SCR',
                'application_deadline' => now()->addDays(30),
            ],
            [
                'slug' => 'waiter-waitress',
                'title' => 'Waiter / Waitress',
                'company' => 'ocean-view-restaurant',
                'category' => 'Hospitality / F&B',
                'description' => 'Join our friendly service team and provide excellent customer service.

Key Responsibilities:
- Take orders and serve food and beverages
- Provide menu recommendations
- Ensure customer satisfaction
- Maintain clean and organized dining area
- Handle payments and transactions

Requirements:
- Previous restaurant experience preferred
- Excellent communication skills
- Ability to work in a fast-paced environment
- Friendly and professional demeanor',
                'requirements' => 'Restaurant experience preferred, communication skills, friendly attitude',
                'benefits' => 'Tips, staff meals, flexible schedule, training provided',
                'location' => 'Anse Royale, Mahe',
                'employment_type' => 'part-time',
                'experience_level' => 'entry',
                'salary_min' => 8000,
                'salary_max' => 12000,
                'currency' => 'SCR',
                'application_deadline' => now()->addDays(15),
            ],
            [
                'slug' => 'receptionist',
                'title' => 'Front Desk Receptionist',
                'company' => 'paradise-resort',
                'category' => 'Hospitality / F&B',
                'description' => 'Be the first point of contact for our guests and ensure a warm welcome.

Key Responsibilities:
- Greet and check-in guests
- Handle reservations and inquiries
- Process payments and check-outs
- Provide information about hotel services
- Maintain front desk area

Requirements:
- Previous hospitality experience
- Excellent customer service skills
- Proficiency in English and French
- Computer literacy',
                'requirements' => 'Hospitality experience, customer service skills, English/French, computer skills',
                'benefits' => 'Staff accommodation, meals, health insurance, career growth',
                'location' => 'Beau Vallon, Mahe',
                'employment_type' => 'full-time',
                'experience_level' => 'entry',
                'salary_min' => 10000,
                'salary_max' => 15000,
                'currency' => 'SCR',
                'application_deadline' => now()->addDays(20),
            ],
            // Sales & Marketing Jobs
            [
                'slug' => 'sales-manager',
                'title' => 'Sales Manager',
                'company' => 'seychelles-trading',
                'category' => 'Sales / Retail / Marketing',
                'description' => 'Lead our sales team and drive revenue growth.

Key Responsibilities:
- Develop and execute sales strategies
- Manage and motivate sales team
- Build relationships with clients
- Analyze sales data and market trends
- Achieve sales targets

Requirements:
- 5+ years of sales management experience
- Strong leadership and negotiation skills
- Proven track record of meeting targets
- Excellent communication abilities',
                'requirements' => '5+ years sales management, leadership skills, proven track record',
                'benefits' => 'Commission structure, health insurance, company vehicle, bonuses',
                'location' => 'Victoria, Mahe',
                'employment_type' => 'full-time',
                'experience_level' => 'senior',
                'salary_min' => 22000,
                'salary_max' => 30000,
                'currency' => 'SCR',
                'application_deadline' => now()->addDays(25),
            ],
            [
                'slug' => 'marketing-specialist',
                'title' => 'Marketing Specialist',
                'company' => 'digital-innovations',
                'category' => 'Sales / Retail / Marketing',
                'description' => 'Create and execute marketing campaigns to promote our digital services.

Key Responsibilities:
- Develop marketing strategies
- Manage social media accounts
- Create content for various channels
- Analyze marketing performance
- Coordinate events and promotions

Requirements:
- 3+ years of marketing experience
- Knowledge of digital marketing tools
- Creative thinking and writing skills
- Social media expertise',
                'requirements' => '3+ years marketing experience, digital marketing knowledge, creative skills',
                'benefits' => 'Creative freedom, flexible hours, professional development, competitive package',
                'location' => 'Beau Vallon, Mahe',
                'employment_type' => 'full-time',
                'experience_level' => 'mid',
                'salary_min' => 15000,
                'salary_max' => 22000,
                'currency' => 'SCR',
                'application_deadline' => now()->addDays(22),
            ],
            [
                'slug' => 'retail-sales-associate',
                'title' => 'Retail Sales Associate',
                'company' => 'seychelles-trading',
                'category' => 'Sales / Retail / Marketing',
                'description' => 'Help customers find the perfect products and provide excellent service.

Key Responsibilities:
- Assist customers with product selection
- Process sales transactions
- Maintain store appearance
- Stock and organize merchandise
- Handle customer inquiries

Requirements:
- Previous retail experience preferred
- Friendly and approachable personality
- Good communication skills
- Ability to work flexible hours',
                'requirements' => 'Retail experience preferred, friendly personality, communication skills',
                'benefits' => 'Employee discounts, commission, flexible schedule, training',
                'location' => 'Victoria, Mahe',
                'employment_type' => 'part-time',
                'experience_level' => 'entry',
                'salary_min' => 7000,
                'salary_max' => 10000,
                'currency' => 'SCR',
                'application_deadline' => now()->addDays(18),
            ],
            // Customer Service Jobs
            [
                'slug' => 'customer-service-representative',
                'title' => 'Customer Service Representative',
                'company' => 'island-bank',
                'category' => 'Customer Service',
                'description' => 'Provide exceptional customer service and support to our banking clients.

Key Responsibilities:
- Assist customers with banking inquiries
- Process transactions and account services
- Resolve customer issues and complaints
- Promote bank products and services
- Maintain accurate records

Requirements:
- Previous customer service experience
- Excellent communication skills
- Attention to detail
- Professional appearance and demeanor',
                'requirements' => 'Customer service experience, communication skills, attention to detail',
                'benefits' => 'Banking benefits, health insurance, career development, competitive salary',
                'location' => 'Victoria, Mahe',
                'employment_type' => 'full-time',
                'experience_level' => 'entry',
                'salary_min' => 12000,
                'salary_max' => 18000,
                'currency' => 'SCR',
                'application_deadline' => now()->addDays(20),
            ],
            [
                'slug' => 'call-center-agent',
                'title' => 'Call Center Agent',
                'company' => 'tech-solutions-ltd',
                'category' => 'Customer Service',
                'description' => 'Handle customer inquiries and provide technical support via phone and email.

Key Responsibilities:
- Answer customer calls and emails
- Provide technical support
- Resolve customer issues
- Document interactions
- Escalate complex issues

Requirements:
- Good communication skills
- Basic technical knowledge
- Patience and problem-solving ability
- Computer literacy',
                'requirements' => 'Communication skills, technical knowledge, problem-solving, computer skills',
                'benefits' => 'Flexible shifts, training provided, health insurance, career growth',
                'location' => 'Victoria, Mahe',
                'employment_type' => 'full-time',
                'experience_level' => 'entry',
                'salary_min' => 10000,
                'salary_max' => 15000,
                'currency' => 'SCR',
                'application_deadline' => now()->addDays(18),
            ],
            // Administrative Jobs
            [
                'slug' => 'administrative-assistant',
                'title' => 'Administrative Assistant',
                'company' => 'island-bank',
                'category' => 'Administrative',
                'description' => 'Provide administrative support to ensure efficient office operations.

Key Responsibilities:
- Manage schedules and appointments
- Handle correspondence and filing
- Prepare documents and reports
- Coordinate meetings and events
- Assist with various administrative tasks

Requirements:
- Previous administrative experience
- Proficiency in MS Office
- Excellent organizational skills
- Strong attention to detail',
                'requirements' => 'Administrative experience, MS Office proficiency, organizational skills',
                'benefits' => 'Stable hours, health insurance, professional development, good work environment',
                'location' => 'Victoria, Mahe',
                'employment_type' => 'full-time',
                'experience_level' => 'entry',
                'salary_min' => 11000,
                'salary_max' => 16000,
                'currency' => 'SCR',
                'application_deadline' => now()->addDays(22),
            ],
            [
                'slug' => 'office-manager',
                'title' => 'Office Manager',
                'company' => 'seychelles-trading',
                'category' => 'Administrative',
                'description' => 'Oversee daily office operations and manage administrative staff.

Key Responsibilities:
- Manage office facilities and supplies
- Supervise administrative staff
- Coordinate office activities
- Handle vendor relationships
- Ensure compliance with policies

Requirements:
- 5+ years of office management experience
- Strong leadership skills
- Excellent organizational abilities
- Budget management experience',
                'requirements' => '5+ years office management, leadership skills, organizational abilities',
                'benefits' => 'Competitive salary, health insurance, leadership role, career advancement',
                'location' => 'Victoria, Mahe',
                'employment_type' => 'full-time',
                'experience_level' => 'senior',
                'salary_min' => 20000,
                'salary_max' => 28000,
                'currency' => 'SCR',
                'application_deadline' => now()->addDays(28),
            ],
            // Finance Jobs
            [
                'slug' => 'accountant',
                'title' => 'Accountant',
                'company' => 'island-bank',
                'category' => 'Finance / Accounting',
                'description' => 'Manage financial records and ensure accurate accounting practices.

Key Responsibilities:
- Prepare financial statements
- Process accounts payable and receivable
- Reconcile bank statements
- Assist with budgeting and forecasting
- Ensure compliance with regulations

Requirements:
- Accounting degree or equivalent
- 3+ years of accounting experience
- Proficiency in accounting software
- Strong analytical skills',
                'requirements' => 'Accounting degree, 3+ years experience, accounting software proficiency',
                'benefits' => 'Professional development, health insurance, competitive package, stable career',
                'location' => 'Victoria, Mahe',
                'employment_type' => 'full-time',
                'experience_level' => 'mid',
                'salary_min' => 18000,
                'salary_max' => 25000,
                'currency' => 'SCR',
                'application_deadline' => now()->addDays(25),
            ],
            [
                'slug' => 'financial-analyst',
                'title' => 'Financial Analyst',
                'company' => 'island-bank',
                'category' => 'Finance / Accounting',
                'description' => 'Analyze financial data and provide insights to support business decisions.

Key Responsibilities:
- Analyze financial performance
- Create financial models and forecasts
- Prepare reports and presentations
- Research market trends
- Support strategic planning

Requirements:
- Finance or Economics degree
- 3+ years of financial analysis experience
- Strong Excel and analytical skills
- Attention to detail',
                'requirements' => 'Finance/Economics degree, 3+ years experience, Excel skills, analytical ability',
                'benefits' => 'Competitive salary, bonuses, professional growth, health insurance',
                'location' => 'Victoria, Mahe',
                'employment_type' => 'full-time',
                'experience_level' => 'mid',
                'salary_min' => 20000,
                'salary_max' => 28000,
                'currency' => 'SCR',
                'application_deadline' => now()->addDays(30),
            ],
            // Healthcare Jobs
            [
                'slug' => 'registered-nurse',
                'title' => 'Registered Nurse',
                'company' => 'seychelles-medical-center',
                'category' => 'Healthcare',
                'description' => 'Provide quality nursing care to patients in our medical facility.

Key Responsibilities:
- Provide patient care and monitoring
- Administer medications and treatments
- Document patient information
- Assist physicians with procedures
- Educate patients and families

Requirements:
- Nursing degree and valid license
- 2+ years of nursing experience
- Strong clinical skills
- Compassionate and caring nature',
                'requirements' => 'Nursing degree, valid license, 2+ years experience, clinical skills',
                'benefits' => 'Competitive salary, health insurance, continuing education, shift allowances',
                'location' => 'Victoria, Mahe',
                'employment_type' => 'full-time',
                'experience_level' => 'mid',
                'salary_min' => 20000,
                'salary_max' => 28000,
                'currency' => 'SCR',
                'application_deadline' => now()->addDays(28),
            ],
            [
                'slug' => 'medical-receptionist',
                'title' => 'Medical Receptionist',
                'company' => 'seychelles-medical-center',
                'category' => 'Healthcare',
                'description' => 'Manage front desk operations and assist patients with appointments.

Key Responsibilities:
- Schedule patient appointments
- Register new patients
- Handle insurance verification
- Process payments
- Maintain patient records

Requirements:
- Previous medical office experience preferred
- Good communication skills
- Computer literacy
- Professional and friendly demeanor',
                'requirements' => 'Medical office experience preferred, communication skills, computer skills',
                'benefits' => 'Health insurance, stable hours, training provided, good work environment',
                'location' => 'Victoria, Mahe',
                'employment_type' => 'full-time',
                'experience_level' => 'entry',
                'salary_min' => 10000,
                'salary_max' => 15000,
                'currency' => 'SCR',
                'application_deadline' => now()->addDays(20),
            ],
            // Education Jobs
            [
                'slug' => 'primary-school-teacher',
                'title' => 'Primary School Teacher',
                'company' => 'island-academy',
                'category' => 'Education',
                'description' => 'Teach and inspire young students in a supportive educational environment.

Key Responsibilities:
- Plan and deliver lessons
- Assess student progress
- Create engaging learning activities
- Communicate with parents
- Maintain classroom discipline

Requirements:
- Teaching degree or certification
- 2+ years of teaching experience
- Passion for education
- Strong communication skills',
                'requirements' => 'Teaching degree/certification, 2+ years experience, passion for education',
                'benefits' => 'School holidays, professional development, health insurance, competitive package',
                'location' => 'Victoria, Mahe',
                'employment_type' => 'full-time',
                'experience_level' => 'mid',
                'salary_min' => 18000,
                'salary_max' => 25000,
                'currency' => 'SCR',
                'application_deadline' => now()->addDays(30),
            ],
            [
                'slug' => 'it-intern',
                'title' => 'IT Intern',
                'company' => 'tech-solutions-ltd',
                'category' => 'Software Development',
                'description' => 'Gain hands-on experience in software development and IT support.

Key Responsibilities:
- Assist with software development projects
- Provide IT support to staff
- Learn from senior developers
- Participate in team meetings
- Complete assigned tasks

Requirements:
- Currently pursuing IT/Computer Science degree
- Basic programming knowledge
- Eagerness to learn
- Good communication skills',
                'requirements' => 'IT/CS degree in progress, basic programming, eagerness to learn',
                'benefits' => 'Mentorship, real-world experience, potential full-time offer, flexible schedule',
                'location' => 'Victoria, Mahe',
                'employment_type' => 'internship',
                'experience_level' => 'entry',
                'salary_min' => 5000,
                'salary_max' => 8000,
                'currency' => 'SCR',
                'application_deadline' => now()->addDays(15),
            ],
        ];

        $createdCount = 0;
        foreach ($jobs as $jobData) {
            $company = $companyMap[$jobData['company']];
            $category = $categoryMap[$jobData['category']];

            JobAdvertisement::firstOrCreate(
                ['slug' => $jobData['slug']],
                [
                    'company_id' => $company->id,
                    'category_id' => $category->id,
                    'title' => $jobData['title'],
                    'description' => $jobData['description'],
                    'requirements' => $jobData['requirements'] ?? null,
                    'benefits' => $jobData['benefits'] ?? null,
                    'location' => $jobData['location'],
                    'employment_type' => $jobData['employment_type'],
                    'experience_level' => $jobData['experience_level'],
                    'salary_min' => $jobData['salary_min'],
                    'salary_max' => $jobData['salary_max'],
                    'currency' => $jobData['currency'],
                    'application_deadline' => $jobData['application_deadline'],
                    'status' => 'published',
                    'published_at' => now()->subDays(rand(0, 7)), // Randomize publish date
                    'views_count' => rand(0, 500),
                    'applications_count' => rand(0, 20),
                ]
            );
            $createdCount++;
        }

        $this->command->info("✅ Successfully created {$createdCount} jobs across " . count($categories) . " categories!");
        $this->command->info("   Categories: " . implode(', ', array_column($categories, 'name')));
        $this->command->info("   Companies: " . count($companies));
    }
}
