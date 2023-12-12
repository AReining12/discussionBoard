// quitCourse.js

// Add event listeners for the quit course buttons
function addQuitCourseListeners() {
    document.querySelectorAll('.quit-course-btn').forEach(button => {
        button.addEventListener('click', function () {
            const boardId = this.getAttribute('data-boardid');
            if (confirm('Are you sure you want to quit this course?')) {
                quitCourse(boardId);
            }
        });
    });
}

// Function to quit a course
function quitCourse(boardId) {
    // Send an AJAX request to the backend to handle quitting the course
    fetch('../cgi_bin/quitCourse.php', {
        method: 'POST',
        body: JSON.stringify({ boardId: boardId }),
        headers: { 'Content-Type': 'application/json' }
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Successfully quit the course!');
                // Update the UI or redirect the user here
            } else {
                alert('Failed to quit the course: ' + data.error);
            }
        })
        .catch(error => console.error('Error:', error));
}

// Call this function at an appropriate time, like after course cards are loaded
addQuitCourseListeners();
