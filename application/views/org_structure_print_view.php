<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= html_escape($title ?? 'تعديل الهيكل التنظيمي') ?></title>

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;600;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jstree@3.3.16/dist/themes/default/style.min.css">

  <style>
    body{font-family:'Tajawal',sans-serif;background:#f6f8fb;}
    .page-wrap{max-width:1200px;margin:24px auto;padding:0 12px;}
    .hero{
      background:linear-gradient(135deg,#001f3f 0%, #0a3d62 55%, #FF8C00 160%);
      color:#fff;border-radius:18px;padding:18px;box-shadow:0 10px 30px rgba(0,0,0,.12);
      margin-bottom:16px;
    }
    .hero h4{margin:0;font-weight:900}
    .hero p{margin:6px 0 0;opacity:.92}
    .cardx{background:#fff;border-radius:18px;box-shadow:0 10px 26px rgba(17,24,39,.08);border:1px solid #eef2f7;}
    .cardx .cardx-h{padding:14px;border-bottom:1px solid #eef2f7;display:flex;gap:10px;align-items:center;justify-content:space-between;flex-wrap:wrap}
    .cardx .cardx-b{padding:14px}
    .btn-orange{background:#FF8C00;border-color:#FF8C00;color:#111;font-weight:900}
    .btn-orange:hover{filter:brightness(.96);color:#111}
    .status-pill{font-weight:900;border-radius:999px;padding:6px 10px;font-size:13px}
    .pill-idle{background:#e9f5ff;color:#0a3d62}
    .pill-saving{background:#fff3cd;color:#8a6d3b}
    .pill-ok{background:#d4edda;color:#155724}
    .pill-err{background:#f8d7da;color:#721c24}
    #orgTree{min-height:520px}
    .hint{color:#6b7280;font-size:13px;margin-top:10px;line-height:1.6}
    .jstree-default .jstree-clicked{background:#eaf2ff}
    .toolbar-right{display:flex;gap:8px;flex-wrap:wrap;align-items:center}

    /* اختيار المدير */
    .mgr-item{
      cursor:pointer;border:1px solid #eef2f7;border-radius:12px;
      padding:10px 12px;margin-bottom:8px;background:#fff;
    }
    .mgr-item:hover{background:#f8fafc}
    .mgr-item.active{border-color:#FF8C00;box-shadow:0 8px 18px rgba(255,140,0,.12)}
    .mgr-search{border-radius:12px}

    /* اختيار الفرع القديم (إذا الموظف تحت أكثر من مدير) */
    .occ-item{
      cursor:pointer;border:1px dashed #dbe3ee;border-radius:12px;
      padding:10px 12px;margin-bottom:8px;background:#fff;
    }
    .occ-item:hover{background:#f8fafc}
    .occ-item.active{border-color:#0a3d62;box-shadow:0 8px 18px rgba(10,61,98,.10)}
    .small-muted{font-size:12px;color:#6b7280}

    /* تحسين مودالات */
    .modal-content{border:0}
    .modal-header{border:0}
    .modal-footer{border:0}
  </style>
</head>
<body>

<div class="page-wrap">
  <div class="hero">
    <h4>تعديل الهيكل التنظيمي</h4>
    <p>
      • الطريقة 1: سحب وإفلات مع تأكيد حفظ.<br>
      • الطريقة 2: اضغط على الموظف (ضغطة) ثم اختر المشرف المباشر واحفظ.<br>
      • إذا الموظف تابع لأكثر من مدير: اختر “الفرع القديم” المراد تعديله فقط.
    </p>
  </div>

  <div class="cardx">
    <div class="cardx-h">
      <div class="d-flex align-items-center" style="gap:10px;flex-wrap:wrap">
        <input id="treeSearch" type="text" class="form-control" style="width:320px;max-width:100%"
               placeholder="ابحث بالاسم أو الرقم الوظيفي...">
        <button id="btnExpand" class="btn btn-light">توسيع الكل</button>
        <button id="btnCollapse" class="btn btn-light">طي الكل</button>
        <button id="btnRefresh" class="btn btn-outline-secondary">تحديث</button>
      </div>

      <div class="toolbar-right">
        <a class="btn btn-orange" href="<?= site_url('OrgStructureEditor/export_excel'); ?>">تصدير Excel</a>
        <a class="btn btn-outline-dark" target="_blank" href="<?= site_url('OrgStructureEditor/print_view'); ?>">طباعة</a>
        <span id="saveStatus" class="status-pill pill-idle">جاهز</span>
      </div>
    </div>

    <div class="cardx-b">
      <div id="orgTree"></div>
      <div class="hint">
        • اضغط على الموظف لفتح نافذة اختيار المشرف المباشر.<br>
        • “غير مرتبطين” يظهرون كمجموعة من emp1 ويمكن سحبهم وربطهم.<br>
        • إذا حاولت حذف الموظف من فرع قديم وفيه تابعين تحته، سيتم رفض العملية تلقائيًا.
      </div>
    </div>
  </div>
</div>

<!-- Modal تأكيد السحب -->
<div class="modal fade" id="confirmSaveModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content" style="border-radius:16px;overflow:hidden">
      <div class="modal-header" style="background:#001f3f;color:#fff">
        <h5 class="modal-title" style="font-weight:900;margin:0">تأكيد حفظ التعديل</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#fff;opacity:1">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div id="confirmText" style="font-weight:800;line-height:1.8"></div>
        <div class="text-muted mt-2" style="font-size:13px">سيتم حفظ التعديل مباشرة بعد التأكيد.</div>
      </div>
      <div class="modal-footer">
        <button type="button" id="btnCancelMove" class="btn btn-light">إلغاء</button>
        <button type="button" id="btnConfirmMove" class="btn btn-orange">تأكيد الحفظ</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal اختيار الفرع القديم (إذا الموظف تحت أكثر من مدير) -->
<div class="modal fade" id="occModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content" style="border-radius:16px;overflow:hidden">
      <div class="modal-header" style="background:#0a3d62;color:#fff">
        <h5 class="modal-title" style="font-weight:900;margin:0">الموظف مرتبط بأكثر من مدير</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#fff;opacity:1">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <div style="font-weight:900;margin-bottom:6px">
          اختر “الفرع القديم” الذي تريد تعديله فقط:
        </div>
        <div class="small-muted mb-2">لن يتم تغيير باقي الروابط للموظف، فقط الفرع الذي تختاره هنا.</div>

        <div class="alert alert-light" style="border-radius:12px;border:1px solid #eef2f7">
          الموظف المختار:
          <span id="occEmployeeText" style="font-weight:900;color:#111"></span>
        </div>

        <div id="occList"></div>

        <input type="hidden" id="occEmployeeId" value="">
        <input type="hidden" id="pickedOldManagerId" value="">
      </div>

      <div class="modal-footer">
        <button class="btn btn-light" data-dismiss="modal">إلغاء</button>
        <button id="btnOccContinue" class="btn btn-orange">متابعة لاختيار المدير الجديد</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal اختيار المشرف المباشر -->
<div class="modal fade" id="pickerModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content" style="border-radius:16px;overflow:hidden">
      <div class="modal-header" style="background:#001f3f;color:#fff">
        <h5 class="modal-title" style="font-weight:900;margin:0">تحديد المشرف المباشر</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#fff;opacity:1">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <div class="mb-2" style="font-weight:900">
          الموظف المختار:
          <span id="pickedEmployeeText" style="color:#0a3d62"></span>
        </div>

        <div class="alert alert-light" style="border-radius:12px;border:1px solid #eef2f7">
          <div style="font-weight:900;margin-bottom:6px">اختر المشرف المباشر (مدير جديد):</div>
          <input id="mgrSearch" class="form-control mgr-search" placeholder="ابحث بالاسم/الرقم/المسمى...">
          <div class="text-muted mt-1" style="font-size:12px">يمكنك اختيار “رأس الهيكل” لجعل الموظف في أعلى الهيكل.</div>
        </div>

        <div id="mgrList" style="max-height:320px;overflow:auto;padding:4px 2px"></div>

        <input type="hidden" id="pickedEmployeeId" value="">
        <input type="hidden" id="pickedManagerId" value="">
      </div>

      <div class="modal-footer">
        <button class="btn btn-light" data-dismiss="modal">إغلاق</button>
        <button id="btnSavePickerMove" class="btn btn-orange">حفظ التعديل</button>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jstree@3.3.16/dist/jstree.min.js"></script>

<script>
(function(){
  const treeUrl   = "<?= site_url('OrgStructureEditor/api_tree'); ?>";
  const moveUrl   = "<?= site_url('OrgStructureEditor/api_move'); ?>"; // للسحب
  const mgrUrl    = "<?= site_url('OrgStructureEditor/api_managers'); ?>";
  const movePickUrl = "<?= site_url('OrgStructureEditor/api_move_by_picker'); ?>";

  // ✅ هنا مكان إضافة هذه المتغيرات
  const occUrl = "<?= site_url('OrgStructureEditor/api_occurrences'); ?>";
  const moveSpecificUrl = "<?= site_url('OrgStructureEditor/api_move_specific'); ?>";

  let searchTimer = null;
  let pendingMove = null;

  function setPill(type, text){
    const el = $("#saveStatus");
    el.removeClass("pill-idle pill-saving pill-ok pill-err");
    el.addClass(type);
    el.text(text);
  }

  function escapeHtml(text){
    return (text||'').replace(/[&<>"']/g, function(m){
      return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m]);
    });
  }

  function initTree(){
    $("#orgTree").jstree("destroy");
    setPill("pill-idle", "جاري التحميل...");

    $.getJSON(treeUrl, function(data){
      $("#orgTree").jstree({
        core: {
          data: data,
          check_callback: function (op, node, parent) {
            if (op === "move_node") {
              if (node && node.id === "__unlinked__") return false;
            }
            return true;
          },
          themes: { dots: true, icons: false }
        },
        plugins: ["dnd", "search", "wholerow"]
      });

      setPill("pill-idle", "جاهز");
    }).fail(function(){
      setPill("pill-err", "فشل تحميل الشجرة");
    });
  }

  // ====== البحث داخل الشجرة ======
  $("#treeSearch").on("keyup", function(){
    clearTimeout(searchTimer);
    const v = $(this).val();
    searchTimer = setTimeout(function(){
      $("#orgTree").jstree(true).search(v);
    }, 250);
  });

  $("#btnExpand").on("click", ()=> $("#orgTree").jstree("open_all"));
  $("#btnCollapse").on("click", ()=> $("#orgTree").jstree("close_all"));
  $("#btnRefresh").on("click", initTree);

  // ====== 1) السحب والإفلات مع تأكيد ======
  $("#orgTree").on("move_node.jstree", function(e, data){
    const tree = $("#orgTree").jstree(true);

    const employeeId = data.node.id;
    const newParentId = data.parent;
    const oldParentId = data.old_parent;
    const oldPosition = data.old_position;

    if (employeeId === "__unlinked__") return;

    const empText = data.node.text;
    let parentText = "رأس الهيكل";
    if (newParentId && newParentId !== "#" && newParentId !== "__unlinked__") {
      const pNode = tree.get_node(newParentId);
      parentText = pNode ? pNode.text : ("مدير ("+newParentId+")");
    }

    $("#confirmText").html(
      "هل تريد نقل:<br><b>"+ escapeHtml(empText) +"</b><br>ليصبح تحت:<br><b>"+ escapeHtml(parentText) +"</b> ؟"
    );

    pendingMove = {
      employee_id: employeeId,
      new_manager_id: (newParentId === "__unlinked__" ? "#" : newParentId),
      rollback: function(){
        tree.move_node(employeeId, oldParentId, oldPosition);
      }
    };

    $("#confirmSaveModal").modal("show");
  });

  $("#btnCancelMove").on("click", function(){
    $("#confirmSaveModal").modal("hide");
    if (pendingMove && pendingMove.rollback) pendingMove.rollback();
    pendingMove = null;
    setPill("pill-idle", "تم الإلغاء");
    setTimeout(()=>setPill("pill-idle","جاهز"), 800);
  });

  $("#btnConfirmMove").on("click", function(){
    if (!pendingMove) return;

    $("#confirmSaveModal").modal("hide");
    setPill("pill-saving", "جاري الحفظ...");

    $.post(moveUrl, pendingMove, function(res){
      if (res && res.ok) {
        setPill("pill-ok", res.msg || "تم الحفظ");
        setTimeout(()=>setPill("pill-idle","جاهز"), 1200);
        initTree();
      } else {
        setPill("pill-err", (res && res.msg) ? res.msg : "فشل الحفظ");
        initTree();
      }
      pendingMove = null;
    }, "json").fail(function(){
      setPill("pill-err", "خطأ اتصال أثناء الحفظ");
      initTree();
      pendingMove = null;
    });
  });

  /* =========================
     2) الضغط على الموظف -> أولاً نفحص occurrences
     إذا له أكثر من مدير: نفتح occModal لاختيار الفرع القديم
     إذا له مدير واحد فقط: نفتح pickerModal مباشرة
  ========================= */

  $("#orgTree").on("select_node.jstree", function(e, data){
    const node = data.node;
    if (!node || node.id === "__unlinked__") return;

    const empId = node.id;
    const empText = node.text;

    setPill("pill-idle", "فحص ارتباطات الموظف...");

    $.getJSON(occUrl, {employee_id: empId}, function(res){
      if (!res || !res.ok) {
        setPill("pill-err", "فشل فحص ارتباطات الموظف");
        return;
      }

      const rows = Array.isArray(res.rows) ? res.rows : [];

      // occurrences تعني ظهورات الموظف تحت مدراء مختلفة
      // لو > 1 -> تعارض
      if (rows.length > 1) {
        openOccModal(empId, empText, rows);
        setPill("pill-idle", "اختر الفرع القديم");
      } else {
        // لا يوجد تعارض: نفتح اختيار المدير مباشرة
        $("#pickedOldManagerId").val(''); // لا نحتاج مدير قديم
        openPickerModal(empId, empText);
        setPill("pill-idle", "اختر المدير الجديد");
      }
    }).fail(function(){
      setPill("pill-err", "خطأ اتصال أثناء فحص الارتباطات");
    });
  });

  function openOccModal(empId, empText, occRows){
    $("#occEmployeeId").val(empId);
    $("#occEmployeeText").text(empText);
    $("#pickedOldManagerId").val('');

    let html = '';
    occRows.forEach(function(o){
      const parentId = (o.parent_id && o.parent_id !== '') ? o.parent_id : '#';
      const label = (parentId === '#') ? 'رأس الهيكل' : ('المدير: ' + parentId);

      html += `
        <div class="occ-item" data-parent="${escapeHtml(String(parentId))}">
          <div style="font-weight:900">${escapeHtml(label)}</div>
          <div class="small-muted">سيتم تعديل هذا الربط فقط (وليس باقي الروابط)</div>
        </div>
      `;
    });

    $("#occList").html(html);
    $("#occModal").modal("show");
  }

  // اختيار فرع قديم
  $(document).on("click", ".occ-item", function(){
    $(".occ-item").removeClass("active");
    $(this).addClass("active");
    $("#pickedOldManagerId").val($(this).data("parent"));
  });

  $("#btnOccContinue").on("click", function(){
    const empId = $("#occEmployeeId").val();
    const empText = $("#occEmployeeText").text();
    const oldMgr = $("#pickedOldManagerId").val();

    if (!oldMgr) {
      alert("فضلاً اختر الفرع القديم (المدير القديم) أولاً.");
      return;
    }

    $("#occModal").modal("hide");
    openPickerModal(empId, empText);
  });

  /* =========================
     Picker Modal: اختيار المدير الجديد
  ========================= */

  function openPickerModal(empId, empText){
    $("#pickedEmployeeId").val(empId);
    $("#pickedEmployeeText").text(empText);
    $("#pickedManagerId").val("");
    $("#mgrSearch").val("");

    loadManagers('');
    $("#pickerModal").modal("show");
  }

  function loadManagers(q){
    $("#mgrList").html('<div class="text-muted p-2">جاري التحميل...</div>');
    $.getJSON(mgrUrl, {q:q}, function(list){
      let html = '';

      // خيار رأس الهيكل
      html += renderMgrItem('#', 'رأس الهيكل (بدون مشرف مباشر)');

      if (Array.isArray(list)) {
        list.forEach(function(it){
          const empId = $("#pickedEmployeeId").val();
          if (String(it.id) === String(empId)) return;
          html += renderMgrItem(it.id, it.text);
        });
      }

      $("#mgrList").html(html);
    }).fail(function(){
      $("#mgrList").html('<div class="text-danger p-2">فشل تحميل قائمة المدراء</div>');
    });
  }

  function renderMgrItem(id, text){
    return `
      <div class="mgr-item" data-id="${escapeHtml(String(id))}">
        ${escapeHtml(text)}
      </div>
    `;
  }

  $(document).on("click", ".mgr-item", function(){
    $(".mgr-item").removeClass("active");
    $(this).addClass("active");
    $("#pickedManagerId").val($(this).data("id"));
  });

  let mgrTimer = null;
  $("#mgrSearch").on("keyup", function(){
    clearTimeout(mgrTimer);
    const q = $(this).val();
    mgrTimer = setTimeout(()=>loadManagers(q), 250);
  });

  // حفظ من نافذة الاختيار
  $("#btnSavePickerMove").on("click", function(){
    const empId = $("#pickedEmployeeId").val();
    const newMgrId = $("#pickedManagerId").val();
    const oldMgrId = $("#pickedOldManagerId").val(); // قد يكون فارغ إذا لا يوجد تعارض

    if (!newMgrId) {
      alert("فضلاً اختر المشرف المباشر أولاً.");
      return;
    }

    const empText = $("#pickedEmployeeText").text();
    const mgrText = $(".mgr-item.active").text().trim() || '---';

    // تأكيد
    let msg = "تأكيد نقل:\n" + empText + "\nليصبح تحت:\n" + mgrText + "\n\n";
    if (oldMgrId) {
      msg += "تنبيه: سيتم تعديل هذا الربط فقط (تحت المدير القديم: " + oldMgrId + ").\n";
      msg += "وسيتم الرفض إذا كان له تابعين في الفرع القديم.\n";
    } else {
      msg += "سيتم نقل الموظف وفق الربط الأساسي.\n";
    }

    const ok = confirm(msg);
    if (!ok) return;

    setPill("pill-saving", "جاري الحفظ...");

    // إذا لدينا oldMgrId -> استخدم النقل المحدد
    if (oldMgrId) {
      $.post(moveSpecificUrl, {
        employee_id: empId,
        old_manager_id: oldMgrId,
        new_manager_id: newMgrId
      }, function(res){
        handleSaveResponse(res);
      }, "json").fail(function(){
        setPill("pill-err", "خطأ اتصال أثناء الحفظ");
      });
    } else {
      // نقل عادي (قد ينقل الشجرة حسب منطق move_employee)
      $.post(movePickUrl, {
        employee_id: empId,
        new_manager_id: newMgrId
      }, function(res){
        handleSaveResponse(res);
      }, "json").fail(function(){
        setPill("pill-err", "خطأ اتصال أثناء الحفظ");
      });
    }
  });

  function handleSaveResponse(res){
    if (res && res.ok) {
      setPill("pill-ok", res.msg || "تم الحفظ");
      $("#pickerModal").modal("hide");
      $("#occModal").modal("hide");
      initTree();
      $("#pickedOldManagerId").val('');
      setTimeout(()=>setPill("pill-idle","جاهز"), 1200);
    } else {
      setPill("pill-err", (res && res.msg) ? res.msg : "فشل الحفظ");
    }
  }

  // تشغيل
  initTree();
})();
</script>

</body>
</html>
