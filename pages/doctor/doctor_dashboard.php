<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/doctor/doctor.css">
    <title>Doctor Dashboard</title>
</head>
<body>

<nav class="navbar navbar-dark bg-dark">
    <div class="container-fluid">
        <span class="navbar-brand">Welcome, Dr. John Smith</span>
        <a href="/logout.php" class="btn btn-outline-light">Logout</a>
    </div>
</nav>

<div class="container my-4">
    <h2>Your Patients</h2>
    <table class="table table-striped">
        <thead>
        <tr><th>Name</th><th>Email</th><th>Actions</th></tr>
        </thead>
        <tbody>
        <tr>
            <td>Jane Doe</td>
            <td>jane@example.com</td>
            <td>
                <button class="btn btn-sm btn-primary me-1" data-bs-toggle="modal" data-bs-target="#reportModal" data-patient-id="1" data-patient-name="Jane Doe">New Report</button>
                <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#prescriptionModal" data-patient-id="1" data-patient-name="Jane Doe">New Prescription</button>
            </td>
        </tr>
        <tr>
            <td>Mark Allen</td>
            <td>mark@example.com</td>
            <td>
                <button class="btn btn-sm btn-primary me-1" data-bs-toggle="modal" data-bs-target="#reportModal" data-patient-id="2" data-patient-name="Mark Allen">New Report</button>
                <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#prescriptionModal" data-patient-id="2" data-patient-name="Mark Allen">New Prescription</button>
            </td>
        </tr>
        </tbody>
    </table>
</div>

<!-- Report Modal -->
<div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="reportForm" action="/pages/doctor/create_medical_report.php" method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reportModalLabel">New Medical Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="doctor_id" value="123">
                <input type="hidden" name="patient_id" id="reportPatientId">
                <div class="mb-3">
                    <label for="reportFindings" class="form-label">Findings</label>
                    <textarea name="findings" id="reportFindings" class="form-control" rows="4" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="reportRecommendations" class="form-label">Recommendations</label>
                    <textarea name="recommendations" id="reportRecommendations" class="form-control" rows="3" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Report</button>
            </div>
        </form>
    </div>
</div>

<!-- Bootstrap JS for modals -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Optional: populate hidden input with patient ID when opening modal
    const reportModal = document.getElementById('reportModal');
    reportModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const patientId = button.getAttribute('data-patient-id');
        document.getElementById('reportPatientId').value = patientId;
    });
</script>

</body>
</html>


