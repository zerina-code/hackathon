<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Patient Detail & History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Patient: <strong>John Doe</strong></h2>
    <p><strong>Date of Birth:</strong> 1985-04-12</p>
    <p><strong>Address:</strong> 123 Elm Street, Springfield</p>
    <p><strong>Phone:</strong> (123) 456-7890</p>

    <!-- ✅ Success Message -->
    <div id="successAlert" class="alert alert-success d-none" role="alert">
        Successfully entered!
    </div>

    <!-- ✅ History Table -->
    <h4 class="mt-5">Patient History</h4>
    <table id="historyTable" class="table table-bordered">
        <thead class="table-light">
        <tr>
            <th>Date</th>
            <th>Diagnosis</th>
            <th>Order</th>
            <th>Prescription</th>
        </tr>
        </thead>
        <tbody>
        <!-- Entries will be prepended here -->
        </tbody>
    </table>

    <!-- ✅ Add New Entry Form -->
    <h4 class="mt-5">Add Diagnostic Entry</h4>
    <form id="entryForm">
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea class="form-control" id="description" rows="3" required></textarea>
        </div>
        <div class="row mb-3">
            <div class="col">
                <label class="form-label">Diagnosis</label>
                <input type="text" id="diagnosis" class="form-control" />
            </div>
            <div class="col">
                <label class="form-label">Order</label>
                <input type="text" id="order" class="form-control" />
            </div>
            <div class="col">
                <label class="form-label">Prescription</label>
                <input type="text" id="prescription" class="form-control" />
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>

<script>
    document.getElementById('entryForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const desc = document.getElementById('description').value.trim();
        const diagnosis = document.getElementById('diagnosis').value.trim();
        const order = document.getElementById('order').value.trim();
        const prescription = document.getElementById('prescription').value.trim();

        if (!diagnosis && !order && !prescription) {
            alert("Please fill at least one of Diagnosis, Order, or Prescription.");
            return;
        }

        const today = new Date().toISOString().split('T')[0];
        const table = document.getElementById('historyTable').querySelector('tbody');

        const row = document.createElement('tr');
        row.innerHTML = `
    <td>${today}</td>
    <td>${diagnosis}</td>
    <td>${order}</td>
    <td>${prescription}</td>
  `;
        table.prepend(row);

        // Reset form
        document.getElementById('entryForm').reset();

        // Show success
        const successAlert = document.getElementById('successAlert');
        successAlert.classList.remove('d-none');
        setTimeout(() => successAlert.classList.add('d-none'), 3000);
    });
</script>
</body>
</html>



