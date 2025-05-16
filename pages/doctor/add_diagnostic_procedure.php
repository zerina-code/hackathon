<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Patient Detail & History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../../css/doctor/doctor.css" />
    <link rel="stylesheet" href="../../css/admin/sidebar.css" />
    <link rel="stylesheet" href="../../css/admin/layout.css" />
    <style>
        .main-wrapper {
            display: flex;
        }

        .sidebar {
            width: 240px;
            min-height: 100vh;
        }

        .content-area {
            flex-grow: 1;
            padding: 2rem;
        }

        .nav-link {
            color: #ffffff;
        }

        .nav-link:hover {
            color: #ffc107;
        }
    </style>
</head>
<body>

<div class="main-wrapper">
    <!-- ✅ Sidebar -->
    <nav class="sidebar bg-dark text-white p-3">
        <div class="sidebar-header mb-4">
            <a href="#" class="navbar-brand text-white">
                <img src="../../assets/logo.png" alt="logo" style="height: 60px;">

            </a>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item"><a class="nav-link" href="doctor_dashboard.php">Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="patients_list.php">Patients</a></li>
            <li class="nav-item"><a class="nav-link" href="add_diagnostic_procedure.php">Add Procedure</a></li>
            <li class="nav-item"><a class="nav-link" href="appointment_approve.php">Appointments</a></li>
        </ul>
    </nav>

    <!-- ✅ Page Content -->
    <div class="content-area">
        <h2 class="mb-4">Patient: <strong>John Doe</strong></h2>
        <p><strong>Date of Birth:</strong> 1985-04-12</p>
        <p><strong>Address:</strong> 123 Elm Street, Springfield</p>
        <p><strong>Phone:</strong> (123) 456-7890</p>

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
                <th>Description</th>
            </tr>
            </thead>
            <tbody>
            <!-- Entries will appear here -->
            </tbody>
        </table>
        <p class="text-muted">Click on a row to print it.</p>

        <!-- ✅ Add Entry Form -->
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
        const tableBody = document.querySelector('#historyTable tbody');

        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${today}</td>
            <td>${diagnosis}</td>
            <td>${order}</td>
            <td>${prescription}</td>
            <td>${desc}</td>
        `;

        row.style.cursor = "pointer";
        row.addEventListener("click", function () {
            const printContent = `
                <h3>Patient Summary</h3>
                <p><strong>Name:</strong> John Doe</p>
                <p><strong>Date of Birth:</strong> 1985-04-12</p>
                <p><strong>Address:</strong> 123 Elm Street, Springfield</p>
                <p><strong>Phone:</strong> (123) 456-7890</p>
                <hr />
                <p><strong>Date:</strong> ${today}</p>
                <p><strong>Diagnosis:</strong> ${diagnosis || "-"}</p>
                <p><strong>Order:</strong> ${order || "-"}</p>
                <p><strong>Prescription:</strong> ${prescription || "-"}</p>
                <p><strong>Description:</strong> ${desc || "-"}</p>
            `;

            const win = window.open('', '', 'height=600,width=800');
            win.document.write('<html><head><title>Print Patient Record</title></head><body>');
            win.document.write(printContent);
            win.document.write('</body></html>');
            win.document.close();
            win.focus();
            win.print();
            win.close();
        });

        tableBody.prepend(row);
        document.getElementById('entryForm').reset();

        const successAlert = document.getElementById('successAlert');
        successAlert.classList.remove('d-none');
        setTimeout(() => successAlert.classList.add('d-none'), 3000);
    });
</script>
</body>
</html>




