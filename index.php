<?php
// ==========================================================================
// DATABASE CONNECTION & LOGIC
// ==========================================================================
include 'db.php';

// Fetch all tasks from the database (Newest first)
$query = "SELECT * FROM tasks ORDER BY id DESC";
$result = mysqli_query($conn, $query);

// Calculate Statistics for the Dashboard
$total_query = mysqli_query($conn, "SELECT COUNT(*) as count FROM tasks");
$total_tasks = mysqli_fetch_assoc($total_query)['count'];

$completed_query = mysqli_query($conn, "SELECT COUNT(*) as count FROM tasks WHERE status = 'Completed'");
$completed_tasks = mysqli_fetch_assoc($completed_query)['count'];

$pending_tasks = $total_tasks - $completed_tasks;
$progress_percentage = $total_tasks > 0 ? round(($completed_tasks / $total_tasks) * 100) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TaskPulse | Developer Dashboard</title>
    
    <style>
        /* ==========================================================================
           FONTS & VARIABLES
           ========================================================================== */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

        :root {
            /* Color Palette */
            --bg-color: #f8fafc;        /* Slate 50 */
            --surface-color: #ffffff;   /* White */
            --text-main: #0f172a;       /* Slate 900 */
            --text-muted: #64748b;      /* Slate 500 */
            --border-color: #e2e8f0;    /* Slate 200 */
            
            /* Accents */
            --primary: #4f46e5;         /* Indigo 600 */
            --primary-hover: #4338ca;   /* Indigo 700 */
            --success: #10b981;         /* Emerald 500 */
            --warning: #f59e0b;         /* Amber 500 */
            --danger: #ef4444;          /* Red 500 */

            /* Typography */
            --font-sans: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            
            /* Shadows */
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        /* ==========================================================================
           RESET & BASE STYLES
           ========================================================================== */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: var(--font-sans);
            background-color: var(--bg-color);
            color: var(--text-main);
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
            padding: 3rem 1rem;
            display: flex;
            justify-content: center;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        /* ==========================================================================
           LAYOUT & CONTAINERS
           ========================================================================== */
        .app-container {
            width: 100%;
            max-width: 800px;
        }

        /* ==========================================================================
           HEADER & DASHBOARD STATS
           ========================================================================== */
        .app-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .app-header h1 {
            font-size: 2.25rem;
            font-weight: 800;
            color: var(--text-main);
            letter-spacing: -0.025em;
        }

        .app-header p {
            color: var(--text-muted);
            font-weight: 500;
            margin-top: 0.25rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.25rem;
            margin-bottom: 1.5rem;
        }

        .stat-card {
            background-color: var(--surface-color);
            padding: 1.5rem;
            border-radius: 12px;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-sm);
            text-align: center;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            color: var(--text-main);
        }

        .stat-label {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-top: 0.25rem;
        }

        /* Progress Bar Widget */
        .progress-widget {
            background-color: var(--surface-color);
            padding: 1.5rem;
            border-radius: 12px;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-sm);
            margin-bottom: 2.5rem;
        }

        .progress-header {
            display: flex;
            justify-content: space-between;
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
        }

        .progress-track {
            width: 100%;
            height: 8px;
            background-color: #e0e7ff;
            border-radius: 9999px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background-color: var(--primary);
            border-radius: 9999px;
            transition: width 0.6s ease-out;
        }

        /* ==========================================================================
           FORMS & INPUTS
           ========================================================================== */
        .input-section {
            background-color: var(--surface-color);
            padding: 1rem;
            border-radius: 12px;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-sm);
            margin-bottom: 2rem;
        }

        .task-form {
            display: flex;
            gap: 1rem;
        }

        .form-input {
            flex: 1;
            padding: 0.75rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-family: inherit;
            font-size: 0.95rem;
            outline: none;
            transition: border-color 0.2s;
        }

        .form-input:focus {
            border-color: var(--primary);
        }

        .form-select {
            padding: 0.75rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-family: inherit;
            font-size: 0.95rem;
            font-weight: 500;
            color: var(--text-main);
            outline: none;
            cursor: pointer;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
            border: none;
            padding: 0 1.5rem;
            border-radius: 8px;
            font-family: inherit;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
        }

        /* ==========================================================================
           MANAGEMENT UTILITIES (SEARCH & FILTERS)
           ========================================================================== */
        .management-bar {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .search-wrapper {
            position: relative;
            width: 100%;
        }

        .search-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid var(--border-color);
            background-color: var(--surface-color);
            border-radius: 8px;
            font-family: inherit;
            font-size: 0.9rem;
            outline: none;
            box-shadow: var(--shadow-sm);
        }

        .search-input:focus {
            border-color: var(--primary);
        }

        .filter-tabs {
            display: flex;
            gap: 0.5rem;
        }

        .filter-btn {
            background: none;
            border: 1px solid var(--border-color);
            background-color: var(--surface-color);
            padding: 0.5rem 1rem;
            font-family: inherit;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-muted);
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .filter-btn:hover {
            border-color: var(--text-muted);
            color: var(--text-main);
        }

        .filter-btn.active {
            background-color: var(--text-main);
            border-color: var(--text-main);
            color: white;
        }

        /* ==========================================================================
           TASK LIST & ITEMS
           ========================================================================== */
        .task-list {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .task-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.25rem;
            background-color: var(--surface-color);
            border-radius: 12px;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-sm);
            border-left: 4px solid var(--border-color);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .task-item:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        /* Category Color Coding */
        .task-item[data-category="Coding"] { border-left-color: var(--primary); }
        .task-item[data-category="School"] { border-left-color: var(--warning); }
        .task-item[data-category="Personal"] { border-left-color: var(--success); }

        .task-info {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .task-title {
            font-size: 1.05rem;
            font-weight: 600;
            color: var(--text-main);
        }

        .task-title.Completed {
            text-decoration: line-through;
            color: var(--text-muted);
        }

        /* Badges */
        .badge {
            display: inline-block;
            padding: 0.25rem 0.6rem;
            font-size: 0.75rem;
            font-weight: 700;
            border-radius: 6px;
            width: fit-content;
        }

        .badge.Coding { background-color: #e0e7ff; color: #3730a3; }
        .badge.School { background-color: #fef3c7; color: #92400e; }
        .badge.Personal { background-color: #d1fae5; color: #065f46; }

        /* Actions */
        .task-actions {
            display: flex;
            gap: 0.5rem;
        }

        .btn-action {
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
            font-weight: 600;
            border-radius: 6px;
            transition: all 0.2s;
        }

        .btn-complete {
            background-color: #f0fdf4;
            color: var(--success);
        }
        .btn-complete:hover {
            background-color: var(--success);
            color: white;
        }

        .btn-delete {
            background-color: #fef2f2;
            color: var(--danger);
        }
        .btn-delete:hover {
            background-color: var(--danger);
            color: white;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: var(--text-muted);
            font-style: italic;
            background-color: var(--surface-color);
            border-radius: 12px;
            border: 1px dashed var(--border-color);
        }
    </style>
</head>
<body>

    <main class="app-container">
        
        <header class="app-header">
            <h1>TaskPulse</h1>
            <p>Developer Operations Dashboard</p>
        </header>

        <section class="stats-grid">
            <div class="stat-card">
                <div class="stat-value"><?php echo $total_tasks; ?></div>
                <div class="stat-label">Total Tasks</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" style="color: var(--success);"><?php echo $completed_tasks; ?></div>
                <div class="stat-label">Completed</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" style="color: var(--warning);"><?php echo $pending_tasks; ?></div>
                <div class="stat-label">Pending</div>
            </div>
        </section>

        <section class="progress-widget">
            <div class="progress-header">
                <span>System Completion Rate</span>
                <span style="color: var(--primary);"><?php echo $progress_percentage; ?>%</span>
            </div>
            <div class="progress-track">
                <div class="progress-fill" style="width: <?php echo $progress_percentage; ?>%;"></div>
            </div>
        </section>

        <?php if (isset($_GET['error'])): ?>
            <div style="background-color: #fef2f2; border-left: 4px solid var(--danger); color: #991b1b; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.9rem; font-weight: 500; display: flex; align-items: center; gap: 8px;">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <?php 
                    if ($_GET['error'] == 'too_short') echo "Task description is too short. Please be more specific.";
                    elseif ($_GET['error'] == 'numbers_only') echo "Tasks cannot be just numbers. Add some text.";
                    elseif ($_GET['error'] == 'spam') echo "Repeated characters detected. Please enter a valid task.";
                    elseif ($_GET['error'] == 'no_letters') echo "Your task must contain at least one letter.";
                    elseif ($_GET['error'] == 'needs_context') echo "Tasks need more context. Use at least two words (e.g., 'Read chapter' instead of just 'Read').";
                    elseif ($_GET['error'] == 'gibberish') echo "Meaningless text detected. Please enter a real objective.";
                ?>
            </div>
        <?php endif; ?>
        
        <section class="input-section">
            <form class="task-form" action="add_task.php" method="POST">
                <input type="text" name="title" class="form-input" placeholder="Initialize new directive..." required>
                <select name="category" class="form-select">
                    <option value="Coding">Coding</option>
                    <option value="School">School</option>
                    <option value="Personal">Personal</option>
                </select>
                <button type="submit" name="submit" class="btn-primary">Add Task</button>
            </form>
        </section>

        <section class="management-bar">
            <div class="search-wrapper">
                <input type="text" id="taskSearch" class="search-input" placeholder="Search directives instantly...">
            </div>
            <div class="filter-tabs">
                <button class="filter-btn active" data-filter="all">All Metrics</button>
                <button class="filter-btn" data-filter="Coding">Coding</button>
                <button class="filter-btn" data-filter="School">School</button>
                <button class="filter-btn" data-filter="Personal">Personal</button>
            </div>
        </section>

        <section class="task-list" id="taskList">
            <?php 
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    ?>
                    <article class="task-item" data-category="<?php echo $row['category']; ?>">
                        <div class="task-info">
                            <span class="task-title <?php echo $row['status']; ?>">
                                <?php echo htmlspecialchars($row['title']); ?>
                            </span>
                            <span class="badge <?php echo $row['category']; ?>">
                                <?php echo $row['category']; ?>
                            </span>
                        </div>
                        
                        <div class="task-actions">
                            <?php if ($row['status'] == 'Pending'): ?>
                                <a href="update_task.php?id=<?php echo $row['id']; ?>" class="btn-action btn-complete">Complete</a>
                            <?php endif; ?>
                            <a href="delete_task.php?id=<?php echo $row['id']; ?>" class="btn-action btn-delete" onclick="return confirm('Purge this task from the database?')">Delete</a>
                        </div>
                    </article>
                    <?php
                }
            } else {
                echo '<div class="empty-state">System operational. Awaiting task input.</div>';
            }
            ?>
        </section>

    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const searchInput = document.getElementById('taskSearch');
            const filterButtons = document.querySelectorAll('.filter-btn');
            const taskItems = document.querySelectorAll('.task-item');

            // Master Filter Engine
            function dynamicFilter() {
                const searchQueries = searchInput.value.toLowerCase();
                const activeFilter = document.querySelector('.filter-btn.active').getAttribute('data-filter');

                taskItems.forEach(item => {
                    const taskTitle = item.querySelector('.task-title').textContent.toLowerCase();
                    const taskCategory = item.getAttribute('data-category');

                    const matchSearch = taskTitle.includes(searchQueries);
                    const matchCategory = (activeFilter === 'all' || taskCategory === activeFilter);

                    if (matchSearch && matchCategory) {
                        item.style.display = 'flex';
                    } else {
                        item.style.display = 'none';
                    }
                });
            }

            // Bind Event Listener to Search Input
            searchInput.addEventListener('input', dynamicFilter);

            // Bind Event Listeners to Tab Buttons
            filterButtons.forEach(button => {
                button.addEventListener('click', () => {
                    filterButtons.forEach(btn => btn.classList.remove('active'));
                    button.classList.add('active');
                    dynamicFilter();
                });
            });
        });
    </script>
</body>
</html>