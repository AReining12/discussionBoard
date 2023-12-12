<!DOCTYPE html>
<!-- Name: Junji Duan   e-mail: junji.duan@mail.mcgill.ca -->
<?php
    include_once('session_start.php');
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Discussion Board</title>
    <link href="../cgi_bin/bootstrap/bootstrap.min.css" rel="stylesheet">
    
    <style>

        body {
            font-family: Arial, sans-serif;
        }

         /* Adjust row to work inside a flex container */
         .row {
            display: flex;
            flex-grow: 1; /* Allow row to grow and fill space */
        }
    
        .container-fluid {
            display: flex;
            min-height: 100vh;
        }
    
        .sidebar {
            flex: 0 0 200px;
            min-height: 100vh;
            background-color: #f8f9fa; /* Light grey background */
            padding: 10px;
            box-shadow: 2px 0px 5px rgba(0,0,0,0.1);
        }
    
        .sidebar a, .sidebar div {
            /* display: block; */
            padding: 8px 10px;
            margin: 5px 0;
            color: #333;
            text-decoration: none;
        }
    
        .sidebar a:hover, .sidebar div:hover {
            background-color: #e9ecef; /* Light hover effect */
        }

        .mycourses-link{
            text-align: center;
        }

    
        .search-box {
            width: 100%;
            padding: 8px;
            margin-top: 10px;
            margin-bottom: 10px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            align-items: center;
        }

        .make-post {
            font-weight: bold;
            color: #007bff; /* Bootstrap primary color */
        }

        .manage-system {
            font-weight: bold;
            color: #007bff; /* Bootstrap primary color */
        }
    
        .content-area {
            padding: 20px;
            background-color: #fff;
            border-left: 1px solid #ced4da;
            min-height: 100vh; 
        }
    
        .post-list {
            list-style-type: none;
            padding: 0;
        }
    
        .post-list div {
            cursor: pointer;
            border-bottom: 1px solid #ddd;
            padding: 10px;
        }
    
        .post-list div:hover {
            background-color: #f5f5f5;
        }

        .welcome {
            position: relative; /* Allows absolute positioning of children */
            display: block;
            background: 
                linear-gradient(
                to top,
                rgba(200, 200, 255, 0),
                rgba(0, 0, 100, 0.5)
                ),
                url(../asserts/images/welcome2.jpg);
            min-height: 95vh;
            background-size: cover;
            position: relative; 
            padding: 20px; 
            margin-top: 22px; /* Adds 20px space above the welcome section */
        }

        .welcome-text {
            margin-top: 1rem;
            font-family: Tahoma, Arial, sans-serif;
            font-weight: bold;
            text-align: center;
            color: white;
            position: absolute;
            top: 10%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .spacer {
            height: 100px; 
        }

        .manage-board-member, .manage-channels, .manage-channel-members, #join {
            position: absolute;
            padding: 8px 12px;
            background-color: #007bff;
            color: white;
            cursor: pointer;
            border-radius: 4px;
        }
        
        .manage-channels {
            top: 160px;
            left: 430px;  /*Position at lower-left corner */
        }

        .manage-board-member {
            top: 160px;
            right: 430px; /* Position at lower-right corner */
        }

        .manage-channel-members {
            bottom: 160px;
            right: 430px; /* Position at lower-right corner */
        }

        .comment-section {
            margin-top: 20px;
        }

        #commentText {
            margin-bottom: 10px;
        }

    </style>

</head>

<body>
    <div class="container-fluid">
        <!-- Main row for sidebar and content area -->
        <div class="row">

            <!-- Left Sidebar -->
            <div class="col-md-2 sidebar">
                <!-- Navigation and utility links in sidebar -->
                <a href="../pages/SelectBoard.html" class="mycourses-link"><b>My Courses</b></a><br><br>
                <a href="<?php
                    echo "../pages/DiscussionBoard.html?course=" . urlencode($_SESSION['boardID']);
                ?>" class="mycourses-link"><b>Back</b></a>

                <!-- List where posts will be dynamically added -->
            </div>

            <!-- Right Content Area -->
            <div class="col-md-10" id="contentArea">
                <!-- Area where the content of posts will be displayed -->

                <!-- 'Make a Post' Form -->
                <div id="makepostSection">
                    <a href="create_channel.php">Create Channel</a><br>
                    <a href="remove_channel.php">Delete Channel</a>
                </div>

            </div>

        </div>
    </div>

    <!-- Including Bootstrap JavaScript for functionality -->
    <script src="../cgi_bin/bootstrap/jquery.min.js"></script>
    <script src="../cgi_bin/bootstrap/popper.min.js"></script>
    <script src="../cgi_bin/bootstrap/bootstrap.min.js"></script>
    <script src="../cgi_bin/connection.js"></script>

    <script>
        /*
        // Dummy data for posts (used for demonstration purposes)
        let posts = [
            // { title: "Post 1", content: "Content for Post 1" },
            // { title: "Post 2", content: "Content for Post 2" },
            // { title: "Post 3", content: "Content for Post 3" },
            // { title: "Post 4", content: "Content for Post 1" },
            // { title: "Post 5", content: "Content for Post 2" },
            // { title: "Post 6", content: "Content for Post 3" },
            // Add more post data as needed
        ];

        let connection = null
        async function init(){
            try {
                connection = await Connection.connect()
                let user_boards = await connection.getBoards()
                let id = parseInt(getUrlParameter('course'))
                let found = false
                let exists = false
                let name = ""
                user_boards.forEach(row => {
                    if (row.board_id == id) {
                        found = true
                        exists = true
                        name = row.board_name
                    }
                })
                if (!found) {
                    let all_boards = await connection.searchBoards("")
                    all_boards.forEach(row => {
                        if (row.board_id == id) {
                            exists = true
                            name = row.board_name
                        }
                    })
                }
                handleCourseSpecificLogic(found, exists, name)
                if (found) {
                    let result = await connection.setBoard(id)
                    if (!result.success) {
                        throw new Error("Server refused to set board")
                    }
                    posts = await connection.getBoardMessages()
                } else {
                    posts = []
                }
                displayPosts()
                return found
            } catch (error) {
                if (error instanceof TypeError) {
                    throw error
                }
                window.location.replace("../landingpage.html")
            }
        }

        function legibleTime(time) {
            let returnTime = 0;
            let returnString = ""
            if (time / 1000 < 1) {
                returnTime = Math.floor(time)
                returnString = " millisecond";
            } else if (time / (60*1000) < 1) {
                returnTime = Math.floor(time / 1000)
                returnString = " second"
            } else if (time / (60*60*1000) < 1) {
                returnTime = Math.floor(time / (60*1000))
                returnString = " minute"
            } else if (time / (24*60*60*1000) < 1) {
                returnTime = Math.floor(time / (60*60*1000))
                returnString = " hour"
            } else if (time / (7*24*60*60*1000) < 1) {
                returnTime = Math.floor(time / (24*60*60*1000))
                returnString = " day"
            } else if (time / (30*24*60*60*1000) < 1) {
                returnTime = Math.floor(time / (7*24*60*60*1000))
                returnString = " week"
            } else if (time / (365*24*60*60*1000) < 1) {
                returnTime = Math.floor(time / (30*24*60*60*1000))
                returnString = " month"
            } else {
                returnTime = Math.floor(time / (365*24*60*60*1000))
                returnString = " year"
            }
            return returnTime + returnString + (returnTime > 1 ? "s" : "")
        }

        // Function to extract URL parameters (e.g., 'course' from the query string)
        function getUrlParameter(name) {
            name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
            const regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
            const results = regex.exec(location.search);
            return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
        }
        
        // Logic specific to handling course-related features
        function handleCourseSpecificLogic(isValidUser, boardExists, courseName) {
            // Create a new element for the welcome section
            const welcomeSection = document.createElement('div');
            welcomeSection.id = 'welcomeSection';
            welcomeSection.className = 'welcome';

            // Add a spacer div to adjust the height
            const spacerDiv = document.createElement('div');
            spacerDiv.className = 'spacer';
            welcomeSection.appendChild(spacerDiv);

            // Create an inner text container
            const welcomeTextDiv = document.createElement('div');
            welcomeTextDiv.className = 'welcome-text';

            // Add welcome message and course name
            // <h1>Discussion Board for ${courseName}</h1>
            welcomeTextDiv.innerHTML = `
            <h1>Discussion Board for ${courseName}</h1>
            `;

            // Append the text container to the welcome section
            welcomeSection.appendChild(welcomeTextDiv);

            // Add the welcome section to the content area
            const contentArea = document.getElementById('contentArea');
            contentArea.prepend(welcomeSection);

            if (!boardExists) {
                welcomeTextDiv.innerHTML = "<h1>Board not found</h1>"
                return
            }

            if (!isValidUser) {
                welcomeTextDiv.innerHTML = "<h3>You must be a member of this board to view the content</h3><div id='join' style='width:100%'>Request to join</div>"
                document.getElementById("join").addEventListener('click', function () {
                    alert('Application denied')
                })
                return
            }

            // Create Manage Board Members button
            const manageBoardMemberButton = document.createElement('div');
            manageBoardMemberButton.className = 'manage-board-member';
            manageBoardMemberButton.id = 'manageBoardMember';
            manageBoardMemberButton.textContent = 'Manage Board Members';
    
            // Create Manage Channels button
            const manageChannelsButton = document.createElement('div');
            manageChannelsButton.className = 'manage-channels';
            manageChannelsButton.id = 'manageChannels';
            manageChannelsButton.textContent = 'Manage Channels';

            // Create Manage Channel Members button
            const manageChannelMembersButton = document.createElement('div');
            manageChannelMembersButton.className = 'manage-channel-members';
            manageChannelMembersButton.id = 'manageChannelMembers';
            manageChannelMembersButton.textContent = 'Manage Channel Members';

            // Append buttons to the welcome section
            welcomeSection.appendChild(manageBoardMemberButton);
            welcomeSection.appendChild(manageChannelsButton);
            welcomeSection.appendChild(manageChannelMembersButton); 

            //  Event listeners for buttons:
            document.getElementById('manageBoardMember').addEventListener('click', function() {
                try {
                    window.location.href = '../cgi_bin/manageMembers.php';
                } catch (error) {
                    console.error('Error navigating to manageMembers.php:', error);
                }
            });

            document.getElementById('manageChannels').addEventListener('click', function() {
                try {
                    window.location.href = '../cgi_bin/manageChannels.php';
                } catch (error) {
                    console.error('Error navigating to manageChannels.php:', error);
                }
            });

            document.getElementById('manageChannelMembers').addEventListener('click', function() {
                try {
                    window.location.href = '../cgi_bin/manageChannelMembers.php';
                } catch (error) {
                    console.error('Error navigating to manageChannelMembers.php:', error);
                }
            });

        }
        
        // Event listener for 'Manage System' button click
        document.getElementById('managesystem').addEventListener('click', function() {
            const welcomeSection = document.getElementById('welcomeSection');
            welcomeSection.style.display = 'block'; // Show the welcome section

            // Clear the previously displayed post content
            const postDisplayArea = document.getElementById('postDisplayArea');
                postDisplayArea.innerHTML = '';

            // Hide 'Make a Post' form section
            const makepostSection = document.getElementById('makepostSection');
            if (makepostSection) {
                makepostSection.style.display = 'none';
            }

        });
        
        // Display make a post page
        document.addEventListener('DOMContentLoaded', function() {
            init().then(result => {
                if (result) {
                    // Event listener for 'Make a Post'
                    document.getElementById('makepost').addEventListener('click', function() {
                        
                        // Hide welcome section
                        if (welcomeSection) {
                            welcomeSection.style.display = 'none';
                        }

                        // Clear the previously displayed post content
                        const postDisplayArea = document.getElementById('postDisplayArea');
                        postDisplayArea.innerHTML = '';

                        // Display the 'Make a Post' form section
                        const makepostSection = document.getElementById('makepostSection');
                        makepostSection.style.display = 'block';
                    });
                }
            })
        });
    
        // Function to display posts in the sidebar
        function displayPosts(filteredPosts = posts) {
            const postList = document.querySelector('.post-list');
            postList.innerHTML = ''; // Clear existing posts
    
            filteredPosts.forEach(post => {
                const postElement = document.createElement('div');
                postElement.classList.add('sidebar-link');
                postElement.textContent = post.message_title //post.title;
                postElement.onclick = () => displayPostContent(post);
                postList.appendChild(postElement);
            });

        }
    
        // Function to display post content
        function displayPostContent(post) {
            const contentArea = document.getElementById('postDisplayArea');
//            const postContent = `<h3>${post.title}</h3><p>${post.content}</p>`;
            const postContent = `<h3>${post.message_title} <span class='channel' style='background-color: hsl(` + ((post.channel_id*130)%360) + `,100%,75%)'>${post.channel_name}</span></h3><h4>${post.author} <span class="time">` + legibleTime(new Date() - new Date(post.message_time)) + ` ago</span></h4><p>${post.message_text}</p>`;
            
            // Add a textarea for comments
            const commentSection = `
                <div class="comment-section">
                    <textarea class="form-control" id="commentText" rows="2" placeholder="Add a comment..."></textarea>
                    <button class="btn btn-primary" id="postComment">Post Comment</button>
                </div>
            `;

            // Replace existing content in contentArea with the new post content
            contentArea.innerHTML = postContent + commentSection;

        // Add event listener to the Post Comment button
        document.getElementById('postComment').addEventListener('click', function() {
            postComment(post.title); // You might want to pass an identifier for the post
        });

            // Hide 'Make a Post' form section
            const makepostSection = document.getElementById('makepostSection');
            if (makepostSection) {
                makepostSection.style.display = 'none';
            }
            
            // Hide welcome section
            if (welcomeSection) {
                welcomeSection.style.display = 'none';
            }
        }

        // Initial display of all posts
        displayPosts(); 

        // Event listener for search functionality
        document.querySelector('.search-box').addEventListener('input', function(event) {
            const searchTerm = event.target.value.toLowerCase();
            const filteredPosts = posts.filter(post => post.title.toLowerCase().includes(searchTerm));
            displayPosts(filteredPosts);

        });

        function postComment(postTitle) {
            const commentText = document.getElementById('commentText').value;
            // Logic to handle the comment post
            // For example, send the comment to your server and then update the UI
            console.log("Comment on post", postTitle, ":", commentText);
            // Clear the comment textarea after posting
            document.getElementById('commentText').value = '';
        }*/

    </script>
    
</body>
</html>
