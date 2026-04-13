<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<script>
(function ($) {
  'use strict';
  $(function () {
    if (typeof $.fn.DataTable === 'undefined') return;
    $('.js-exportable').each(function () {
      if ($.fn.DataTable.isDataTable(this)) return;
      $(this).DataTable({
        language: {
          url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/ar.json',
        },
        pageLength: 25,
        order: [],
        scrollX: true,
        autoWidth: false,
      });
    });
  });
})(jQuery);
</script>
