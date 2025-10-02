<?php
require_once 'includes/auth.php';

// Require login for calendar
requireLogin();
$user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Calendar - DailyDo</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/style.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
  <script defer src="assets/script.js"></script>
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    .calendar-container {
      background: #F1F0E4;
      border-radius: 20px;
      padding: 2rem;
    }
    
    .calendar-header {
      background: white;
      border-radius: 15px;
      padding: 1.5rem;
      margin-bottom: 2rem;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .calendar-grid {
      background: white;
      border-radius: 15px;
      overflow: hidden;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .calendar-day-header {
      background: #896C6C;
      color: white;
      padding: 1rem;
      text-align: center;
      font-weight: 600;
      border-right: 1px solid rgba(255,255,255,0.2);
    }
    
    .calendar-day-header:last-child {
      border-right: none;
    }
    
    .calendar-day {
      min-height: 120px;
      border: 1px solid #e9ecef;
      padding: 0.5rem;
      position: relative;
      cursor: pointer;
      transition: background-color 0.2s ease;
    }
    
    .calendar-day:hover {
      background-color: #f8f9fa;
    }
    
    .calendar-day.other-month {
      background-color: #f8f9fa;
      color: #6c757d;
    }
    
    .calendar-day.today {
      background-color: #fff3cd;
      border-color: #ffc107;
    }
    
    .calendar-day-number {
      font-weight: 600;
      margin-bottom: 0.5rem;
    }
    
    .task-indicator {
      font-size: 0.75rem;
      padding: 0.2rem 0.4rem;
      border-radius: 8px;
      margin-bottom: 0.2rem;
      display: block;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      cursor: pointer;
    }
    
    .task-high {
      background-color: #dc3545;
      color: white;
    }
    
    .task-medium {
      background-color: #ffc107;
      color: #212529;
    }
    
    .task-low {
      background-color: #28a745;
      color: white;
    }
    
    .task-completed {
      background-color: #6c757d;
      color: white;
      text-decoration: line-through;
    }
    
    .nav-button {
      background: #896C6C;
      color: white;
      border: none;
      border-radius: 10px;
      padding: 0.5rem 1rem;
      font-weight: 600;
      transition: all 0.3s ease;
    }
    
    .nav-button:hover {
      background: #7a5d5d;
      color: white;
    }
    
    .month-year-display {
      color: #896C6C;
      font-weight: 700;
      font-size: 1.5rem;
    }
  </style>
</head>
<body>
  <!-- SIDEBAR OVERLAY -->
  <div class="sidebar-overlay" id="sidebarOverlay"></div>
  
  <!-- SIDEBAR -->
  <div class="sidebar" id="sidebar">
    <div class="sidebar-header">
      <h4 class="sidebar-brand">DailyDo</h4>
      <button class="sidebar-toggle d-lg-none" id="sidebarToggle">
        <i class="bi bi-x-lg"></i>
      </button>
    </div>
    <nav class="sidebar-nav">
      <ul class="nav flex-column">
        <li class="nav-item">
          <a class="nav-link" href="profile.php">
            <i class="bi bi-person-circle me-2"></i>
            Profile
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="dashboard.php">
            <i class="bi bi-speedometer2 me-2"></i>
            Dashboard
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="tasks.php">
            <i class="bi bi-list-task me-2"></i>
            Task List
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" href="calendar.php">
            <i class="bi bi-calendar-check me-2"></i>
            Calendar
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="admin.php">
            <i class="bi bi-people me-2"></i>
            User Management
          </a>
        </li>
      </ul>
    </nav>
    <div class="sidebar-footer">
      <a class="nav-link logout-link" href="#" id="logoutBtn">
        <i class="bi bi-box-arrow-right me-2"></i>
        Logout
      </a>
    </div>
  </div>

  <!-- MAIN CONTENT -->
  <div class="main-content">
    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top" style="box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
      <div class="container-fluid">
        <button class="navbar-toggler d-lg-none me-2" type="button" id="mobileMenuToggle">
          <i class="bi bi-list"></i>
        </button>
        <a class="navbar-brand fw-bold d-lg-none" href="calendar.php">DailyDo</a>
        <div class="ms-auto">
          <span class="navbar-text d-none d-lg-inline" id="currentDate">Loading...</span>
        </div>
      </div>
    </nav>

    <!-- TOAST NOTIFICATION -->
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1055;">
      <div id="successToast" class="toast align-items-center text-white border-0" role="alert" aria-live="assertive" aria-atomic="true" style="background: #896C6C;">
        <div class="d-flex">
          <div class="toast-body" id="toastMessage">
            Calendar loaded successfully!
          </div>
          <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
      </div>
    </div>

    <!-- CALENDAR SECTION -->
    <section class="calendar-section mt-4">
      <div class="container-fluid px-4 py-4">
        <!-- Header Section -->
        <div class="row mb-4">
          <div class="col-12">
            <h1 class="display-6 fw-bold mb-2"><i class="bi bi-calendar-check me-2" style="color: #896C6C;"></i>Task Calendar</h1>
            <p class="text-muted fs-5 mb-4">View your tasks organized by due dates</p>
          </div>
        </div>

        <!-- Calendar Container -->
        <div class="calendar-container">
          <!-- Calendar Header with Navigation -->
          <div class="calendar-header">
            <div class="row align-items-center">
              <div class="col-auto">
                <button class="nav-button" id="prevMonth">
                  <i class="bi bi-chevron-left"></i> Previous
                </button>
              </div>
              <div class="col text-center">
                <h2 class="month-year-display mb-0" id="monthYearDisplay">Loading...</h2>
              </div>
              <div class="col-auto">
                <button class="nav-button" id="nextMonth">
                  Next <i class="bi bi-chevron-right"></i>
                </button>
              </div>
            </div>
            <div class="row mt-3">
              <div class="col text-center">
                <button class="btn btn-outline-secondary btn-sm" id="todayBtn">
                  <i class="bi bi-calendar-day me-1"></i>Today
                </button>
              </div>
            </div>
          </div>

          <!-- Calendar Grid -->
          <div class="calendar-grid">
            <!-- Day Headers -->
            <div class="row g-0">
              <div class="col calendar-day-header">Sunday</div>
              <div class="col calendar-day-header">Monday</div>
              <div class="col calendar-day-header">Tuesday</div>
              <div class="col calendar-day-header">Wednesday</div>
              <div class="col calendar-day-header">Thursday</div>
              <div class="col calendar-day-header">Friday</div>
              <div class="col calendar-day-header">Saturday</div>
            </div>
            
            <!-- Calendar Days -->
            <div id="calendarDays">
              <!-- Days will be generated by JavaScript -->
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>

  <!-- Task Detail Modal -->
  <div class="modal fade" id="taskDetailModal" tabindex="-1" aria-labelledby="taskDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content" style="border-radius: 20px; border: none;">
        <div class="modal-header" style="background: #896C6C; color: white; border-radius: 20px 20px 0 0;">
          <h5 class="modal-title" id="taskDetailModalLabel">
            <i class="bi bi-calendar-event me-2"></i>Tasks for <span id="selectedDate"></span>
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" style="padding: 2rem;">
          <div id="taskDetailContent">
            <!-- Task details will be loaded here -->
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    let currentDate = new Date();
    let currentMonth = currentDate.getMonth();
    let currentYear = currentDate.getFullYear();
    let tasksData = {};

    const monthNames = [
      'January', 'February', 'March', 'April', 'May', 'June',
      'July', 'August', 'September', 'October', 'November', 'December'
    ];

    // Initialize calendar on page load
    document.addEventListener('DOMContentLoaded', async function() {
      await loadTasks();
      renderCalendar();
      
      // Event listeners
      document.getElementById('prevMonth').addEventListener('click', async () => {
        currentMonth--;
        if (currentMonth < 0) {
          currentMonth = 11;
          currentYear--;
        }
        await loadTasks();
        renderCalendar();
      });

      document.getElementById('nextMonth').addEventListener('click', async () => {
        currentMonth++;
        if (currentMonth > 11) {
          currentMonth = 0;
          currentYear++;
        }
        await loadTasks();
        renderCalendar();
      });

      document.getElementById('todayBtn').addEventListener('click', async () => {
        const today = new Date();
        currentMonth = today.getMonth();
        currentYear = today.getFullYear();
        await loadTasks();
        renderCalendar();
      });
    });

    // Load tasks from API
    async function loadTasks() {
      try {
        const response = await fetch('api/tasks.php');
        const data = await response.json();
        
        if (data.success) {
          // Group tasks by date
          tasksData = {};
          data.tasks.forEach(task => {
            if (task.deadline) {
              const taskDate = new Date(task.deadline);
              const dateKey = `${taskDate.getFullYear()}-${String(taskDate.getMonth() + 1).padStart(2, '0')}-${String(taskDate.getDate()).padStart(2, '0')}`;
              
              if (!tasksData[dateKey]) {
                tasksData[dateKey] = [];
              }
              tasksData[dateKey].push(task);
            }
          });
        }
      } catch (error) {
        console.error('Error loading tasks:', error);
      }
    }

    // Render calendar
    function renderCalendar() {
      const monthYearDisplay = document.getElementById('monthYearDisplay');
      monthYearDisplay.textContent = `${monthNames[currentMonth]} ${currentYear}`;

      const calendarDays = document.getElementById('calendarDays');
      calendarDays.innerHTML = '';

      // Get first day of month and number of days
      const firstDay = new Date(currentYear, currentMonth, 1);
      const lastDay = new Date(currentYear, currentMonth + 1, 0);
      const daysInMonth = lastDay.getDate();
      const startingDayOfWeek = firstDay.getDay();

      // Calculate previous month days to show
      const prevMonth = currentMonth === 0 ? 11 : currentMonth - 1;
      const prevYear = currentMonth === 0 ? currentYear - 1 : currentYear;
      const daysInPrevMonth = new Date(prevYear, prevMonth + 1, 0).getDate();

      let dayCount = 1;
      let nextMonthDayCount = 1;

      // Generate 6 weeks (42 days)
      for (let week = 0; week < 6; week++) {
        const weekRow = document.createElement('div');
        weekRow.className = 'row g-0';

        for (let day = 0; day < 7; day++) {
          const dayCell = document.createElement('div');
          dayCell.className = 'col calendar-day';

          const dayIndex = week * 7 + day;
          let dayNumber, isCurrentMonth = true, cellDate;

          if (dayIndex < startingDayOfWeek) {
            // Previous month days
            dayNumber = daysInPrevMonth - startingDayOfWeek + dayIndex + 1;
            dayCell.classList.add('other-month');
            isCurrentMonth = false;
            cellDate = new Date(prevYear, prevMonth, dayNumber);
          } else if (dayCount <= daysInMonth) {
            // Current month days
            dayNumber = dayCount;
            cellDate = new Date(currentYear, currentMonth, dayNumber);
            dayCount++;
          } else {
            // Next month days
            dayNumber = nextMonthDayCount;
            dayCell.classList.add('other-month');
            isCurrentMonth = false;
            const nextMonth = currentMonth === 11 ? 0 : currentMonth + 1;
            const nextYear = currentMonth === 11 ? currentYear + 1 : currentYear;
            cellDate = new Date(nextYear, nextMonth, dayNumber);
            nextMonthDayCount++;
          }

          // Check if it's today
          const today = new Date();
          if (cellDate.toDateString() === today.toDateString()) {
            dayCell.classList.add('today');
          }

          // Create day content
          const dayNumberDiv = document.createElement('div');
          dayNumberDiv.className = 'calendar-day-number';
          dayNumberDiv.textContent = dayNumber;
          dayCell.appendChild(dayNumberDiv);

          // Add tasks for this date
          const dateKey = `${cellDate.getFullYear()}-${String(cellDate.getMonth() + 1).padStart(2, '0')}-${String(cellDate.getDate()).padStart(2, '0')}`;
          if (tasksData[dateKey]) {
            const tasksToShow = tasksData[dateKey].slice(0, 3); // Show max 3 tasks
            tasksToShow.forEach(task => {
              const taskDiv = document.createElement('div');
              taskDiv.className = `task-indicator task-${task.priority}`;
              if (task.status === 'completed') {
                taskDiv.classList.add('task-completed');
              }
              taskDiv.textContent = task.title;
              taskDiv.title = task.title;
              dayCell.appendChild(taskDiv);
            });

            if (tasksData[dateKey].length > 3) {
              const moreDiv = document.createElement('div');
              moreDiv.className = 'task-indicator';
              moreDiv.style.background = '#6c757d';
              moreDiv.style.color = 'white';
              moreDiv.textContent = `+${tasksData[dateKey].length - 3} more`;
              dayCell.appendChild(moreDiv);
            }
          }

          // Add click event to show task details
          dayCell.addEventListener('click', () => showTaskDetails(cellDate, tasksData[dateKey] || []));

          weekRow.appendChild(dayCell);
        }

        calendarDays.appendChild(weekRow);
      }
    }

    // Show task details in modal
    function showTaskDetails(date, tasks) {
      const modal = new bootstrap.Modal(document.getElementById('taskDetailModal'));
      const selectedDateSpan = document.getElementById('selectedDate');
      const taskDetailContent = document.getElementById('taskDetailContent');

      selectedDateSpan.textContent = date.toLocaleDateString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      });

      if (tasks.length === 0) {
        taskDetailContent.innerHTML = `
          <div class="text-center py-4">
            <i class="bi bi-calendar-x" style="font-size: 3rem; color: #6c757d;"></i>
            <h5 class="mt-3 text-muted">No tasks scheduled</h5>
            <p class="text-muted">You have no tasks due on this date.</p>
          </div>
        `;
      } else {
        let tasksHtml = '';
        tasks.forEach(task => {
          const priorityColor = task.priority === 'high' ? '#dc3545' : 
                               task.priority === 'medium' ? '#ffc107' : '#28a745';
          const statusIcon = task.status === 'completed' ? 'bi-check-circle-fill' : 'bi-circle';
          const statusClass = task.status === 'completed' ? 'text-decoration-line-through text-muted' : '';

          tasksHtml += `
            <div class="card mb-3 border-0 shadow-sm">
              <div class="card-body">
                <div class="d-flex align-items-start">
                  <i class="bi ${statusIcon} me-3 mt-1" style="color: ${priorityColor}; font-size: 1.2rem;"></i>
                  <div class="flex-grow-1">
                    <h6 class="card-title mb-2 ${statusClass}">${task.title}</h6>
                    ${task.description ? `<p class="card-text text-muted small ${statusClass}">${task.description}</p>` : ''}
                    <div class="d-flex align-items-center gap-3">
                      <span class="badge" style="background: ${priorityColor};">${task.priority.toUpperCase()}</span>
                      <small class="text-muted">
                        <i class="bi bi-clock me-1"></i>
                        ${new Date(task.deadline).toLocaleTimeString('en-US', {hour: '2-digit', minute: '2-digit'})}
                      </small>
                      <small class="text-muted">
                        <i class="bi bi-flag me-1"></i>
                        ${task.status.charAt(0).toUpperCase() + task.status.slice(1)}
                      </small>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          `;
        });
        taskDetailContent.innerHTML = tasksHtml;
      }

      modal.show();
    }
  </script>
</body>
</html>