<?php /* templateo/org_pyramid.php */ ?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<title>الهيكل التنظيمي</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@400;700&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
:root{
  --blue:#001f3f; --orange:#FF8C00; --glass:rgba(255,255,255,.08); --border:rgba(255,255,255,.2);
}
body{font-family:'Tajawal',sans-serif;background:linear-gradient(135deg,var(--blue),#34495e,var(--orange));background-size:400% 400%;animation:grad 20s ease infinite;color:#fff;min-height:100vh;overflow-x:hidden}
@keyframes grad{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}

.container-box{max-width:1400px;margin:30px auto;padding:20px}
.header-title{font-family:'El Messiri',sans-serif;text-align:center;margin-bottom:8px;text-shadow:0 4px 14px rgba(0,0,0,.45)}
.sub{opacity:.85;text-align:center;margin-bottom:14px}

.board{
  position:relative;background:var(--glass);backdrop-filter:blur(12px);
  border:1px solid var(--border);border-radius:16px;box-shadow:0 18px 40px rgba(0,0,0,.35);
  padding:40px 20px;min-height:500px;overflow:auto;
}
.grid{display:flex;flex-direction:column;gap:50px;align-items:center}

.level{
    display:flex;gap:25px;justify-content:center;flex-wrap:wrap;min-height:92px;
    animation: fadeInUp .5s both;
}
<?php for ($i = 0; $i < 10; $i++): /* Staggered delay for levels */ ?>
.level:nth-child(<?php echo $i + 1; ?>) { animation-delay: <?php echo $i * 0.15; ?>s; }
<?php endfor; ?>
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.node{
  /* FIX: Added z-index to ensure nodes are on top of SVG lines */
  position:relative; z-index: 1; 
  min-width:240px;max-width:280px;background:linear-gradient(180deg,rgba(255,255,255,.12),rgba(255,255,255,.06));
  border:1px solid rgba(255,255,255,.25);border-radius:14px;padding:12px 14px;
  box-shadow:inset 0 1px 0 rgba(255,255,255,.25), 0 10px 32px rgba(0,0,0,.25);
  transition: transform .3s, box-shadow .3s, background-color .3s;
  text-align:center;
}
.node:hover{
  transform:translateY(-5px) scale(1.03);
  box-shadow:inset 0 1px 0 rgba(255,255,255,.3), 0 16px 42px rgba(0,0,0,.4);
  background-color: rgba(255, 140, 0, 0.1);
}
.node.dimmed { opacity: 0.3; filter: grayscale(50%); }

/* FIX: Restored the employee ID badge style */
.badge-id{
  position:absolute;right:-8px;top:-12px; z-index:2;
  background:linear-gradient(135deg,#ffa94d,#ff7f00);
  color:#1b1b1b;border-radius:999px;padding:3px 10px;font-size:12px;border:1px solid rgba(0,0,0,.1);
  box-shadow:0 4px 10px rgba(0,0,0,.25);
  font-weight: 700;
}

.name{font-weight:700; font-size:1.1rem;}
.title{font-size:.9rem;opacity:.8; margin-top:4px;}

/* FIX: Corrected z-index to ensure SVG is visible but behind nodes */
svg#links{position:absolute;inset:0;pointer-events:none; z-index: 0;}

path.link{
  fill:none;stroke:#fff;
  /* FIX: Increased opacity and width for better visibility */
  stroke-opacity:.4;stroke-width:2px; 
  filter:drop-shadow(0 2px 4px rgba(0,0,0,.35));
  transition: all .4s ease-in-out;
}
path.link.highlight {
    stroke: #FFB84D;
    stroke-opacity: 1;
    stroke-width: 3px;
}
</style>
</head>
<body>

<div class="container-box">
  <h2 class="header-title mt-3">الهيكل التنظيمي</h2>
  <div class="sub small">
     
  </div>

  <div class="board">
    <svg id="links"></svg>
    <?php if (empty($levels)): ?>
      <div class="text-center py-5 opacity-75">لا توجد بيانات لعرضها.</div>
    <?php else: ?>
      <div class="grid" id="grid">
        <?php foreach ($levels as $lvl => $nodes): ?>
          <div class="level" data-level="<?php echo $lvl; ?>">
            <?php foreach ($nodes as $u): $p = $map[$u] ?? (object)['name'=>$u,'title'=>'—']; ?>
              <div class="node" id="n-<?php echo htmlspecialchars($u); ?>" data-id="<?php echo htmlspecialchars($u); ?>">
                <span class="badge-id">#<?php echo htmlspecialchars($u); ?></span>
                <div class="name"><?php echo htmlspecialchars($p->name); ?></div>
                <div class="title"><?php echo htmlspecialchars($p->title); ?></div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</div>

<script>
window.addEventListener('load', () => {
    const EDGES = <?php echo json_encode($edges ?? []); ?>;
    const svg = document.getElementById('links');
    const board = document.querySelector('.board');
    const allNodes = document.querySelectorAll('.node');
    let allLinks = [];

    if (!EDGES.length || !svg || !board) return;

    const drawLinks = () => {
        svg.innerHTML = '';
        allLinks = [];
        const boardRect = board.getBoundingClientRect();

        EDGES.forEach(([parent, child]) => {
            const parentNode = document.getElementById('n-' + parent);
            const childNode = document.getElementById('n-' + child);
            if (!parentNode || !childNode) return;

            const pRect = parentNode.getBoundingClientRect();
            const cRect = childNode.getBoundingClientRect();

            const x1 = pRect.left - boardRect.left + board.scrollLeft + (pRect.width / 2);
            const y1 = pRect.top - boardRect.top + board.scrollTop + pRect.height;
            const x2 = cRect.left - boardRect.left + board.scrollLeft + (cRect.width / 2);
            const y2 = cRect.top - boardRect.top + board.scrollTop;

            const d = `M ${x1},${y1} C ${x1},${y1 + 70} ${x2},${y2 - 70} ${x2},${y2}`;
            const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
            path.setAttribute('d', d);
            path.classList.add('link');
            path.dataset.parent = parent;
            path.dataset.child = child;
            svg.appendChild(path);
            allLinks.push(path);

            const length = path.getTotalLength();
            path.style.strokeDasharray = length;
            path.style.strokeDashoffset = length;
            setTimeout(() => { path.style.strokeDashoffset = '0'; }, 100);
        });
    };

    const setupInteractivity = () => {
        allNodes.forEach(node => {
            node.addEventListener('mouseenter', () => {
                const currentId = node.dataset.id;
                allNodes.forEach(n => n.classList.add('dimmed'));
                node.classList.remove('dimmed');

                allLinks.forEach(link => {
                    const isUpstream = link.dataset.child === currentId;
                    const isDownstream = link.dataset.parent === currentId;
                    if (isUpstream || isDownstream) {
                        link.classList.add('highlight');
                        document.getElementById('n-' + link.dataset.parent)?.classList.remove('dimmed');
                        document.getElementById('n-' + link.dataset.child)?.classList.remove('dimmed');
                    }
                });
            });

            node.addEventListener('mouseleave', () => {
                allNodes.forEach(n => n.classList.remove('dimmed'));
                allLinks.forEach(link => link.classList.remove('highlight'));
            });
        });
    };
    
    // Use ResizeObserver to ensure lines are drawn only after the layout is stable
    const observer = new ResizeObserver(() => {
        requestAnimationFrame(drawLinks);
    });

    // FIX: Delay the initial observation to let CSS animations finish
    setTimeout(() => {
        observer.observe(board);
        drawLinks(); // Initial draw
    }, 600); // Animation is 500ms, so 600ms is a safe margin.
    
    board.addEventListener('scroll', () => requestAnimationFrame(drawLinks));
    
    setupInteractivity();
});
</script>
</body>
</html>