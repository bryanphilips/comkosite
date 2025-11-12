/* SERVICES v3 — center-pivot staggered reveal animation */
(function(){
  const root = document.querySelector('.services-page');
  if(!root) return;

  const items = root.querySelectorAll('.svc-item');
  if(!('IntersectionObserver' in window)){
    items.forEach(i=>i.classList.add('_in'));
    return;
  }

  const io = new IntersectionObserver(entries=>{
    entries.forEach(ent=>{
      if(ent.isIntersecting){
        ent.target.classList.add('_in');
        io.unobserve(ent.target);
      }
    });
  }, {threshold:.2});

  items.forEach(i=>io.observe(i));
})();