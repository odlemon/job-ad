---
config:
  theme: neo
---
classDiagram
    class User {
        +int user_id PK
        +string email
        +string password_hash
        +string user_type
        +datetime created_at
        +datetime last_login
        +bool is_active
        +bool is_verified
        +string phone
        +verifyAccount()
        +login()
        +logout()
        +resetPassword()
    }
    
    class JobSeeker {
        +int seeker_id PK
        +int user_id FK
        +string first_name
        +string last_name
        +string profile_photo
        +text bio
        +string location
        +string cv_file_path
        +datetime cv_uploaded_at
        +datetime updated_at
        +uploadCV()
        +updateProfile()
        +deleteProfile()
        +followCompany()
        +saveJob()
    }
    
    class Employer {
        +int employer_id PK
        +int user_id FK
        +string company_name
        +string company_logo
        +text company_description
        +string industry
        +string company_size
        +string website
        +string address
        +int coin_balance
        +datetime verified_at
        +datetime updated_at
        +postJob()
        +buyCoins()
        +manageUsers()
        +reviewApplications()
    }
    
    class EmployerUser {
        +int emp_user_id PK
        +int employer_id FK
        +string name
        +string email
        +string role
        +string permissions
        +bool is_active
        +datetime created_at
        +addUser()
        +editUser()
        +deleteUser()
    }
    
    class Admin {
        +int admin_id PK
        +int user_id FK
        +string admin_level
        +string permissions
        +datetime created_at
        +manageUsers()
        +moderateJobs()
        +sendCoins()
        +generateReports()
        +toggleSettings()
    }
    class Job {
        +int job_id PK
        +int employer_id FK
        +string title
        +text description
        +text requirements
        +string location
        +int category_id FK
        +string contract_type
        +decimal salary_min
        +decimal salary_max
        +string salary_period
        +int coins_cost
        +datetime posted_at
        +datetime expires_at
        +string status
        +bool requires_approval
        +bool is_approved
        +int approved_by FK
        +datetime approved_at
        +int views_count
        +int applications_count
        +publish()
        +extend()
        +edit()
        +delete()
        +getStatistics()
    }
    
    class Application {
        +int application_id PK
        +int job_id FK
        +int seeker_id FK
        +text cover_letter
        +string cv_file_path
        +string status
        +datetime applied_at
        +datetime updated_at
        +int reviewed_by FK
        +text employer_notes
        +submit()
        +updateStatus()
        +withdraw()
    }
    
    class SavedJob {
        +int saved_id PK
        +int seeker_id FK
        +int job_id FK
        +datetime saved_at
        +save()
        +unsave()
    }
    
    class FollowedCompany {
        +int follow_id PK
        +int seeker_id FK
        +int employer_id FK
        +datetime followed_at
        +follow()
        +unfollow()
    }
    
    class JobAlert {
        +int alert_id PK
        +int seeker_id FK
        +string keywords
        +string location
        +int category_id FK
        +string contract_type
        +decimal salary_min
        +bool is_active
        +string frequency
        +datetime created_at
        +create()
        +update()
        +delete()
        +sendAlert()
    }
    class Category {
        +int category_id PK
        +string name
        +string slug
        +text description
        +int parent_id FK
        +bool is_active
        +int order
        +create()
        +update()
        +delete()
    }
    
    class Location {
        +int location_id PK
        +string city
        +string province
        +string country
        +string postal_code
        +bool is_active
        +create()
        +update()
    }
    class CoinPackage {
        +int package_id PK
        +string name
        +int coins_amount
        +decimal price
        +decimal discount_percentage
        +bool is_active
        +int order
        +create()
        +update()
        +delete()
    }
    
    class Transaction {
        +int transaction_id PK
        +int employer_id FK
        +string transaction_type
        +int coins_amount
        +decimal amount_paid
        +string payment_method
        +string payment_status
        +string transaction_ref
        +string invoice_number
        +datetime transaction_date
        +text notes
        +processPayment()
        +generateInvoice()
        +refund()
    }
    
    class CoinHistory {
        +int history_id PK
        +int employer_id FK
        +int transaction_id FK
        +string action_type
        +int coins_change
        +int balance_before
        +int balance_after
        +string description
        +datetime created_at
        +logTransaction()
    }
    class Course {
        +int course_id PK
        +int institution_id FK
        +string course_name
        +text description
        +string category
        +string location
        +decimal price
        +string duration
        +string contact_info
        +string website
        +datetime posted_at
        +datetime expires_at
        +string status
        +bool is_approved
        +int approved_by FK
        +int views_count
        +publish()
        +edit()
        +delete()
    }
    
    class Institution {
        +int institution_id PK
        +int user_id FK
        +string institution_name
        +string logo
        +text description
        +string contact_email
        +string contact_phone
        +string address
        +string website
        +datetime created_at
        +addCourse()
        +editProfile()
    }
    class Advertisement {
        +int ad_id PK
        +int advertiser_id FK
        +string ad_title
        +text ad_content
        +string ad_image
        +string ad_url
        +string ad_position
        +int days_duration
        +decimal price
        +string status
        +bool is_approved
        +int approved_by FK
        +datetime approved_at
        +datetime start_date
        +datetime end_date
        +int clicks_count
        +int impressions_count
        +submit()
        +approve()
        +reject()
        +getStatistics()
    }
    
    class Advertiser {
        +int advertiser_id PK
        +int user_id FK
        +string company_name
        +string contact_email
        +string contact_phone
        +datetime created_at
        +submitAd()
        +makePayment()
    }
    class Notification {
        +int notification_id PK
        +int user_id FK
        +string notification_type
        +string title
        +text message
        +string action_url
        +bool is_read
        +datetime created_at
        +datetime read_at
        +send()
        +markAsRead()
    }
    
    class PushNotification {
        +int push_id PK
        +string recipient_type
        +string recipient_ids
        +string title
        +text message
        +string action_url
        +datetime scheduled_at
        +datetime sent_at
        +string status
        +int sent_count
        +create()
        +send()
        +getStatistics()
    }
    class SupportTicket {
        +int ticket_id PK
        +int user_id FK
        +string subject
        +text message
        +string status
        +string priority
        +int assigned_to FK
        +datetime created_at
        +datetime updated_at
        +datetime resolved_at
        +create()
        +assign()
        +respond()
        +close()
    }
    
    class FAQ {
        +int faq_id PK
        +string question
        +text answer
        +string category
        +int order
        +bool is_active
        +datetime created_at
        +datetime updated_at
        +create()
        +update()
        +delete()
    }
    
    class Tutorial {
        +int tutorial_id PK
        +string title
        +text content
        +string video_url
        +string target_audience
        +int order
        +bool is_active
        +datetime created_at
        +datetime updated_at
        +create()
        +update()
        +delete()
    }
    class SystemSetting {
        +int setting_id PK
        +string setting_key
        +string setting_value
        +string setting_type
        +text description
        +datetime updated_at
        +int updated_by FK
        +get()
        +update()
    }
    User "1" --> "0..1" JobSeeker
    User "1" --> "0..1" Employer
    User "1" --> "0..1" Admin
    User "1" --> "0..1" Institution
    User "1" --> "0..1" Advertiser
    
    Employer "1" --> "0..*" EmployerUser
    Employer "1" --> "0..*" Job
    Employer "1" --> "0..*" Transaction
    Employer "1" --> "0..*" CoinHistory
    Employer "1" --> "0..*" FollowedCompany
    
    JobSeeker "1" --> "0..*" Application
    JobSeeker "1" --> "0..*" SavedJob
    JobSeeker "1" --> "0..*" FollowedCompany
    JobSeeker "1" --> "0..*" JobAlert
    
    Job "1" --> "0..*" Application
    Job "1" --> "0..*" SavedJob
    Job "1" --> "1" Category
    
    Institution "1" --> "0..*" Course
    
    Advertiser "1" --> "0..*" Advertisement
    
    Category "1" --> "0..*" Category : parent
    Category "1" --> "0..*" JobAlert
    
    Transaction "0..1" --> "0..*" CoinHistory
    
    User "1" --> "0..*" Notification
    User "1" --> "0..*" SupportTicket
    
    Admin "1" --> "0..*" Job : approves
    Admin "1" --> "0..*" Course : approves
    Admin "1" --> "0..*" Advertisement : approves
    Admin "1" --> "0..*" SupportTicket : assigns