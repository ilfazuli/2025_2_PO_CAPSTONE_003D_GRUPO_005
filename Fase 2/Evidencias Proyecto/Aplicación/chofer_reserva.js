document.addEventListener('DOMContentLoaded', () => {
  const titleEl = document.getElementById('cal-title');
  const bodyEl  = document.getElementById('cal-body');
  const prevBtn = document.getElementById('cal-prev');
  const nextBtn = document.getElementById('cal-next');
  const hiddenFecha = document.getElementById('fechaSeleccionada');
  const selectMotivo = document.getElementById('motivo');
  const reservarBtn  = document.querySelector('.reservar-btn');

  if (!titleEl || !bodyEl || !prevBtn || !nextBtn) return;

  const hoy = new Date();
  const state = { current:new Date(hoy.getFullYear(), hoy.getMonth(), 1), selected:null };
  const fmtISO = d => `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
  const sameDay = (a,b)=>a&&b&&a.getFullYear()===b.getFullYear()&&a.getMonth()===b.getMonth()&&a.getDate()===b.getDate();

  function render(){
    titleEl.textContent = state.current.toLocaleDateString('es-CL',{month:'long',year:'numeric'}).toUpperCase();
    bodyEl.innerHTML = '';
    const y = state.current.getFullYear(), m = state.current.getMonth();
    const first = new Date(y,m,1), firstW = first.getDay();
    const daysIn = new Date(y,m+1,0).getDate();
    const daysPrev = new Date(y,m,0).getDate();
    let day=1, next=1;
    for(let r=0;r<6;r++){
      const tr=document.createElement('tr');
      for(let c=0;c<7;c++){
        const i=r*7+c, td=document.createElement('td');
        if(i<firstW){ td.textContent=daysPrev-(firstW-1-i); td.className='prev'; }
        else if(day<=daysIn){
          const d=new Date(y,m,day); td.textContent=day;
          if(sameDay(d,hoy)) td.classList.add('hoy');
          if(sameDay(d,state.selected)) td.classList.add('sel');
          td.dataset.date=fmtISO(d); day++;
        }else{ td.textContent=next++; td.className='next'; }
        tr.appendChild(td);
      }
      bodyEl.appendChild(tr);
    }
  }

  prevBtn.addEventListener('click',()=>{ state.current=new Date(state.current.getFullYear(), state.current.getMonth()-1,1); render(); });
  nextBtn.addEventListener('click',()=>{ state.current=new Date(state.current.getFullYear(), state.current.getMonth()+1,1); render(); });

  bodyEl.addEventListener('click',(e)=>{
    const td=e.target.closest('td'); if(!td) return;
    const iso=td.dataset.date; if(!iso) return; // ignora prev/next
    const [Y,M,D]=iso.split('-').map(Number); state.selected=new Date(Y,M-1,D);
    hiddenFecha && (hiddenFecha.value=iso);
    bodyEl.querySelectorAll('td.sel').forEach(el=>el.classList.remove('sel'));
    td.classList.add('sel'); checkReady();
  });

  function checkReady(){
  if(!reservarBtn) return;
  const tieneFecha  = !!(hiddenFecha && hiddenFecha.value);
  const tieneMotivo = !!(selectMotivo && selectMotivo.value);
  reservarBtn.disabled = !(tieneFecha && tieneMotivo);
  }
  selectMotivo && selectMotivo.addEventListener('change', checkReady);

  render(); checkReady();
});
document.addEventListener('DOMContentLoaded', () => {
  const clickables = [
    ...document.querySelectorAll('.cal-nav'),
    ...document.querySelectorAll('.reservar-btn')
  ];
  function addRipple(e, el){
    if (el.disabled) return;
    const rect = el.getBoundingClientRect();
    const size = Math.max(rect.width, rect.height);
    const x = e.clientX - rect.left - size/2;
    const y = e.clientY - rect.top  - size/2;
    const span = document.createElement('span');
    span.className = 'btn-ripple';
    span.style.width = span.style.height = `${size}px`;
    span.style.left = `${x}px`;
    span.style.top  = `${y}px`;
    el.querySelectorAll('.btn-ripple').forEach(r => r.remove());
    el.appendChild(span);
    span.addEventListener('animationend', () => span.remove());
  }
  clickables.forEach(el => {
    el.addEventListener('click', (e) => addRipple(e, el));
    el.setAttribute('tabindex','0');
    el.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); el.click(); }
    });
  });
});