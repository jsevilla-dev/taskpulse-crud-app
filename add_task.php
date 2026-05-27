<?php
include 'db.php';

if (isset($_POST['submit'])) {
    
    // 1. Grab the data and trim empty spaces
    $title = trim($_POST['title']);
    $category = $_POST['category'];

    // ==========================================
    // ADVANCED HEURISTIC SPAM & CONTEXT FILTER
    // ==========================================
    
    // Rule A: Must be at least 4 characters long
    if (strlen($title) < 4) {
        header("Location: index.php?error=too_short");
        exit();
    }

    // Rule B: Cannot be purely numbers
    if (is_numeric($title)) {
        header("Location: index.php?error=numbers_only");
        exit();
    }

    // Rule C: Detect keyboard smashes (e.g., "aaaaaa" or "hhhhhh")
    if (preg_match('/(.)\1{4,}/', $title)) {
        header("Location: index.php?error=spam");
        exit();
    }

    // Rule D: Must contain at least one actual letter
    if (!preg_match('/[a-zA-Z]/', $title)) {
        header("Location: index.php?error=no_letters");
        exit();
    }

    // Rule E: Context Check (Must be at least two words)
    // A real task usually has an action and a target (e.g., "Fix bug" instead of just "Bug")
    if (str_word_count($title) < 2) {
        header("Location: index.php?error=needs_context");
        exit();
    }

    // Rule F: Known Gibberish Dictionary
    // Blocks common developer lazy-testing words
    $gibberish = ['asdf', 'qwer', 'zxcv', 'test test', 'blah'];
    foreach ($gibberish as $bad_word) {
        if (stripos($title, $bad_word) !== false) {
            header("Location: index.php?error=gibberish");
            exit();
        }
    }
    // ==========================================

    // 2. Clean the data to prevent SQL Injection
    $title = mysqli_real_escape_string($conn, $title);
    $category = mysqli_real_escape_string($conn, $category);

    // 3. Write and execute the SQL instruction
    $sql = "INSERT INTO tasks (title, category) VALUES ('$title', '$category')";

    if (mysqli_query($conn, $sql)) {
        header("Location: index.php"); 
        exit();
    } else {
        echo "Error saving task: " . mysqli_error($conn);
    }
}
?>