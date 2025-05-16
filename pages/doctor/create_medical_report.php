<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Medical Report</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2>Create Medical Report</h2>

    <form action="/pages/doctor/create_medical_report.php" method="POST">
        <!-- Hidden doctor and patient IDs (set manually or via JS) -->
        <input type="hidden" name="doctor_id" value="123">
        <input type="hidden" name="patient_id" value="456">

        <div class="mb-3">
            <label for="findings" class="form-label">Findings</label>
            <textarea class="form-control" id="findings" name="findings" rows="5" required></textarea>
        </div>

        <div class="mb-3">
            <label for="recommendations" class="form-label">Recommendations</label>
            <textarea class="form-control" id="recommendations" name="recommendations" rows="4" required></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Submit Report</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
