<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Register New Patient</title>
    <link href="/assets/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h2>Register New Patient</h2>
    <form method="post">
        <div class="mb-3">
            <label class="form-label">First Name</label>
            <input name="first_name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Last Name</label>
            <input name="last_name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input name="email" type="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input name="password" type="password" class="form-control" required>
        </div>
        <button class="btn btn-success">Register</button>
    </form>
</div>
</body>
</html>