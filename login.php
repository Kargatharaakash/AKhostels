<?php
session_start();
$pageTitle = "Login";
include('header.php');

require("db-connect.php");

$errmsg = '';
if (isset($_POST['login']) && isset($_POST['email']) && !empty($_POST['email']) && isset($_POST['role'])) {
    $email = $_POST['email'];
    $role = $_POST['role'];
    $redirect = 'index.php';

    if ($role === 'admin') {
        $query = "SELECT * FROM admins WHERE email='" . $email . "' LIMIT 1;";
        $redirect = 'admin-dashboard.php';
    } elseif ($role === 'student') {
        $query = "SELECT * FROM students WHERE email='" . $email . "' LIMIT 1;";
        $redirect = 'student-dashboard.php';
    } else {
        $query = '';
    }

    if ($query) {
        $result = $conn->query($query);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $_SESSION['currentUser'] = $row['email'];
            $_SESSION['loginId'] = $row['id'];
            $_SESSION['role'] = $role;
            header("Location: $redirect");
            exit();
        } else {
            $errmsg = "User does not exist or role mismatch.";
        }
        $conn->close();
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
      <div class="text-center mb-8">
        <div class="font-['Pacifico'] text-3xl text-blue-600 mb-2">AK Hostels</div>
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Welcome</h1>
        <p class="text-gray-600">Sign in to your account</p>
      </div>

      <?php if (!empty($errmsg)) : ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm mb-4">
          <?php echo htmlspecialchars($errmsg); ?>
        </div>
      <?php endif; ?>

      <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="space-y-6">
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

        <!-- Hidden role input -->
        <input type="hidden" name="role" id="role" value="student">

        <div>
          <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <i class="ri-mail-line text-gray-400"></i>
            </div>
            <input
              type="email"
              name="email"
              id="email"
              required
              class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none text-sm"
              placeholder="Enter your email address"
            />
          </div>
        </div>

        <button
          type="submit"
          name="login"
          class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition">
          Sign In
        </button>

        <div class="text-center">
          <span class="text-gray-600">Don't have an account? </span>
          <a href="register.php" class="text-blue-600 hover:text-blue-800 font-medium">Register</a>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  // Role toggle script
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
