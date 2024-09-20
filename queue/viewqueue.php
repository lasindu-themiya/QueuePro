
<?php

require '../connections/connection.php';


if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get the current date
$current_date = date('Y-m-d');
$current_time = date('H:i:s');

$details = "SELECT clinic.*, doctorlogin.DID, doctorlogin.First_Name, doctorlogin.Last_Name, schedule.SID, schedule.Target_Patient_Type, schedule.Start_Time, schedule.Duration
            FROM schedule
            INNER JOIN clinic ON schedule.SID = clinic.SID
            INNER JOIN doctorlogin ON schedule.DID = doctorlogin.DID
            WHERE clinic.Date = '$current_date' ORDER BY Room_Number ASC";

$result = mysqli_query($con, $details);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Today's Clinics</title>
    <link rel="title icon" href="../pictures/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@3.4.0/fonts/remixicon.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- <link rel="stylesheet" href="style.css" /> -->
    <link rel="stylesheet" href="../css/main.css" />

    <style>
        nav {
            padding: 2rem 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            position: relative;
        }

        .nav__logo {
            font-size: 1.7rem;
            font-weight: 600;
            color: #4b70f5;
        }

        .nav__links {
            list-style: none;
            display: flex;
            align-items: center;
            gap: 2rem;
            position: inherit;
        }

        .link a {
            text-decoration: none;
            padding: 2rem;
            color: #6b7280;
            cursor: pointer;
            transition: 0.3s;
        }

        .link a:hover {
            color: #4b70f5;
        }

        .fa-bars {
            display: none;
            font-size: 15px;
            cursor: pointer;
        }

        @media (max-width: 800px) {
            .nav-toggle {
                        display: block;
                        cursor: pointer;
                    }
                    .fa-bars {
                    display: block;
                    font-size: 25px;
                    color: #74C0FC;
                    cursor: pointer;
                }
                
                .nav__links {
                    display: none;
                    flex-direction: column;
                    gap: 2rem;
                    background-color: #fff;
                    padding: 1rem;
                    position: fixed;
                    top: 60px;
                    right: 10px;
                    border-radius: 10px;
                    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                    z-index: 1;
                }
                
                .nav__links.active {
                    display: flex;
                }
        }    

        .search-bar {
            display: flex;
            justify-content: center;
            padding: 20px;
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }

        .card-container {
            padding: 20px;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .card {
            background-color: #fff;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-10px);
        }

        .card-content {
            padding: 20px;
        }

        .card-content p {
            margin: 10px 0;
            font-size: 16px;
        }

        .action-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .action-button:hover {
            background-color: #0056b3;
        }


@media (max-width: 800px) {
            .search-bar {
                flex-direction: column;
                align-items: center;
            }

            .search-bar form {
                display: flex;
                flex-direction: column;
                gap: 10px;
                width: 100%;
                max-width: 300px;
            }

            .search-bar input[type="text"],
            .search-bar input[type="date"],
            .search-bar button {
                width: 100%;
                max-width: 100%;
            }
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleButton = document.querySelector('.fa-bars');
            const navLinks = document.querySelector('.nav__links');

            toggleButton.addEventListener('click', function() {
                navLinks.classList.toggle('active');
            });
        });

        function viewQueue(scheduleID, clinicID, startTime, duration) {
            window.location.href = "processqueue.php?scheduleID=" + encodeURIComponent(scheduleID) + 
                                   "&clinicID=" + encodeURIComponent(clinicID) + 
                                   "&startTime=" + encodeURIComponent(startTime) + 
                                   "&duration=" + encodeURIComponent(duration);
        }
    </script>
</head>
<body>
<nav>
    <div class="nav__logo">QueuePro</div>
    <ul class="nav__links">
        <li class="link"><a href="../index.php">Home</a></li>
        <li class="link"><a href="../appointment/makeappointment.php">Make Appointment</a></li>
        <li class="link"><a href="../appointment/verify_email.php">View Appointment</a></li>
        <li class="link"><a href="viewqueue.php">View Queue</a></li>
        <li class="link"><a href="../appointment/viewdoctors.php">View Doctor Details</a></li>
    </ul>
    <i class="fa-solid fa-bars" style="color: #74C0FC;"></i>
</nav>
<div class="card-container">
    
    <?php
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<div class='card'>";
            echo "<div class='card-content'>";
            echo "<p><strong>Doctor Name:</strong> " . htmlspecialchars($row['First_Name']) . " " . htmlspecialchars($row['Last_Name']) . "</p>";
            echo "<p><strong>Start Time:</strong> " . htmlspecialchars($row['Start_Time']) . "</p>";
            echo "<p><strong>Duration:</strong> " . htmlspecialchars($row['Duration']) . "</p>";
            echo "<p><strong>Date:</strong> " . htmlspecialchars($row['Date']) . "</p>";
            echo "<p><strong>Room Number:</strong> " . htmlspecialchars($row['Room_Number']) . "</p>";
            echo "<p><strong>Current State:</strong> " . htmlspecialchars($row['Current_State']) . "</p>";
            if ($row['Current_State'] === 'OnGoing' && $row['P_Limit'] > 0 ) {
                echo "<button class='action-button' onclick=\"viewQueue('{$row['SID']}', '{$row['CID']}', '{$row['Start_Time']}', '{$row['Duration']}')\">View Queue</button>";
            } else {
                echo "<button class='action-button' style='display:none;' disabled>View Queue</button>";
            }
            echo "</div>";
            echo "</div>";
        }
    } else {
        echo "<p>No clinics found for today.</p>";
    }
    ?>
</div>
</body>
</html>

<?php
mysqli_close($con);
?>