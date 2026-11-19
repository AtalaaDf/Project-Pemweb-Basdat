document.querySelectorAll('[data-confirm]').forEach(el => {
  el.addEventListener('click', event => {
    if (!confirm(el.getAttribute('data-confirm') || 'Yakin ingin menghapus data ini?')) {
      event.preventDefault();
    }
  });
});
