<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Department Selection System</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(120deg, #e0f7fa, #f1f8e9);
        }

        #app {
            display: flex;
            flex-direction: column;
        }

        header {
            background-color: #2f8d46;
            color: white;
            text-align: center;
            padding: 1.5em 0;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        header h1 {
            margin: 0;
            font-size: 2.5em;
        }

        nav {
            background-color: #4caf50;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
        }

        nav ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
        }

        nav li {
            margin: 0;
        }

        nav a {
            text-decoration: none;
            color: #fff;
            padding: 1em 2em;
            display: block;
            transition: background-color 0.3s ease;
        }

        nav a:hover {
            background-color: #45a049;
        }

        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #fff;
            min-width: 150px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 1;
        }

        .dropdown-content a {
            color: #4caf50;
            padding: 10px 15px;
            text-decoration: none;
            display: block;
            text-align: left;
        }

        .dropdown-content a:hover {
            background-color: #f1f1f1;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        .section {
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: flex-start;
            padding: 60px 10%;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            animation: fadeIn 2s ease-in-out;
        }

        .section h2 {
            font-size: 2.5em;
            color: #2f8d46;
            margin-bottom: 20px;
            animation: typing 3s steps(30, end), blink 0.5s step-end infinite alternate;
            white-space: nowrap;
            overflow: hidden;
            border-right: 4px solid #2f8d46;
        }

        .section img {
            max-width: 500px;
            height: auto;
            margin: 20px 0;
            border-radius: 10px;
            border: 3px solid #2f8d46;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .section p {
            font-size: 1.2em;
            color: #555;
            line-height: 1.8;
        }

        .section ul {
            margin-top: 20px;
            padding-left: 20px;
        }

        .section ul li {
            font-size: 1.2em;
            margin-bottom: 10px;
            color: #333;
            position: relative;
            padding-left: 20px;
        }

        @keyframes typing {
            from {
                width: 0;
            }
            to {
                width: 100%;
            }
        }

        @keyframes blink {
            from {
                border-color: transparent;
            }
            to {
                border-color: #2f8d46;
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body onload="changeContent('home')">
    <div id="app">
        <header>
            <h1>Debre Markos University Department Selection System</h1>
        </header>
        <nav>
            <ul>
                <li><a href="#" onclick="changeContent('home')">Home</a></li>
                <li><a href="#" onclick="changeContent('features')">Features</a></li>
                <li><a href="#" onclick="changeContent('how-it-works')">How It Works</a></li>
                <li><a href="#" onclick="changeContent('about-us')">About Us</a></li>
                <li><a href="#" onclick="changeContent('contact-us')">Contact Us</a></li>
                <li class="dropdown">
                    <a href="#">Login</a>
                    <div class="dropdown-content">
                        <a href="login.php?role=admin">Admin</a>
                        <a href="login.php?role=student">Student</a>
                        <a href="login.php?role=coordinator">Coordinator</a>
                        <a href="login.php?role=department_head">Department Head</a>
                    </div>
                </li>
            </ul>
        </nav>
        <main>
            <div id="content" class="section">
                <h2>Welcome to Debre Markos University Department Selection System</h2>
                <p>Empowering students and departments with a seamless selection process.</p>
                <img src="img/graduation.jpg" alt="Graduation Photo">
            </div>
        </main>
    </div>

    <script>
        function changeContent(page) {
            const contentDiv = document.getElementById('content');
            const pages = {
                'home': `<h2>Welcome to Debre Markos University Department Selection System</h2>
                         <p>Empowering students and departments with a seamless selection process.</p>
                         <img src="img/graduation.jpg" alt="Graduation Photo">`,

                'features': `<h2>Features</h2>
                             <ul>
                                <li>Easy Department Selection</li>
                                <li>Real-time Application Tracking</li>
                                <li>Customizable User Roles</li>
                                <li>Secure Login and Account Management</li>
                             </ul>`,

                'how-it-works': `<h2>How It Works</h2>
                             <ul>
                                <li>Students select their preferred departments through a user-friendly portal.</li>
                                <li>Coordinators review applications and assign students based on preferences.</li>
                                <li>Departments manage their student capacity efficiently.</li>
                             </ul>`,

                'about-us': `<h2>About Us</h2>
                             <p>Debre Markos University, known for excellence in education and research, introduces the Department Selection System to streamline the student-department assignment process. Our mission is to enhance collaboration and efficiency across departments and students.</p>`,

                'contact-us': `<h2>Contact Us</h2>
                             <ul>
                                <li>Email: support@departmentsystem.dmu.et</li>
                                <li>Phone: +251-58-111-1234</li>
                                <li>Address: Debre Markos University, Ethiopia</li>
                             </ul>`,
            };
            contentDiv.innerHTML = pages[page] || '<h2>Page not found!</h2>';
        }
    </script>
</body>

</html>
