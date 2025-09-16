// ---Buttons---
const addToDo = document.querySelector("#create-button");
const clearToDo = document.querySelector("#clear-button");
const darkModeToggle = document.querySelector("#themeToggle");

const modal = document.querySelector("#taskModal");
const closeBtn = document.querySelector(".close");
const form = document.querySelector("#taskForm");
// const removeBtn = document.querySelectorAll(".remove-btn");
// const editBtn = document.querySelectorAll(".edit-btn");

// ---Filter Buttons---
const filterButtons = document.querySelectorAll("#filterBtn");
const searchBar = document.querySelector("#search");

// ---Form---
const taskForm = document.querySelector("#taskForm");
const taskInput = document.querySelector("#taskTitle");
const taskCategory = document.querySelector("#category");
const taskDescription = document.querySelector("#description");
const taskDue = document.querySelector("#dueDate");
const taskStatus = document.querySelector("#status");

// ---List---
const taskList = document.querySelector("#taskList");

// ---Stats---
const statDone = document.querySelector("#statDone");
const statTotal = document.querySelector("#statTotal");
const genId = () => `${Date.now()}-${Math.random().toString(36).slice(2, 8)}`;

const taskInfo = [
  taskInput,
  taskCategory,
  taskDescription,
  taskDue,
  taskStatus,
];

// ---let---
let taskBoard = safeParse("listOfTasks");
let currentTime = new Date();
let isEditing = false;
let editTaskId = null;

// ---CRUD Operation and Data Storage using locateStorage---
const tasks = {
  addTask(
    taskTitle,
    category,
    description,
    dueDate = "None",
    status = false,
    createdAt
  ) {
    const newTask = {
      id: genId(),
      taskTitle: taskTitle.trim(),
      category,
      description: description.trim(),
      dueDate,
      status,
      createdAt: new Date().toISOString(),
    };
    taskBoard.push(newTask);
    localStorage.setItem("listOfTasks", JSON.stringify(taskBoard));
    renderTasks();
  },

  viewTask() {
    return safeParse("listOfTasks");
  },
  clearTask() {
    localStorage.removeItem("listOfTasks");
  },
};

// ---Event Listeners---
taskForm.addEventListener("submit", function (e) {
  e.preventDefault();

  const values = [];
  taskInfo.forEach((field) => values.push(field.value));

  if (isEditing) {
    const index = taskBoard.findIndex((t) => t.id === editTaskId);
    if (index !== -1) {
      taskBoard[index].taskTitle = values[0];
      taskBoard[index].category = values[1];
      taskBoard[index].description = values[2];
      taskBoard[index].dueDate = values[3];
      taskBoard[index].status = values[4];
      // Save updated list
      localStorage.setItem("listOfTasks", JSON.stringify(taskBoard));
      renderTasks();
    }
    isEditing = false;
    editTaskId = null;
    taskForm.querySelector("button[type='submit']").textContent = "Add Task";
  } else {
    tasks.addTask(...values);
  }

  closeModal();
});

filterButtons.forEach((filter) => {
  filter.addEventListener("click", (e) => {
    const dataValue = filter.dataset.value;
    console.log(dataValue);

    if (dataValue === "all") {
      renderTasks();
    } else {
      let filteredTask = taskBoard.filter(
        (t) => String(t.status) === String(dataValue)
      );
      renderTasks(filteredTask);
    }
  });
});

searchBar.addEventListener("input", function () {
  const searchInput = searchBar.value;
  const filterSearch = searchTask(taskBoard, searchInput);
  renderTasks(filterSearch);
});

addToDo.addEventListener("click", () => {
  modal.style.display = "block";
});

window.addEventListener("click", (e) => {
  if (e.target === modal) {
    modal.style.display = "none";
    closeModal();
  }
});

closeBtn.addEventListener("click", closeModal);
clearToDo.addEventListener("click", tasks.clearTask);

// ---Functions---
function renderTasks(tasks = taskBoard) {
  taskList.innerHTML = "";

  tasks.forEach((task) => {
    const card = document.createElement("div");
    card.className = "task";
    card.dataset.id = task.id;

    const details = document.createElement("div");
    details.className = "task-details";

    const title = document.createElement("span");
    title.className = "task-title";
    title.textContent = task.taskTitle;

    const category = document.createElement("span");
    category.className = "task-category";
    category.textContent = `Category: ${task.category}`;

    const desc = document.createElement("span");
    desc.className = "task-desc";
    desc.textContent = `Description: ${task.description}`;

    const due = document.createElement("span");
    due.className = "task-due";
    due.textContent = `Due: ${task.dueDate}`;

    const status = document.createElement("span");
    status.className = "task-status";
    status.textContent = `Status: ${task.status}`;

    const timestamp = document.createElement("span");
    timestamp.className = "task-timestamp";
    timestamp.textContent = `Timestamp: ${task.createdAt}`;

    details.append(title, category, desc, due, status, timestamp);

    const actions = document.createElement("div");
    actions.className = "task-actions";

    const editBtn = document.createElement("button");
    editBtn.className = "edit-btn";
    editBtn.dataset.id = task.id;
    editBtn.type = "button";
    editBtn.textContent = "✏️ Edit";
    editBtn.addEventListener("click", editTask);

    const removeBtn = document.createElement("button");
    removeBtn.className = "remove-btn";
    removeBtn.dataset.id = task.id;
    removeBtn.type = "button";
    removeBtn.textContent = "🗑 Remove";
    removeBtn.addEventListener("click", removeTask);

    actions.append(editBtn, removeBtn);

    card.append(details, actions);
    taskList.append(card);
  });

  statTotal.textContent = `${tasks.length}`;
}

function clearTasks() {
  console.log("Clearing Task Data");
  localStorage.clear();
  let emptyCard = document.createElement("div");
  emptyCard.className = "empty";
  taskList.innerHTML = "";
  emptyCard.textContent = "No tasks yet. Add one above 👆";
  taskList.appendChild(emptyCard);
  statTotal.textContent = `0`;
}

// Good Search Bar
function searchTask(array, task) {
  const q = task.toLowerCase();
  return array.filter((t) =>
    Object.keys(t).some((k) => String(t[k]).toLowerCase().includes(q))
  );
}

function removeTask() {
  const taskId = this.dataset.id;
  const index = taskBoard.findIndex((n) => n.id === taskId);
  console.log(taskId);
  console.log(index);
  if (index !== -1) {
    taskBoard.splice(index, 1);
    localStorage.setItem("listOfTasks", JSON.stringify(taskBoard));
    renderTasks();
  }
}

function editTask() {
  const taskId = this.dataset.id;
  const index = taskBoard.findIndex((n) => n.id === taskId);
  console.log(taskId);
  console.log(index);
  if (index !== -1) {
    const task = taskBoard[index];
    taskInfo.forEach((field, i) => {
      field.value = task[Object.keys(task)[i + 1]];
    });

    isEditing = true;
    editTaskId = taskId;

    const submitBtn = taskForm.querySelector("button[type='submit']");
    submitBtn.textContent = "Update Task";

    modal.style.display = "block";
  }
}

function closeModal() {
  modal.style.display = "none";
  taskForm.reset();
  isEditing = false;
  editTaskId = null;

  const submitBtn = taskForm.querySelector("button[type='submit']");
  submitBtn.textContent = "Add Task";
}

function safeParse(key) {
  const raw = localStorage.getItem(key);
  try {
    return raw && raw !== "undefined" ? JSON.parse(raw) : [];
  } catch {
    return [];
  }
}

renderTasks();
