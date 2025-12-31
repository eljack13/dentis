<?php

use yii\helpers\Url;
use yii\helpers\Html;

/** @var yii\web\View $this */

$this->title = 'Agenda';
$this->params['breadcrumbs'][] = $this->title;

$eventsUrl    = Url::to(['cita/events']);
$createUrl    = Url::to(['cita/create-ajax']);
$catalogosUrl = Url::to(['cita/catalogos']);
$moveUrl      = Url::to(['cita/move-ajax']);
$infoUrl      = Url::to(['cita/info-ajax']);
$deleteUrl    = Url::to(['cita/delete-ajax']);
$updateAjaxUrl = Url::to(['cita/update-ajax']);

$csrfToken = Yii::$app->request->getCsrfToken();

$this->registerCssFile('https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css', ['position' => \yii\web\View::POS_HEAD]);
$this->registerJsFile('https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js', ['position' => \yii\web\View::POS_END]);
$this->registerJsFile('https://cdn.jsdelivr.net/npm/sweetalert2@11', ['position' => \yii\web\View::POS_END]);

$this->registerCss(<<<CSS
.agenda-header{display:flex;align-items:flex-end;justify-content:space-between;gap:16px;margin-bottom:14px;}
.agenda-title{font-weight:800;letter-spacing:-.02em;margin:0;}
.agenda-subtitle{color:#6b7280;margin-top:4px;}
#calendarWrap{background:#fff;border-radius:20px;padding:14px;box-shadow:0 14px 34px rgba(0,0,0,.08);overflow:hidden;border:1px solid rgba(0,0,0,.06);}
#calendar{min-height:760px;}
.fc .fc-toolbar{padding:10px 10px 14px;}
.fc .fc-toolbar-title{font-size:1.25rem;font-weight:800;letter-spacing:-.01em;}
.fc .fc-button{border-radius:12px !important;padding:.45rem .75rem !important;font-weight:700;}
.fc .fc-button-primary{border-color:rgba(0,0,0,.10) !important;background:rgba(0,0,0,.06) !important;color:#111827 !important;}
.fc .fc-button-primary:hover{background:rgba(0,0,0,.10) !important;}
.fc .fc-button-primary:not(:disabled).fc-button-active{background:#111827 !important;border-color:#111827 !important;color:#fff !important;}
.fc .fc-daygrid-day-number{font-weight:700;color:#111827;}
.fc .fc-col-header-cell-cushion{padding:10px 0;color:#6b7280;font-weight:800;text-transform:capitalize;}
.fc .fc-daygrid-event{border-radius:12px;padding:3px 6px;border:1px solid rgba(0,0,0,.12);box-shadow:0 8px 18px rgba(0,0,0,.06);}
.fc .fc-day-today{background:rgba(17,24,39,.03) !important;}
.swal2-popup{border-radius:18px !important;padding:18px !important;}
.swal2-title{font-weight:900 !important;letter-spacing:-.02em;}
.swal2-html-container{margin-top:10px !important;}
.sa-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px;}
.sa-field label{display:block;font-weight:800;margin-bottom:6px;color:#111827;}
.sa-input,.sa-select,.sa-textarea{width:100%;border-radius:14px;border:1px solid rgba(0,0,0,.12);padding:10px 12px;outline:none;}
.sa-input:focus,.sa-select:focus,.sa-textarea:focus{border-color:rgba(17,24,39,.35);box-shadow:0 0 0 4px rgba(17,24,39,.08);}
.sa-textarea{min-height:90px;resize:vertical;}
.sa-help{margin-top:8px;color:#6b7280;font-size:.92rem;}
.sa-inline{display:flex;gap:10px;align-items:center;flex-wrap:wrap;}
.sa-pill{padding:7px 10px;border-radius:999px;background:rgba(0,0,0,.05);border:1px solid rgba(0,0,0,.08);font-weight:800;color:#111827;font-size:.85rem;}
CSS);

$this->registerJs(<<<JS
function escapeHtml(str) {
  return String(str ?? '')
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#039;');
}

function buildErrorMessage(data){
  let msg = (data && data.message) ? data.message : 'Ocurrió un error.';
  if (data && data.errors) {
    const lines = Object.entries(data.errors).map(function(pair){
      const k = pair[0];
      const v = pair[1] || [];
      return '• ' + k + ': ' + v.join(', ');
    });
    msg += "\\n\\n" + lines.join("\\n");
  }
  return msg;
}

async function fetchJsonOrThrow(url){
 const res = await fetch(url, { 
    method: 'GET',
    credentials: 'same-origin', 
    headers: { 
      'Accept': 'application/json',
      'X-Requested-With': 'XMLHttpRequest' 
    }
  });
  const text = await res.text();
  let json;
  try { json = JSON.parse(text); } catch (e) { throw new Error(text.substring(0, 220)); }
  if (!res.ok) throw new Error((json && json.message) ? json.message : 'Error al consultar el servidor.');
  return json;
}

async function postJsonOrThrow(url, payload, csrf){
  const res = await fetch(url, {
    method: 'POST',
    credentials: 'same-origin', 
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
      'X-CSRF-Token': csrf
    },
    body: JSON.stringify(payload || {})
  });
  const text = await res.text();
  let json;
  try { json = JSON.parse(text); } catch (e) { throw new Error(text.substring(0, 220)); }
  if (!res.ok || !json.success) throw new Error(buildErrorMessage(json));
  return json;
}

function fmtFromMysql(s){
  if (!s) return '';
  const d = new Date(String(s).replace(' ', 'T'));
  if (isNaN(d.getTime())) return s;
  const pad = (n)=>String(n).padStart(2,'0');
  return d.getFullYear() + '-' + pad(d.getMonth()+1) + '-' + pad(d.getDate()) + 'T' + pad(d.getHours()) + ':' + pad(d.getMinutes());
}

function fmtNice(s){
  if (!s) return '-';
  const d = new Date(String(s).replace(' ', 'T'));
  if (isNaN(d.getTime())) return s;
  return d.toLocaleString('es-MX', { weekday:'short', year:'numeric', month:'short', day:'2-digit', hour:'2-digit', minute:'2-digit' });
}

document.addEventListener('DOMContentLoaded', function () {
  if (typeof FullCalendar === 'undefined') return;
  if (typeof Swal === 'undefined') return;

  const el = document.getElementById('calendar');
  if (!el) return;

  const calendar = new FullCalendar.Calendar(el, {
    locale: 'es',
    initialView: 'dayGridMonth',
    height: 'auto',
    nowIndicator: true,
    selectable: true,
    editable: true,
    eventDurationEditable: true,
    eventStartEditable: true,
    expandRows: true,

    headerToolbar: {
      left: 'prev,next today',
      center: 'title',
      right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
    },

    firstDay: 1,
    allDaySlot: false,
    slotMinTime: '07:00:00',
    slotMaxTime: '22:00:00',
    slotDuration: '00:15:00',
    dayMaxEvents: true,

    events: {
      url: '$eventsUrl',
      method: 'GET',
      failure: function() { Swal.fire({ icon:'error', title:'No se pudieron cargar las citas' }); }
    },

    eventDrop: async function(info) {
      const e = info.event;

      const ok = await Swal.fire({
        title: '¿Guardar cambio?',
        text: 'Se moverá la cita a la nueva fecha/hora.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, guardar',
        cancelButtonText: 'Cancelar'
      });

      if (!ok.isConfirmed) { info.revert(); return; }

      try {
        await postJsonOrThrow('$moveUrl', {
          id: e.id,
          start: e.start ? e.start.toISOString() : null,
          end: e.end ? e.end.toISOString() : null
        }, '$csrfToken');

        Swal.fire({ icon:'success', title:'Actualizado', timer:900, showConfirmButton:false });
        calendar.refetchEvents();
      } catch (err) {
        info.revert();
        Swal.fire({ icon:'error', title:'Error', text: err.message });
      }
    },

    dateClick: async function(info) {
    const base = info.date; // Date real
    const pad = (n)=>String(n).padStart(2,'0');
    const toLocalInput = (d)=>{
      const x = new Date(d.getTime());
      return x.getFullYear() + '-' + pad(x.getMonth()+1) + '-' + pad(x.getDate()) + 'T' + pad(x.getHours()) + ':' + pad(x.getMinutes());
    };

    const defaultStart = new Date(base.getTime());
    defaultStart.setHours(9, 0, 0, 0); // default 09:00 am (cámbialo si quieres)

    try {
      const cat = await fetchJsonOrThrow('$catalogosUrl');
      if (!cat.success) throw new Error(cat.message || 'No se pudieron cargar catálogos.');

      const pacientes = cat.pacientes || [];
      const servicios = cat.servicios || [];

      const pacientesOptions = pacientes.map(function(p){
        return '<option value="' + p.id + '">' + escapeHtml(p.nombre) + '</option>';
      }).join('');

      const serviciosOptions = servicios.map(function(s){
        const extra = s.duracion_min ? ' (' + s.duracion_min + ' min)' : '';
        return '<option value="' + s.id + '" data-min="' + (s.duracion_min ?? '') + '">' + escapeHtml(s.nombre) + extra + '</option>';
      }).join('');

      const result = await Swal.fire({
        title: 'Nueva cita',
        confirmButtonText: 'Crear cita',
        cancelButtonText: 'Cancelar',
        showCancelButton: true,
        focusConfirm: false,
        width: 820,
        html:
          '<div class="sa-grid">' +
          '  <div class="sa-field">' +
          '    <label>Inicio (elige fecha y hora)</label>' +
          '    <input id="sw-inicio" type="datetime-local" class="sa-input" value="' + escapeHtml(toLocalInput(defaultStart)) + '">' +
          '    <div class="sa-help">Selecciona la hora exacta.</div>' +
          '  </div>' +
          '  <div class="sa-field">' +
          '    <label>Fin</label>' +
          '    <input id="sw-fin" type="datetime-local" class="sa-input" value="">' +
          '    <div class="sa-help">Opcional. Si lo dejas vacío, se calcula con la duración del servicio.</div>' +
          '  </div>' +
          '</div>' +

          '<div class="sa-grid" style="margin-top:12px;">' +
          '  <div class="sa-field">' +
          '    <label>Paciente</label>' +
          '    <input id="sw-paciente-search" class="sa-input" placeholder="Buscar paciente..." autocomplete="off">' +
          '    <select id="sw-paciente" class="sa-select" size="8" style="margin-top:8px;">' + pacientesOptions + '</select>' +
          '  </div>' +

          '  <div class="sa-field">' +
          '    <label>Servicio</label>' +
          '    <input id="sw-servicio-search" class="sa-input" placeholder="Buscar servicio..." autocomplete="off">' +
          '    <select id="sw-servicio" class="sa-select" size="8" style="margin-top:8px;">' + serviciosOptions + '</select>' +
          '    <div class="sa-help">La duración puede calcular el fin automáticamente.</div>' +
          '  </div>' +
          '</div>' +

          '<div class="sa-field" style="margin-top:12px;">' +
          '  <label>Notas</label>' +
          '  <textarea id="sw-notas" class="sa-textarea" placeholder="Opcional"></textarea>' +
          '</div>',
        didOpen: function() {
          const pacienteSearch = document.getElementById('sw-paciente-search');
          const servicioSearch = document.getElementById('sw-servicio-search');
          const pacienteSel = document.getElementById('sw-paciente');
          const servicioSel = document.getElementById('sw-servicio');

          if (servicioSel.options.length > 0) servicioSel.selectedIndex = 0;

          const filterSelect = function(input, select){
            const q = (input.value || '').toLowerCase();
            Array.from(select.options).forEach(function(opt){
              opt.hidden = !opt.textContent.toLowerCase().includes(q);
            });
          };

          pacienteSearch.addEventListener('input', function(){ filterSelect(pacienteSearch, pacienteSel); });
          servicioSearch.addEventListener('input', function(){ filterSelect(servicioSearch, servicioSel); });
        },
        preConfirm: function(){
          const inicio = document.getElementById('sw-inicio').value;
          const fin = document.getElementById('sw-fin').value;
          const paciente_id = document.getElementById('sw-paciente').value;
          const servicio_id = document.getElementById('sw-servicio').value;
          const notas = (document.getElementById('sw-notas').value || '').trim();

          if (!inicio) { Swal.showValidationMessage('Selecciona fecha y hora de inicio.'); return false; }
          if (!paciente_id) { Swal.showValidationMessage('Selecciona un paciente.'); return false; }
          if (!servicio_id) { Swal.showValidationMessage('Selecciona un servicio.'); return false; }

          return { inicio, fin, paciente_id, servicio_id, notas };
        }
      });

      if (!result.isConfirmed || !result.value) return;

      Swal.fire({ title:'Guardando...', allowOutsideClick:false, didOpen: function(){ Swal.showLoading(); } });

      const payload = {
        inicio: result.value.inicio,
        fin: result.value.fin || null,
        paciente_id: result.value.paciente_id,
        servicio_id: result.value.servicio_id,
        notas: result.value.notas
      };

      const res = await fetch('$createUrl', {
      method: 'POST',
      credentials: 'same-origin', // ✅
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-Token': '$csrfToken'
      },
      body: JSON.stringify(payload)
    });

      const text = await res.text();
      let data;
      try { data = JSON.parse(text); } catch (e3) { throw new Error(text.substring(0, 220)); }

      if (!res.ok || !data.success) throw new Error(buildErrorMessage(data));

      Swal.fire({ icon:'success', title:'Cita creada', timer:900, showConfirmButton:false });
      calendar.refetchEvents();

    } catch (e) {
      Swal.fire({ icon:'error', title:'Error', text: e.message });
    }
  },

    eventClick: async function(info) {
      info.jsEvent.preventDefault();
      const id = info.event.id;

      try {
        const data = await fetchJsonOrThrow('$infoUrl?id=' + encodeURIComponent(id));
        if (!data.success) throw new Error(data.message || 'No se pudo cargar la cita.');

        const c = data.cita;

        const cat = await fetchJsonOrThrow('$catalogosUrl');
        if (!cat.success) throw new Error(cat.message || 'No se pudieron cargar catálogos.');

        const pacientes = cat.pacientes || [];
        const servicios = cat.servicios || [];

        const pacientesOptions = pacientes.map(function(p){
          const sel = String(p.id) === String(c.paciente_id) ? ' selected' : '';
          return '<option value="' + p.id + '"' + sel + '>' + escapeHtml(p.nombre) + '</option>';
        }).join('');

        const serviciosOptions = servicios.map(function(s){
          const sel = String(s.id) === String(c.servicio_id) ? ' selected' : '';
          const extra = s.duracion_min ? ' (' + s.duracion_min + ' min)' : '';
          return '<option value="' + s.id + '"' + sel + '>' + escapeHtml(s.nombre) + extra + '</option>';
        }).join('');

        const result = await Swal.fire({
          title: 'Editar cita',
          confirmButtonText: 'Guardar cambios',
          cancelButtonText: 'Cerrar',
          showCancelButton: true,
          showDenyButton: true,
          denyButtonText: 'Eliminar',
          focusConfirm: false,
          width: 820,
          html:
            '<div class="sa-inline" style="margin-bottom:10px;">' +
            '  <div class="sa-pill">🆔 Cita: <b>#' + escapeHtml(c.id) + '</b></div>' +
            '  <div class="sa-pill">📅 ' + escapeHtml(fmtNice(c.inicio)) + '</div>' +
            '</div>' +

            '<div class="sa-grid">' +
            '  <div class="sa-field">' +
            '    <label>Paciente</label>' +
            '    <select id="ed-paciente" class="sa-select" size="8">' + pacientesOptions + '</select>' +
            '  </div>' +

            '  <div class="sa-field">' +
            '    <label>Servicio</label>' +
            '    <select id="ed-servicio" class="sa-select" size="8">' + serviciosOptions + '</select>' +
            '    <div class="sa-help">Al guardar, se recalcula fin con la duración del servicio.</div>' +
            '  </div>' +
            '</div>' +

            '<div class="sa-grid" style="margin-top:12px;">' +
            '  <div class="sa-field">' +
            '    <label>Inicio</label>' +
            '    <input id="ed-inicio" class="sa-input" type="datetime-local" value="' + escapeHtml(fmtFromMysql(c.inicio)) + '">' +
            '  </div>' +
            '  <div class="sa-field">' +
            '    <label>Fin</label>' +
            '    <input id="ed-fin" class="sa-input" type="datetime-local" value="' + escapeHtml(fmtFromMysql(c.fin)) + '">' +
            '  </div>' +
            '</div>' +

            '<div class="sa-field" style="margin-top:12px;">' +
            '  <label>Notas</label>' +
            '  <textarea id="ed-notas" class="sa-textarea" placeholder="Opcional">' + escapeHtml(c.notas || '') + '</textarea>' +
            '</div>',
          preConfirm: function(){
            const paciente_id = document.getElementById('ed-paciente').value;
            const servicio_id = document.getElementById('ed-servicio').value;
            const inicio = document.getElementById('ed-inicio').value;
            const fin = document.getElementById('ed-fin').value;
            const notas = (document.getElementById('ed-notas').value || '').trim();

            if (!paciente_id) { Swal.showValidationMessage('Selecciona un paciente.'); return false; }
            if (!servicio_id) { Swal.showValidationMessage('Selecciona un servicio.'); return false; }
            if (!inicio) { Swal.showValidationMessage('Selecciona inicio.'); return false; }

            return {
              id: c.id,
              paciente_id,
              servicio_id,
              inicio,
              fin,
              notas
            };
          }
        });

        if (result.isDismissed) return;

        if (result.isDenied) {
          const ok = await Swal.fire({
            title: '¿Eliminar cita?',
            text: 'Esta acción no se puede deshacer.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
          });
          if (!ok.isConfirmed) return;

          await postJsonOrThrow('$deleteUrl', { id: c.id }, '$csrfToken');
          Swal.fire({ icon:'success', title:'Eliminada', timer:900, showConfirmButton:false });
          calendar.refetchEvents();
          return;
        }

        Swal.fire({ title:'Guardando...', allowOutsideClick:false, didOpen: function(){ Swal.showLoading(); } });

        await postJsonOrThrow('$updateAjaxUrl', result.value, '$csrfToken');

        Swal.fire({ icon:'success', title:'Actualizada', timer:900, showConfirmButton:false });
        calendar.refetchEvents();

      } catch (e) {
        Swal.fire({ icon:'error', title:'Error', text: e.message });
      }
    }
  });

  calendar.render();
});
JS, \yii\web\View::POS_END);

?>

<div class="agenda-header">
  <div>
    <h2 class="agenda-title"><?= Html::encode($this->title) ?></h2>
    <div class="agenda-subtitle">Click en una fecha para crear una cita. Click en una cita para ver/editar.</div>
  </div>
</div>

<div id="calendarWrap">
  <div id="calendar"></div>
</div>