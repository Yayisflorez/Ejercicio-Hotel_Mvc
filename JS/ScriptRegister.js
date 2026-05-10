

document.addEventListener('DOMContentLoaded', function() {
  const regForm = document.getElementById('regForm');
  if (regForm) {
    regForm.addEventListener('submit', function (e) {
      // Limpiar errores previos
      const errorFields = [
        'tipoDocError', 'documentoError', 'nombreError', 'apellidoError', 'telefonoError', 'emailError', 'pwdStrengthError', 'confirmPasswordError'
      ];
      errorFields.forEach(id => {
        const el = document.getElementById(id);
        if (el) {
          el.textContent = '';
          el.classList.add('hidden');
        }
      });

      let valid = true;

      // Validaciones
      const tipoDoc = document.getElementById('tipo_documento_id').value.trim();
      const documento = document.getElementById('documento').value.trim();
      const nombre = document.getElementById('nombre').value.trim();
      const apellido = document.getElementById('apellido').value.trim();
      const telefono = document.getElementById('telefono').value.trim();
      const email = document.getElementById('email').value.trim();
      const pwd = document.getElementById('pwd').value;
      const pwd2 = document.getElementById('pwd2').value;
      const fuerte = /^(?=.*[A-Z])(?=.*\d).{8,}$/;

      if (!tipoDoc) {
        document.getElementById('tipoDocError').textContent = 'El tipo de documento es obligatorio';
        document.getElementById('tipoDocError').classList.remove('hidden');
        valid = false;
      }
      if (!documento) {
        document.getElementById('documentoError').textContent = 'El número de documento es obligatorio';
        document.getElementById('documentoError').classList.remove('hidden');
        valid = false;
      }
      if (!nombre) {
        document.getElementById('nombreError').textContent = 'El nombre es obligatorio';
        document.getElementById('nombreError').classList.remove('hidden');
        valid = false;
      } else if (nombre.length < 3) {
        document.getElementById('nombreError').textContent = 'El nombre debe tener al menos 3 caracteres';
        document.getElementById('nombreError').classList.remove('hidden');
        valid = false;
      }
      if (!apellido) {
        document.getElementById('apellidoError').textContent = 'El apellido es obligatorio';
        document.getElementById('apellidoError').classList.remove('hidden');
        valid = false;
      }
      if (!telefono) {
        document.getElementById('telefonoError').textContent = 'El teléfono es obligatorio';
        document.getElementById('telefonoError').classList.remove('hidden');
        valid = false;
      }
      if (!email) {
        document.getElementById('emailError').textContent = 'El email es obligatorio';
        document.getElementById('emailError').classList.remove('hidden');
        valid = false;
      } else if (!/^\S+@\S+\.\S+$/.test(email)) {
        document.getElementById('emailError').textContent = 'Email no válido';
        document.getElementById('emailError').classList.remove('hidden');
        valid = false;
      }
      if (!pwd) {
        document.getElementById('pwdStrengthError').textContent = 'La contraseña es obligatoria';
        document.getElementById('pwdStrengthError').classList.remove('hidden');
        valid = false;
      } else if (!fuerte.test(pwd)) {
        document.getElementById('pwdStrengthError').textContent = 'La contraseña debe tener al menos 8 caracteres, una mayúscula y un número.';
        document.getElementById('pwdStrengthError').classList.remove('hidden');
        valid = false;
      }
      if (!pwd2) {
        document.getElementById('confirmPasswordError').textContent = 'Confirma la contraseña';
        document.getElementById('confirmPasswordError').classList.remove('hidden');
        valid = false;
      } else if (pwd !== pwd2) {
        document.getElementById('confirmPasswordError').textContent = 'Las contraseñas no coinciden';
        document.getElementById('confirmPasswordError').classList.remove('hidden');
        valid = false;
      }

      if (!valid) {
        e.preventDefault();
      } else {
        e.preventDefault(); // Detener envío normal

        const formData = new FormData(regForm);

        // Mostrar pantalla de carga inmediatamente
        const overlay = document.getElementById('loadingOverlay');
        const fill = document.getElementById('loadingBarFill');
        if (overlay && fill) {
          overlay.classList.add('active');
          fill.style.width = '30%';
        }

        fetch('index.php?action=registerUserAjax', {
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(data => {
          if (data.status === 'success') {
            if (fill) fill.style.width = '100%';
            
            // Redirigir al terminar la animación
            setTimeout(() => {
              window.location.href = 'index.php?action=getFormLoginUser&success=1';
            }, 1000);
          } else if (data.status === 'error') {
            // Ocultar pantalla de carga si hay error para mostrar los errores
            if (overlay) overlay.classList.remove('active');
            
            // Manejar errores del servidor
            for (let field in data.errors) {
              let errorEl = null;
              if (field === 'email') errorEl = document.getElementById('emailError');
              else if (field === 'documento') errorEl = document.getElementById('documentoError');
              
              if (errorEl) {
                errorEl.textContent = data.errors[field];
                errorEl.classList.remove('hidden');
              } else {
                alert(data.errors[field]);
              }
            }
          }
        })
        .catch(err => {
          console.error(err);
          alert('Error de conexión al intentar crear la cuenta.');
        });
      }
    });
  }
});
