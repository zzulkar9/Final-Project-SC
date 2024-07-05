<!DOCTYPE html>
<html>
<head>
    <title>Assignment 3 - Student Information</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        h2 {
            margin-bottom: 20px;
        }

        form {
            margin-bottom: 20px;
        }

        select {
            padding: 5px;
        }

        th, td {
            padding: 5px;
        }

        #studentInfo {
            margin-top: 20px;
        }

        .login-button {
            position: absolute;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .home-button {
            position: absolute;
            top: 20px;
            right: 150px;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

    </style>
</head>
<body>
    <h2>Assignment 3 - Customer Info</h2>
    <button class="home-button" onclick="location.href='/index.html '">Homepage</button>
    <button class="login-button" onclick="location.href='login.php'">Login to Buy</button>

    <form>
        <label for="customers">Select a customer:</label>
        <select name="customers" onchange="showCustomer(this.value)">
            <option value="">Select a customer:</option>
            <option value="1">Zul</option>
            <option value="2">Ariff</option>
            <option value="3">Alep</option>
            <option value="4">Fahmi</option>
        </select>
    </form>

    <div id="studentInfo">Customer information will be listed here...</div>

    <script>
        function showCustomer(str) {
            if (str == "") {
                document.getElementById("studentInfo").innerHTML = "";
                return;
            }
            const xhttp = new XMLHttpRequest();
            xhttp.onload = function() {
                document.getElementById("studentInfo").innerHTML = this.responseText;
            }
            xhttp.open("GET", "getData.php?q=" + str);
            xhttp.send();
        }
    </script>
</body>
</html>