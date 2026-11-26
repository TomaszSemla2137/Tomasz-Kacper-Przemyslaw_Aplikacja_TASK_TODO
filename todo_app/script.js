    const API_URL = 'api.php';

    // Pobranie i wyrenderowanie zadań przy starcie
    async function loadTasks() {
        const res = await fetch(API_URL);
        const tasks = await res.json();
        const list = document.getElementById('taskList');
        list.innerHTML = '';

        tasks.forEach(task => {
            const li = document.createElement('li');
            li.innerHTML = `
                <span class="${task.is_completed == 1 ? 'completed' : ''}" 
                      style="cursor: pointer;" 
                      onclick="toggleTask(${task.id}, ${task.is_completed == 1 ? 0 : 1})">
                    ${task.title}
                </span>
                <button class="delete-btn" onclick="deleteTask(${task.id})">Usuń</button>
            `;
            list.appendChild(li);
        });
    }

    // Wysłanie nowego zadania metodą POST
    async function addTask() {
        const input = document.getElementById('taskInput');
        if (!input.value.trim()) return;

        await fetch(API_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ title: input.value })
        });
        input.value = '';
        loadTasks();
    }

    // Aktualizacja statusu metodą PATCH
    async function toggleTask(id, newStatus) {
        await fetch(API_URL, {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: id, is_completed: newStatus })
        });
        loadTasks();
    }

    // Usunięcie rekordu metodą DELETE
    async function deleteTask(id) {
        await fetch(API_URL, {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: id })
        });
        loadTasks();
    }

    // Inicjalizacja
    loadTasks();