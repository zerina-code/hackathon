<?php
function renderTable($type, $data) {
    echo '<table id="adminTable" class="table table-striped table-bordered">';
    echo '<thead><tr>';

    // Define columns by table type
    if ($type === 'appointments') {
        $columns = ['Doctor Name', 'Speciality', 'Patient Name', 'Appointment Time', 'Status'];
    } elseif ($type === 'doctors') {
        $columns = ['Picture', 'Name', 'Speciality', 'Number of Patients', 'Review','Account Status'];
    } elseif ($type === 'patients') {
        $columns = ['Name', 'Date of Birth', 'Address', 'Phone Number', 'Last Visit'];
    } else {
        echo '</tr></thead><tbody></tbody></table>';
        return;
    }

    // Print column headers
    foreach ($columns as $col) {
        echo "<th>{$col}</th>";
    }

    echo '</tr></thead><tbody>';

    // Table data
    foreach ($data as $row) {
        echo '<tr>';
        if ($type === 'appointments') {
            echo "<td>{$row['doctorName']}</td>";
            echo "<td>{$row['speciality']}</td>";
            echo "<td>{$row['patientName']}</td>";
            echo "<td>{$row['time']}</td>";
            echo '<td>
                    <label class="toggle-switch">
                        <input type="checkbox" ' . ($row['status'] ? 'checked' : '') . '>
                        <span class="slider"></span>
                    </label>
                  </td>';
        }

        if ($type === 'doctors') {
            echo '<td><img src="https://ui-avatars.com/api/?name=Doctor&background=cccccc&color=ffffff&size=40" class="profile-img" alt="Doctor"></td>';
            echo "<td>{$row['name']}</td>";
            echo "<td>{$row['speciality']}</td>";
            echo "<td>{$row['patients']}</td>";

            // Nova kolona: Review (ocjena zvjezdicama)
            echo '<td>';
            $rating = $row['review'];
            $fullStars = floor($rating);
            $halfStar = ($rating - $fullStars) >= 0.5;
            for ($i = 0; $i < $fullStars; $i++) {
                echo '<i class="fas fa-star text-warning"></i>';
            }
            if ($halfStar) {
                echo '<i class="fas fa-star-half-alt text-warning"></i>';
            }
            $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
            for ($i = 0; $i < $emptyStars; $i++) {
                echo '<i class="far fa-star text-warning"></i>';
            }
            echo '</td>';

            // Kolona za status
            echo '<td>
            <label class="toggle-switch">
                <input type="checkbox" ' . ($row['status'] ? 'checked' : '') . '>
                <span class="slider"></span>
            </label>
          </td>';
        }


        if ($type === 'patients') {
            echo "<td>{$row['name']}</td>";
            echo "<td>{$row['dob']}</td>";
            echo "<td>{$row['address']}</td>";
            echo "<td>{$row['phone']}</td>";
            echo "<td>{$row['lastVisit']}</td>";
        }
        echo '</tr>';
    }

    echo '</tbody></table>';
}
?>
