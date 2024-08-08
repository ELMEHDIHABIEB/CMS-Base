<style>
        /* Sidebar Styles */
        #sidebar {
            height: 100vh;
            position: fixed;
            left: 0;
            z-index: 100;
            background-color: #343a40;
            width: 160px;
          margin-top: 23px;
}
        }
        .sidebar-content {
            padding: 1rem;
        }
        .sidebar-content h4 {
            margin-bottom: 1rem;
            color: white;
        }

        /* Navigation Link Styles */
        .nav-link {
            padding: 0.75rem;
            border-radius: 0.25rem;
            transition: background-color 0.3s, padding 0.3s;
            color: white;
            display: flex;
            align-items: center;
        }
        .nav-link:hover {
            background-color: #495057;
        }
        .nav-link i {
            font-size: 1.25rem;
            margin-right: 0.3rem;
        }
        /* Hide text labels on small screens */
        .nav-link .nav-label {
            display: inline;
        }
        
            .sidebar-content h4 {
                display: none;
            } @media (max-width: 767px) {
            #sidebar {
                width: 60px;
                overflow-x: hidden;
              margin-top: 17px;
            }
            .nav-link {
                padding: 0.75rem;
                justify-content: center;
                text-align: center;
            }
            .nav-link .nav-label {
                display: none;
            }
            .nav-link i {
                font-size: 1.5rem;
            }
        }

       
        }
  .flex-column {
    flex-direction: column!important;
    margin-left: -2px;
}
  main {
    margin-left: 150px;
    padding: 2rem;
    margin-top: 35px;
} @media (max-width: 767px) {
            main {
                margin-left: 30px;
            }
    </style>
<nav id="sidebar" class="bg-dark">
<div class="sidebar-content">
<ul class="nav flex-column">
<li class="nav-item">
<a class="nav-link text-white" href="dashboard.php">
<i class="bi bi-house-door"></i>
<span class="nav-label">Dashboard</span>
</a>
</li>
<li class="nav-item">
<a class="nav-link text-white" href="offers.php">
<i class="bi bi-megaphone"></i>
<span class="nav-label">Offers</span>
</a>
</li>
<li class="nav-item">
<a class="nav-link text-white" href="profile.php">
<i class="bi bi-person-square"></i>
<span class="nav-label">My Profile</span>
</a>
</li>
<li class="nav-item">
<a class="nav-link text-white" href="cv.php">
<i class="bi bi-journal-text"></i>
<span class="nav-label">My Resume</span>
</a>
</li>
<li class="nav-item">
<a class="nav-link text-white" href="message.php">
<i class="bi bi-chat-dots"></i>
<span class="nav-label">Messages</span>
</a>
</li>
<li class="nav-item">
<a class="nav-link text-white" href="privacy.php">
<i class="bi bi-shield-lock"></i>
<span class="nav-label">Privacy</span>
</a>
</li>
<li class="nav-item">
<a class="nav-link text-white" href="settings.php">
<i class="bi bi-bell"></i>
<span class="nav-label">Notifications</span>
</a>
</li>
<li class="nav-item">
<a class="nav-link text-white" href="settings.php">
<i class="bi bi-gear"></i>
<span class="nav-label">Settings</span>
</a>
</li>
</ul>
</div>
</nav>
