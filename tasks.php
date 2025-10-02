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
    <section class="task-section py-4">
      <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-4">
          <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
              <h1 class="fw-bold" style="color: #333;">Task List</h1>
              <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTaskModal" style="background: #896C6C; border: none;">
                <i class="bi bi-plus-circle me-2"></i>Add New Task
              </button>
            </div>
          </div>
        </div>

        <!-- Filter and Search -->
        <div class="row mb-4">
          <div class="col-12 col-md-6">
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-search"></i></span>
              <input type="text" class="form-control" id="searchTasks" placeholder="Search tasks...">
            </div>
          </div>
          <div class="col-12 col-md-6">
            <div class="d-flex gap-2">
              <select class="form-select" id="filterPriority">
                <option value="">All Priorities</option>
                <option value="high">High</option>
                <option value="medium">Medium</option>
                <option value="low">Low</option>
              </select>
              <select class="form-select" id="filterStatus">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="completed">Completed</option>
              </select>
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
      <div class="modal-content">
        <div class="modal-header" style="background: #F1F0E4;">
          <h5 class="modal-title" id="addTaskModalLabel">Add New Task</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="taskForm">
            <input type="hidden" id="taskId" value="">
            <div class="row">
              <div class="col-12 mb-3">
                <label for="taskTitle" class="form-label">Title *</label>
                <input type="text" class="form-control" id="taskTitle" required>
              </div>
              <div class="col-12 mb-3">
                <label for="taskDescription" class="form-label">Description</label>
                <textarea class="form-control" id="taskDescription" rows="3"></textarea>
              </div>
              <div class="col-12 col-md-6 mb-3">
                <label for="taskDeadline" class="form-label">Deadline</label>
                <input type="datetime-local" class="form-control" id="taskDeadline">
              </div>
              <div class="col-12 col-md-6 mb-3">
                <label for="taskPriority" class="form-label">Priority</label>
                <select class="form-select" id="taskPriority">
                  <option value="low">Low</option>
                  <option value="medium" selected>Medium</option>
                  <option value="high">High</option>
                </select>
              </div>
              <div class="col-12 mb-3">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="taskReminder">
                  <label class="form-check-label" for="taskReminder">
                    Set Reminder
                  </label>
                </div>
              </div>
              <div class="col-12" id="reminderSettings" style="display: none;">
                <label for="reminderTime" class="form-label">Reminder Time</label>
                <select class="form-select" id="reminderTime">
                  <option value="15">15 minutes before</option>
                  <option value="30">30 minutes before</option>
                  <option value="60">1 hour before</option>
                  <option value="1440">1 day before</option>
                </select>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" id="saveTaskBtn" style="background: #896C6C; border: none;">Save Task</button>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Global variables
    let tasks = JSON.parse(localStorage.getItem('tasks')) || [];
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
    document.getElementById('saveTaskBtn').addEventListener('click', function() {
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

      const task = {
        id: taskId || Date.now().toString(),
        title,
        description,
        deadline,
        priority,
        reminder,
        reminderTime: reminder ? reminderTime : null,
        status: 'pending',
        createdAt: taskId ? tasks.find(t => t.id === taskId).createdAt : new Date().toISOString(),
        updatedAt: new Date().toISOString()
      };

      if (taskId) {
        // Edit existing task
        const index = tasks.findIndex(t => t.id === taskId);
        tasks[index] = task;
        showToast('Task updated successfully!');
      } else {
        // Add new task
        tasks.push(task);
        showToast('Task added successfully!');
      }

      // Save to localStorage
      localStorage.setItem('tasks', JSON.stringify(tasks));
      
      // Reset form and close modal
      resetForm();
      bootstrap.Modal.getInstance(document.getElementById('addTaskModal')).hide();
      
      // Reload tasks
      loadTasks();
    });

    // Load and display tasks
    function loadTasks() {
      const container = document.getElementById('tasksContainer');
      const searchTerm = document.getElementById('searchTasks').value.toLowerCase();
      const priorityFilter = document.getElementById('filterPriority').value;
      const statusFilter = document.getElementById('filterStatus').value;

      // Filter tasks
      let filteredTasks = tasks.filter(task => {
        const matchesSearch = task.title.toLowerCase().includes(searchTerm) || 
                            task.description.toLowerCase().includes(searchTerm);
        const matchesPriority = !priorityFilter || task.priority === priorityFilter;
        const matchesStatus = !statusFilter || task.status === statusFilter;
        
        return matchesSearch && matchesPriority && matchesStatus;
      });

      // Sort by deadline and priority
      filteredTasks.sort((a, b) => {
        if (a.status !== b.status) {
          return a.status === 'pending' ? -1 : 1;
        }
        if (a.deadline && b.deadline) {
          return new Date(a.deadline) - new Date(b.deadline);
        }
        const priorityOrder = { high: 3, medium: 2, low: 1 };
        return priorityOrder[b.priority] - priorityOrder[a.priority];
      });

      if (filteredTasks.length === 0) {
        container.innerHTML = `
          <div class="text-center py-5">
            <i class="bi bi-list-task" style="font-size: 4rem; color: #ccc;"></i>
            <h4 class="mt-3 text-muted">No tasks found</h4>
            <p class="text-muted">Add your first task to get started!</p>
          </div>
        `;
        return;
      }

      container.innerHTML = filteredTasks.map(task => createTaskCard(task)).join('');
    }

    // Create task card HTML
    function createTaskCard(task) {
      const priorityColors = {
        high: 'danger',
        medium: 'warning',
        low: 'success'
      };
      
      const priorityColor = priorityColors[task.priority];
      const isOverdue = task.deadline && new Date(task.deadline) < new Date() && task.status === 'pending';
      const deadlineText = task.deadline ? new Date(task.deadline).toLocaleString() : 'No deadline';
      
      return `
        <div class="card mb-3 task-card ${task.status === 'completed' ? 'completed-task' : ''}" data-task-id="${task.id}">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
              <div class="d-flex align-items-start">
                <div class="task-checkbox me-3 mt-1" onclick="toggleTaskStatus('${task.id}')" title="${task.status === 'completed' ? 'Mark as Pending' : 'Mark as Completed'}">
                  <i class="bi bi-${task.status === 'completed' ? 'check-circle-fill' : 'circle'}" style="font-size: 1.2rem; color: ${task.status === 'completed' ? '#198754' : '#6c757d'}; cursor: pointer;"></i>
                </div>
                <div class="flex-grow-1">
                  <div class="d-flex align-items-center mb-2">
                    <h5 class="card-title mb-0 ${task.status === 'completed' ? 'text-decoration-line-through text-muted' : ''}">${task.title}</h5>
                    <span class="badge bg-${priorityColor} ms-2">${task.priority.toUpperCase()}</span>
                    ${task.reminder ? '<i class="bi bi-bell-fill ms-2" style="color: #896C6C;"></i>' : ''}
                    ${isOverdue ? '<span class="badge bg-danger ms-2">OVERDUE</span>' : ''}
                  </div>
                ${task.description ? `<p class="card-text ${task.status === 'completed' ? 'text-muted' : ''}">${task.description}</p>` : ''}
                <small class="text-muted">
                  <i class="bi bi-calendar me-1"></i>${deadlineText}
                </small>
              </div>
              <div class="d-flex gap-2">
                <button class="btn btn-sm btn-outline-primary" onclick="editTask('${task.id}')" title="Edit Task">
                  <i class="bi bi-pencil"></i>
                </button>
                <button class="btn btn-sm btn-outline-danger" onclick="deleteTask('${task.id}')" title="Delete Task">
                  <i class="bi bi-trash"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
      `;
    }

    // Toggle task status
    function toggleTaskStatus(taskId) {
      const task = tasks.find(t => t.id === taskId);
      if (task) {
        task.status = task.status === 'completed' ? 'pending' : 'completed';
        task.updatedAt = new Date().toISOString();
        localStorage.setItem('tasks', JSON.stringify(tasks));
        showToast(`Task marked as ${task.status}!`);
        loadTasks();
      }
    }

    // Edit task
    function editTask(taskId) {
      const task = tasks.find(t => t.id === taskId);
      if (task) {
        // Populate form
        document.getElementById('taskId').value = task.id;
        document.getElementById('taskTitle').value = task.title;
        document.getElementById('taskDescription').value = task.description || '';
        document.getElementById('taskDeadline').value = task.deadline || '';
        document.getElementById('taskPriority').value = task.priority;
        document.getElementById('taskReminder').checked = task.reminder || false;
        document.getElementById('reminderTime').value = task.reminderTime || '15';
        
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
        text: 'This action cannot be undone!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!'
      }).then((result) => {
        if (result.isConfirmed) {
          tasks = tasks.filter(t => t.id !== taskId);
          localStorage.setItem('tasks', JSON.stringify(tasks));
          showToast('Task deleted successfully!');
          loadTasks();
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