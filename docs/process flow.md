flowchart TB
    Start([Platform Entry]) --> Browse{Browse Platform}
    
    %% Public Browsing - No Login Required
    Browse -->|View Jobs| Public_Jobs[Browse Job Listings<br/>Public Access]
    Browse -->|View Courses| Public_Courses[Browse Course Ads<br/>Separate Page<br/>View Only]
    
    Public_Jobs --> NeedApply{Want to Apply?}
    NeedApply -->|Yes| JS_Register
    NeedApply -->|No| Public_Jobs
    
    Browse -->|Sign In/Register| UserSelect{Select User Type}
    
    %% Job Seeker Path
    UserSelect -->|Job Seeker| JS_Register[Register/Login<br/>Job Seeker]
    JS_Register --> JS_Dashboard[Job Seeker Dashboard]
    
    JS_Dashboard --> JS_Profile[Profile Management<br/>Edit/Delete Profile<br/>Upload CV]
    JS_Dashboard --> JS_Search[Search & Apply Jobs<br/>Filter: Location, Category<br/>Salary, Contract Type]
    JS_Dashboard --> JS_Saved[Saved/Liked Jobs<br/>Followed Companies]
    JS_Dashboard --> JS_Settings[Settings]
    JS_Dashboard --> JS_Help[Help/Support/FAQ<br/>Tutorials]
    
    JS_Search --> JS_Apply[Apply to Job<br/>Submit Application]
    JS_Apply --> JS_Status[Get Status Updates<br/>Email & Push Notifications<br/>Shortlisted/Rejected]
    
    %% Employer Path
    UserSelect -->|Employer| EMP_Register[Register/Login<br/>New Company]
    EMP_Register --> EMP_Welcome[Welcome Bonus<br/>Receive Initial JobCoins<br/>If Admin Enabled]
    EMP_Welcome --> EMP_Dashboard[Employer Dashboard]
    
    EMP_Dashboard --> EMP_Profile[Company Profile<br/>Manage Users<br/>Add/Edit/Delete]
    EMP_Dashboard --> EMP_Billing[Billing & Transactions<br/>Invoice History<br/>Reports<br/>Buy Coins<br/>Coin Balance]
    EMP_Dashboard --> EMP_Stats[Job Ad Statistics<br/>Total Posted Jobs<br/>Ad Performance]
    EMP_Dashboard --> EMP_Settings[Settings]
    EMP_Dashboard --> EMP_Support[Help/Support/FAQ<br/>Tutorials]
    
    EMP_Dashboard --> EMP_PostJob[Create Job Post<br/>Deduct Coins]
    EMP_PostJob --> Auto_Approve{Auto-Approval<br/>Enabled?}
    
    Auto_Approve -->|Yes| Job_Published[Job Published<br/>Live Immediately]
    Auto_Approve -->|No| ADMIN_JobReview[Admin Review Queue]
    
    ADMIN_JobReview --> ADMIN_JobDecision{Approve?}
    ADMIN_JobDecision -->|Yes| Job_Published
    ADMIN_JobDecision -->|No| EMP_Rejected[Job Rejected<br/>Notify Employer]
    
    Job_Published --> Job_Live[Job Visible<br/>on Platform]
    Job_Live --> Public_Jobs
    
    EMP_Dashboard --> EMP_Manage[Manage Posted Jobs<br/>Edit/Delete/Remove<br/>Extend Period<br/>View Ad Stats]
    EMP_Dashboard --> EMP_Apps[View Applications<br/>Review CVs<br/>Shortlist/Reject<br/>Send Notifications]
    
    EMP_Apps --> Notify_Seeker[Send Email & Push<br/>to Job Seeker<br/>Status Update]
    Notify_Seeker --> JS_Status
    
    %% Admin Path
    UserSelect -->|Admin| ADMIN_Login[Admin Login]
    ADMIN_Login --> ADMIN_Dashboard[Admin Dashboard<br/>Full Platform Overview]
    
    ADMIN_Dashboard --> ADMIN_Toggle[Job Approval Toggle<br/>Enable/Disable<br/>Auto-Approval]
    ADMIN_Toggle -.->|Controls| Auto_Approve
    
    ADMIN_Dashboard --> ADMIN_Coins[Coin Management<br/>Send Coins to Company<br/>Send to All Companies<br/>Welcome Bonus Toggle]
    ADMIN_Coins -.->|Assigns| EMP_Welcome
    
    ADMIN_Dashboard --> ADMIN_Users[User Management<br/>Employers & Job Seekers<br/>Verify/Suspend/Delete]
    ADMIN_Dashboard --> ADMIN_Jobs[Job Ad Management<br/>Monitor/Moderate<br/>Remove/Edit Posts]
    ADMIN_Dashboard --> ADMIN_Courses[Course Ad Management<br/>Approve/Deny<br/>Manage Course Listings]
    
    ADMIN_Dashboard --> ADMIN_Adverts[Advertisement Management<br/>Review Submissions<br/>Approve/Deny]
    ADMIN_Adverts --> ADMIN_AdvertApprove{Approve Ad?}
    ADMIN_AdvertApprove -->|Yes| ADMIN_PayLink[Send Payment Link<br/>to Advertiser]
    ADMIN_AdvertApprove -->|No| ADMIN_AdvertReject[Reject & Notify]
    ADMIN_PayLink --> ADMIN_PlaceAd[Place Ad on Site<br/>For Specified Days]
    
    ADMIN_Dashboard --> ADMIN_Finance[Transactions/Billing<br/>Refunds/Coin History<br/>Reports]
    ADMIN_Dashboard --> ADMIN_Push[Push Notification<br/>Creator & Sender]
    ADMIN_Dashboard --> ADMIN_Support[Support/Ticket<br/>Handling System]
    ADMIN_Dashboard --> ADMIN_Content[Content Management<br/>FAQ/Tutorials<br/>Categories/Locations]
    ADMIN_Dashboard --> ADMIN_Reports[Analytics & Reports<br/>Platform Statistics<br/>User Activity]
    
    %% Services
    JS_Apply -.->|Email/Push| Notification[Notification Service]
    Job_Published -.->|Email/Push| Notification
    Notify_Seeker -.->|Email/Push| Notification
    ADMIN_Push -.->|Send| Notification
    
    EMP_Billing -.->|Process| Payment[Payment Gateway]
    ADMIN_PayLink -.->|Process| Payment
    
    %% Styling
    classDef publicStyle fill:#E8EAF6,stroke:#3F51B5,stroke-width:2px,color:#000
    classDef jobSeekerStyle fill:#E3F2FD,stroke:#1976D2,stroke-width:3px,color:#000
    classDef employerStyle fill:#FFF3E0,stroke:#F57C00,stroke-width:3px,color:#000
    classDef adminStyle fill:#F3E5F5,stroke:#7B1FA2,stroke-width:3px,color:#000
    classDef serviceStyle fill:#E8F5E9,stroke:#388E3C,stroke-width:2px,color:#000
    classDef decisionStyle fill:#FFF9C4,stroke:#F9A825,stroke-width:3px,color:#000
    classDef startStyle fill:#BBDEFB,stroke:#0D47A1,stroke-width:3px,color:#000
    
    class Start,Browse startStyle
    class Public_Jobs,Public_Courses,NeedApply publicStyle
    class JS_Register,JS_Dashboard,JS_Profile,JS_Search,JS_Saved,JS_Settings,JS_Help,JS_Apply,JS_Status jobSeekerStyle
    class EMP_Register,EMP_Welcome,EMP_Dashboard,EMP_Profile,EMP_Billing,EMP_Stats,EMP_Settings,EMP_Support,EMP_PostJob,EMP_Manage,EMP_Apps,EMP_Rejected,Notify_Seeker employerStyle
    class ADMIN_Login,ADMIN_Dashboard,ADMIN_Toggle,ADMIN_Coins,ADMIN_Users,ADMIN_Jobs,ADMIN_Courses,ADMIN_Adverts,ADMIN_AdvertApprove,ADMIN_PayLink,ADMIN_AdvertReject,ADMIN_PlaceAd,ADMIN_Finance,ADMIN_Push,ADMIN_Support,ADMIN_Content,ADMIN_Reports,ADMIN_JobReview,ADMIN_JobDecision adminStyle
    class Notification,Payment,Job_Published,Job_Live serviceStyle
    class UserSelect,NeedApply,Auto_Approve,ADMIN_JobDecision,ADMIN_AdvertApprove decisionStyle