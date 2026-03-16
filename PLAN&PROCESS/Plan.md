# Agile Development Project Plan for Reading APP
## Group: TeamSpirit

**Core Division of Labor**: UI Team (2 members: Jianyuan Gui, Zijian Cao), Database Team (2 members: Wentong Yang, Yiru Li), Backend Team (4 members: Yize Xiao, Zhixin Zhu, Jiarui Zheng, Sihan Huang). The project implements the development of a reading APP based on the Scrum agile framework, focusing on the core closed loop of "Reading-Practice-Vocabulary Accumulation", adapting to the lightweight Laravel+MySQL technology stack, and delivering a runnable basic version in phases.

### Project Overview
This project focuses on the core development needs of a reading APP, centered on academic/general reading scenarios, and builds a lightweight reading practice application based on the Laravel+MySQL technology stack around core demands such as article browsing, exercise practice, and vocabulary learning. The project strictly follows agile development ideas, delivers product increments in phases according to function priorities (P0: Must-have, P1: Should-have), and ultimately delivers a runnable basic version of the reading APP to meet users' core needs for article reading, exercise practice, vocabulary accumulation, and learning data statistics.

## 1. Function List
Functions are divided into **P0 (Must-have, completed in Iteration 1)** and **P1 (Should-have, completed in Iteration 2)** according to priorities. All functions are adapted to the core usage scenarios of the reading APP, with no redundant modules, complying with the phased delivery requirements of agile iteration.

### 1.1 Core User-side Functions
#### 1.1.1 Login System + Personal Homepage Management
- P0: Account password login/guest mode, basic display of personal homepage (read articles, unfinished exercises, entrance to learning statistics)
- P1: Persistent login status, local caching of personal learning data, customizable display items on personal homepage (prioritize frequently used categories/favorite articles)

#### 1.1.2 Core Article Operation Module
- P0: Paged browsing of article lists, article reading page (clear display of title/text), simple article search (keyword matching), article classification and filtering (by difficulty/subject/browsed/read but unfinished exercises/unread)
- P1: Automatic saving of reading progress, article collection (add/cancel/view collection list), pop-up window for word definitions (integrate simple dictionary API, triggered by clicking new words)

#### 1.1.3 Exercise Practice Module
- P0: Display of article-associated exercises, exercise answering (multiple choice/fill-in-the-blank basic question types), instant feedback on answering results (correct/incorrect)
- P1: Automatic collection of wrong answers, association of exercise practice records with articles (ability to trace back to articles for reReading)

#### 1.1.4 Vocabulary Learning Module
- P0: None (core dependent on Iteration 1)
- P1: New word book function (add new words during reading, view new word list, delete new words), learning statistics (cumulative number of new words, number of read articles, number of completed exercises)

### 1.2 Technical Adaptation Requirements
1. Implementation of basic technology stack: The backend implements interfaces/business logic based on the Laravel framework, the database uses MySQL for data storage, the front-end pages are built with Blade templates, the styles are uniformly adapted based on Bootstrap, and a small amount of JavaScript is supplemented for core interaction scenarios;
2. Version control: Use Git+GitHub for code management throughout the process, submit versions by iteration phase, and keep clear commit logs;
3. Performance adaptation: Article list loading ≤ 3s, no lag in switching article reading pages, response time of search/filter functions ≤ 2s.

## 2. Requirements Analysis
### 2.1 User Requirement Analysis
- **Core Users**: Learners with reading practice + exercise training needs (e.g., students, exam candidates)
- **Core Demands**: Conveniently browse/retrieve reading materials, complete article-associated exercise practice, accumulate new words during reading, clearly track their own reading/learning progress, and avoid ineffective exercise brushing/reading;
- **Core Pain Points**: Existing reading tools lack a closed loop of "Reading-Practice-Vocabulary Accumulation", cumbersome search/filter functions, no personalized learning data statistics, and inability to adapt to fragmented reading practice scenarios;
- **Solution**: Focus on the core closed loop of "Reading-Practice", simplify the operation process, enhance the efficiency of article screening/search, supplement the new word book and learning statistics functions, and ensure the application is easy to operate and deploy based on a lightweight technology stack.

### 2.2 Functional Requirement Analysis
1. Complete MVP (Minimum Viable Product) in Iteration 1: Realize the complete core process of "Login/Guest Mode → Article Screening/Search → Article Reading → Exercise Practice → Result Feedback → Personal Homepage Viewing" to ensure the application can be used normally;
2. Complete optimization functions in Iteration 2: Supplement optimization functions such as vocabulary learning, reading progress saving, and collection to improve user experience, perfect learning data statistics, and form a complete closed loop of "Reading-Practice-Accumulation-Statistics";
3. Data accuracy: User reading records, practice records, and new word data must be persistently stored, support local caching in guest mode, and automatically synchronize after login.

### 2.3 Non-Functional Requirement Analysis
1. **Performance**: Page loading time ≤ 3s, no delay in exercise answering/new word adding operations, support loading 10+ article lists simultaneously without lag;
2. **Compatibility**: Front-end pages are adapted to mainstream browsers (Chrome/Edge/Firefox) and common screen resolutions (1920*1080/1366*768);
3. **Usability**: New users can complete the core operation of "finding articles → reading articles → doing exercises" in 3 steps or less, with a concise and intuitive interface and operation logic in line with common reading APP habits;
4. **Security**: Encrypted storage of user account information, no risk of data leakage during transmission, no malicious pop-ups/ads;
5. **Maintainability**: Modular code development (decoupling of login module, article module, exercise module, vocabulary module), clear database table structure design, facilitating subsequent function expansion.

## 3. Team Division of Labor
### 3.1 Core Role Allocation (Adapted to Small Team Agile Mode)
#### 3.1.1 UI Team (2 members: Jianyuan Gui, Zijian Cao)
- Core Responsibilities:
  1. Design core page prototypes of the APP (login page, personal homepage, article list page, reading page, exercise practice page, new word book page);
  2. Output standardized UI design drafts, strictly adapt to Bootstrap style specifications, and ensure responsive page adaptation to different resolutions;
  3. Define page interaction logic (e.g., pop-up window for word definitions triggered by clicking new words, interactive feedback for automatic saving of reading progress);
- Collaboration Requirements:
  1. Synchronize page design details with the backend team and provide cut images/style specification documents;
  2. Participate in functional experience testing and optimize page display and interaction details.

#### 3.1.2 Database Team (2 members: Wentong Yang, Yiru Li)
- Core Responsibilities:
  1. Design and implement database table structures (user table, article table, exercise table, new word book table, collection table, reading progress table, practice record table, etc.);
  2. Write data initialization scripts and populate basic test article/exercise data;
  3. Optimize database query statements (e.g., SQL performance of article screening/search) to ensure data storage/reading efficiency;
  4. Formulate data synchronization rules (local caching in guest mode → cloud synchronization after login);
- Collaboration Requirements:
  1. Synchronize table structure design with the backend team and provide data operation specification documents;
  2. Participate in functional testing to verify the accuracy of data storage, query, and synchronization.

#### 3.1.3 Backend Team (4 members: Yize Xiao, Zhixin Zhu, Jiarui Zheng, Sihan Huang)
- Subdivision 1: Login System + Personal Homepage Management
  - Core Responsibilities:
    1. Implement the core logic of account password login/guest mode based on Laravel;
    2. Develop the personal homepage data display function (connect to the database to obtain user reading/practice data);
    3. Implement local caching of user data, persistent login status, and data synchronization logic;
    4. Write corresponding interfaces and Blade front-end pages;
  - Collaboration Requirements:
    1. Confirm user table structure and data synchronization rules with the database team;
    2. Connect with the UI team to restore the personal homepage page.
- Subdivision 2: Core Function Development
  - Core Responsibilities:
    1. Article Module: Implement article list/reading, search/filter, reading progress saving, and article collection functions;
    2. Exercise Module: Implement exercise display, answering, feedback, and wrong answer collection functions;
    3. Vocabulary Module: Integrate dictionary API to implement pop-up window for word definitions, develop new word book and learning statistics functions;
  - Collaboration Requirements:
    1. Confirm the design of article/exercise/new word table structures with the database team;
    2. Connect with the UI team to implement article reading, exercise practice, new word book and other pages;
    3. Conduct cross-module joint debugging to ensure the closed loop of the "Reading-Practice-Vocabulary Accumulation" process.

### 3.2 General Requirements (All Members)
1. Participate in daily agile stand-up meetings (10 minutes/day recommended), synchronize task progress, today's plan, and blocking issues;
2. Submit code in a standardized manner using Git+GitHub, with commit logs following the format of "Module + Function + Modification Description" (e.g., Login Module - Fix abnormal data caching in guest mode);
3. Participate in functional testing, prioritize verifying self-responsible modules, and cooperate with cross-module joint debugging;
4. Familiarize with the project's basic technology stack (Laravel/MySQL/Blade/Bootstrap) and take the initiative to undertake cross-role support work.

## 4. Development Schedule (Agile Iteration Mode)
The project is promoted in "2 rounds of core iterations + 1 round of acceptance testing & optimization" with a total cycle of 4 weeks recommended. Each iteration focuses on priority functions and delivers runnable product increments.

### 4.1 Iteration 1 (Weeks 1-2: P0 Core Function Development)
- Core Goal: Complete the development of all P0 functions, realize the core process of "Login → Article Screening/Reading → Exercise Practice → Personal Homepage Viewing", and ensure the application can run normally;
- Core Deliverables:
  1. Runnable version of P0 functions (deployed based on Laravel+MySQL);
  2. Database table structure design document, core function interface document;
  3. GitHub code repository (including complete Iteration 1 code);
- Core Tasks of Each Role:
  1. UI Team: Output design drafts of P0 core pages (login page, article list page, reading page, exercise practice page, personal homepage);
  2. Database Team: Complete the design and initialization scripts of P0-related table structures (user table, article table, exercise table);
  3. Backend Team:
     - Login/Personal Homepage: Complete login/guest mode and basic display functions of personal homepage;
     - Core Functions: Complete article list/reading, search/filter, and exercise display/answering/feedback functions.

### 4.2 Iteration 2 (Week 3: P1 Optimization Function Development)
- Core Goal: Complete the development of all P1 functions, supplement optimization functions such as vocabulary learning, reading progress, collection, and learning statistics, and improve user experience;
- Core Deliverables:
  1. Integrated version of P1 functions;
  2. Functional optimization test report, code review records;
  3. Iteration 2 code update (GitHub);
- Core Tasks of Each Role:
  1. UI Team: Output design drafts of P1 function pages (new word book, collection list, learning statistics page);
  2. Database Team: Supplement table structures related to new word book/collection/reading progress/practice records and optimize query statements;
  3. Backend Team:
     - Login/Personal Homepage: Complete persistent login status and user data synchronization functions;
     - Core Functions: Complete pop-up window for word definitions, new word book, reading progress saving, article collection, and learning statistics functions.

### 4.3 Acceptance Testing & Optimization (Week 4)
- Core Goal: Conduct full-process functional testing, bug fixing, experience polishing, and deliver the final runnable version;
- Core Deliverables:
  1. Final runnable basic version of the reading APP;
  2. Complete project documents (requirements document, design document, test report, user guide);
  3. Final GitHub code repository (including all iteration codes and documents);
- Core Tasks of Each Role:
  1. UI Team: Verify all page display and interaction effects, optimize detailed experience;
  2. Database Team: Verify the accuracy of full-scale data storage/query/synchronization and optimize performance;
  3. Backend Team: Complete full-scale bug fixing, final code review, and deploy the runnable version;
  4. Whole Team: Organize project documents and complete the final version delivery.
