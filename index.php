<?php
session_start();
if (!isset($_SESSION['username'])) {
    $_SESSION['flash_message'] = "You must log in first.";
    $_SESSION['redirect_to'] = $_SERVER['REQUEST_URI'];
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>R&D Data Search Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Quicksand', sans-serif;
        }
        
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background: #000;
            color: #fff;
            overflow-x: hidden;
        }
        
        .background {
            position: fixed;
            width: 100vw;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 2px;
            flex-wrap: wrap;
            overflow: hidden;
            z-index: -1;
        }
        
        .background::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background: linear-gradient(#000, #4e54c8, #000);
            animation: animate 5s linear infinite;
        }
        
        @keyframes animate {
            0% {
                transform: translateY(-100%);
            }
            100% {
                transform: translateY(100%);
            }
        }
        
        .background span {
            position: relative;
            display: block;
            width: calc(6.25vw - 2px);
            height: calc(6.25vw - 2px);
            background: #181818;
            z-index: 2;
            transition: 1.5s;
        }
        
        .background span:hover {
            background: #4e54c8;
            transition: 0s;
        }
        
        .content-wrapper {
            position: relative;
            z-index: 10;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            width: 100%;
        }
        
        .main-container {
            display: flex;
            flex-grow: 1;
        }
        
        .sidebar {
            background: rgba(34, 34, 34, 0.9);
            width: 250px;
            padding: 20px;
            transition: transform 0.3s ease;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5);
            height: 100vh;
            position: fixed;
            z-index: 100;
        }
        
        .sidebar.hidden {
            transform: translateX(-100%);
        }
        
        .sidebar h2 {
            font-size: 1.5em;
            color: #4e54c8;
            text-transform: uppercase;
            margin-bottom: 20px;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .sidebar ul {
            list-style: none;
        }
        
        .sidebar li {
            margin-bottom: 15px;
        }
        
        .sidebar a {
            display: block;
            padding: 10px;
            background: #333;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            transition: 0.3s;
        }
        
        .sidebar a:hover {
            background: #4e54c8;
        }
        
        .sidebar input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            background: #333;
            border: none;
            border-radius: 4px;
            color: #fff;
        }
        
        .sidebar button {
            width: 100%;
            padding: 10px;
            background: #4e54c8;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: 0.3s;
        }
        
        .sidebar button:hover {
            opacity: 0.8;
        }
        
        .main-content {
            flex-grow: 1;
            margin-left: 250px;
            padding: 30px;
            transition: margin-left 0.3s ease;
            background: rgba(0, 0, 0, 0.7);
        }
        
        .main-content.full-width {
            margin-left: 0;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .header h1 {
            font-size: 2.5em;
            font-weight: 700;
            color: #4e54c8;
        }
        
        .user-info {
            text-align: right;
        }
        
        .user-info p {
            margin-bottom: 5px;
        }
        
        .user-info a {
            color: #4e54c8;
            text-decoration: none;
        }
        
        .user-info a:hover {
            text-decoration: underline;
        }
        
        .search-container {
            display: flex;
            margin-bottom: 20px;
        }
        
        .search-container input {
            flex-grow: 1;
            padding: 15px;
            background: #333;
            border: none;
            border-radius: 4px 0 0 4px;
            color: #fff;
        }
        
        .search-container button {
            padding: 15px 25px;
            background: #4e54c8;
            color: #fff;
            border: none;
            border-radius: 0 4px 4px 0;
            cursor: pointer;
            transition: 0.3s;
        }
        
        .search-container button:hover {
            opacity: 0.8;
        }
        
        #loading {
            text-align: center;
            color: #4e54c8;
            margin: 20px 0;
            font-weight: 600;
        }
        
        #results {
            background: rgba(34, 34, 34, 0.9);
            padding: 20px;
            border-radius: 4px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5);
        }
        
        .result-item {
            background: #333;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 4px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .result-item:hover {
            transform: scale(1.02);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
        }
        
        .result-item h3 {
            color: #4e54c8;
            margin-bottom: 10px;
        }
        
        footer {
            background: rgba(34, 34, 34, 0.9);
            text-align: center;
            padding: 15px;
            color: #aaa;
        }
        
        .control-buttons {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 100;
        }
        
        .control-button {
            display: block;
            width: 50px;
            height: 50px;
            background: #4e54c8;
            color: #fff;
            border: none;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 10px;
            cursor: pointer;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            transition: 0.3s;
        }
        
        .control-button:hover {
            transform: scale(1.1);
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .search-container {
                flex-direction: column;
            }
            
            .search-container input,
            .search-container button {
                width: 100%;
                border-radius: 4px;
                margin-bottom: 10px;
            }
            
            .background span {
                width: calc(10vw - 2px);
                height: calc(10vw - 2px);
            }
        }
        
        @media (max-width: 600px) {
            .background span {
                width: calc(20vw - 2px);
                height: calc(20vw - 2px);
            }
        }
        
        /* Animation for loading */
        @keyframes pulse {
            0% { opacity: 0.6; }
            50% { opacity: 1; }
            100% { opacity: 0.6; }
        }
        
        .animate-pulse {
            animation: pulse 1.5s infinite;
        }
    </style>
</head>
<body>
    <div class="background">
        <?php for($i = 0; $i < 100; $i++): ?>
            <span></span>
        <?php endfor; ?>
    </div>
    
    <div class="content-wrapper">
        <div class="main-container">
            <div id="sidebar" class="sidebar">
                <h2>
                    <img src="images.png" alt="Logo" style="height: 30px; width: 30px; margin-right: 10px;"> 
                    R&D Dashboard
                </h2>
                <ul>
                    <li><a href="#">Dashboard</a></li>
                    <li>
                        <input type="text" id="sidebarSearchInput" placeholder="Ask about R&D...">
                        <button onclick="searchData('sidebarSearchInput')">Search</button>
                    </li>
                </ul>
            </div>
            
            <div class="main-content" id="mainContent">
                <div class="header">
                    <h1>R&D Search</h1>
                    <div class="user-info">
                        <p>Welcome, <span><?php echo htmlspecialchars($_SESSION['username']); ?></span></p>
                        <a href="logout.php">Logout</a>
                    </div>
                </div>
                
                <p style="margin-bottom: 20px; color: #aaa;">Search your research data intelligently using Natural Language Processing.</p>
                
                <div class="search-container">
                    <input type="text" id="mainSearchInput" placeholder="Ask about R&D...">
                    <button onclick="searchData('mainSearchInput')">Search</button>
                </div>
                
                <div id="loading" class="hidden animate-pulse">Searching...</div>
                
                <div id="results"></div>
            </div>
        </div>
        
        <footer>
            © 2025 R&D AI Search. All rights reserved.
        </footer>
    </div>
    
    <div class="control-buttons">
        <button class="control-button" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
    </div>
    
    <script>
        // Create animated background
        const backgroundSpans = document.querySelectorAll('.background span');
        
        backgroundSpans.forEach(span => {
            span.style.animationDelay = Math.random() * 5 + 's';
            span.style.animationDuration = Math.random() * 5 + 5 + 's';
        });
        
        function searchData(inputId) {
            const query = document.getElementById(inputId).value;
            const loading = document.getElementById("loading");
            const resultsContainer = document.getElementById("results");
            
            if (query.trim() === "") {
                alert("Please enter a search query.");
                return;
            }
            
            resultsContainer.innerHTML = "";
            loading.classList.remove("hidden");
            
            fetch(`./search.php?query=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    loading.classList.add("hidden");
                    if (data.error) {
                        resultsContainer.innerHTML = `<p style="color: #ff6b6b;">${data.error}</p>`;
                        return;
                    }
                    
                    let resultHTML = `<h2 style="font-size: 1.8em; margin-bottom: 20px; color: #4e54c8;">Search Results</h2>`;
                    
                    data.forEach(result => {
                        resultHTML += `
                            <div class="result-item">
                                <h3>${result.title}</h3>
                                <p>${result.snippet}</p>
                            </div>
                        `;
                    });
                    
                    resultsContainer.innerHTML = resultHTML;
                })
                .catch(error => {
                    loading.classList.add("hidden");
                    console.error(error);
                    resultsContainer.innerHTML = `<p style="color: #ff6b6b;">An error occurred while searching.</p>`;
                });
        }
        
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('hidden');
            document.getElementById('mainContent').classList.toggle('full-width');
        }
    </script>
</body>
</html>