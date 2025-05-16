<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Diagnostic Procedure</title>
    <link href="/assets/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h2>Add Diagnostic Procedure</h2>

    <form method="post" action="">
        <div class="mb-3">
            <label for="patient" class="form-label">Patient</label>
            <select name="patient_id" id="patient" class="form-select">
                <option value="">– Select patient –</option>
                <!-- Add patient options manually or via JS -->
                <option value="1">John Doe</option>
                <option value="2">Jane Smith</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="procedure" class="form-label">Procedure Description</label>
            <textarea name="procedure" id="procedure" class="form-control" rows="4"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>
</body>
</html>
