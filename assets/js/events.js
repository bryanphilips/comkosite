/* Events Calendar — Dynamic Loader + Filters + Pagination */
const EventsCalendar = (() => {
  const q = (sel, root=document) => root.querySelector(sel);
  const qa = (sel, root=document) => Array.from(root.querySelectorAll(sel));

  const fmtDate = iso => {
    const d = new Date(iso);
    if (isNaN(d)) return iso;
    return d.toLocaleDateString(undefined, { year:'numeric', month:'short', day:'2-digit', weekday:'short' });
  };
  const isPast = iso => (new Date(iso)) < new Date(new Date().toDateString());

  function renderShell(mount) {
    mount.innerHTML = `
      <div class="events-toolbar">
        <div class="events-search">
          <span>🔎</span>
          <input id="ec-q" type="search" placeholder="Search event or location…">
        </div>
        <div class="events-filter">
          <label>Month:
            <input id="ec-month" type="month">
          </label>
          <label>Show:
            <select id="ec-show">
              <option value="all">All</option>
              <option value="upcoming" selected>Upcoming</option>
              <option value="past">Past</option>
            </select>
          </label>
        </div>
      </div>

      <table class="events-table">
        <thead><tr><th>Date</th><th>Event</th><th>Location</th><th>Add to Calendar</th></tr></thead>
        <tbody></tbody>
      </table>

      <div class="ec-pagination">
        <button id="ec-prev">Prev</button>
        <div class="info" id="ec-info"></div>
        <button id="ec-next">Next</button>
      </div>
    `;
  }

  function renderRows(mount, events) {
    const tbody = q('tbody', mount);
    tbody.innerHTML = events.map(ev => {
      const badge = isPast(ev.date)
        ? `<span class="badge-past">Past</span>`
        : `<span class="badge-upcoming">Upcoming</span>`;
      return `<tr>
        <td data-label="Date">${fmtDate(ev.date)}<div>${badge}</div></td>
        <td data-label="Event"><strong>${ev.title}</strong>${ev.description ? `<div class="muted small">${ev.description}</div>`:''}</td>
        <td data-label="Location">${ev.location}</td>
        <td data-label="Calendar"><a class="btn-icon" href="${ev.ics}" download>📅 Download </a></td>
      </tr>`;
    }).join('');
  }

  function paginate(list, page, size) {
    const total = list.length;
    const pages = Math.max(1, Math.ceil(total/size));
    const p = Math.min(Math.max(1, page), pages);
    const start = (p-1)*size;
    return { page:p, pages, total, slice: list.slice(start, start+size) };
  }

  function bind(mount, events) {
    const SIZE = 6;
    let state = { q:'', month:'', show:'upcoming', page:1 };

    const UI = {
      q: q('#ec-q', mount),
      month: q('#ec-month', mount),
      show: q('#ec-show', mount),
      prev: q('#ec-prev', mount),
      next: q('#ec-next', mount),
      info: q('#ec-info', mount),
    };

    const applyFilters = () => {
      let list = events.slice().sort((a,b)=> new Date(a.date) - new Date(b.date));
      const today = new Date(new Date().toDateString());

      if (state.q) {
        const needle = state.q.toLowerCase();
        list = list.filter(ev =>
          ev.title.toLowerCase().includes(needle) ||
          ev.location.toLowerCase().includes(needle) ||
          ev.description.toLowerCase().includes(needle)
        );
      }

      if (state.month) {
        const [y,m] = state.month.split('-').map(Number);
        list = list.filter(ev => {
          const d = new Date(ev.date);
          return d.getFullYear()===y && (d.getMonth()+1)===m;
        });
      }

      if (state.show==='upcoming') list = list.filter(ev => new Date(ev.date) >= today);
      if (state.show==='past') list = list.filter(ev => new Date(ev.date) < today);

      return list;
    };

    const refresh = () => {
      const filtered = applyFilters();
      const { page, pages, total, slice } = paginate(filtered, state.page, SIZE);
      renderRows(mount, slice);
      UI.info.textContent = `${page} / ${pages} · ${total} events`;
      UI.prev.disabled = (page<=1);
      UI.next.disabled = (page>=pages);
    };

    UI.q.addEventListener('input', ()=>{ state.q=UI.q.value.trim(); state.page=1; refresh(); });
    UI.month.addEventListener('input', ()=>{ state.month=UI.month.value; state.page=1; refresh(); });
    UI.show.addEventListener('change', ()=>{ state.show=UI.show.value; state.page=1; refresh(); });
    UI.prev.addEventListener('click', ()=>{ state.page--; refresh(); });
    UI.next.addEventListener('click', ()=>{ state.page++; refresh(); });

    refresh();
  }

  async function init({ mount, dataUrl }) {
    const root = typeof mount==='string' ? q(mount) : mount;
    if (!root) return;

    renderShell(root);

    let events = [];
    try {
      const res = await fetch(dataUrl, { cache:'no-store' });
      events = await res.json();
    } catch { events = []; }

    events = events.map(e => ({
      title: e.title || e.event || 'Untitled Event',
      date: e.date,
      end: e.end || null,
      location: e.location || '',
      description: e.description || '',
      ics: e.ics || '/assets/events/sample-event.ics'
    }));

    bind(root, events);
  }

  return { init };
})();