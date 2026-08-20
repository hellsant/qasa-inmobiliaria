// Confirmación para borrados
document.querySelectorAll('form[data-confirm]').forEach(form => {
  form.addEventListener('submit', e => {
    if (!confirm(form.dataset.confirm || '¿Estás seguro?')) e.preventDefault();
  });
});

// Preview de foto individual (equipo / historias)
document.querySelectorAll('input[type="file"][data-preview]').forEach(input => {
  input.addEventListener('change', () => {
    const img = document.getElementById(input.dataset.preview);
    if (img && input.files[0]) img.src = URL.createObjectURL(input.files[0]);
  });
});