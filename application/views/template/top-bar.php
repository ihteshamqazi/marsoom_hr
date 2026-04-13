<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php
  $display_name = $this->session->userdata('name');
  if (empty($display_name)) {
      $display_name = $this->session->userdata('username') ?: '';
  }
?>

<div class="navbar col-12">
    <div class="box">
        <button type="button" class="sidebar-toggle" id="sidebarToggle" aria-label="القائمة">
            <i class="bi bi-list"></i>
        </button>
        <div class="link">
            <a href="<?= site_url('dashboard'); ?>">
                <img src="<?= base_url('newassets/images/logo-dash.svg'); ?>" alt="">
            </a>
        </div>
        <?php
          $tb1 = strtolower((string) $this->uri->segment(1));
          $tb2 = strtolower((string) $this->uri->segment(2));
          $top_req = function (string $method) use ($tb1, $tb2): string {
              return ($tb1 === 'requisitions' && $tb2 === strtolower($method)) ? 'active' : '';
          };
        ?>
        <div class="link link-menu nav-top-triple">
            <ul>
                <li><a class="<?= $top_req('my_requests'); ?>" href="<?= site_url('requisitions/my_requests'); ?>">طلباتي الوظيفية</a></li>
                <li><a class="<?= $top_req('create'); ?>" href="<?= site_url('requisitions/create'); ?>">طلب احتياج وظيفي</a></li>
                <li><a class="<?= $top_req('approvals'); ?>" href="<?= site_url('requisitions/approvals'); ?>">مراجعة الطلبات</a></li>
            </ul>
        </div>
    </div>
    <div class="box">
        <div class="link d-flex align-items-center gap-2">
            <a href="#" class="icon-circle" title="الإعدادات"><i class="bi bi-gear"></i></a>
            <a href="#" class="icon-circle" title="الإشعارات"><i class="bi bi-bell"></i></a>
            <a href="#" class="icon-circle" title="معلومات"><i class="bi bi-info-circle"></i></a>
        </div>
        <div class="link box-welcom">
            <div class="welcom">
                <?php
                $profile_img = base_url('images/man.png');
                if (!file_exists(FCPATH . 'images/man.png')) {
                    if (file_exists(FCPATH . 'newassets/images/man.png')) {
                        $profile_img = base_url('newassets/images/man.png');
                    } elseif (file_exists(FCPATH . 'assets/images/user.png')) {
                        $profile_img = base_url('assets/images/user.png');
                    } elseif (file_exists(FCPATH . 'assets/imeges/user.png')) {
                        $profile_img = base_url('assets/imeges/user.png');
                    } else {
                        $profile_img = base_url('newassets/images/users.svg');
                    }
                }
                ?>
                <img src="<?= $profile_img ?>" alt="Profile" class="profile-avatar-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';">
                <i class="bi bi-person-circle profile-fallback" style="font-size:2rem; display:none;"></i>
                <span>مرحباً, <?= html_escape($display_name); ?></span>
            </div>
        </div>
        <a class="link" href="<?= site_url('users/logout'); ?>" title="تسجيل الخروج">
            <i class="bi bi-box-arrow-right"></i>
        </a>
    </div>
</div>
