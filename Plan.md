# Scrum Agile Development Project Plan for Academic English Learning Windows Web Application
## Group TeamSpirit

### Project Overview
This project is a practical assignment for the Agile Software Engineering course, aiming to develop a **Windows web-exclusive academic English learning application** for English learners. Focusing on the four core competencies of academic English—vocabulary, listening, reading and writing—it creates an interactive and fragmented learning experience with support for personalized learning path planning, learning data recording and progress analysis. Adhering strictly to the Scrum agile framework, the project plans sprint iterations in line with the 6-week course development cycle. The 7-member team divides and rotates roles according to Scrum guidelines, and will ultimately deliver a runnable finished Demo of the Windows web application along with a complete set of project documents, to meet English learners' demands for academic English improvement.

---

## 1. Function List
Functions are prioritized into **P0 (core mandatory, to be completed in Sprint 1)**, **P1 (important optimization, to be completed in Sprint 2)** and **P2 (expansion optional, to be advanced as needed in the acceptance phase)**. All functions are tailored to Windows web operation habits, adapted for large-screen display and keyboard-mouse interaction, with no redundant modules, complying with the phased delivery requirements of agile iteration.

### 1.1 Core User-side Functions
#### 1.1.1 Account and Learning Profile Management
- P0: Email + password registration/login, guest mode, basic personal learning profile functions (recording completed exercises, answer accuracy rate, cumulative learning duration)
- P1: Learning profile filtering/query, persistent account status, basic local + cloud synchronization of learning data
- P2: Multi-account quick login, learning profile data export

#### 1.1.2 Core Academic English Practice Module
- P0: Flashcards/matching/blank-filling for high-frequency academic words, blank-filling/rewriting for academic writing sentence patterns, short dialogues/lecture clips for academic listening with multiple-choice questions, basic academic reading passages with detail comprehension questions
- P1: Classified retrieval of practice question banks, automatic collection of wrong answers, speed/repeat playback of listening audio (web-adapted)
- P2: Main idea generalization/inference questions for academic reading, imitation practice of academic writing sentence patterns, expansion of word roots, affixes, synonyms and antonyms

#### 1.1.3 Practice Experience Optimization
- P0: Instant single-question feedback (correct/incorrect + brief explanation), post-practice summary (accuracy rate, time spent, number of wrong answers), real-time display of practice progress bar, basic badge incentives (unlocked by completing specified exercises)
- P1: Web-adapted shortcut keys for answering (Enter to submit/ Space to play audio), eye protection mode for practice pages, real-time marking of wrong answers
- P2: Practice collection, custom practice groups, query of practice history records

#### 1.1.4 Personalized Learning Path
- P0: Initial academic English proficiency assessment (vocabulary + writing + listening), daily practice package recommendation based on assessment results, one-click access to learning via practice packages
- P1: Intelligent adjustment of daily practice packages (difficulty optimized according to practice accuracy), visual display of learning paths (large-screen charts for web)
- P2: Precise push of weak knowledge points, personalized learning goal setting (daily/weekly practice volume)

#### 1.1.5 Basic Tool Functions
- P0: Chinese-English switching for Windows web, adaptive practice pages for different resolutions, local caching of learning data
- P1: Quick query of academic vocabulary, practice settings (audio auto-play/feedback pop-up switch)
- P2: Web offline practice (question bank caching), learning reminders (browser desktop notifications)

### 1.2 Enhanced Experience Functions
1. Learning data visualization: Weekly/monthly learning statistics charts (trends of practice times and accuracy rate), tags for weak knowledge points (e.g., academic writing conjunctions/listening digital information), learning efficiency analysis
2. Light social interaction and sharing: Sharing of practice achievements/badges to social platforms, sharing of learning check-in records (web-generated sharing links/images)
3. Web-exclusive optimization: Customizable shortcut keys, personalized layout of practice pages, special review of wrong answer notebooks

---

## 2. Requirements Analysis
### 2.1 User Requirement Analysis
- **Core Users**: Academic English learners preparing for degree courses
- **Core Demands**: Efficiently solidify academic English foundations before the start of degree courses, adapt to fragmented learning scenarios (spare time, study breaks), need lightweight and interactive practice forms, and expect to track learning progress clearly and obtain targeted learning guidance to avoid blind practice.
- **Core Pain Points**: Existing academic English learning resources are mostly paper-based or general-purpose software, lacking specialized practice for target users. There are few lightweight products adapted for the Windows web, with cumbersome operations that fail to meet fragmented and personalized learning needs.
- **Solution**: Focus on users' academic English preview needs, build a Windows web-exclusive lightweight product, simplify the operation process, highlight specialized practice for the four core competencies, realize personalized learning path planning through initial assessment, and adapt to the characteristics of web with keyboard-mouse operation and large-screen display to meet fragmented learning needs.

### 2.2 Functional Requirement Analysis
1. Complete the MVP (Minimum Viable Product) in Sprint 1: Realize the complete core process of "registration/guest login → proficiency assessment → practice selection → answering → feedback viewing → learning profile recording" to ensure the normal use of the Windows web application;
2. Function adaptation to Windows web operation habits: Support answering via mouse click and keyboard shortcut keys, smooth page jump, and adaptive audio/practice interface for large-screen display;
3. Data accuracy and synchronization: Learning data and practice records adopt the mode of **web local caching + cloud synchronization**, ensuring no data loss in guest mode and automatic synchronization after login;
4. Practice professionalism: The academic English question bank is tailored to users' preview requirements, with vocabulary, sentence patterns, listening/reading materials adapted to the introductory level of academic English and concise and easy-to-understand explanations.

### 2.3 Non-Functional Requirement Analysis
1. **Performance**: Page loading time of the Windows web application ≤ 5s, no lag in practice page switching, listening audio loading time ≤ 3s, adapted for opening multiple tabs simultaneously;
2. **Compatibility**: Support mainstream Windows browsers (Chrome 90+, Edge 90+, Firefox 90+, 360 Browser), no interface disorder or function failure, adapted for common resolutions such as 1920*1080, 1366*768 and 2560*1440;
3. **Usability**: Zero learning cost for new users, access to practice in 3 steps or less, concise and intuitive interface, and operation logic in line with Windows web users' habits;
4. **Security**: Encrypted storage of user account information and learning data, no risk of data leakage during transmission, and no malicious pop-ups/ads on the web;
5. **Maintainability**: Clear code structure, modular development, and independent updatability of question banks and practice modules, facilitating subsequent iterative optimization based on user feedback.

---

## 3. Team Division of Labor
### 3.1 Scrum Core Role Allocation
The Scrum framework defines only 3 official roles. In the 7-member team, 1 person acts as the Product Owner (PO), 1 as the Scrum Master (SM), and 5 as the Development Team (DT). **Role rotation rule**: Roles are rotated at the end of each sprint, and each team member serves as PO/SM at least once.

#### 3.1.1 Product Owner (PO)
- Number of People: 1
- Initial Responsibilities in Sprint 0: Sort out freshmen's academic English learning needs, formulate the Product Backlog, divide function priorities, host sprint reviews, collect customer/lecturer feedback and adjust requirements
- Core Position: Decision-maker of product value, bridge of demands

#### 3.1.2 Scrum Master (SM)
- Number of People: 1
- Initial Responsibilities in Sprint 0: Host all Scrum meetings, maintain the Sprint Backlog, track task progress, remove development obstacles, synchronize project progress with lecturers, supervise Windows web adaptation requirements
- Core Position: Guardian of processes, servant leader of the team

#### 3.1.3 Development Team (DT)
- Number of People: 5
- Initial Responsibilities in Sprint 0: Complete all development work of the Windows web application (UI design/front-end development/back-end development/testing/documentation), claim tasks independently, and collaborate to deliver product increments
- Core Position: Cross-functional, self-organizing delivery team

### 3.2 Functional Division of Labor in the Development Team
The Development Team is a cross-functional and self-organizing team with clear core responsibilities assigned according to the specified names for the project. All members participate in multiple tasks to avoid single-function division, adapting to the development rhythm of the Windows web application. All work adheres to the core requirements of large-screen design, keyboard-mouse interaction and browser compatibility.

#### 3.2.1 UI Design
- Person in Charge: Jianyuan Gui, Zhixin Zhu
- Core Work Content (Windows Web Exclusive): Design Windows web-exclusive interfaces, complete interaction process design/UI sketches/standardized design drafts, adapt to large-screen display of different resolutions, and cooperate with development to restore interfaces
- Cross-Collaboration Requirements: Participate in demand sorting, function testing and web experience optimization

#### 3.2.2 Account & Learning Profile Development
- Person in Charge: Wentong Yang
- Core Work Content (Windows Web Exclusive): Implement email + password registration/login, guest mode and personal learning profile functions, develop local caching/cloud synchronization of data, and adapt to persistent account status on Windows web
- Cross-Collaboration Requirements: Participate in front-end joint debugging, function testing and document writing of the user data module

#### 3.2.3 Core Practice Module Development
- Person in Charge: Jiarui Zheng, Yize Xiao
- Core Work Content (Windows Web Exclusive): Develop academic English vocabulary/writing/listening/reading practice modules, implement web-based answering interaction, audio playback and question bank loading, and adapt to keyboard-mouse operation/shortcut keys
- Cross-Collaboration Requirements: Participate in question bank/vocabulary sorting, code review and practice module testing

#### 3.2.4 Practice Experience Development
- Person in Charge: Yiru Li
- Core Work Content (Windows Web Exclusive): Implement instant single-question feedback, practice summary, progress bar/badge incentives, develop web-adapted answering shortcut keys and eye protection mode, and optimize practice interaction experience
- Cross-Collaboration Requirements: Participate in function testing, bug fixing and document writing of practice experience

#### 3.2.5 Personalized Learning Path Development
- Person in Charge: Zijian Cao
- Core Work Content (Windows Web Exclusive): Develop initial proficiency assessment, daily practice package recommendation and learning path visualization functions, adapt to large-screen chart display on Windows web, and implement intelligent adjustment of practice packages
- Cross-Collaboration Requirements: Participate in front-end joint debugging, function testing and document writing of the learning path module

#### 3.2.6 General Requirements (All Members)
- Core Work Content (Windows Web Exclusive): Participate in question bank/vocabulary sorting and proofreading, code review, pair programming and daily scrum reporting, and take the initiative to solve Windows web development bottlenecks (browser compatibility/resolution adaptation)
- Cross-Collaboration Requirements: Master at least 1 core skill, provide cross-position support, and be familiar with basic web development

---

## 4. Development Time Estimation
The project is a 6-week Scrum iteration, including **Sprint 0 (Design & Planning, 1 week)**, **Sprint 1 (Prototype Development, 1 week)**, **Feedback & Iteration (1 week)**, **Sprint 2 (Personalized Optimization, 1 week)** and **Acceptance Testing & Polishing (2 weeks)**. All Scrum meetings are held during course time, and core development tasks are completed after class (3-4 hours per person per day). Development is carried out in phases according to the P0→P1→P2 function priority, with a runnable product increment of the Windows web application delivered in each sprint. Meanwhile, the artifact updates, meeting minutes and submission tasks required by the course are completed as scheduled.

### 4.1 Overall Sprint Plan
#### 4.1.1 Sprint 0 (Week 2)
- Core Development Goals (Windows Web Exclusive): Complete demand refinement, Windows web-exclusive design and task breakdown, formulate risk response plans, and build the basic project framework
- Core Deliverables: Product Backlog, Sprint Backlog, UI design drafts, task breakdown sheet, risk response plan, GitHub repository initialization
- Course Assessment Focuses: Product Backlog construction, sprint planning meeting, GitHub initialization
- Core Tasks of the Development Team: Demand sorting, UI design, task breakdown, repository construction

#### 4.1.2 Sprint 1 (Week 3)
- Core Development Goals (Windows Web Exclusive): Complete P0 core function development, realize the complete practice process, and finish basic integration and joint debugging of the Windows web application
- Core Deliverables: Runable prototype of P0 functions, module docking documents, end-to-end test report, initial bug test list, updated Backlog
- Course Assessment Focuses: Daily scrum, agile method application, core function development
- Core Tasks of the Development Team: P0 function development of each module, basic web integration, initial testing

#### 4.1.3 Customer Feedback & Iteration (Week 4)
- Core Development Goals (Windows Web Exclusive): Demonstrate the core prototype, collect feedback, adjust requirements, rearrange tasks, and plan the development goals of Sprint 2
- Core Deliverables: Customer feedback records, updated Product Backlog, Sprint 2 development plan, demand adjustment documents
- Course Assessment Focuses: Sprint review meeting, team collaboration, demand iteration
- Core Tasks of the Development Team: Prototype demonstration, feedback collection, demand adjustment, task rearrangement

#### 4.1.4 Sprint 2 (Week 5)
- Core Development Goals (Windows Web Exclusive): Complete P1 important optimization function development, realize the full functionality of personalized learning paths, and optimize the Windows web experience
- Core Deliverables: P1 function version, personalized learning path module, optimized web experience version, code review records, bug fixing records
- Course Assessment Focuses: Sprint retrospective meeting, real-time artifact update, function optimization
- Core Tasks of the Development Team: Personalized path development, experience optimization, bug fixing, code review

#### 4.1.5 Acceptance Testing & Polishing (Week 6)
- Core Development Goals (Windows Web Exclusive): Complete P2 expansion functions (optional), conduct comprehensive testing of the Windows web application, fix bugs, polish the experience, write documents, and deliver the finished Demo
- Core Deliverables: Finished Demo (Windows web application), complete project documents, user guide, test report, final GitHub version, personal contribution document materials
- Course Assessment Focuses: Final sprint review, project summary, personal contribution documents, final product delivery
- Core Tasks of the Development Team: Comprehensive testing, bug fixing, experience polishing, document writing, finished product demonstration