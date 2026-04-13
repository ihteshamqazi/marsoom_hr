/**
 * SMS platform: validation, confirm modal, CSV preview (loaded after jQuery/Bootstrap).
 */
(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('smsForm');
    if (!form) return;

    var messageEl = document.getElementById('message_body');
    var charCount = document.getElementById('charCount');
    var mobilesEl = document.getElementById('mobile_numbers');
    var fileEl = document.getElementById('excel_file');
    var totalCount = document.getElementById('totalCount');
    var validCount = document.getElementById('validCount');
    var invalidCount = document.getElementById('invalidCount');
    var fileHintBox = document.getElementById('fileHintBox');

    var re966 = /^9665\d{8}$/;
    var re05 = /^05\d{8}$/;

    function splitMobiles(text) {
      if (!text) return [];
      return text.split(/[\s,;]+/).map(function (x) { return (x || '').trim(); }).filter(Boolean);
    }
    function onlyDigits(s) { return (s || '').replace(/\D+/g, ''); }

    function normalizeOne(m) {
      m = onlyDigits(m);
      if (re05.test(m)) {
        m = '966' + m.substring(1);
      }
      return m;
    }

    function analyzeMobiles() {
      var raw = splitMobiles(mobilesEl.value);
      var cleaned = raw.map(normalizeOne).filter(Boolean);

      var valid = 0, invalid = 0;
      var seen = new Set();
      var unique = [];

      cleaned.forEach(function (m) {
        if (seen.has(m)) return;
        seen.add(m);
        unique.push(m);
      });

      unique.forEach(function (m) {
        if (re966.test(m)) valid++; else invalid++;
      });

      totalCount.textContent = unique.length;
      validCount.textContent = valid;
      invalidCount.textContent = invalid;

      return { unique: unique, valid: valid, invalid: invalid };
    }

    function updateChar() {
      charCount.textContent = (messageEl.value || '').length;
    }

    messageEl.addEventListener('input', updateChar);
    mobilesEl.addEventListener('input', analyzeMobiles);

    fileEl.addEventListener('change', function () {
      if (fileEl.files && fileEl.files.length) {
        fileHintBox.style.display = 'block';
      } else {
        fileHintBox.style.display = 'none';
      }
    });

    updateChar();
    analyzeMobiles();

    var modalEl = document.getElementById('confirmModal');
    var confirmModal = modalEl && window.bootstrap ? new window.bootstrap.Modal(modalEl) : null;
    var openConfirm = document.getElementById('openConfirm');

    if (openConfirm && confirmModal) {
      openConfirm.addEventListener('click', function () {
        var region = document.getElementById('region').value;
        var msg = (messageEl.value || '').trim();
        var info = analyzeMobiles();
        var hasFile = (fileEl.files && fileEl.files.length > 0);

        if (!region) { alert('اختر المنطقة أولاً'); return; }
        if (!msg) { alert('اكتب محتوى الرسالة أولاً'); return; }
        if (info.valid === 0 && !hasFile) {
          alert('لا يوجد أرقام صالحة للإرسال. أدخل أرقام أو ارفع ملف CSV.');
          return;
        }

        document.getElementById('mRegion').textContent = region;
        document.getElementById('mCount').textContent = info.valid;
        document.getElementById('mMessage').textContent = msg;
        var mFileBadge = document.getElementById('mFileBadge');
        mFileBadge.style.display = hasFile ? 'inline-flex' : 'none';

        confirmModal.show();
      });
    }

    var confirmSend = document.getElementById('confirmSend');
    if (confirmSend) {
      confirmSend.addEventListener('click', function () {
        document.getElementById('smsForm').submit();
      });
    }

    /* CSV preview */
    var csvPreviewWrap = document.getElementById('csvPreviewWrap');
    var csvRowsEl = document.getElementById('csvRows');
    var csvTotalEl = document.getElementById('csvTotal');
    var csvValidEl = document.getElementById('csvValid');
    var csvInvalidEl = document.getElementById('csvInvalid');
    var btnCopyCsvValid = document.getElementById('copyCsvValid');
    var btnClearCsv = document.getElementById('clearCsv');

    var csvUniqueNumbers = [];
    var csvValidNumbers = [];

    function parseCSVLine(line) {
      var out = [];
      var cur = '';
      var inQuotes = false;
      var i, ch;
      for (i = 0; i < line.length; i++) {
        ch = line[i];
        if (ch === '"') {
          if (inQuotes && line[i + 1] === '"') { cur += '"'; i++; }
          else inQuotes = !inQuotes;
        } else if (ch === ',' && !inQuotes) {
          out.push(cur);
          cur = '';
        } else {
          cur += ch;
        }
      }
      out.push(cur);
      return out;
    }

    function extractMobilesFromCSVText(text) {
      var lines = (text || '').split(/\r?\n/).filter(function (l) { return l.trim() !== ''; });
      var rawCells = [];
      lines.forEach(function (line) {
        var cells = parseCSVLine(line);
        cells.forEach(function (c) { rawCells.push((c || '').trim()); });
      });

      var extracted = [];
      rawCells.forEach(function (cell) {
        if (!cell) return;
        if (/[eE]\+?\d+/.test(cell) && /[\d.]+/.test(cell)) {
          var n = Number(cell);
          if (Number.isFinite(n)) {
            extracted.push(String(Math.trunc(n)));
            return;
          }
        }
        var matches = cell.match(/\d{9,15}/g);
        if (matches) {
          matches.forEach(function (m) { extracted.push(m); });
        } else {
          extracted.push(cell);
        }
      });

      var normalized = extracted.map(normalizeOne).filter(Boolean);
      var seen = new Set();
      var unique = [];
      normalized.forEach(function (m) {
        if (seen.has(m)) return;
        seen.add(m);
        unique.push(m);
      });
      return unique;
    }

    function renderCSVPreview(numbers) {
      csvRowsEl.innerHTML = '';
      csvUniqueNumbers = numbers || [];

      if (!csvUniqueNumbers.length) {
        csvPreviewWrap.style.display = 'none';
        csvTotalEl.textContent = '0';
        csvValidEl.textContent = '0';
        csvInvalidEl.textContent = '0';
        csvValidNumbers = [];
        return;
      }

      csvPreviewWrap.style.display = 'block';

      var valid = 0, invalid = 0;
      var validList = [];
      var limit = 5000;

      csvUniqueNumbers.slice(0, limit).forEach(function (m, idx) {
        var ok = re966.test(m);
        if (ok) { valid++; validList.push(m); } else invalid++;

        var tr = document.createElement('tr');
        tr.innerHTML =
          '<td>' + (idx + 1) + '</td>' +
          '<td style="font-weight:700">' + m + '</td>' +
          '<td><span class="badge rounded-pill ' + (ok ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger') + '">' +
          (ok ? 'صالح' : 'غير صالح') + '</span></td>';
        csvRowsEl.appendChild(tr);
      });

      if (csvUniqueNumbers.length > limit) {
        var tr2 = document.createElement('tr');
        tr2.innerHTML = '<td colspan="3" class="text-muted small">تم عرض أول ' + limit + ' رقم فقط للمعاينة.</td>';
        csvRowsEl.appendChild(tr2);
      }

      csvTotalEl.textContent = Math.min(csvUniqueNumbers.length, limit);
      csvValidEl.textContent = valid;
      csvInvalidEl.textContent = invalid;
      csvValidNumbers = validList;
    }

    function handleFilePreview() {
      csvPreviewWrap.style.display = 'none';
      csvRowsEl.innerHTML = '';
      csvTotalEl.textContent = '0';
      csvValidEl.textContent = '0';
      csvInvalidEl.textContent = '0';
      csvUniqueNumbers = [];
      csvValidNumbers = [];

      if (!(fileEl.files && fileEl.files.length)) return;

      var f = fileEl.files[0];
      var ext = (f.name.split('.').pop() || '').toLowerCase();
      if (ext !== 'csv') return;

      f.text().then(function (text) {
        var nums = extractMobilesFromCSVText(text);
        renderCSVPreview(nums);
      }).catch(function (e) { console.error(e); });
    }

    fileEl.addEventListener('change', function () {
      if (fileEl.files && fileEl.files.length) {
        fileHintBox.style.display = 'block';
      } else {
        fileHintBox.style.display = 'none';
      }
      handleFilePreview();
    });

    if (btnCopyCsvValid) {
      btnCopyCsvValid.addEventListener('click', function () {
        if (!csvValidNumbers.length) {
          alert('لا يوجد أرقام صالحة في ملف CSV.');
          return;
        }
        mobilesEl.value = csvValidNumbers.join('\n');
        analyzeMobiles();
        alert('تم نسخ الأرقام الصالحة إلى الحقل اليدوي.');
      });
    }

    if (btnClearCsv) {
      btnClearCsv.addEventListener('click', function () {
        fileEl.value = '';
        fileHintBox.style.display = 'none';
        csvPreviewWrap.style.display = 'none';
        csvRowsEl.innerHTML = '';
        csvTotalEl.textContent = '0';
        csvValidEl.textContent = '0';
        csvInvalidEl.textContent = '0';
        csvUniqueNumbers = [];
        csvValidNumbers = [];
      });
    }
  });
})();
