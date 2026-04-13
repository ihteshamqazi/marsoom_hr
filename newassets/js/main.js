
// كود اللودنج
(function(){
      // Failsafe: لو العنصر اتحط بعدين، نراقبه
      function hideLoader(loader) {
        if (!loader) return;
        // اضف class بحيث يعمل fade
        loader.classList.add('hidden');
        // بعد انتهاء الفايد، اشيله من الـ DOM
        setTimeout(() => {
          if (loader.parentNode) loader.parentNode.removeChild(loader);
        }, 600); // شوي أكبر من transition
      }

      // لو الصفحة انتهت تحميلها
      if (document.readyState === 'complete') {
        hideLoader(document.querySelector('.loading') || document.getElementById('pageLoader'));
      } else {
        // نستنى حدث load (يتضمن الصور وCSS وframes)
        window.addEventListener('load', function() {
          hideLoader(document.querySelector('.loading') || document.getElementById('pageLoader'));
        });
      }

      // مراقب كنسلة أخيرة: لو العنصر اتضاف بعدين ديناميكياً
      const observer = new MutationObserver((mutations, obs) => {
        const l = document.querySelector('.loading');
        if (l && document.readyState === 'complete') {
          hideLoader(l);
          obs.disconnect();
        }
      });
      observer.observe(document.documentElement, { childList: true, subtree: true });

      // Fallback: لو كل شيء فشل، اختفه بعد 8 ثواني
      setTimeout(() => {
        hideLoader(document.querySelector('.loading'));
      }, 8000);
    })();





    
/* js/main.js */
$(document).ready(function(){
  // Sidebar toggle for mobile/tablet
  function toggleSidebar(open) {
    var sidebar = document.getElementById('mainSidebar');
    var overlay = document.getElementById('sidebarOverlay');
    if (!sidebar || !overlay) return;
    if (open) {
      sidebar.classList.add('sidebar-open');
      overlay.classList.add('active');
      document.body.style.overflow = 'hidden';
    } else {
      sidebar.classList.remove('sidebar-open');
      overlay.classList.remove('active');
      document.body.style.overflow = '';
    }
  }
  $('#sidebarToggle').on('click', function() { toggleSidebar(true); });
  $('#sidebarClose, #sidebarOverlay').on('click', function() { toggleSidebar(false); });
  $(document).on('click', '.sidebar a', function() {
    if (window.innerWidth <= 768) toggleSidebar(false);
  });

  // Initialize Owl Carousel
  $('.login-slider').owlCarousel({
    loop:true,
    margin:0,
    rtl:true,
    nav: false,
    dots: true,      // النقاط (Pagination)
    autoplay:true,
    autoplayTimeout:5000,
    animateOut: 'fadeOut', // الخروج بتأثير Fade
    animateIn: 'fadeIn',   // الدخول بتأثير Fade
    smartSpeed: 300 ,       // سرعة الانتقال بالمللي ثانية
    responsive:{
      0:{ items:1 },
      600:{ items:1 },
      1000:{ items:1 }
    }
  });
});



// احصائيات الشارت الأول (فقط في لوحة التحكم)
if (document.getElementById('collectionChart')) {
const ctx1 = document.getElementById('collectionChart').getContext('2d');
new Chart(ctx1, {
  type: 'bar',
  data: {
    labels: ['يناير', 'فبراير', 'مارس', 'ابريل', 'مايو', 'يونيو', 'يوليو'],
    datasets: [
      {
        label: 'مسددين',
        data: [40, 35, 38, 32, 34, 50, 45],
        backgroundColor: '#FFA722',
        borderRadius: 5,
        barThickness: 20
      },
      {
        label: 'غير مسددين',
        data: [45, 40, 35, 30, 25, 50, 48],
        backgroundColor: '#F4F4F4',
        borderRadius: 5,
        barThickness: 20
      }
    ]
  },
  options: {
    responsive: false,
    maintainAspectRatio: false,
    scales: {
      y: {
        position: 'right', // ✅ الأرقام على اليمين
        beginAtZero: true,
        max: 50,
        ticks: {
          callback: function(value) {
            return value + 'k';
          },
          stepSize: 10
        },
        grid: {
          drawTicks: false,
          color: '#eee'
        }
      },
      x: {
        grid: {
          display: false
        }
      }
    },
    plugins: {
      legend: {
        display: false
      }
    }
  }
});
}

// احصائيات الشارت الثاني
if (document.getElementById('orderStatusChart')) {
const ctx2 = document.getElementById('orderStatusChart').getContext('2d');
const data = {
  labels: ['تمرير الأقساط ', 'تعديل المديونية', 'طلبات الجدولة'], // You might infer 'Pending' from total - completed - rejected
  datasets: [{
    data: [4879, 2124, 8143 - 4879 - 2124], // Corresponding values from the image
    backgroundColor: [
      '#FFB74D', // Color for 'Completed' (orange)
      '#5C6BC0', // Color for 'Rejected' (blue/purple)
      '#FFCC80'  // Color for 'Pending' (lighter orange, inferred)
    ],
    borderColor: [
      '#FFB74D',
      '#5C6BC0',
      '#FFCC80'
    ],
    borderWidth: 1
  }]
};

const options = {
  cutout: '70%', // To create the donut effect
  responsive: true,
  plugins: {
    legend: {
      display: false // Hide default legend as the image has a custom one
    },
    tooltip: {
      enabled: true
    }
  }
};

const orderStatusChart = new Chart(ctx2, {
  type: 'doughnut',
  data: data,
  options: options
});
}

// احصائيات الشارت الثالث
if (document.getElementById('orderStatusChart3')) {
const ctx3 = document.getElementById('orderStatusChart3').getContext('2d');

const data2 = {
  labels: ['جميع الطلبات', 'مرفوض ', 'تم الإنجاز'],
  datasets: [{
    data: [4879, 2124, 8143 - 4879 - 2124],
    backgroundColor: [
      '#FFC989', // أخضر مختلف
      '#5866B7', // أزرق فاتح
      '#F29220'  // برتقالي غامق
    ],
    borderColor: [
      '#FFC989',
      '#5866B7',
      '#F29220'
    ],
    borderWidth: 1
  }]
};

const options2 = {
  cutout: '70%',
  responsive: true,
  plugins: {
    legend: {
      display: false
    },
    tooltip: {
      enabled: true
    }
  }
};

const orderStatusChart3 = new Chart(ctx3, {
  type: 'doughnut',
  data: data2,
  options: options2
});
}




