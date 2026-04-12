<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>لوحة تحكم المهام البرمجية</title>
  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Google Fonts - Inter for clean typography -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
    }
  </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-indigo-50 to-purple-100 p-4 text-right">

  <!-- Loading Overlay -->
  <div id="loading-overlay" class="fixed inset-0 bg-gray-200 bg-opacity-75 flex justify-center items-center z-50 hidden">
    <div class="text-xl font-semibold text-gray-700">جارٍ التحميل...</div>
  </div>

  <!-- Custom Alert Modal -->
  <div id="custom-alert-modal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-[1000] hidden">
    <div class="bg-white p-8 rounded-lg shadow-xl max-w-sm w-full text-center">
      <p id="alert-message" class="text-lg text-gray-800 mb-6"></p>
      <button id="alert-ok-button" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-lg shadow-md transition duration-300">موافق</button>
    </div>
  </div>

  <!-- User ID Display -->
  <div class="text-sm text-gray-600 mb-4 rounded-lg bg-white p-3 shadow-md">
    معرف المستخدم الخاص بك: <span id="display-user-id" class="font-mono text-indigo-700 select-all"></span>
  </div>

  <!-- Main Title -->
  <h1 class="text-4xl font-extrabold text-indigo-800 mb-8 text-center drop-shadow-lg">
    لوحة تحكم إدارة المهام البرمجية
  </h1>

  <!-- Dashboard Section -->
  <div class="bg-white rounded-2xl shadow-xl p-8 mb-8 border-b-4 border-indigo-300">
    <h2 class="text-3xl font-bold text-indigo-700 mb-6 border-b pb-4 border-indigo-200">ملخص لوحة التحكم</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <!-- Card: Total Tasks -->
      <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-6 text-center shadow-md transform hover:scale-105 transition-transform duration-300">
        <p id="total-tasks" class="text-5xl font-extrabold text-indigo-600 mb-2">0</p>
        <p class="text-lg text-indigo-800">إجمالي المهام</p>
      </div>
      <!-- Card: Completed Tasks -->
      <div class="bg-green-50 border border-green-200 rounded-xl p-6 text-center shadow-md transform hover:scale-105 transition-transform duration-300">
        <p id="completed-tasks" class="text-5xl font-extrabold text-green-600 mb-2">0</p>
        <p class="text-lg text-green-800">مهام مكتملة</p>
      </div>
      <!-- Card: In Progress Tasks -->
      <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 text-center shadow-md transform hover:scale-105 transition-transform duration-300">
        <p id="in-progress-tasks" class="text-5xl font-extrabold text-yellow-600 mb-2">0</p>
        <p class="text-lg text-yellow-800">مهام قيد التنفيذ</p>
      </div>
      <!-- Card: To Do Tasks -->
      <div class="bg-red-50 border border-red-200 rounded-xl p-6 text-center shadow-md transform hover:scale-105 transition-transform duration-300">
        <p id="todo-tasks" class="text-5xl font-extrabold text-red-600 mb-2">0</p>
        <p class="text-lg text-red-800">مهام لم تبدأ</p>
      </div>
    </div>

    <!-- Overall Progress Bar -->
    <div class="mb-6">
      <h3 class="text-xl font-semibold text-gray-700 mb-3">التقدم الإجمالي للمهام</h3>
      <div class="w-full bg-gray-200 rounded-full h-8 overflow-hidden shadow-inner">
        <div
          id="overall-progress-bar"
          class="bg-gradient-to-r from-teal-400 to-teal-600 h-full text-center text-white text-lg font-bold rounded-full flex items-center justify-center transition-all duration-700 ease-out"
          style="width: 0%;"
        >
          <span id="overall-progress-text">0.0</span>%
        </div>
      </div>
    </div>

    <!-- Progress by Employee -->
    <div class="mb-6">
      <h3 class="text-xl font-semibold text-gray-700 mb-3">التقدم حسب الموظف</h3>
      <div id="progress-by-employee" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Employee progress cards will be inserted here by JavaScript -->
      </div>
      <p id="no-employee-tasks" class="text-gray-500 mt-4 hidden">لا توجد مهام حاليًا لعرض التقدم حسب الموظف.</p>
    </div>
  </div>

  <!-- Add New Task Section -->
  <div class="bg-white rounded-2xl shadow-xl p-8 mb-8 border-b-4 border-emerald-300">
    <h2 class="text-3xl font-bold text-emerald-700 mb-6 border-b pb-4 border-emerald-200">إضافة مهمة جديدة</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
      <input
        type="text"
        id="newTaskName"
        placeholder="اسم المهمة"
        class="p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-400 transition duration-200"
      />
      <input
        type="text"
        id="assignedEmployee"
        placeholder="الموظف المسؤول"
        class="p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-400 transition duration-200"
      />
      <input
        type="date"
        id="startDate"
        class="p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-400 transition duration-200"
      />
      <input
        type="date"
        id="endDate"
        class="p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-400 transition duration-200"
      />
      <select
        id="status"
        class="p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-400 transition duration-200 bg-white"
      >
        <option value="To Do">لم تبدأ</option>
        <option value="In Progress">قيد التنفيذ</option>
        <option value="Done">مكتملة</option>
      </select>
      <div class="flex items-center space-x-2 space-x-reverse">
        <input
          type="range"
          id="newTaskProgress"
          min="0"
          max="100"
          step="1"
          value="0"
          oninput="document.getElementById('newTaskProgressValue').textContent = this.value; updateNewTaskStatus(this.value);"
          class="flex-grow h-2 bg-emerald-200 rounded-lg appearance-none cursor-pointer range-lg [&::-webkit-slider-thumb]:w-4 [&::-webkit-slider-thumb]:h-4 [&::-webkit-slider-thumb]:bg-emerald-500 [&::-webkit-slider-thumb]:rounded-full [&::-webkit-slider-thumb]:shadow-lg [&::-webkit-slider-thumb]:transition-transform [&::-webkit-slider-thumb]:duration-200 [&::-webkit-slider-thumb]:hover:scale-110"
        />
        <span class="text-gray-700 font-medium w-12 text-center"><span id="newTaskProgressValue">0</span>%</span>
      </div>
    </div>
    <button
      id="addTaskButton"
      class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-6 rounded-lg shadow-lg transform hover:scale-105 transition duration-300 focus:outline-none focus:ring-4 focus:ring-emerald-500 focus:ring-opacity-50 disabled:opacity-50 disabled:cursor-not-allowed"
    >
      إضافة مهمة
    </button>
  </div>

  <!-- Tasks List Section -->
  <div class="bg-white rounded-2xl shadow-xl p-8 border-b-4 border-blue-300">
    <h2 class="text-3xl font-bold text-blue-700 mb-6 border-b pb-4 border-blue-200">قائمة المهام</h2>
    <div id="tasks-list-container" class="overflow-x-auto">
      <p id="no-tasks-message" class="text-center text-gray-500 text-lg py-10 hidden">لا توجد مهام حتى الآن. ابدأ بإضافة مهام جديدة!</p>
      <table id="tasks-table" class="min-w-full bg-white rounded-lg overflow-hidden shadow-md hidden">
        <thead class="bg-blue-100 border-b-2 border-blue-200">
          <tr>
            <th class="py-3 px-4 text-right text-sm font-semibold text-blue-800 uppercase tracking-wider">المهمة</th>
            <th class="py-3 px-4 text-right text-sm font-semibold text-blue-800 uppercase tracking-wider">الموظف</th>
            <th class="py-3 px-4 text-right text-sm font-semibold text-blue-800 uppercase tracking-wider">تاريخ البدء</th>
            <th class="py-3 px-4 text-right text-sm font-semibold text-blue-800 uppercase tracking-wider">تاريخ الانتهاء</th>
            <th class="py-3 px-4 text-right text-sm font-semibold text-blue-800 uppercase tracking-wider">الحالة</th>
            <th class="py-3 px-4 text-right text-sm font-semibold text-blue-800 uppercase tracking-wider">التقدم</th>
            <th class="py-3 px-4 text-center text-sm font-semibold text-blue-800 uppercase tracking-wider">إجراءات</th>
          </tr>
        </thead>
        <tbody id="tasks-table-body">
          <!-- Task rows will be inserted here by JavaScript -->
        </tbody>
      </table>
    </div>
  </div>


  <!-- Firebase SDK Imports -->
  <script type="module">
    import { initializeApp } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-app.js";
    import { getAuth, signInAnonymously, signInWithCustomToken, onAuthStateChanged } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-auth.js";
    import { getFirestore, collection, addDoc, getDocs, updateDoc, deleteDoc, doc, onSnapshot } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-firestore.js";

    let db;
    let auth;
    let currentUserId = '';
    let app_id = '';
    let isAuthReady = false;
    let listenerSetupAttempted = false; // Flag to ensure listener is set up only once

    // Show loading overlay
    function showLoading() {
      document.getElementById('loading-overlay').classList.remove('hidden');
    }

    // Hide loading overlay
    function hideLoading() {
      document.getElementById('loading-overlay').classList.add('hidden');
    }

    // Function to show custom alert modal
    function showAlertModal(message) {
      const modal = document.getElementById('custom-alert-modal');
      const msgText = document.getElementById('alert-message');
      msgText.textContent = message;
      modal.classList.remove('hidden');
    }

    // Function to hide custom alert modal
    document.getElementById('alert-ok-button').onclick = function() {
      document.getElementById('custom-alert-modal').classList.add('hidden');
    };

    // Function to handle exponential backoff for API calls
    async function callWithRetry(fn, retries = 5, delay = 1000) {
      try {
        return await fn();
      } catch (error) {
        if (retries > 0 && (error.code === 'unavailable' || error.code === 'resource-exhausted')) {
          await new Promise(res => setTimeout(res, delay));
          return callWithRetry(fn, retries - 1, delay * 2);
        }
        throw error;
      }
    }

    // Initialize Firebase and set up authentication
    window.onload = async function() {
      showLoading();
      try {
        app_id = typeof __app_id !== 'undefined' ? __app_id : 'default-app-id';
        const firebaseConfig = typeof __firebase_config !== 'undefined' ? JSON.parse(__firebase_config) : {};

        // Log Firebase config and app_id for debugging
        console.log("Firebase Config:", firebaseConfig);
        console.log("App ID:", app_id);

        const app = initializeApp(firebaseConfig);
        db = getFirestore(app);
        auth = getAuth(app);

        // Log DB and Auth instances after initialization
        console.log("Firestore DB instance:", db);
        console.log("Firebase Auth instance:", auth);


        onAuthStateChanged(auth, async (user) => {
          if (user) {
            currentUserId = user.uid;
            console.log("User signed in:", currentUserId);
          } else {
            try {
              if (typeof __initial_auth_token !== 'undefined' && __initial_auth_token) {
                await callWithRetry(() => signInWithCustomToken(auth, __initial_auth_token));
                console.log("Signed in with custom token.");
              } else {
                await callWithRetry(() => signInAnonymously(auth));
                console.log("Signed in anonymously.");
              }
              // Ensure currentUserId is set after any sign-in attempt
              currentUserId = auth.currentUser?.uid || crypto.randomUUID(); 
              console.log("Current User ID (after auth attempt):", currentUserId);
            } catch (error) {
              console.error("Error during anonymous sign-in or custom token sign-in:", error);
              showAlertModal("فشل تسجيل الدخول. سيتم استخدام معرف مؤقت. الرجاء إعادة تحميل الصفحة إذا استمرت المشكلة.");
              currentUserId = crypto.randomUUID(); // Fallback to a random ID
              console.log("Fallback currentUserId after auth error:", currentUserId);
            }
          }
          document.getElementById('display-user-id').textContent = currentUserId;
          isAuthReady = true;
          hideLoading();
          // Attempt to set up listener only once auth is truly ready
          if (!listenerSetupAttempted) {
            setupTaskListener();
            listenerSetupAttempted = true;
          }
        });
      } catch (error) {
        console.error("Failed to initialize Firebase:", error);
        showAlertModal("حدث خطأ فادح في تهيئة Firebase. سيتم استخدام معرف مؤقت وقد لا تعمل الميزات المستندة إلى قاعدة البيانات. الرجاء إعادة تحميل الصفحة.");
        currentUserId = crypto.randomUUID(); // Fallback if Firebase init fails
        document.getElementById('display-user-id').textContent = currentUserId;
        isAuthReady = true;
        hideLoading();
        // Even if init fails, try to set up listener with fallback ID
        if (!listenerSetupAttempted) {
            setupTaskListener();
            listenerSetupAttempted = true;
        }
      }
    };

    // Function to update the dashboard UI
    function updateDashboardUI(tasks) {
      const totalTasks = tasks.length;
      const completedTasks = tasks.filter(task => task.status === 'Done').length;
      const inProgressTasks = tasks.filter(task => task.status === 'In Progress').length;
      const todoTasks = tasks.filter(task => task.status === 'To Do').length;
      const overallProgress = totalTasks > 0 ? (completedTasks / totalTasks) * 100 : 0;

      document.getElementById('total-tasks').textContent = totalTasks;
      document.getElementById('completed-tasks').textContent = completedTasks;
      document.getElementById('in-progress-tasks').textContent = inProgressTasks;
      document.getElementById('todo-tasks').textContent = todoTasks;

      const overallProgressBar = document.getElementById('overall-progress-bar');
      const overallProgressText = document.getElementById('overall-progress-text');
      if (overallProgressBar && overallProgressText) {
        overallProgressBar.style.width = `${overallProgress}%`;
        overallProgressText.textContent = overallProgress.toFixed(1);
      }

      // Update progress by employee
      const tasksByEmployee = tasks.reduce((acc, task) => {
        acc[task.assignedEmployee] = acc[task.assignedEmployee] || [];
        acc[task.assignedEmployee].push(task);
        return acc;
      }, {});

      const progressByEmployeeDiv = document.getElementById('progress-by-employee');
      progressByEmployeeDiv.innerHTML = ''; // Clear previous cards

      if (Object.keys(tasksByEmployee).length > 0) {
        document.getElementById('no-employee-tasks').classList.add('hidden');
        for (const employee in tasksByEmployee) {
          const employeeTasks = tasksByEmployee[employee];
          const empCompleted = employeeTasks.filter(t => t.status === 'Done').length;
          const empProgress = employeeTasks.length > 0 ? (empCompleted / employeeTasks.length) * 100 : 0;

          const employeeCard = `
            <div class="bg-purple-50 border border-purple-200 rounded-xl p-5 shadow-sm">
              <p class="text-lg font-medium text-purple-800 mb-2">${employee}</p>
              <div class="w-full bg-gray-200 rounded-full h-6">
                <div
                  class="bg-gradient-to-r from-purple-400 to-purple-600 h-full text-center text-white text-sm font-semibold rounded-full flex items-center justify-center transition-all duration-500"
                  style="width: ${empProgress}%"
                >
                  ${empProgress.toFixed(1)}%
                </div>
              </div>
              <p class="text-sm text-gray-600 mt-2">(${empCompleted} من ${employeeTasks.length} مهام مكتملة)</p>
            </div>
          `;
          progressByEmployeeDiv.insertAdjacentHTML('beforeend', employeeCard);
        }
      } else {
        document.getElementById('no-employee-tasks').classList.remove('hidden');
      }
    }

    // Function to render tasks in the table
    function renderTasks(tasks) {
      const tasksTableBody = document.getElementById('tasks-table-body');
      tasksTableBody.innerHTML = ''; // Clear existing rows

      if (tasks.length === 0) {
        document.getElementById('no-tasks-message').classList.remove('hidden');
        document.getElementById('tasks-table').classList.add('hidden');
      } else {
        document.getElementById('no-tasks-message').classList.add('hidden');
        document.getElementById('tasks-table').classList.remove('hidden');
        tasks.forEach(task => {
          const row = `
            <tr class="border-b border-gray-100 hover:bg-blue-50 transition duration-150 ease-in-out">
              <td class="py-3 px-4">
                <input
                  type="text"
                  value="${task.taskName || ''}"
                  onchange="updateTask('${task.id}', 'taskName', this.value)"
                  class="w-full bg-transparent border-none focus:ring-0 outline-none p-1"
                />
              </td>
              <td class="py-3 px-4">
                <input
                  type="text"
                  value="${task.assignedEmployee || ''}"
                  onchange="updateTask('${task.id}', 'assignedEmployee', this.value)"
                  class="w-full bg-transparent border-none focus:ring-0 outline-none p-1"
                />
              </td>
              <td class="py-3 px-4">
                <input
                  type="date"
                  value="${task.startDate || ''}"
                  onchange="updateTask('${task.id}', 'startDate', this.value)"
                  class="w-full bg-transparent border-none focus:ring-0 outline-none p-1"
                />
              </td>
              <td class="py-3 px-4">
                <input
                  type="date"
                  value="${task.endDate || ''}"
                  onchange="updateTask('${task.id}', 'endDate', this.value)"
                  class="w-full bg-transparent border-none focus:ring-0 outline-none p-1"
                />
              </td>
              <td class="py-3 px-4">
                <select
                  onchange="updateTask('${task.id}', 'status', this.value)"
                  class="w-full bg-transparent border-none focus:ring-0 outline-none p-1"
                >
                  <option value="To Do" ${task.status === 'To Do' ? 'selected' : ''}>لم تبدأ</option>
                  <option value="In Progress" ${task.status === 'In Progress' ? 'selected' : ''}>قيد التنفيذ</option>
                  <option value="Done" ${task.status === 'Done' ? 'selected' : ''}>مكتملة</option>
                </select>
              </td>
              <td class="py-3 px-4">
                <div class="flex items-center space-x-2 space-x-reverse">
                  <input
                    type="range"
                    min="0"
                    max="100"
                    step="1"
                    value="${task.progress || 0}"
                    oninput="document.getElementById('progress-value-${task.id}').textContent = this.value; updateTask('${task.id}', 'progress', this.value);"
                    class="flex-grow h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer range-sm [&::-webkit-slider-thumb]:w-3 [&::-webkit-slider-thumb]:h-3 [&::-webkit-slider-thumb]:bg-blue-500 [&::-webkit-slider-thumb]:rounded-full [&::-webkit-slider-thumb]:shadow-md [&::-webkit-slider-thumb]:transition-transform [&::-webkit-slider-thumb]:duration-200 [&::-webkit-slider-thumb]:hover:scale-110"
                  />
                  <span class="text-gray-700 font-medium w-10 text-center"><span id="progress-value-${task.id}">${task.progress || 0}</span>%</span>
                </div>
              </td>
              <td class="py-3 px-4 text-center">
                <button
                  onclick="deleteTask('${task.id}')"
                  class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-lg shadow-md transform hover:scale-105 transition duration-300 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-opacity-75"
                >
                  حذف
                </button>
              </td>
            </tr>
          `;
          tasksTableBody.insertAdjacentHTML('beforeend', row);
        });
      }
    }

    // Listen for real-time updates from Firestore
    function setupTaskListener() {
      // Ensure db and currentUserId are available before proceeding
      if (!db) {
        console.error("Firestore DB instance is null. Cannot set up task listener.");
        showAlertModal("حدث خطأ: لا يمكن الاتصال بقاعدة البيانات. الرجاء إعادة تحميل الصفحة.");
        return;
      }
      if (!currentUserId || currentUserId === '') {
        console.error("currentUserId is null or empty. Cannot set up task listener.");
        showAlertModal("حدث خطأ: معرف المستخدم غير متاح. الرجاء إعادة تحميل الصفحة.");
        return;
      }
      if (!isAuthReady) {
        console.warn("Authentication not yet ready. Waiting for auth state to be confirmed.");
        // This should not happen if setupTaskListener is called after isAuthReady is true
        return;
      }
      
      console.log("Attempting to set up task listener with:");
      console.log("  App ID:", app_id);
      console.log("  Current User ID:", currentUserId);

      const tasksCollectionRef = collection(db, `artifacts/${app_id}/users/${currentUserId}/programming_tasks`);
      onSnapshot(tasksCollectionRef, (snapshot) => {
        const fetchedTasks = snapshot.docs.map(doc => ({
          id: doc.id,
          ...doc.data()
        }));
        renderTasks(fetchedTasks);
        updateDashboardUI(fetchedTasks);
      }, (error) => {
        console.error("Error fetching tasks:", error);
        showAlertModal(`حدث خطأ أثناء جلب المهام: ${error.message}. الرجاء التحقق من اتصالك بالإنترنت.`);
      });
    }

    // Function to add a new task
    document.getElementById('addTaskButton').onclick = async function() {
      if (!db || !currentUserId || !isAuthReady) {
        showAlertModal("Firestore غير مهيأ أو معرف المستخدم غير متاح. الرجاء إعادة تحميل الصفحة.");
        return;
      }
      showLoading();
      const newTaskName = document.getElementById('newTaskName').value.trim();
      const assignedEmployee = document.getElementById('assignedEmployee').value.trim();
      const startDate = document.getElementById('startDate').value;
      const endDate = document.getElementById('endDate').value;
      const status = document.getElementById('status').value;
      const progress = parseInt(document.getElementById('newTaskProgress').value, 10);

      if (newTaskName === '' || assignedEmployee === '') {
        showAlertModal("الرجاء إدخال اسم المهمة والموظف المسؤول.");
        hideLoading();
        return;
      }

      console.log("Attempting to add task with:");
      console.log("  App ID:", app_id);
      console.log("  Current User ID:", currentUserId);

      try {
        const tasksCollectionRef = collection(db, `artifacts/${app_id}/users/${currentUserId}/programming_tasks`);
        await callWithRetry(() => addDoc(tasksCollectionRef, {
          taskName: newTaskName,
          assignedEmployee: assignedEmployee,
          startDate: startDate,
          endDate: endDate,
          status: status,
          progress: progress
        }));
        // Clear form fields
        document.getElementById('newTaskName').value = '';
        document.getElementById('assignedEmployee').value = '';
        document.getElementById('startDate').value = '';
        document.getElementById('endDate').value = '';
        document.getElementById('status').value = 'To Do';
        document.getElementById('newTaskProgress').value = 0;
        document.getElementById('newTaskProgressValue').textContent = '0';
      } catch (e) {
        console.error("Error adding document: ", e);
        showAlertModal("حدث خطأ أثناء إضافة المهمة. الرجاء المحاولة مرة أخرى.");
      } finally {
        hideLoading();
      }
    };

    // Function to update an existing task
    window.updateTask = async function(id, field, value) {
      if (!db || !currentUserId || !isAuthReady) {
        showAlertModal("Firestore غير مهيأ أو معرف المستخدم غير متاح. الرجاء إعادة تحميل الصفحة.");
        return;
      }
      showLoading();
      console.log(`Attempting to update task ${id} with field ${field}, value ${value}:`);
      console.log("  App ID:", app_id);
      console.log("  Current User ID:", currentUserId);
      try {
        const taskRef = doc(db, `artifacts/${app_id}/users/${currentUserId}/programming_tasks`, id);
        const updateData = { [field]: value };

        if (field === 'progress') {
          const progressValue = Math.max(0, Math.min(100, parseInt(value, 10) || 0));
          updateData.progress = progressValue;
          if (progressValue === 100) {
            updateData.status = 'Done';
          } else if (progressValue > 0 && progressValue < 100) {
            updateData.status = 'In Progress';
          } else {
            updateData.status = 'To Do';
          }
        }
        await callWithRetry(() => updateDoc(taskRef, updateData));
      } catch (e) {
        console.error("Error updating document: ", e);
        showAlertModal("حدث خطأ أثناء تحديث المهمة. الرجاء المحاولة مرة أخرى.");
      } finally {
        hideLoading();
      }
    };

    // Function to delete a task
    window.deleteTask = async function(id) {
      if (!db || !currentUserId || !isAuthReady) {
        showAlertModal("Firestore غير مهيأ أو معرف المستخدم غير متاح. الرجاء إعادة تحميل الصفحة.");
        return;
      }
      showLoading();
      console.log(`Attempting to delete task ${id}:`);
      console.log("  App ID:", app_id);
      console.log("  Current User ID:", currentUserId);
      try {
        const taskRef = doc(db, `artifacts/${app_id}/users/${currentUserId}/programming_tasks`, id);
        await callWithRetry(() => deleteDoc(taskRef));
      } catch (e) {
        console.error("Error deleting document: ", e);
        showAlertModal("حدث خطأ أثناء حذف المهمة. الرجاء المحاولة مرة أخرى.");
      } finally {
        hideLoading();
      }
    };

    // Function to update status based on new task progress slider
    window.updateNewTaskStatus = function(progressValue) {
      const statusSelect = document.getElementById('status');
      if (progressValue == 100) {
        statusSelect.value = 'Done';
      } else if (progressValue > 0) {
        statusSelect.value = 'In Progress';
      } else {
        statusSelect.value = 'To Do';
      }
    };
  </script>

</body>
</html>