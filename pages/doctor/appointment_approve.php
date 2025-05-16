<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Approve Appointments</title>
<!--    <link href="/assets/bootstrap.min.css" rel="stylesheet">-->
    <link rel="stylesheet" href="/css/doctor/doctor.css">


</head>
<body>
<div class="container mt-4">
    <h2 class="mb-4">Pending Appointments</h2>
    <table class="table table-hover">
        <thead>
        <tr>
            <th>Patient</th>
            <th>Date</th>
            <th>Reason</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <!-- Example static row (replace with DB data or JS-rendered rows) -->
        <tr>
            <td>John Doe</td>
            <td>2025-05-17 10:30</td>
            <td>Routine Checkup</td>
            <td>
                <form method="post" action="approve_appointment.php">
                    <input type="hidden" name="id" value="1">
                    <button name="action" value="approve" class="btn btn-success btn-sm">Approve</button>
                    <button name="action" value="reject" class="btn btn-danger btn-sm">Reject</button>
                </form>
            </td>
        </tr>

        <!-- Repeat for more appointments or populate via JavaScript -->
        </tbody>
    </table>
</div>
</body>
</html>
