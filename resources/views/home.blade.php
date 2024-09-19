@include('layouts.app')
<link rel="stylesheet" href="/css/page/home.css">
<style>
    .content {
        all: unset;
    }

    /* Scoped styles inside the project-page container */
    .project-page {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        font-family: Arial, sans-serif;
        background-color: #edf4f0; /* Secondary color */
    }

    .project-page .container {
        width: 60%;
        background: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .project-page .row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .project-page .row h1,
    .tasks h1{
        font-size: 24px;
        color: #4caf93; /* Primary color */
    }

    .project-page .create-project-link {
        text-decoration: none;
        color: #4caf93; /* Primary color for link */
        font-weight: bold;
        font-size: 16px;
    }

    .project-page .create-project-link:hover {
        color: #388f70; /* Darker hover effect */
        text-decoration: underline;
    }

    .project-page .projects,
    .project-page .tasks {
        margin-bottom: 30px;
    }

    .project-page ul {
        list-style-type: none;
    }

    /* List item styling for both Projects and Tasks */
    .project-page ul li {
        padding: 10px;
        background-color: #edf4f0; /* Secondary color for list items */
        margin-bottom: 10px;
        border-radius: 5px;
        border: 1px solid #ddd;
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer; /* Makes the item look clickable */
    }

    /* Project/Task details aligned on the left */
    .project-page ul li .details {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
    }

    /* Right-aligned small info section for tasks and members */
    .project-page ul li .info {
        font-size: 14px;
        color: #555;
        text-align: right;
    }

    /* Hover effect - change background to primary color */
    .project-page ul li:hover {
        background-color: #4caf93; /* Primary color on hover */
        color: white; /* Change text color for better contrast */
    }

    .project-page ul li a {
        text-decoration: none;
        color: inherit;
        width: 100%;
        display: flex;
        justify-content: space-between;
    }

    .project-page .tasks h2,
    .project-page .projects h2 {
        font-size: 20px;
        color: #4caf93; /* Primary color */
    }

    .project-page ul li .task-project {
        font-weight: bold;
        margin-bottom: 5px;
    }
</style>
<main class="content">
    <div class="project-page">
        <div class="container">
            <!-- First Row: Projects Title and Create Project Link -->
            <div class="row">
                <h1>Projects</h1>
                <a href="#" class="create-project-link">Create Project</a>
            </div>

            <!-- Second Row: List of Projects -->
            <div class="projects">
                <ul id="projects-list">
                    <!-- Projects will be dynamically injected here -->
                </ul>
            </div>

            <!-- Second Section: Tasks Assigned to Me -->
            <div class="tasks">
                <h1 style="margin-bottom: 20px">Tasks Assigned to Me</h1>
                <ul id="tasks-list">
                    <!-- Tasks will be dynamically injected here -->
                </ul>
            </div>
        </div>
    </div>

    <script>
        // Mock Data for Projects and Tasks
        const projects = [
            { id: 1, name: "Project Alpha", role: "Manager", tasks_count: 5, members_count: 3 },
            { id: 2, name: "Project Beta", role: "Developer", tasks_count: 3, members_count: 2 },
            { id: 3, name: "Project Gamma", role: "Tester", tasks_count: 7, members_count: 4 }
        ];

        const tasks = [
            { id: 1, description: "Task A for Project Alpha", due_date: "Oct 10", project: { name: "Project Alpha" } },
            { id: 2, description: "Task B for Project Beta", due_date: "Oct 12", project: { name: "Project Beta" } },
            { id: 3, description: "Task C for Project Gamma", due_date: "Oct 15", project: { name: "Project Gamma" } }
        ];

        // Injecting Projects into the Projects List
        const projectsList = document.getElementById('projects-list');
        projects.forEach(project => {
            const projectItem = document.createElement('li');
            projectItem.innerHTML = `
                <a href="#">
                <div class="details">
                    ${project.name} (${project.role})
                </div>
                <div class="info">
                    ${project.tasks_count} tasks<br>
                    ${project.members_count} members
                </div>
                </a>
            `;
            projectsList.appendChild(projectItem);
        });

        // Injecting Tasks into the Tasks List
        const tasksList = document.getElementById('tasks-list');
        tasks.forEach(task => {
            const taskItem = document.createElement('li');
            taskItem.innerHTML = `
            <a href="#">
 <div class="details">
                    <div class="task-project">${task.project.name}</div>
                    <div>${task.description}</div>
                </div>
                <div class="info">
                    Due: ${task.due_date}
                </div>
</a>

            `;
            tasksList.appendChild(taskItem);
        });
    </script>
</main>
@include('layouts.footer')
