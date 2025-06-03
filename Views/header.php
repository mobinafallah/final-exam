<!DOCTYPE html>
<html lang="en" style="height: 100%;">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>My Website</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <style>
    :root {
      --primary-bg: #133853;
      --primary-text: #F4845A;
    }

    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: var(--primary-bg) !important;
      color: var(--primary-text) !important;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }

    main {
      flex: 1;
      background-color: var(--primary-bg) !important;
    }

    .navbar {
      background-color: var(--primary-bg) !important;
      box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    .navbar-brand, .nav-link {
      color: var(--primary-text) !important;
    }

    .navbar-brand {
      position: absolute;
      left: 50%;
      transform: translateX(-50%);
    }

    .navbar-nav {
      margin-right: auto;
    }

    .auth-buttons {
      margin-left: auto;
    }

    @media (max-width: 991px) {
      .navbar-brand {
        position: static;
        transform: none;
      }
      .auth-buttons {
        margin: 10px 0;
      }
    }

    .navbar-toggler {
      border-color: var(--primary-text) !important;
    }

    .navbar-toggler-icon {
      background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(244, 132, 90, 1)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e") !important;
    }

    .btn-outline-primary {
      color: var(--primary-text) !important;
      border-color: var(--primary-text) !important;
      background-color: transparent !important;
    }

    .btn-outline-primary:hover {
      background-color: var(--primary-text) !important;
      color: var(--primary-bg) !important;
    }

    .btn-primary {
      background-color: var(--primary-text) !important;
      border-color: var(--primary-text) !important;
      color: var(--primary-bg) !important;
    }

    .btn-primary:hover {
      opacity: 0.9;
    }

    .welcome-section {
      padding: 80px 20px;
      text-align: center;
      color: var(--primary-text);
    }

    footer {
      background-color: var(--primary-bg);
      color: var(--primary-text);
    }

    footer a {
      color: var(--primary-text);
      text-decoration: none;
    }

    footer a:hover {
      color: var(--primary-text);
      opacity: 0.8;
    }
  </style>
</head>
<body>

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg">
    <div class="container position-relative">
      <!-- منو سمت چپ -->
      <div class="order-lg-1">
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav">
          <li class="nav-item"><a class="nav-link" href="/website/">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="/website/post">Posts</a></li>
          <li class="nav-item"><a class="nav-link" href="/website/ranked">Rank</a></li>
          <li class="nav-item"><a class="nav-link" href="/website/dashboard">Dashboard</a></li>
        </ul>
        </div>
      </div>

      <!-- لوگو وسط -->
      <a class="navbar-brand fw-bold order-lg-2" href="#">MyWebsite</a>

      <!-- دکمه‌ها سمت راست -->
      <div class="auth-buttons order-lg-3">
        <a href="/website/login" class="btn btn-outline-primary me-2">Login</a>
        <a href="/website/register" class="btn btn-primary">Register</a>
      </div>
    </div>
  </nav>