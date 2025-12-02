document.addEventListener('DOMContentLoaded', () => {
    const dlgFotoFull = document.getElementById('dlgFotoFull');
    const imgFotoFull = document.getElementById('fotoFull');
    const csrf = document.querySelector('meta[name="csrf-token"]').content;

    // ---- TOAST ASIGNACIÓN ----
    const toast = document.getElementById('asignacionToast');
    if (toast) {
        setTimeout(() => { toast.hidden = false; }, 800);
        toast.querySelector('[data-toast-dismiss]')?.addEventListener('click', () => {
            toast.hidden = true;
        });
    }
    document.addEventListener('click', (e) => {
        const img = e.target.closest('.info-foto');
        if (!img) return;

        const fullUrl = img.dataset.full || img.src;
        imgFotoFull.src = fullUrl;
        if (typeof dlgFotoFull.showModal === 'function') {
            dlgFotoFull.showModal();
        }
    });
    // ---- EFECTO RIPPLE EN BOTONES ----
    document.querySelectorAll('.btn').forEach(btn => {
        btn.setAttribute('data-ripple', '1');
        btn.addEventListener('click', (e) => {
            const r = document.createElement('span');
            const rect = btn.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            r.className = 'ripple';
            r.style.width = r.style.height = `${size}px`;
            r.style.left = `${e.clientX - rect.left - size / 2}px`;
            r.style.top = `${e.clientY - rect.top - size / 2}px`;
            btn.querySelectorAll('.ripple').forEach(x => x.remove());
            btn.appendChild(r);
            r.addEventListener('animationend', () => r.remove());
        });
    });

    // ---- FILTROS ----
    const filtroTexto = document.getElementById('filtroTexto');
    const filtroEstado = document.getElementById('filtroEstado');
    const lista = document.getElementById('listaOT');

    function normaliza(s) {
        return (s || '').toString().toLowerCase();
    }

    function aplicaFiltros() {
        const txt = normaliza(filtroTexto?.value);
        const est = (filtroEstado?.value || '').toLowerCase();

        lista.querySelectorAll('.ot-card').forEach(card => {
            const id = normaliza(card.dataset.id);
            const head = normaliza(card.querySelector('.ot-head').innerText);
            const okTxt = !txt || id.includes(txt) || head.includes(txt);
            const okEst = !est || (card.dataset.estado === est);
            card.style.display = (okTxt && okEst) ? '' : 'none';
        });
    }
    filtroTexto?.addEventListener('input', aplicaFiltros);
    filtroEstado?.addEventListener('change', aplicaFiltros);

    // ---- RELOJ EN VIVO ----
    function iniciarReloj(card) {
        const span = card.querySelector('.ot-tiempo');
        if (!span) return;

        let baseSeg = parseInt(span.dataset.tiempoBase || '0', 10);
        let inicio = span.dataset.ultimoInicio ? new Date(span.dataset.ultimoInicio) : null;

        if (span._interval) clearInterval(span._interval);

        span._interval = setInterval(() => {
            if (card.dataset.estado !== 'en-curso') return;

            let total = baseSeg;
            if (inicio) {
                const ahora = new Date();
                const diffSeg = Math.floor((ahora - inicio) / 1000);
                total = baseSeg + diffSeg;
            }

            const h = String(Math.floor(total / 3600)).padStart(2, '0');
            const m = String(Math.floor((total % 3600) / 60)).padStart(2, '0');
            const s = String(total % 60).padStart(2, '0');
            span.textContent = `${h}:${m}:${s}`;
        }, 1000);
    }

    // activar relojes al cargar
    document.querySelectorAll('.ot-card').forEach(card => {
        if (card.dataset.estado === 'en-curso') {
            iniciarReloj(card);
        }
    });

    // ---- DIALOGOS ----
    const dlgInfo = document.getElementById('dlgInfo');
    const infoFolio = document.getElementById('infoFolio');
    const infoPatente = document.getElementById('infoPatente');
    const infoMarca = document.getElementById('infoMarca');
    const infoModelo = document.getElementById('infoModelo');
    const infoAnio = document.getElementById('infoAnio');
    const infoMotivo = document.getElementById('infoMotivo');
    const infoComentarios = document.getElementById('infoComentarios');
    const infoFotos = document.getElementById('infoFotos');

    const dlgComentario = document.getElementById('dlgComentario');
    const txtComentario = document.getElementById('comentarioTexto');
    const btnComentarioGuardar = document.getElementById('btnComentarioGuardar');
    let otIdComentario = null;

    const inputFoto = document.getElementById('inputFotoOt');

    // ---- AJAX GENÉRICO ACCIONES ----
    async function enviarAccion(otId, accion) {
        try {
            const resp = await fetch(`/mecanico/ot/${otId}/${accion}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });

            if (!resp.ok) {
                console.error('Error HTTP', resp.status, await resp.text());
                alert('No se pudo ejecutar la acción.');
                return false;
            }
            return true;
        } catch (e) {
            console.error(e);
            alert('Error de comunicación.');
            return false;
        }
    }

    // ---- COMENTARIOS (AJAX) ----
    async function enviarComentario(otId, texto) {
        try {
            const resp = await fetch(`/mecanico/ot/${otId}/comentario`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ texto })
            });

            if (!resp.ok) {
                console.error('Error comentario', resp.status, await resp.text());
                alert('Error al guardar comentario');
                return false;
            }
            return true;
        } catch (e) {
            console.error(e);
            alert('Error de comunicación al guardar comentario');
            return false;
        }
    }

    // ---- INFO VEHÍCULO (AJAX) ----
    async function cargarInfo(otId) {
        try {
            const resp = await fetch(`/mecanico/ot/${otId}/info`, {
                headers: { 'Accept': 'application/json' }
            });
            if (!resp.ok) {
                console.error('Error info', resp.status, await resp.text());
                alert('No se pudo cargar la información del vehículo.');
                return;
            }

            const data = await resp.json();

            infoFolio.textContent   = data.ot.folio;
            infoPatente.textContent = data.ot.patente;
            infoMarca.textContent   = data.ot.marca || '-';
            infoModelo.textContent  = data.ot.modelo || '-';
            infoAnio.textContent    = data.ot.anio || '-';
            infoMotivo.textContent  = data.ot.motivo || '-';

            // ----- COMENTARIOS -----
            infoComentarios.innerHTML = '';
            if (data.comentarios && data.comentarios.length) {
                data.comentarios.forEach(c => {
                    const li = document.createElement('li');
                    li.textContent = c;
                    infoComentarios.appendChild(li);
                });
            } else {
                const li = document.createElement('li');
                li.className = 'muted';
                li.textContent = 'Sin comentarios registrados.';
                infoComentarios.appendChild(li);
            }

            // ----- FOTOS -----
            infoFotos.innerHTML = '';                    

            if (data.fotos && data.fotos.length) {
                data.fotos.forEach(f => {
                    const img = document.createElement('img');
                    img.src = f.url;
                    img.dataset.full = f.url;            
                    img.alt = 'Foto vehículo';
                    img.className = 'info-foto';         
                    infoFotos.appendChild(img);
                });
            } else {
                infoFotos.innerHTML =
                    '<p class="muted">Aún no hay fotos registradas.</p>';
            }

            dlgInfo.showModal();

        } catch (e) {
            console.error(e);
            alert('Error al cargar la información del vehículo.');
        }
    }


    // ---- SUBIR FOTO (AJAX) ----
    async function subirFoto(otId, file) {
        const formData = new FormData();
        formData.append('foto', file);
        formData.append('tipo', 'PROCESO');

        try {
            const resp = await fetch(`/mecanico/ot/${otId}/foto`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrf },
                body: formData
            });

            if (!resp.ok) {
                console.error('Error foto', resp.status, await resp.text());
                alert('No se pudo subir la foto.');
                return false;
            }

            alert('Foto subida correctamente.');
            return true;
        } catch (e) {
            console.error(e);
            alert('Error de comunicación al subir la foto.');
            return false;
        }
    }

    // ---- CLICK EN LISTA DE OT ----
    lista.addEventListener('click', async (e) => {
        const btn = e.target.closest('button[data-action]');
        if (!btn) return;

        const card = btn.closest('.ot-card');
        const otId = card.dataset.id;
        const action = btn.dataset.action;

        switch (action) {

            case 'info':
                await cargarInfo(otId);
                break;

            case 'comentario':
                otIdComentario = otId;
                txtComentario.value = '';
                dlgComentario.showModal();
                break;

            case 'foto':
                if (!inputFoto) {
                    alert('No se encontró el input de foto.');
                    return;
                }
                inputFoto.onchange = async () => {
                    const file = inputFoto.files[0];
                    if (!file) return;
                    await subirFoto(otId, file);
                    inputFoto.value = '';
                };
                inputFoto.click();
                break;

            case 'iniciar':
                if (await enviarAccion(otId, 'iniciar')) {
                    card.dataset.estado = 'en-curso';
                    const span = card.querySelector('.ot-tiempo');
                    span.dataset.ultimoInicio = new Date().toISOString();
                    iniciarReloj(card);

                    btn.disabled = true;
                    card.querySelector('[data-action="pausar"]').disabled = false;
                    card.querySelector('[data-action="finalizar"]').disabled = false;
                }
                break;

            case 'pausar':
                if (await enviarAccion(otId, 'pausar')) {
                    card.dataset.estado = 'pausado';
                    const span = card.querySelector('.ot-tiempo');

                    // guardar como base lo que se ve en pantalla
                    const [h, m, s] = span.textContent.split(':').map(x => parseInt(x, 10) || 0);
                    const totalSeg = h * 3600 + m * 60 + s;
                    span.dataset.tiempoBase = totalSeg.toString();
                    span.dataset.ultimoInicio = '';

                    if (span._interval) clearInterval(span._interval);

                    btn.disabled = true;
                    card.querySelector('[data-action="iniciar"]').disabled = false;
                    card.querySelector('[data-action="finalizar"]').disabled = true;
                }
                break;

            case 'finalizar':
                if (!confirm('¿Finalizar trabajo?')) return;

                if (await enviarAccion(otId, 'finalizar')) {
                    const span = card.querySelector('.ot-tiempo');
                    if (span && span._interval) clearInterval(span._interval);
                    card.dataset.estado = 'finalizado';
                    card.remove();
                    alert('Trabajo finalizado. La OT queda lista para que el Jefe de Taller la cierre.');
                }
                break;
        }
    });

    // ---- GUARDAR COMENTARIO DESDE EL MODAL ----
    btnComentarioGuardar?.addEventListener('click', async (e) => {
        e.preventDefault();
        const texto = (txtComentario.value || '').trim();
        if (!texto) {
            alert('Escribe un comentario.');
            return;
        }
        if (!otIdComentario) {
            alert('OT no válida.');
            return;
        }

        const ok = await enviarComentario(otIdComentario, texto);
        if (ok) {
            dlgComentario.close();
            alert('Comentario guardado.');
        }
    });

    // aplicar filtros iniciales
    aplicaFiltros();



    
});
