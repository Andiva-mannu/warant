@extends('layouts.app')

@section('content')
  <h2>Users</h2>
  <p class="muted">Simple user management UI — create, edit, delete users.</p>

  <div style="margin-bottom:12px">
    <button id="refresh">Refresh</button>
    <button id="show-create">Create user</button>
  </div>

  <div id="form-wrap" style="display:none;margin-bottom:12px">
    <h3 id="form-title">Create user</h3>
    <div class="form-row">
      <label>Name</label>
      <input id="u-name" type="text">
    </div>
    <div class="form-row">
      <label>Email</label>
      <input id="u-email" type="email">
    </div>
    <div class="form-row">
      <label>Password</label>
      <input id="u-password" type="password">
    </div>
    <div class="form-row">
      <label>Comfirm Password</label>
      <input id="u-password-confirm" type="password-confirm">
    </div>
    <div class="form-row">
      <label><input id="u-is-admin" type="checkbox"> Is admin</label>
    </div>
    <div class="form-row">
      <button id="save-user">Save</button>
      <button id="cancel-form" type="button">Cancel</button>
    </div>
  </div>

  <table id="users-table" aria-live="polite">
    <thead>
      <tr><th>ID</th><th>Name</th><th>Email</th><th>Admin</th><th>Actions</th></tr>
    </thead>
    <tbody></tbody>
  </table>

  <script>
    const apiBase = '/users/data';
    let editId = null;

    function getAuthHeaders(isJson = false) {
      // Use the personal access token stored in localStorage
      const token = localStorage.getItem('api_token');
      if (!token) { alert('Not authenticated — please login'); window.location = '/login'; return {}; }
      const headers = { 'Authorization': 'Bearer ' + token };
      if (isJson) headers['Content-Type'] = 'application/json';
      return headers;
    }

    async function fetchUsers(){
  const headers = getAuthHeaders(false);
  const res = await fetch(apiBase, { headers });
      if(!res.ok){ alert('Could not fetch users'); return; }
      const json = await res.json();
      const tbody = document.querySelector('#users-table tbody'); tbody.innerHTML='';
      const users = json.data || json; // API resource pagination may wrap in data
      users.forEach(u=>{
        const tr = document.createElement('tr');
        tr.innerHTML = `<td>${u.id}</td><td>${u.name}</td><td>${u.email}</td><td>${u.is_admin? 'Yes':'No'}</td><td><button data-id='${u.id}' class='edit'>Edit</button> <button data-id='${u.id}' class='delete'>Delete</button></td>`;
        tbody.appendChild(tr);
      });
    }

    document.getElementById('refresh').addEventListener('click', fetchUsers);
    document.getElementById('show-create').addEventListener('click', ()=>{
      editId=null; document.getElementById('form-title').textContent='Create user'; document.getElementById('form-wrap').style.display='block';
    });
    document.getElementById('cancel-form').addEventListener('click', ()=>{document.getElementById('form-wrap').style.display='none'});

    document.getElementById('save-user').addEventListener('click', async ()=>{
      const name = document.getElementById('u-name').value;
      const email = document.getElementById('u-email').value;
      const password = document.getElementById('u-password').value;
      const password_confirm = document.getElementById('u-password-confirm').value;
      const is_admin = document.getElementById('u-is-admin').checked;
      const payload = {name,email,password,is_admin};
  const headers = getAuthHeaders(true);
  const opts = {method: editId? 'PUT':'POST', headers, body: JSON.stringify(payload)};
      const url = editId? `${apiBase}/${editId}`: apiBase;
      const res = await fetch(url, opts);
      if(!res.ok){ alert('Save failed'); return }
      document.getElementById('form-wrap').style.display='none'; fetchUsers();
    });

    document.querySelector('#users-table').addEventListener('click', async (e)=>{
      if(e.target.classList.contains('delete')){
        const id = e.target.dataset.id; if(!confirm('Delete user?')) return;
  const res = await fetch(`${apiBase}/${id}`, {method:'DELETE', headers: getAuthHeaders(false)});
        if(!res.ok){ alert('Delete failed'); return } fetchUsers();
      }
      if(e.target.classList.contains('edit')){
  const id = e.target.dataset.id; const r = await fetch(`${apiBase}/${id}`, { headers: getAuthHeaders(false) }); if(!r.ok){ alert('Failed'); return }
        const j = await r.json(); const u = j.data || j;
        editId = u.id; document.getElementById('u-name').value = u.name; document.getElementById('u-email').value = u.email; document.getElementById('u-password').value=''; document.getElementById('u-is-admin').checked = u.is_admin;
        document.getElementById('form-title').textContent='Edit user'; document.getElementById('form-wrap').style.display='block';
      }
    });

    // initial load
    fetchUsers();
  </script>

@endsection
