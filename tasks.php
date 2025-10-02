<?php
require_once 'includes/auth.php';

// Require login for tasks
requireLogin();
$user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Task List - DailyDo</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/style.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
  <script defer src="assets/script.js"></script>
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
          <a class="nav-link active" href="tasks.php">
            <i class="bi bi-list-task me-2"></i>
            Task List
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">
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
        <a class="navbar-brand fw-bold d-lg-none" href="tasks.php">DailyDo</a>
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
            Task updated successfully!
          </div>
          <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
      </div>
    </div>

    <!-- TASK LIST SECTION -->
    <section class="task-section mt-4">
      <div class="container-fluid px-4 py-4">
        <!-- Header Section -->
        <div class="row mb-4">
          <div class="col-12">
            <h1 class="display-6 fw-bold mb-2"><i class="bi bi-list-task me-2" style="color: #896C6C;"></i>Task Management</h1>
            <p class="text-muted fs-5 mb-4">Organize and track your tasks efficiently</p>
            <div class="d-flex justify-content-end">
               <button class="btn btn-lg fw-bold" data-bs-toggle="modal" data-bs-target="#addTaskModal" style="background: #896C6C; color: white; border: none; border-radius: 15px; padding: 12px 24px;">
                 <i class="bi bi-plus-circle me-2"></i>Add New Task
               </button>
             </div>
          </div>
        </div>

        <!-- Filter and Search Section -->
        <div class="card mb-4 border-0 shadow-sm" style="background: #F1F0E4; border-radius: 20px;">
          <div class="card-body p-4">
            <div class="d-flex align-items-center mb-3">
              <i class="bi bi-funnel fs-4 me-3" style="color: #896C6C;"></i>
              <h5 class="mb-0 fw-bold" style="color: #333;">Filter & Search</h5>
            </div>
            <div class="row g-3">
              <div class="col-12 col-lg-6">
                <div class="input-group">
                  <span class="input-group-text" style="background: white; border: 2px solid #DDDAD0; border-right: none;"><i class="bi bi-search" style="color: #896C6C;"></i></span>
                  <input type="text" class="form-control form-control-lg" id="searchTasks" placeholder="Search tasks..." style="border: 2px solid #DDDAD0; border-left: none; border-radius: 0 15px 15px 0;">
                </div>
              </div>
              <div class="col-12 col-lg-3">
                 <select class="form-select form-select-lg" id="filterPriority" style="border: 2px solid #DDDAD0; border-radius: 15px;">
                   <option value="">All Priorities</option>
                   <option value="high">High Priority</option>
                   <option value="medium">Medium Priority</option>
                   <option value="low">Low Priority</option>
                 </select>
               </div>
               <div class="col-12 col-lg-3">
                 <select class="form-select form-select-lg" id="filterStatus" style="border: 2px solid #DDDAD0; border-radius: 15px;">
                   <option value="">All Status</option>
                   <option value="pending">Pending</option>
                   <option value="completed">Completed</option>
                 </select>
               </div>
            </div>
          </div>
        </div>

        <!-- Tasks Container -->
        <div class="row">
          <div class="col-12">
            <div id="tasksContainer">
              <!-- Tasks will be dynamically loaded here -->
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- FOOTER -->
    <footer class="footer">
      <p>&copy; 2025 DailyDo. All rights reserved.</p>
    </footer>
  </div>

  <!-- ADD/EDIT TASK MODAL -->
  <div class="modal fade" id="addTaskModal" tabindex="-1" aria-labelledby="addTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content" style="border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
        <div class="modal-header" style="background: #F1F0E4; border-radius: 20px 20px 0 0; border-bottom: 2px solid #DDDAD0; padding: 1.5rem;">
          <div class="d-flex align-items-center">
            <i class="bi bi-plus-circle-fill me-3" style="color: #896C6C; font-size: 1.5rem;"></i>
            <h5 class="modal-title mb-0 fw-bold" id="addTaskModalLabel" style="color: #333;">Add New Task</h5>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="background-size: 1.2rem;"></button>
        </div>
        <div class="modal-body" style="padding: 2rem;">
          <form id="taskForm">
            <input type="hidden" id="taskId" value="">
            <div class="row g-4">
               <div class="col-12">
                 <label for="taskTitle" class="form-label fw-semibold d-flex align-items-center" style="color: #333; font-size: 1rem;">
                   <i class="bi bi-pencil-square me-2" style="color: #896C6C;"></i>Task Title *
                 </label>
                 <input type="text" class="form-control form-control-lg" id="taskTitle" required placeholder="Enter your task title..." style="border: 2px solid #DDDAD0; border-radius: 15px; padding: 12px 16px;">
               </div>
               <div class="col-12">
                 <label for="taskDescription" class="form-label fw-semibold d-flex align-items-center" style="color: #333; font-size: 1rem;">
                   <i class="bi bi-text-paragraph me-2" style="color: #896C6C;"></i>Description
                 </label>
                 <textarea class="form-control" id="taskDescription" rows="4" placeholder="Add task details..." style="border: 2px solid #DDDAD0; border-radius: 15px; padding: 12px 16px; resize: vertical;"></textarea>
               </div>
               <div class="col-12 col-md-6">
                 <label for="taskDeadline" class="form-label fw-semibold d-flex align-items-center" style="color: #333; font-size: 1rem;">
                   <i class="bi bi-calendar3 me-2" style="color: #896C6C;"></i>Deadline
                 </label>
                 <input type="datetime-local" class="form-control form-control-lg" id="taskDeadline" style="border: 2px solid #DDDAD0; border-radius: 15px; padding: 12px 16px;">
               </div>
               <div class="col-12 col-md-6">
                 <label for="taskPriority" class="form-label fw-semibold d-flex align-items-center" style="color: #333; font-size: 1rem;">
                   <i class="bi bi-flag me-2" style="color: #896C6C;"></i>Priority
                 </label>
                 <select class="form-select form-select-lg" id="taskPriority" style="border: 2px solid #DDDAD0; border-radius: 15px; padding: 12px 16px;">
                   <option value="low">Low Priority</option>
                   <option value="medium" selected>Medium Priority</option>
                   <option value="high">High Priority</option>
                 </select>
               </div>
               <div class="col-12">
                 <div class="form-check p-3" style="background: #F8F9FA; border-radius: 15px; border: 2px solid #E9ECEF;">
                   <input class="form-check-input" type="checkbox" id="taskReminder" style="transform: scale(1.2);">
                   <label class="form-check-label fw-semibold ms-2 d-flex align-items-center" for="taskReminder" style="color: #333;">
                     <i class="bi bi-bell me-2" style="color: #896C6C;"></i>Set Reminder
                   </label>
                 </div>
               </div>
               <div class="col-12" id="reminderSettings" style="display: none;">
                 <label for="reminderTime" class="form-label fw-semibold d-flex align-items-center" style="color: #333; font-size: 1rem;">
                   <i class="bi bi-clock me-2" style="color: #896C6C;"></i>Reminder Time
                 </label>
                 <select class="form-select form-select-lg" id="reminderTime" style="border: 2px solid #DDDAD0; border-radius: 15px; padding: 12px 16px;">
                   <option value="15">15 minutes before</option>
                   <option value="30">30 minutes before</option>
                   <option value="60">1 hour before</option>
                   <option value="1440">1 day before</option>
                 </select>
               </div>
             </div>
          </form>
        </div>
        <div class="modal-footer" style="border-top: 2px solid #DDDAD0; padding: 1.5rem; border-radius: 0 0 20px 20px;">
          <button type="button" class="btn btn-lg px-4" data-bs-dismiss="modal" style="background: #E9ECEF; color: #6c757d; border: 2px solid #DEE2E6; border-radius: 15px; font-weight: 600;">Cancel</button>
          <button type="button" class="btn btn-lg px-4" id="saveTaskBtn" style="background: #896C6C; color: white; border: 2px solid #896C6C; border-radius: 15px; font-weight: 600;">💾 Save Task</button>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Global variables
    let tasks = [];
    let editingTaskId = null;

    // Sidebar functionality
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const sidebarToggle = document.getElementById('sidebarToggle');
    
    // Mobile menu toggle
    if (mobileMenuToggle) {
      mobileMenuToggle.addEventListener('click', function() {
        sidebar.classList.add('show');
        sidebarOverlay.classList.add('show');
      });
    }
    
    // Sidebar close button
    if (sidebarToggle) {
      sidebarToggle.addEventListener('click', function() {
        sidebar.classList.remove('show');
        sidebarOverlay.classList.remove('show');
      });
    }
    
    // Overlay click to close sidebar
    if (sidebarOverlay) {
      sidebarOverlay.addEventListener('click', function() {
        sidebar.classList.remove('show');
        sidebarOverlay.classList.remove('show');
      });
    }
    
    // Close sidebar on window resize if desktop
    window.addEventListener('resize', function() {
      if (window.innerWidth >= 992) {
        sidebar.classList.remove('show');
        sidebarOverlay.classList.remove('show');
      }
    });

    // Initialize page
    function initializePage() {
      // Set current date
      const now = new Date();
      const options = { month: 'long', day: '2-digit', weekday: 'long' };
      const formattedDate = now.toLocaleDateString('en-US', options);
      const dateString = formattedDate.replace(/,/, ',').replace(/(\w+) (\d+), (\w+)/, '$1 $2, $3');
      document.getElementById('currentDate').textContent = dateString;
      
      // Load tasks
      loadTasks();
    }

    // Logout functionality
    document.getElementById('logoutBtn').addEventListener('click', function(e) {
      e.preventDefault();
      window.location.href = '/logout';
    });

    // Task reminder checkbox toggle
    document.getElementById('taskReminder').addEventListener('change', function() {
      const reminderSettings = document.getElementById('reminderSettings');
      if (this.checked) {
        reminderSettings.style.display = 'block';
      } else {
        reminderSettings.style.display = 'none';
      }
    });

    // Save task
    document.getElementById('saveTaskBtn').addEventListener('click', async function() {
      const title = document.getElementById('taskTitle').value.trim();
      const description = document.getElementById('taskDescription').value.trim();
      const deadline = document.getElementById('taskDeadline').value;
      const priority = document.getElementById('taskPriority').value;
      const reminder = document.getElementById('taskReminder').checked;
      const reminderTime = document.getElementById('reminderTime').value;
      const taskId = document.getElementById('taskId').value;

      if (!title) {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Please enter a task title.'
        });
        return;
      }

      const taskData = {
        title,
        description,
        deadline: deadline || null,
        priority,
        reminder,
        reminder_time: reminder ? parseInt(reminderTime) : 15,
        status: 'pending'
      };

      try {
        let response;
        if (taskId) {
          // Edit existing task
          taskData.id = taskId;
          response = await fetch('/api/tasks.php', {
            method: 'PUT',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify(taskData)
          });
        } else {
          // Add new task
          response = await fetch('/api/tasks.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify(taskData)
          });
        }

        const data = await response.json();

        if (!data.success) {
          throw new Error(data.error || 'Failed to save task');
        }

        showToast(taskId ? 'Task updated successfully!' : 'Task added successfully!');
        
        // Reset form and close modal
        resetForm();
        bootstrap.Modal.getInstance(document.getElementById('addTaskModal')).hide();
        
        // Reload tasks
        await loadTasks();
      } catch (error) {
        console.error('Error saving task:', error);
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: error.message
        });
      }
    });

    // Load and display tasks
    async function loadTasks() {
      const container = document.getElementById('tasksContainer');
      const searchTerm = document.getElementById('searchTasks').value.toLowerCase();
      const priorityFilter = document.getElementById('filterPriority').value;
      const statusFilter = document.getElementById('filterStatus').value;

      try {
        // Build query parameters
        const params = new URLSearchParams();
        if (searchTerm) params.append('search', searchTerm);
        if (priorityFilter) params.append('priority', priorityFilter);
        if (statusFilter) params.append('status', statusFilter);

        const response = await fetch(`/api/tasks.php?${params.toString()}`);
        const data = await response.json();

        if (!data.success) {
          throw new Error(data.error || 'Failed to load tasks');
        }

        tasks = data.tasks;

        if (tasks.length === 0) {
          container.innerHTML = `
            <div class="text-center py-5">
              <i class="bi bi-list-task" style="font-size: 4rem; color: #ccc;"></i>
              <h4 class="mt-3 text-muted">No tasks found</h4>
              <p class="text-muted">Add your first task to get started!</p>
            </div>
          `;
          return;
        }

        container.innerHTML = tasks.map(task => createTaskCard(task)).join('');
      } catch (error) {
        console.error('Error loading tasks:', error);
        container.innerHTML = `
          <div class="text-center py-5">
            <i class="bi bi-exclamation-triangle" style="font-size: 4rem; color: #dc3545;"></i>
            <h4 class="mt-3 text-danger">Error loading tasks</h4>
            <p class="text-muted">${error.message}</p>
          </div>
        `;
      }
    }

    // Create task card HTML
    function createTaskCard(task) {
      const priorityColors = {
        high: '#dc3545',
        medium: '#6c757d',
        low: '#198754'
      };
      
      const priorityIcons = {
        high: 'exclamation-triangle-fill',
        medium: 'dash-circle-fill',
        low: 'check-circle-fill'
      };
      
      const priorityColor = priorityColors[task.priority];
      const priorityIcon = priorityIcons[task.priority];
      const isOverdue = task.deadline && new Date(task.deadline) < new Date() && task.status === 'pending';
      const deadlineText = task.deadline ? new Date(task.deadline).toLocaleString() : 'No deadline';
      
      return `
        <div class="card mb-4 task-card ${task.status === 'completed' ? 'completed-task' : ''}" data-task-id="${task.id}" style="border: none; border-radius: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); background: ${task.status === 'completed' ? '#f8f9fa' : 'white'}; border-left: 5px solid ${priorityColor};">
          <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-start">
              <div class="d-flex align-items-start flex-grow-1">
                <div class="task-checkbox me-3 mt-1" onclick="toggleTaskStatus('${task.id}')" title="${task.status === 'completed' ? 'Mark as Pending' : 'Mark as Completed'}">
                  <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: ${task.status === 'completed' ? '#198754' : '#e9ecef'}; cursor: pointer; transition: all 0.3s ease;">
                    <i class="bi bi-${task.status === 'completed' ? 'check' : ''}" style="font-size: 1.1rem; color: ${task.status === 'completed' ? 'white' : '#6c757d'};"></i>
                  </div>
                </div>
                <div class="flex-grow-1">
                  <div class="d-flex align-items-center mb-3">
                    <h5 class="card-title mb-0 fw-bold ${task.status === 'completed' ? 'text-decoration-line-through text-muted' : ''}" style="color: ${task.status === 'completed' ? '#6c757d' : '#333'}; font-size: 1.25rem;">${task.title}</h5>
                    <span class="badge ms-3 px-3 py-2 d-flex align-items-center" style="background: ${priorityColor}; color: white; border-radius: 25px; font-size: 0.8rem; font-weight: 600;">
                      <i class="bi bi-${priorityIcon} me-2"></i>${task.priority.toUpperCase()}
                    </span>
                    ${task.reminder ? '<i class="bi bi-bell-fill ms-3" style="color: #896C6C; font-size: 1.1rem;" title="Reminder Set"></i>' : ''}
                    ${isOverdue ? '<span class="badge bg-danger ms-3 px-3 py-2 d-flex align-items-center" style="border-radius: 25px; animation: pulse 2s infinite;"><i class="bi bi-exclamation-triangle-fill me-2"></i>OVERDUE</span>' : ''}
                  </div>
                  ${task.description ? `<p class="card-text mb-3 ${task.status === 'completed' ? 'text-muted' : ''}" style="color: #666; line-height: 1.6; font-size: 1rem;">${task.description}</p>` : ''}
                  <div class="d-flex align-items-center">
                    <small class="text-muted d-flex align-items-center" style="font-size: 0.9rem;">
                      <i class="bi bi-calendar3 me-2" style="color: #896C6C;"></i>
                      <span>${deadlineText}</span>
                    </small>
                  </div>
                </div>
              </div>
              <div class="d-flex gap-2 ms-3">
                <button class="btn btn-sm" onclick="editTask('${task.id}')" title="Edit Task" style="background: #F1F0E4; color: #896C6C; border: 2px solid #DDDAD0; border-radius: 12px; padding: 8px 12px; transition: all 0.3s ease;">
                  <i class="bi bi-pencil-square"></i>
                </button>
                <button class="btn btn-sm" onclick="deleteTask('${task.id}')" title="Delete Task" style="background: #ffe6e6; color: #dc3545; border: 2px solid #ffcccc; border-radius: 12px; padding: 8px 12px; transition: all 0.3s ease;">
                  <i class="bi bi-trash3"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
      `;
    }

    // Toggle task status
    async function toggleTaskStatus(taskId) {
      const task = tasks.find(t => t.id == taskId);
      if (task) {
        const newStatus = task.status === 'completed' ? 'pending' : 'completed';
        
        try {
          const response = await fetch('/api/tasks.php', {
            method: 'PUT',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify({
              id: taskId,
              title: task.title,
              description: task.description,
              priority: task.priority,
              deadline: task.deadline,
              reminder: task.reminder,
              reminder_time: task.reminder_time,
              status: newStatus
            })
          });

          const data = await response.json();

          if (!data.success) {
            throw new Error(data.error || 'Failed to update task');
          }

          showToast(`Task marked as ${newStatus}!`);
          await loadTasks();
        } catch (error) {
          console.error('Error updating task status:', error);
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.message
          });
        }
      }
    }

    // Edit task
    function editTask(taskId) {
      const task = tasks.find(t => t.id == taskId);
      if (task) {
        // Populate form
        document.getElementById('taskId').value = task.id;
        document.getElementById('taskTitle').value = task.title;
        document.getElementById('taskDescription').value = task.description || '';
        
        // Format deadline for datetime-local input
        if (task.deadline) {
          const date = new Date(task.deadline);
          const formattedDate = date.toISOString().slice(0, 16);
          document.getElementById('taskDeadline').value = formattedDate;
        } else {
          document.getElementById('taskDeadline').value = '';
        }
        
        document.getElementById('taskPriority').value = task.priority;
        document.getElementById('taskReminder').checked = task.reminder || false;
        document.getElementById('reminderTime').value = task.reminder_time || '15';
        
        // Show/hide reminder settings
        const reminderSettings = document.getElementById('reminderSettings');
        reminderSettings.style.display = task.reminder ? 'block' : 'none';
        
        // Update modal title
        document.getElementById('addTaskModalLabel').textContent = 'Edit Task';
        
        // Show modal
        new bootstrap.Modal(document.getElementById('addTaskModal')).show();
      }
    }

    // Delete task
    function deleteTask(taskId) {
      Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!'
      }).then(async (result) => {
        if (result.isConfirmed) {
          try {
            const response = await fetch('/api/tasks.php', {
              method: 'DELETE',
              headers: {
                'Content-Type': 'application/json'
              },
              body: JSON.stringify({ id: taskId })
            });

            const data = await response.json();

            if (!data.success) {
              throw new Error(data.error || 'Failed to delete task');
            }

            showToast('Task deleted successfully!');
            await loadTasks();
          } catch (error) {
            console.error('Error deleting task:', error);
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: error.message
            });
          }
        }
      });
    }

    // Reset form
    function resetForm() {
      document.getElementById('taskForm').reset();
      document.getElementById('taskId').value = '';
      document.getElementById('reminderSettings').style.display = 'none';
      document.getElementById('addTaskModalLabel').textContent = 'Add New Task';
    }

    // Show toast notification
    function showToast(message) {
      document.getElementById('toastMessage').textContent = message;
      const toast = new bootstrap.Toast(document.getElementById('successToast'));
      toast.show();
    }

    // Search and filter event listeners
    document.getElementById('searchTasks').addEventListener('input', loadTasks);
    document.getElementById('filterPriority').addEventListener('change', loadTasks);
    document.getElementById('filterStatus').addEventListener('change', loadTasks);

    // Reset form when modal is hidden
    document.getElementById('addTaskModal').addEventListener('hidden.bs.modal', resetForm);

    // Initialize page on load
    document.addEventListener('DOMContentLoaded', initializePage);
  </script>
</body>
</html>