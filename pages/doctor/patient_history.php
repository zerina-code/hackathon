<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/doctor/doctor.css">
    <title>Patient History</title>
</head>
<body>

<div class="container my-4">
    <h1>History for Jane Doe</h1>

    <h3>Appointments</h3>
    <ul>
        <li>2025-05-10 09:30 (Completed)</li>
        <li>2025-05-17 10:00 (Scheduled)</li>
    </ul>

    <h3>Prescriptions</h3>
    <ul>
        <li>Paracetamol 500mg, 3x/day <small>(2025-05-10)</small></li>
        <li>Ibuprofen 200mg, 2x/day <small>(2025-05-05)</small></li>
    </ul>

    <h3>Medical Reports</h3>
    <ul>
        <li>
            <strong>Findings:</strong> Elevated temperature and fatigue<br>
            <strong>Recommendations:</strong> Rest and hydration<br>
            <small>(2025-05-10)</small>
        </li>
        <li>
            <strong>Findings:</strong> Normal vital signs<br>
            <strong>Recommendations:</strong> Continue regular checkups<br>
            <small>(2025-04-28)</small>
        </li>
    </ul>

    <a href="/pages/doctor/edit_patient.php?patient=123" class="btn btn-secondary">Edit Parameters</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
