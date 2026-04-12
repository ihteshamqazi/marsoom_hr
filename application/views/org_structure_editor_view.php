<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= html_escape($title ?? 'تعديل الهيكل التنظيمي') ?></title>

  <!-- Bootstrap 5.3 RTL -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css">
  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@400;500;600;700&family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">
  <!-- FontAwesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <!-- AOS -->
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  <!-- jsTree -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jstree@3.3.16/dist/themes/default/style.min.css">

  <style>
    :root{
      --primary-blue:#001f3f;
      --primary-orange:#FF8C00;
      --secondary-blue:#0a3d62;
      --secondary-orange:#ff9f43;
      --dark-bg:#0d1b2a;
      --darker-bg:#0a1929;

      --glass-bg: rgba(255,255,255,.05);
      --glass-border: rgba(255,255,255,.10);
      --card-bg: rgba(255,255,255,.07);

      --text-light:#fff;
      --text-muted: rgba(255,255,255,.68);

      --shadow-lg: 0 20px 40px rgba(0,0,0,.40);
      --shadow-sm: 0 10px 25px rgba(0,0,0,.20);

      --radius-xxl: 24px;
      --radius-xl: 18px;
      --radius-lg: 14px;
    }

    *{box-sizing:border-box}
    body{
      font-family:'Tajawal', sans-serif;
      background: linear-gradient(135deg, var(--darker-bg) 0%, var(--primary-blue) 30%, #1a1a2e 70%, var(--dark-bg) 100%);
      min-height:100vh;
      color:var(--text-light);
      overflow-x:hidden;
      background-attachment: fixed;
    }

    /* Background pattern */
    .bg-pattern{
      position:fixed; inset:0;
      background-image:
        radial-gradient(circle at 10% 20%, rgba(255,140,0,.05) 0%, transparent 20%),
        radial-gradient(circle at 90% 80%, rgba(0,31,63,.05) 0%, transparent 20%),
        linear-gradient(45deg, transparent 48%, rgba(255,140,0,.03) 50%, transparent 52%),
        linear-gradient(-45deg, transparent 48%, rgba(0,31,63,.03) 50%, transparent 52%);
      background-size: 400px 400px, 400px 400px, 100px 100px, 100px 100px;
      z-index:-2;
      animation: patternMove 20s linear infinite;
    }
    @keyframes patternMove{
      0%{background-position:0 0,0 0,0 0,0 0}
      100%{background-position:400px 400px,400px 400px,100px 100px,100px 100px}
    }

    .floating-orb{position:fixed;border-radius:50%;filter: blur(40px);opacity:.15;animation: orbFloat 30s ease-in-out infinite;z-index:-1;}
    .orb-1{width:320px;height:320px;background:var(--primary-orange);top:10%;right:6%;}
    .orb-2{width:440px;height:440px;background:var(--primary-blue);bottom:8%;left:6%;animation-duration:40s;animation-delay:-10s;}
    .orb-3{width:220px;height:220px;background:var(--secondary-orange);top:55%;left:22%;animation-duration:26s;animation-delay:-5s;}
    @keyframes orbFloat{
      0%,100%{transform:translate(0,0) scale(1)}
      25%{transform:translate(100px,-50px) scale(1.08)}
      50%{transform:translate(-50px,100px) scale(.92)}
      75%{transform:translate(-100px,-50px) scale(1.05)}
    }

    .wrap{max-width:1400px;margin:24px auto;padding:0 14px;position:relative;z-index:1;}

    /* Header */
    .header-nav{
      background: rgba(255,255,255,.03);
      border: 1px solid var(--glass-border);
      border-radius: var(--radius-xxl);
      box-shadow: 0 8px 32px rgba(0,0,0,.28);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      padding: 16px 18px;
      display:flex;
      justify-content:space-between;
      align-items:center;
      gap:12px;
      margin-bottom: 18px;
      position:relative;
      overflow:hidden;
    }
    .header-nav::before{
      content:'';
      position:absolute; top:0; left:0; right:0;
      height:2px;
      background: linear-gradient(90deg, transparent, var(--primary-orange), transparent);
    }
    .title-box h1{
      font-family:'El Messiri', serif;
      font-size: 1.75rem;
      font-weight: 900;
      margin:0;
      line-height: 1.2;
      background: linear-gradient(135deg, #ffffff, #ffd166);
      -webkit-background-clip:text;
      -webkit-text-fill-color:transparent;
    }
    .title-box p{
      margin:6px 0 0;
      color: var(--text-muted);
      font-size: .95rem;
    }

    .btn-marsom{
      border: 1px solid rgba(255,255,255,.18);
      background: rgba(255,255,255,.06);
      color:#fff;
      border-radius: 14px;
      padding: 10px 14px;
      font-weight: 900;
      text-decoration:none;
      display:inline-flex;
      align-items:center;
      gap:10px;
      transition: all .25s ease;
      white-space:nowrap;
    }
    .btn-marsom:hover{
      transform: translateY(-2px);
      border-color: rgba(255,140,0,.55);
      box-shadow: 0 10px 22px rgba(255,140,0,.14);
      color:#fff;
    }
    .btn-marsom.primary{
      background: linear-gradient(135deg, rgba(255,140,0,.22), rgba(255,140,0,.10));
      border-color: rgba(255,140,0,.35);
    }

    /* Main section card */
    .section{
      background: var(--glass-bg);
      border: 1px solid var(--glass-border);
      border-radius: var(--radius-xxl);
      backdrop-filter: blur(18px);
      -webkit-backdrop-filter: blur(18px);
      box-shadow: var(--shadow-sm);
      padding: 18px;
      position: relative;
      overflow:hidden;
    }
    .section::before{
      content:'';
      position:absolute; top:0; left:0; right:0;
      height:3px;
      background: linear-gradient(90deg, var(--primary-orange), var(--primary-blue));
      opacity:.9;
    }

    .toolbar{
      display:flex;
      flex-wrap:wrap;
      gap:10px;
      align-items:center;
      justify-content:space-between;
      padding: 6px 2px 12px;
      border-bottom: 1px solid rgba(255,255,255,.10);
      margin-bottom: 12px;
    }

    .tool-left, .tool-right{
      display:flex;
      flex-wrap:wrap;
      gap:10px;
      align-items:center;
    }

    .input-glass{
      background: rgba(255,255,255,.06) !important;
      border: 1px solid rgba(255,255,255,.14) !important;
      color: #fff !important;
      border-radius: 14px !important;
      padding: 10px 12px !important;
      font-weight: 800;
      box-shadow: 0 10px 25px rgba(0,0,0,.12);
    }
    .input-glass::placeholder{color: rgba(255,255,255,.55)}

    .btn-mini{
      border-radius: 14px;
      padding: 10px 12px;
      font-weight: 900;
      border: 1px solid rgba(255,255,255,.18);
      background: rgba(255,255,255,.06);
      color:#fff;
      text-decoration:none;
      display:inline-flex;
      align-items:center;
      gap:10px;
      transition: all .25s ease;
    }
    .btn-mini:hover{
      transform: translateY(-2px);
      border-color: rgba(255,140,0,.55);
      box-shadow: 0 10px 22px rgba(255,140,0,.14);
      color:#fff;
    }
    .btn-mini.primary{
      background: linear-gradient(135deg, rgba(255,140,0,.25), rgba(255,140,0,.10));
      border-color: rgba(255,140,0,.35);
    }
    .btn-mini.ghost{
      background: rgba(255,255,255,.04);
      border-color: rgba(255,255,255,.14);
    }
    .btn-mini.report{
      background: linear-gradient(135deg, rgba(74,105,189,.22), rgba(0,31,63,.10));
      border-color: rgba(74,105,189,.25);
    }

    .status-pill{
      font-weight: 900;
      border-radius: 999px;
      padding: 8px 12px;
      font-size: 13px;
      border: 1px solid rgba(255,255,255,.16);
      background: rgba(255,255,255,.06);
      color:#fff;
      display:inline-flex;
      align-items:center;
      gap:8px;
      box-shadow: 0 10px 25px rgba(0,0,0,.12);
      white-space:nowrap;
    }
    .pill-idle{border-color: rgba(74,105,189,.30); background: rgba(74,105,189,.10);}
    .pill-saving{border-color: rgba(255,159,67,.45); background: rgba(255,159,67,.12);}
    .pill-ok{border-color: rgba(46,204,113,.45); background: rgba(46,204,113,.12);}
    .pill-err{border-color: rgba(231,76,60,.55); background: rgba(231,76,60,.12);}

    /* jsTree area */
    #orgTree{
      min-height: 560px;
      background: rgba(255,255,255,.03);
      border: 1px solid rgba(255,255,255,.12);
      border-radius: var(--radius-xl);
      padding: 12px;
      box-shadow: 0 10px 25px rgba(0,0,0,.14);
    }

    .hint{
      margin-top: 12px;
      color: var(--text-muted);
      font-size: 13px;
      line-height: 1.8;
    }

    /* jsTree overrides */
    .jstree-default .jstree-wholerow-hovered{background: rgba(255,255,255,.06) !important;}
    .jstree-default .jstree-clicked{background: rgba(255,140,0,.14) !important;}
    .jstree-default .jstree-anchor{color: rgba(255,255,255,.92) !important; font-weight: 800;}
    .jstree-default .jstree-icon{filter: brightness(1.1);}
    .jstree-default .jstree-search{color: #ffd166 !important; font-weight: 900;}

    /* Modals (glass) */
    .modal-content.glass{
      background: rgba(15, 27, 42, .82);
      border: 1px solid rgba(255,255,255,.12);
      backdrop-filter: blur(18px);
      -webkit-backdrop-filter: blur(18px);
      border-radius: 18px;
      overflow:hidden;
      box-shadow: var(--shadow-lg);
      color:#fff;
    }
    .modal-header.glass-h{
      background: rgba(255,255,255,.04);
      border-bottom: 1px solid rgba(255,255,255,.10);
      position:relative;
    }
    .modal-header.glass-h::before{
      content:'';
      position:absolute; top:0; left:0; right:0;
      height:2px;
      background: linear-gradient(90deg, transparent, var(--primary-orange), transparent);
    }
    .modal-title{
      font-family:'El Messiri', serif;
      font-weight: 900;
    }
    .btn-close.btn-close-white{filter: invert(1); opacity:.9}

    .card-like{
      background: rgba(255,255,255,.05);
      border: 1px solid rgba(255,255,255,.12);
      border-radius: 14px;
      padding: 12px;
    }

    /* Manager items */
    .mgr-item{
      cursor:pointer;
      border: 1px solid rgba(255,255,255,.12);
      border-radius: 14px;
      padding: 10px 12px;
      margin-bottom: 10px;
      background: rgba(255,255,255,.04);
      transition: all .18s ease;
      font-weight: 800;
      color: rgba(255,255,255,.92);
    }
    .mgr-item:hover{
      transform: translateY(-1px);
      border-color: rgba(255,140,0,.35);
      background: rgba(255,140,0,.08);
    }
    .mgr-item.active{
      border-color: rgba(255,140,0,.55);
      box-shadow: 0 10px 22px rgba(255,140,0,.14);
      background: rgba(255,140,0,.10);
    }

    @media(max-width: 768px){
      .header-nav{flex-direction:column; align-items:stretch}
      .tool-left, .tool-right{justify-content:center}
      #orgTree{min-height: 520px}
    }
  </style>
</head>

<body>
  <div class="bg-pattern"></div>
  <div class="floating-orb orb-1"></div>
  <div class="floating-orb orb-2"></div>
  <div class="floating-orb orb-3"></div>

  <div class="wrap">

    <!-- Header -->
    <div class="header-nav" data-aos="fade-down" data-aos-duration="800">
      <div class="title-box">
        <h1>تعديل الهيكل التنظيمي</h1>
        <p>طريقتان للتعديل: سحب وإفلات مع تأكيد — أو اضغط على الموظف واختر “المشرف المباشر” ثم حفظ.</p>
      </div>

      <div class="header-actions d-flex gap-2 flex-wrap">
        <a class="btn-marsom" href="<?= site_url('users1/main_hr1'); ?>">
          <i class="fas fa-house"></i> الرئيسية
        </a>
        <a class="btn-marsom primary" href="<?= site_url('OrgStructureEditor'); ?>">
          <i class="fas fa-sitemap"></i> شاشة الهيكل
        </a>
      </div>
    </div>

    <!-- Main Section -->
    <div class="section" data-aos="fade-up" data-aos-delay="80">

      <div class="toolbar">
        <div class="tool-left">
          <input id="treeSearch" type="text" class="form-control input-glass" style="width:340px;max-width:100%"
                 placeholder="ابحث بالاسم أو الرقم الوظيفي...">
          <button id="btnExpand" class="btn-mini ghost" type="button">
            <i class="fas fa-up-right-and-down-left-from-center"></i> توسيع الكل
          </button>
          <button id="btnCollapse" class="btn-mini ghost" type="button">
            <i class="fas fa-down-left-and-up-right-to-center"></i> طي الكل
          </button>
          <button id="btnRefresh" class="btn-mini ghost" type="button">
            <i class="fas fa-rotate"></i> تحديث
          </button>
        </div>

        <div class="tool-right">
          <a class="btn-mini primary" href="<?= site_url('OrgStructureEditor/export_excel'); ?>">
            <i class="fas fa-file-excel"></i> تصدير Excel
          </a>
          <a class="btn-mini report" target="_blank" href="<?= site_url('OrgStructureEditor/print_view'); ?>">
            <i class="fas fa-print"></i> طباعة
          </a>

          <span id="saveStatus" class="status-pill pill-idle">
            <i class="fas fa-circle-check"></i> جاهز
          </span>
        </div>
      </div>

      <div id="orgTree"></div>

      

      <div class="hint">
        • اضغط على أي موظف لفتح نافذة اختيار “المشرف المباشر”.<br>
        • السحب والإفلات متاح مع تأكيد الحفظ.<br>
        • تلميح: استخدم البحث لتحديد الموظف بسرعة ثم اختر مديره من النافذة.
      </div>

    </div>
  </div>

  <!-- Modal تأكيد السحب -->
  <div class="modal fade" id="confirmSaveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content glass">
        <div class="modal-header glass-h">
          <h5 class="modal-title mb-0">تأكيد حفظ التعديل</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div id="confirmText" style="font-weight:900;line-height:1.8"></div>
          <div class="mt-2" style="color:rgba(255,255,255,.70);font-size:13px">
            سيتم حفظ التعديل مباشرة بعد التأكيد.
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" id="btnCancelMove" class="btn-mini ghost">
            <i class="fas fa-xmark"></i> إلغاء
          </button>
          <button type="button" id="btnConfirmMove" class="btn-mini primary">
            <i class="fas fa-check"></i> تأكيد الحفظ
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal اختيار المشرف المباشر -->
  <div class="modal fade" id="pickerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content glass">
        <div class="modal-header glass-h">
          <h5 class="modal-title mb-0">تحديد المشرف المباشر</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <div class="mb-2" style="font-weight:900">
            الموظف المختار:
            <span id="pickedEmployeeText" style="color:#ffd166"></span>
          </div>

          <div class="card-like mb-3">
            <div style="font-weight:900;margin-bottom:8px">
              اختر المشرف المباشر (مدير جديد):
            </div>

            <input id="mgrSearch" class="form-control input-glass" placeholder="ابحث بالاسم/الرقم/المسمى...">
            <div class="mt-2" style="color:rgba(255,255,255,.70);font-size:12px">
              يمكنك اختيار “رأس الهيكل” لجعل الموظف في أعلى الهيكل.
            </div>
          </div>

          <div id="mgrList" style="max-height:340px;overflow:auto;padding:4px 2px"></div>

          <input type="hidden" id="pickedEmployeeId" value="">
          <input type="hidden" id="pickedManagerId" value="">
        </div>

        <div class="modal-footer">
          <button class="btn-mini ghost" data-bs-dismiss="modal" type="button">
            <i class="fas fa-arrow-right"></i> إغلاق
          </button>
          <button id="btnSavePickerMove" class="btn-mini primary" type="button">
            <i class="fas fa-floppy-disk"></i> حفظ التعديل
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/jstree@3.3.16/dist/jstree.min.js"></script>
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

  <script>
    AOS.init({ duration: 800, once: true, offset: 50, easing: 'ease-out-cubic' });
  </script>

  <script>
  (function(){
    const treeUrl     = "<?= site_url('OrgStructureEditor/api_tree'); ?>";
    const moveUrl     = "<?= site_url('OrgStructureEditor/api_move'); ?>"; // للسحب
    const mgrUrl      = "<?= site_url('OrgStructureEditor/api_managers'); ?>";
    const movePickUrl = "<?= site_url('OrgStructureEditor/api_move_by_picker'); ?>";

    let searchTimer = null;
    let pendingMove = null;

    const confirmModalEl = document.getElementById('confirmSaveModal');
    const pickerModalEl  = document.getElementById('pickerModal');
    const confirmModal   = new bootstrap.Modal(confirmModalEl, {backdrop:'static'});
    const pickerModal    = new bootstrap.Modal(pickerModalEl);

    function setPill(type, text, icon){
      const el = $("#saveStatus");
      el.removeClass("pill-idle pill-saving pill-ok pill-err").addClass(type);
      el.html((icon ? `<i class="${icon}"></i>` : `<i class="fas fa-circle-info"></i>`) + " " + (text||''));
    }

    function escapeHtml(text){
      return (text||'').replace(/[&<>"']/g, function(m){
        return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m]);
      });
    }

    function initTree(){
      $("#orgTree").jstree("destroy");
      setPill("pill-saving", "جاري التحميل...", "fas fa-spinner fa-spin");

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

        setPill("pill-idle", "جاهز", "fas fa-circle-check");
      }).fail(function(){
        setPill("pill-err", "فشل تحميل الشجرة", "fas fa-triangle-exclamation");
      });
    }

    // ====== البحث داخل الشجرة ======
    $("#treeSearch").on("keyup", function(){
      clearTimeout(searchTimer);
      const v = $(this).val();
      searchTimer = setTimeout(function(){
        const inst = $("#orgTree").jstree(true);
        if (inst) inst.search(v);
      }, 250);
    });

    $("#btnExpand").on("click", ()=> $("#orgTree").jstree("open_all"));
    $("#btnCollapse").on("click", ()=> $("#orgTree").jstree("close_all"));
    $("#btnRefresh").on("click", initTree);

    // ====== 1) السحب والإفلات مع تأكيد ======
    $("#orgTree").on("move_node.jstree", function(e, data){
      const tree = $("#orgTree").jstree(true);

      const employeeId  = data.node.id;
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

      confirmModal.show();
    });

    $("#btnCancelMove").on("click", function(){
      confirmModal.hide();
      if (pendingMove && pendingMove.rollback) pendingMove.rollback();
      pendingMove = null;

      setPill("pill-idle", "تم الإلغاء", "fas fa-ban");
      setTimeout(()=>setPill("pill-idle","جاهز","fas fa-circle-check"), 900);
    });

    $("#btnConfirmMove").on("click", function(){
      if (!pendingMove) return;

      confirmModal.hide();
      setPill("pill-saving", "جاري الحفظ...", "fas fa-spinner fa-spin");

      $.post(moveUrl, pendingMove, function(res){
        if (res && res.ok) {
          setPill("pill-ok", (res.msg || "تم الحفظ"), "fas fa-circle-check");
          setTimeout(()=>setPill("pill-idle","جاهز","fas fa-circle-check"), 1200);
        } else {
          setPill("pill-err", (res && res.msg) ? res.msg : "فشل الحفظ", "fas fa-triangle-exclamation");
          initTree();
        }
        pendingMove = null;
      }, "json").fail(function(){
        setPill("pill-err", "خطأ اتصال أثناء الحفظ", "fas fa-triangle-exclamation");
        initTree();
        pendingMove = null;
      });
    });

    // ====== 2) الضغط على الموظف -> فتح اختيار المدير ======
    $("#orgTree").on("select_node.jstree", function(e, data){
      const node = data.node;
      if (!node || node.id === "__unlinked__") return;

      $("#pickedEmployeeId").val(node.id);
      $("#pickedEmployeeText").text(node.text);
      $("#pickedManagerId").val("");

      loadManagers('');
      pickerModal.show();
    });

    function renderMgrItem(id, text){
      return `
        <div class="mgr-item" data-id="${escapeHtml(String(id))}">
          <i class="fas fa-user-shield me-1" style="opacity:.85"></i>
          ${escapeHtml(text)}
        </div>
      `;
    }

    // تحميل المدراء (بحث)
    function loadManagers(q){
      $("#mgrList").html('<div class="p-2" style="color:rgba(255,255,255,.70)"><i class="fas fa-spinner fa-spin"></i> جاري التحميل...</div>');

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
        $("#mgrList").html('<div class="p-2 text-danger"><i class="fas fa-triangle-exclamation"></i> فشل تحميل قائمة المدراء</div>');
      });
    }

    // اختيار مدير من القائمة
    $(document).on("click", ".mgr-item", function(){
      $(".mgr-item").removeClass("active");
      $(this).addClass("active");
      $("#pickedManagerId").val($(this).data("id"));
    });

    // بحث داخل المدراء
    let mgrTimer = null;
    $("#mgrSearch").on("keyup", function(){
      clearTimeout(mgrTimer);
      const q = $(this).val();
      mgrTimer = setTimeout(()=>loadManagers(q), 250);
    });

    // حفظ من نافذة الاختيار
    $("#btnSavePickerMove").on("click", function(){
      const empId = $("#pickedEmployeeId").val();
      const mgrId = $("#pickedManagerId").val();

      if (!mgrId) {
        alert("فضلاً اختر المشرف المباشر أولاً.");
        return;
      }

      const empText = $("#pickedEmployeeText").text();
      const mgrText = $(".mgr-item.active").text().trim() || '---';
      const ok = confirm("تأكيد نقل:\n" + empText + "\nليصبح تحت:\n" + mgrText + "\n\nملاحظة: سيتم نقل جميع التابعين معه حسب منطق الحفظ في السيرفر.");
      if (!ok) return;

      setPill("pill-saving", "جاري الحفظ...", "fas fa-spinner fa-spin");

      $.post(movePickUrl, {employee_id: empId, new_manager_id: mgrId}, function(res){
        if (res && res.ok) {
          setPill("pill-ok", (res.msg || "تم الحفظ"), "fas fa-circle-check");
          pickerModal.hide();
          initTree();
          setTimeout(()=>setPill("pill-idle","جاهز","fas fa-circle-check"), 1200);
        } else {
          setPill("pill-err", (res && res.msg) ? res.msg : "فشل الحفظ", "fas fa-triangle-exclamation");
        }
      }, "json").fail(function(){
        setPill("pill-err", "خطأ اتصال أثناء الحفظ", "fas fa-triangle-exclamation");
      });
    });

    // تشغيل
    initTree();
  })();
  </script>

</body>
</html>
