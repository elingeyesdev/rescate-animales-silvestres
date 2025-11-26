// public/js/confirm-delete.js
(function(){
  var targetForm=null, backdrop=null, modal=null;

  function buildModal(){
    var m=document.createElement('div');
    m.id='confirmDeleteModal';
    m.className='modal fade';
    m.style.display='none';
    m.innerHTML='<div class="modal-dialog modal-sm modal-dialog-centered" role="document">'
      +'<div class="modal-content">'
      +'<div class="modal-header"><h5 class="modal-title">Eliminar registro</h5>'
      +'<button type="button" class="close" aria-label="Close">&times;</button></div>'
      +'<div class="modal-body"><p class="mb-0">¿Estás seguro de querer eliminar?</p></div>'
      +'<div class="modal-footer">'
      +'<button type="button" class="btn btn-secondary js-cancel">Cancelar</button>'
      +'<button type="button" class="btn btn-danger js-confirm">Eliminar</button>'
      +'</div></div></div>';
    document.body.appendChild(m);
    return m;
  }

  function openModal(form){
    targetForm=form;
    if(!modal) modal=buildModal();
    modal.style.display='block';
    modal.classList.add('show');
    backdrop=document.createElement('div');
    backdrop.className='modal-backdrop fade show';
    document.body.appendChild(backdrop);
    document.body.classList.add('modal-open');
  }
  function closeModal(){
    if(!modal) return;
    modal.classList.remove('show');
    modal.style.display='none';
    if(backdrop){backdrop.remove();backdrop=null;}
    document.body.classList.remove('modal-open');
    targetForm=null;
  }

  document.addEventListener('click',function(e){
    var btn=e.target.closest('.js-confirm-delete');
    if(btn){
      e.preventDefault();
      var form=btn.closest('form');
      if(form) openModal(form);
    }
    if(e.target.closest('.js-cancel')||e.target.closest('.close')){ e.preventDefault(); closeModal(); }
    if(e.target.closest('.js-confirm')){ e.preventDefault(); var f=targetForm; closeModal(); if(f) f.submit(); }
  });

  document.addEventListener('keydown',function(e){
    if(e.key==='Escape'&&modal&&modal.classList.contains('show')) closeModal();
  });
})();