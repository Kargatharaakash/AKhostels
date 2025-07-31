<?php
$pageTitle = "Register";
include('header.php');
require('db-connect.php');

$errmsg = '';
$successmsg = '';

if (isset($_POST['register']) && isset($_POST['role'])) {
    $role = $_POST['role'];
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);

    if ($role === 'student') {
        if ($name && $email) {
            $stmt = $conn->prepare("INSERT INTO students (fullname, email) VALUES (?, ?)");
            $stmt->bind_param("ss", $name, $email);
            if ($stmt->execute()) {
                $successmsg = "Student registered successfully.";
            } else {
                $errmsg = "Error registering student.";
            }
            $stmt->close();
        } else {
            $errmsg = "All fields are required for student registration.";
        }
    }  elseif ($role === 'admin') {
        if ($name && $email) {
            $check = $conn->prepare("SELECT id FROM admins WHERE email=? LIMIT 1");
            $check->bind_param("s", $email);
            $check->execute();
            $check->store_result();
            if ($check->num_rows > 0) {
                $errmsg = "An admin with this email already exists.";
            } else {
                $stmt = $conn->prepare("INSERT INTO admins (name, email) VALUES (?, ?)");
                $stmt->bind_param("ss", $name, $email);
                if ($stmt->execute()) {
                    $successmsg = "Admin registered successfully.";
                } else {
                    $errmsg = "Error registering admin.";
                }
                $stmt->close();
            }
            $check->close();
        } else {
            $errmsg = "All fields are required for admin registration.";
        }
    } else {
        $errmsg = "Invalid role selected.";
    }
}
?>

<!-- ✅ TAILWIND & ICONS -->
<script src="https://cdn.tailwindcss.com"></script>
<link
  href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap"
  rel="stylesheet"
/>
<link
  rel="stylesheet"
  href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css"
/>

<style>
  body {
    background-color: #f9fafb;
  }
  .role-toggle.active {
    background-color: #3b82f6;
    color: white;
  }
</style>

<div class="min-h-screen flex items-center justify-center px-4 py-8">
  <div class="w-full max-w-md">
    <div class="bg-white rounded-2xl shadow-xl p-8">
      <div class="text-center mb-4">
        <div class="font-['Pacifico'] text-3xl text-blue-600 mb-2">AK Hostels</div>
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Create Account</h1>
        <p class="text-gray-600">Register a new user</p>
      </div>


      <form method="post" class="space-y-6" id="registerForm">
        <div class="flex bg-gray-100 rounded-lg p-1 mb-4">
          <button type="button"
            class="role-toggle flex-1 py-2 px-4 rounded-md text-sm font-medium active"
            data-role="student">
            <i class="ri-graduation-cap-line mr-2"></i> Student
          </button>
          <button type="button"
            class="role-toggle flex-1 py-2 px-4 rounded-md text-sm font-medium"
            data-role="admin">
            <i class="ri-admin-line mr-2"></i> Admin
          </button>
        </div>

        <input type="hidden" name="role" id="role" value="student">

        <div>
          <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
          <input
            type="text"
            name="name"
            id="name"
            required
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none text-sm"
            placeholder="Enter your full name"
          />
        </div>

        <div>
          <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
          <input
            type="email"
            name="email"
            id="email"
            required
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none text-sm"
            placeholder="Enter your email address"
          />
        </div>

        <button
          type="submit"
          name="register"
          class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition">
          Register
        </button>

        <div class="text-center">
          <span class="text-gray-600">Already have an account? </span>
          <a href="login.php" class="text-blue-600 hover:text-blue-800 font-medium">Login</a>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  const roleButtons = document.querySelectorAll('.role-toggle');
  const roleInput = document.getElementById('role');

  roleButtons.forEach(button => {
    button.addEventListener('click', () => {
      roleButtons.forEach(btn => btn.classList.remove('active'));
      button.classList.add('active');
      roleInput.value = button.dataset.role;
    });
  });
</script>

<?php include('footer.php'); ?>
