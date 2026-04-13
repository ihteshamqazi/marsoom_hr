      </div>
    </div>
  </section>

  <?php $this->load->view('components/app-footer'); ?>

  <?php $this->load->view('includes/common-js-links'); ?>
  <script src="<?php echo base_url(); ?>newassets/js/owl.carousel.min.js"></script>
  <?php if (!empty($extra_js)): ?>
    <?php if (is_array($extra_js)): ?>
      <?php foreach ($extra_js as $js): ?>
        <script src="<?php echo preg_match('#^https?://#i', (string)$js) ? $js : base_url($js); ?>"></script>
      <?php endforeach; ?>
    <?php else: ?>
      <script src="<?php echo preg_match('#^https?://#i', (string)$extra_js) ? $extra_js : base_url($extra_js); ?>"></script>
    <?php endif; ?>
  <?php endif; ?>
  <?php if (!empty($after_js_view)): ?>
    <?php $this->load->view($after_js_view); ?>
  <?php endif; ?>
</body>
</html>
