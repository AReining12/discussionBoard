<?php

$filename = "courses.csv";

// 检查是否为 POST 请求
$isPostRequest = $_SERVER["REQUEST_METHOD"] == "POST";

// 仅当为 POST 请求时处理课程创建
if ($isPostRequest) {
    $courseName = $_POST['courseName'];
    $courseSection = $_POST['courseSection'];
    $semester = $_POST['semester'];

    // 简单的表单验证
    if(empty($courseName) || empty($courseSection) || empty($semester)) {
        echo "<script type='text/javascript'>
                alert('Please fill all the fields');
                window.location.href='your_create_course_page.php';
              </script>";
        exit;
    }

    $file = fopen($filename, "a");
    if ($file === false) {
        echo "<script type='text/javascript'>
                alert('Error opening file');
                window.location.href='your_create_course_page.php';
              </script>";
        exit;
    }

    fputcsv($file, array($courseName, $courseSection, $semester));
    fclose($file);

    echo "<script type='text/javascript'>
            alert('New course created successfully');
            window.location.href='your_create_course_page.php';
          </script>";
    exit;
}

// 非 POST 请求时，返回所有课程的 JSON 数据
header('Content-Type: application/json');
echo json_encode(getCourses());

// 函数：获取所有课程
function getCourses() {
    global $filename;
    $courses = array();
    if (($file = fopen($filename, "r")) !== FALSE) {
        while (($row = fgetcsv($file)) !== FALSE) {
            $courses[] = $row;
        }
        fclose($file);
    }
    return $courses;
}

// 函数：搜索课程
function searchCourses($searchTerm) {
    $courses = getCourses();
    $searchResults = array();
    foreach ($courses as $course) {
        if (strpos(strtolower($course[0]), strtolower($searchTerm)) !== FALSE ||
            strpos(strtolower($course[1]), strtolower($searchTerm)) !== FALSE ||
            strpos(strtolower($course[2]), strtolower($searchTerm)) !== FALSE) {
            $searchResults[] = $course;
        }
    }
    return $searchResults;
}
?>
