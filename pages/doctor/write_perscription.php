
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/doctor/doctor.css">
    <title>Write Prescription</title>
</head>
<body>
<div class="container my-4">
    <h1>Write Prescription</h1>
    <form action="/pages/doctor/create_prescription.php" method="POST">
        <input type="hidden" name="doctor_id" value="<?= htmlspecialchars($doctorId) ?>">
        <div class="mb-3">
            <label for="patientId" class="form-label">Patient</label>
            <select name="patient_id" id="patientId" class="form-select" required>
                <option value="">Select patient…</option>
                <?php foreach ($patients as $p): ?>
                    <option value="<?= htmlspecialchars($p['id']) ?>"><?= htmlspecialchars($p['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="prescContent" class="form-label">Prescription Details</label>
            <textarea name="content" id="prescContent" class="form-control" rows="5" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Save Prescription</button>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>



