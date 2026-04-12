<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>خطة عمل اللجنة التقنية</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap" rel="stylesheet">
  <style>
    body {
      min-height: 100vh;
      background: linear-gradient(135deg, #0E1F3B 0%, #1b2a49 50%, #0E1F3B 100%);
      font-family: 'Tajawal', sans-serif;
      padding: 30px;
      color: #ffffff;
    }

    .container-custom {
      max-width: 1200px;
      margin: auto;
    }

    .section-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 50px;
    }

    .section-title {
      font-weight: 900;
      font-size: 42px;
      color: #F29840;
      text-shadow: 2px 2px 8px rgba(0,0,0,0.5);
    }

    .add-stage-btn {
      background-color: #F29840;
      border: none;
      color: white;
      font-weight: bold;
      padding: 10px 20px;
      font-size: 18px;
      border-radius: 10px;
    }

    .timeline {
      display: flex;
      overflow-x: auto;
      padding: 20px 0;
      margin-bottom: 50px;
      gap: 40px;
      position: relative;
    }

    .timeline-item {
      flex: 0 0 auto;
      text-align: center;
      position: relative;
      cursor: pointer;
    }

    .timeline-item::before {
      content: "";
      position: absolute;
      top: 22px;
      left: 50%;
      transform: translateX(-50%);
      width: 12px;
      height: 12px;
      background: #F29840;
      border-radius: 50%;
      border: 2px solid white;
      z-index: 2;
    }

    .timeline-item .label {
      margin-top: 40px;
      font-weight: bold;
      color: #ffffff;
      white-space: nowrap;
    }

    .timeline-line {
      position: absolute;
      top: 28px;
      left: 0;
      width: 100%;
      height: 2px;
      background: #F29840;
      z-index: 1;
    }

    .stage-card {
      background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(8px);
      border-radius: 20px;
      padding: 20px 25px;
      margin-bottom: 30px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
    }

    .stage-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 10px;
    }

    .stage-title {
      font-size: 26px;
      font-weight: bold;
      color: #ffffff;
    }

    .add-task-btn {
      background-color: #F29840;
      border: none;
      color: white;
      font-weight: bold;
      padding: 8px 15px;
      font-size: 16px;
      border-radius: 10px;
    }

    .progress {
      height: 12px;
      background-color: #eee;
      border-radius: 50px;
      overflow: hidden;
      margin-top: 10px;
    }

    .progress-bar {
      background-color: #F29840;
      border-radius: 50px;
    }

    .progress-percentage {
      font-size: 16px;
      font-weight: bold;
      margin-top: 5px;
    }

    .task-card {
      background: rgba(255, 255, 255, 0.85);
      border-radius: 20px;
      padding: 25px;
      margin-bottom: 20px;
      color: #222;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4);
      position: relative;
    }

    .task-title {
      font-size: 22px;
      font-weight: bold;
      color: #0E1F3B;
      margin-bottom: 10px;
    }

    .task-desc {
      font-size: 16px;
      color: #444;
      line-height: 1.8;
      margin-bottom: 10px;
    }

    .status-label {
      font-size: 14px;
      font-weight: bold;
      padding: 5px 10px;
      border-radius: 15px;
      display: inline-block;
      margin-top: 10px;
    }

    .status-inprogress { background-color: #f0ad4e; color: white; }
    .status-completed { background-color: #5cb85c; color: white; }
    .status-pending { background-color: #d9534f; color: white; }

    .change-status-btn {
      position: absolute;
      top: 20px;
      left: 20px;
      background-color: #0E1F3B;
      border: none;
      color: white;
      font-size: 14px;
      padding: 6px 10px;
      border-radius: 10px;
    }
  </style>
</head>

<body>

<div class="container-custom">

  <!-- العنوان وزر إضافة مرحلة -->
  <div class="section-header">
    <h1 class="section-title">خطة عمل اللجنة التقنية</h1>
    <button class="btn add-stage-btn" data-bs-toggle="modal" data-bs-target="#addStageModal">➕ إضافة مرحلة</button>
  </div>

  <!-- التايملاين -->
  <div class="timeline position-relative">
    <div class="timeline-line"></div>

    <div class="timeline-item" onclick="showStage('stage1')">
      <div class="label"> انشاء النظام وإدارة التحصيل  </div>
    </div>
    <div class="timeline-item" onclick="showStage('stage2')">
      <div class="label">  إدارة العمليات </div>
    </div>
    <div class="timeline-item" onclick="showStage('stage3')">
      <div class="label">   وحدة الجودة </div>
    </div>
     <div class="timeline-item" onclick="showStage('stage4')">
      <div class="label">    وحدة العناية بالعملاء   </div>
    </div>
      <div class="timeline-item" onclick="showStage('stage5')">
      <div class="label">     الإدارة القانونية       </div>
    </div>

      <div class="timeline-item" onclick="showStage('stage6')">
      <div class="label">      إدارة الموارد البشرية         </div>
    </div>

      <div class="timeline-item" onclick="showStage('stage7')">
      <div class="label">           الإدارة المالية         </div>
    </div>

    


    </div>

  </div>

  <!-- مكان عرض تفاصيل المراحل -->
  <div id="stage-details"></div>

</div>

<!-- مودالات الإضافة -->

<!-- Modal إضافة مرحلة -->
<div class="modal fade" id="addStageModal" tabindex="-1" aria-labelledby="addStageModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content" style="background: #ffffff; border-radius: 20px;">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="addStageModalLabel" style="color: #0E1F3B;">➕ إضافة مرحلة جديدة</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
      </div>
      <div class="modal-body">
        <?php echo form_open('/users/add_phase'); ?>   
          <div class="mb-4">
            <label for="stageName" class="form-label fw-bold" style="color: #555;">📄 اسم المرحلة</label>
            <input type="text" name="name" class="form-control form-control-lg" id="stageName" required>
          </div>
         
          <div class="d-grid gap-2">
            <button type="submit" class="btn btn-lg" style="background-color: #F29840; color: white;">💾 حفظ المرحلة</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal إضافة مهمة -->
<div class="modal fade" id="addTaskModal" tabindex="-1" aria-labelledby="addTaskModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content" style="background: #ffffff; border-radius: 20px;">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="addTaskModalLabel" style="color: #0E1F3B;">➕ إضافة مهمة جديدة</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
      </div>
      <div class="modal-body">
       <?php echo form_open('/users/add_task/1'); ?>
          <div class="mb-4">
            <label for="taskName" class="form-label fw-bold" style="color: #555;">📄 اسم المهمة</label>
            <input type="text" name="name" class="form-control form-control-lg" id="taskName" required>
          </div>
          <div class="mb-4">
            <label for="dueDate" class="form-label fw-bold" style="color: #555;">📅 تاريخ الإنجاز</label>
            <input type="date" name="dedline" class="form-control form-control-lg" id="dueDate" required>
          </div>
          <div class="d-grid gap-2">
            <button type="submit" class="btn btn-success btn-lg">💾 حفظ المهمة</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal تغيير حالة المهمة -->
<div class="modal fade" id="changeStatusModal" tabindex="-1" aria-labelledby="changeStatusModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-md">
    <div class="modal-content" style="background: #ffffff; border-radius: 20px;">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="changeStatusModalLabel" style="color: #0E1F3B;">🔄 تغيير حالة المهمة</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
      </div>
      <div class="modal-body">
        <form id="updateTaskForm" method="post">
          <div class="mb-4">
            <label for="taskStatus" class="form-label fw-bold" style="color: #555;">🔰 اختر حالة المهمة</label>
            <select class="form-select form-select-lg" name="task_status" id="taskStatus" required>
              <option value="0">قيد التنفيذ</option>
              <option value="1">تم الانجاز </option>
              <option value="2"> طلب مستقبلي</option>
              <option value="3">   معلقة </option>
            </select>
          </div>
          <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary btn-lg">🔄 تحديث الحالة</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>



<!-- Modal تمديد مدة المهمة -->
<div class="modal fade" id="extendDateModal" tabindex="-1" aria-labelledby="extendDateModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="background: #ffffff; border-radius: 20px;">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="extendDateModalLabel" style="color: #0E1F3B;">🕒 تمديد مدة المهمة</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
      </div>
      <div class="modal-body">
        <form id="extendForm" method="post">
          <div class="mb-4">
            <label for="newDueDate" class="form-label fw-bold" style="color: #555;">📅 اختر تاريخ جديد</label>
            <input type="date" class="form-control" name="new_due_date" id="newDueDate" required>
          </div>
          <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary btn-lg">💾 حفظ التاريخ</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>





<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- <script>
function openExtendModal() {
  var extendModal = new bootstrap.Modal(document.getElementById('extendDateModal'));
  extendModal.show();
}

document.getElementById('extendForm').addEventListener('submit', function(e) {
  e.preventDefault();
  const date = document.getElementById('newDueDate').value;
  if (date) {
    alert('✅ تم تحديث تاريخ التنفيذ إلى: ' + date);
    const modal = bootstrap.Modal.getInstance(document.getElementById('extendDateModal'));
    modal.hide();
  }
});
</script>
 -->

   <script>
// مصفوفة العملاء من PHP
let customers = <?php echo json_encode($this->user_model->get_operation_orders12555521111()); ?>;

let customers1 = <?php echo json_encode($this->user_model->get_operation_orders12555521111222()); ?>;

let selectedExtendTaskId = null;

// عرض المرحلة
function showStage(stageId) {
  let content = '';

 if (stageId === 'stage1') {
  content = `
    <div class="stage-card">
      <div class="stage-header">
        <div class="stage-title">📌  انشاء النظام وإدارة التحصيل  </div>
        <button class="btn add-task-btn" data-bs-toggle="modal" data-bs-target="#addTaskModal">➕ إضافة مهمة</button>
      </div>
      <div class="progress">
        <div class="progress-bar" style="width: 100%;"></div>
      </div>
      <div class="progress-percentage text-white">نسبة الإنجاز: 100%</div>
  `;

  customers.forEach(customer => {
    content += `
      <div class="task-card">
        <button 
          class="change-status-btn" 
          data-bs-toggle="modal" 
          data-bs-target="#changeStatusModal" 
          onclick="setSelectedTaskId(${customer.id})"
        >
          تغيير الحالة
        </button>
        
        <button 
          class="btn btn-sm btn-warning" 
          style="font-size:12px;" 
          data-bs-toggle="modal" 
          data-bs-target="#extendDateModal" 
          onclick="openExtendModal(${customer.id})"
        >
          🕒 تمديد المدة
        </button>


        
        <div class="task-title">🔧  ${customer.name}      </div>
        <div class="task-desc">${customer.dedline}</div>

 
     
  
${customer.status == 0 ? `
  <span class="status-label" style="background-color: orange; color: white;">قيد التنفيذ</span>
` : customer.status == 1 ? `
  <span class="status-label" style="background-color: green; color: white;">تم الإنجاز</span>
` : customer.status == 2 ? `
  <span class="status-label" style="background-color: blue; color: white;">طلب مستقبلي</span>
` : customer.status == 3 ? `
  <span class="status-label" style="background-color: red; color: white;">معلقة</span>
` : ''}

  
 





      </div>
    `;
  });

  content += `</div>`;
} else if (stageId === 'stage2') {
    content = `
    <div class="stage-card">
      <div class="stage-header">
        <div class="stage-title">📌      إدارة العمليات     </div>
        <button class="btn add-task-btn" data-bs-toggle="modal" data-bs-target="#addTaskModal">➕ إضافة مهمة</button>
      </div>
      <div class="progress">
        <div class="progress-bar" style="width: 50%;"></div>
      </div>
      <div class="progress-percentage text-white">نسبة الإنجاز: 50%</div>
  `;

  customers1.forEach(customer1 => {
    content += `
      <div class="task-card">
        <button 
          class="change-status-btn" 
          data-bs-toggle="modal" 
          data-bs-target="#changeStatusModal" 
          onclick="setSelectedTaskId(${customer1.id})"
        >
          تغيير الحالة
        </button>
        
        <button 
          class="btn btn-sm btn-warning" 
          style="font-size:12px;" 
          data-bs-toggle="modal" 
          data-bs-target="#extendDateModal" 
          onclick="openExtendModal(${customer1.id})"
        >
          🕒 تمديد المدة
        </button>


        
        <div class="task-title">🔧  ${customer1.name}      </div>
        <div class="task-desc">${customer1.dedline}</div>


 

      <a 
  href="#" 
  onclick="window.open('https://services.marsoom.net/all_orders/assets/imeges/${customer1.path}', '_blank', 'width=800,height=600'); return false;" 
  class="btn btn-primary" 
  style="font-family: 'Tajawal', sans-serif; font-weight: bold; font-size: 15px;"
>
  📄 محضر اجتماع
</a>


 
     
  
${customer1.status == 0 ? `
  <span class="status-label" style="background-color: orange; color: white;">قيد التنفيذ</span>
` : customer1.status == 1 ? `
  <span class="status-label" style="background-color: green; color: white;">تم الإنجاز</span>
` : customer1.status == 2 ? `
  <span class="status-label" style="background-color: blue; color: white;">طلب مستقبلي</span>
` : customer1.status == 3 ? `
  <span class="status-label" style="background-color: red; color: white;">معلقة</span>
` : ''}

  
 





      </div>
    `;
  });

  content += `</div>`;
 
} else if (stageId === 'stage3') {
  content = `
    <div class="stage-card">
      <div class="stage-header">
        <div class="stage-title">📌   وحدة الجودة       </div>
        <button class="btn add-task-btn" data-bs-toggle="modal" data-bs-target="#addTaskModal">➕ إضافة مهمة</button>
      </div>
      <div class="progress">
        <div class="progress-bar" style="width: 60%;"></div>
      </div>
      <div class="progress-percentage text-white">نسبة الإنجاز: 60%</div>
  `;

  customers1.forEach(customer => {
    content += `
      <div class="task-card">
        <button 
          class="change-status-btn" 
          data-bs-toggle="modal" 
          data-bs-target="#changeStatusModal" 
          onclick="setSelectedTaskId(${customer1.id})"
        >
          تغيير الحالة
        </button>
        
        <button 
          class="btn btn-sm btn-warning" 
          style="font-size:12px;" 
          data-bs-toggle="modal" 
          data-bs-target="#extendDateModal" 
          onclick="openExtendModal(${customer1.id})"
        >
          🕒 تمديد المدة
        </button>
        
        <div class="task-title">🔧 تصميم شاشة تسجيل الدخول</div>
        <div class="task-desc">${customer1.id}</div>
        <span class="status-label status-inprogress">قيد التنفيذ</span>
      </div>
    `;
  });

  content += `</div>`;
} else if (stageId === 'stage4') {
  content = `
    <div class="stage-card">
      <div class="stage-header">
        <div class="stage-title">📌    وحدة العناية بالعملاء         </div>
        <button class="btn add-task-btn" data-bs-toggle="modal" data-bs-target="#addTaskModal">➕ إضافة مهمة</button>
      </div>
      <div class="progress">
        <div class="progress-bar" style="width: 60%;"></div>
      </div>
      <div class="progress-percentage text-white">نسبة الإنجاز: 60%</div>
  `;

  customers1.forEach(customer => {
    content += `
      <div class="task-card">
        <button 
          class="change-status-btn" 
          data-bs-toggle="modal" 
          data-bs-target="#changeStatusModal" 
          onclick="setSelectedTaskId(${customer1.id})"
        >
          تغيير الحالة
        </button>
        
        <button 
          class="btn btn-sm btn-warning" 
          style="font-size:12px;" 
          data-bs-toggle="modal" 
          data-bs-target="#extendDateModal" 
          onclick="openExtendModal(${customer1.id})"
        >
          🕒 تمديد المدة
        </button>
        
        <div class="task-title">🔧 تصميم شاشة تسجيل الدخول</div>
        <div class="task-desc">${customer1.id}</div>
        <span class="status-label status-inprogress">قيد التنفيذ</span>
      </div>
    `;
  });

  content += `</div>`;
} else if (stageId === 'stage5') {
  content = `
    <div class="stage-card">
      <div class="stage-header">
        <div class="stage-title">📌     الإدارة القانونية             </div>
        <button class="btn add-task-btn" data-bs-toggle="modal" data-bs-target="#addTaskModal">➕ إضافة مهمة</button>
      </div>
      <div class="progress">
        <div class="progress-bar" style="width: 60%;"></div>
      </div>
      <div class="progress-percentage text-white">نسبة الإنجاز: 60%</div>
  `;

  customers1.forEach(customer => {
    content += `
      <div class="task-card">
        <button 
          class="change-status-btn" 
          data-bs-toggle="modal" 
          data-bs-target="#changeStatusModal" 
          onclick="setSelectedTaskId(${customer1.id})"
        >
          تغيير الحالة
        </button>
        
        <button 
          class="btn btn-sm btn-warning" 
          style="font-size:12px;" 
          data-bs-toggle="modal" 
          data-bs-target="#extendDateModal" 
          onclick="openExtendModal(${customer1.id})"
        >
          🕒 تمديد المدة
        </button>
        
        <div class="task-title">🔧 تصميم شاشة تسجيل الدخول</div>
        <div class="task-desc">${customer1.id}</div>
        <span class="status-label status-inprogress">قيد التنفيذ</span>
      </div>
    `;
  });

  content += `</div>`;
} else if (stageId === 'stage6') {
  content = `
    <div class="stage-card">
      <div class="stage-header">
        <div class="stage-title">📌   إدارة الموارد البشرية                 </div>
        <button class="btn add-task-btn" data-bs-toggle="modal" data-bs-target="#addTaskModal">➕ إضافة مهمة</button>
      </div>
      <div class="progress">
        <div class="progress-bar" style="width: 60%;"></div>
      </div>
      <div class="progress-percentage text-white">نسبة الإنجاز: 60%</div>
  `;

  customers1.forEach(customer => {
    content += `
      <div class="task-card">
        <button 
          class="change-status-btn" 
          data-bs-toggle="modal" 
          data-bs-target="#changeStatusModal" 
          onclick="setSelectedTaskId(${customer1.id})"
        >
          تغيير الحالة
        </button>
        
        <button 
          class="btn btn-sm btn-warning" 
          style="font-size:12px;" 
          data-bs-toggle="modal" 
          data-bs-target="#extendDateModal" 
          onclick="openExtendModal(${customer1.id})"
        >
          🕒 تمديد المدة
        </button>
        
        <div class="task-title">🔧 تصميم شاشة تسجيل الدخول</div>
        <div class="task-desc">${customer1.id}</div>
        <span class="status-label status-inprogress">قيد التنفيذ</span>
      </div>
    `;
  });

  content += `</div>`;
} else if (stageId === 'stage7') {
  content = `
    <div class="stage-card">
      <div class="stage-header">
        <div class="stage-title">📌       الإدارة المالية                  </div>
        <button class="btn add-task-btn" data-bs-toggle="modal" data-bs-target="#addTaskModal">➕ إضافة مهمة</button>
      </div>
      <div class="progress">
        <div class="progress-bar" style="width: 60%;"></div>
      </div>
      <div class="progress-percentage text-white">نسبة الإنجاز: 60%</div>
  `;

  customers1.forEach(customer => {
    content += `
      <div class="task-card">
        <button 
          class="change-status-btn" 
          data-bs-toggle="modal" 
          data-bs-target="#changeStatusModal" 
          onclick="setSelectedTaskId(${customer1.id})"
        >
          تغيير الحالة
        </button>
        
        <button 
          class="btn btn-sm btn-warning" 
          style="font-size:12px;" 
          data-bs-toggle="modal" 
          data-bs-target="#extendDateModal" 
          onclick="openExtendModal(${customer1.id})"
        >
          🕒 تمديد المدة
        </button>
        
        <div class="task-title">🔧 تصميم شاشة تسجيل الدخول</div>
        <div class="task-desc">${customer1.id}</div>
        <span class="status-label status-inprogress">قيد التنفيذ</span>
      </div>
    `;
  });

  content += `</div>`;
}


  document.getElementById('stage-details').innerHTML = content;
}

// تحديد ID المهمة لتغيير الحالة
function setSelectedTaskId(id) {
  let form = document.getElementById('updateTaskForm');
  form.action = `${window.location.origin}/all_orders/users/task_update/${id}`;
}

// تحديد ID المهمة لتمديد التاريخ
function openExtendModal(id) {
  selectedExtendTaskId = id;
  let form = document.getElementById('extendForm');
  form.action = `${window.location.origin}/all_orders/users/time_update/${id}`;
}
</script>




</body>
</html>
