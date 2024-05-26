function fetchProfileDatat() { // Función para obtener y mostrar los datos del perfil
    fetch('fetchProfileData.php') // Realiza una solicitud a 'fetchProfileData.php'
    .then(response => response.json()) // Convierte la respuesta a formato JSON
    .then(data => { // Man0eja los datos JSON recibidos con el metodo then
        var profileContainer = document.querySelector('.profile-container');

        if (Object.keys(data).length === 0) {
            profileContainer.innerHTML = '<p>No hay datos disponibles. Por favor, ingresa un perfil.</p>';
        } else { // Si hay datos disponibles
            profileContainer.innerHTML = `
                <div class="profile-info">
                    <img src="logoGYB-removebg-preview.png" alt="Logo de la Empresa" class="logo-profile">
                    <h2>${data.nombre_empresa}</h2>
                    <p><strong>Dirección:</strong> ${data.direccion}</p>
                    <p><strong>Correo:</strong> ${data.correo_empresa}</p>
                    <p><strong>Teléfono:</strong> ${data.telefono}</p>
                    <p><strong>Descripción:</strong> ${data.descripcion}</p>
                    <p><strong>Archivo: </strong> <a href= "${data.archivo}" target="_blank">Abrir archivo</a></p>
                </div>
            `;
        }
    })
    .catch(error => console.error('Error:', error));
}

function fetchProfileData(option) {
    fetch('fetchProfileData.php')
    .then(response => response.json())
    .then(data => {
        var profileContainer = document.querySelector('.profile-container');
        
        // Ocultar el perfil en 'trabajadores' , 'constructoras' y 'obras'
        if (option === "trabajadores" || option === "constructoras" || option === "obras") {
            profileContainer.style.display = "none";
            return;
        } else {
            profileContainer.style.display = "flex"; // Mostrar el perfil si no está en 'trabajadores' o 'constructoras'
        }

        if (Object.keys(data).length === 0) {
            profileContainer.innerHTML = '<p>No hay datos disponibles. Por favor, ingresa un perfil.</p>';
        } else {
            profileContainer.innerHTML = `
                <div class="profile-info" style="text-align: center;">
                    <img src="logoGYB-removebg-preview.png" alt="Logo de la Empresa" class="logo-profile" style="width: 150px; height: auto;">
                    <h2>${data.nombre_empresa}</h2>
                    <p><strong>Dirección:</strong> ${data.direccion}</p>
                    <p><strong>Correo:</strong> ${data.correo_empresa}</p>
                    <p><strong>Teléfono:</strong> ${data.telefono}</p>
                    <p><strong>Descripción:</strong> ${data.descripcion}</p>
                    <p><strong>Archivo: </strong> <a href= "${data.archivo}" target="_blank">Abrir archivo</a></p>
                    <button onclick="editProfile()">Editar</button>
                </div>
            `;
        }
    })
    .catch(error => console.error('Error:', error));
}

function editProfile() {
    var formContainer = document.getElementById("form-container");
    formContainer.innerHTML = `
        <h2>Editar Perfil de la Empresa</h2>
        <form id="perfil-form" enctype="multipart/form-data">
            <input type="text" placeholder="Nombre de la empresa" name="nombre_empresa" required>
            <input type="text" placeholder="Dirección" name="direccion" required>
            <input type="email" placeholder="Correo electrónico" name="correo_empresa" required>
            <input type="number" placeholder="Teléfono" name="telefono" required>
            <textarea placeholder="Descripción de la empresa" name="descripcion" required></textarea>
            <input type="file" id="archivo" name="archivo">
            <button type="button" onclick="saveProfileData()">Guardar</button>
        </form>
    `;
    fetchProfileDataToEdit(); // Cargar datos existentes para editar
}

function fetchProfileDataToEdit() {
    fetch('fetchProfileData.php')
    .then(response => response.json())
    .then(data => {
        document.querySelector('input[name="nombre_empresa"]').value = data.nombre_empresa;
        document.querySelector('input[name="direccion"]').value = data.direccion;
        document.querySelector('input[name="correo_empresa"]').value = data.correo_empresa;
        document.querySelector('input[name="telefono"]').value = data.telefono;
        document.querySelector('textarea[name="descripcion"]').value = data.descripcion;
    })
    .catch(error => console.error('Error:', error));
}

function saveProfileData() {
    var form = document.getElementById("perfil-form");
    var formData = new FormData(form);

    fetch('editProfileData.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        alert(data);
        if (data.startsWith("Perfil actualizado")) {
            // Si la respuesta del servidor indica que el perfil se actualizó correctamente,
            // llamar a fetchProfileData() para mostrar los datos guardados
            fetchProfileData('perfil');
        }
    })
    .catch(error => console.error('Error:', error));
}


function showForm(option) {
    var formContainer = document.getElementById("form-container");
    if (option === "trabajadores") {
        formContainer.innerHTML = `
            <h2>Formulario de Trabajadores</h2>
            <form id="trabajadores-form" enctype="multipart/form-data">
                <input type="text" placeholder="Nombre" name="nombre" required>
                <select name="tipo_documento" required>
                    <option value="" disabled selected>Selecciona tipo de documento</option>
                    <option value="Cedula de ciudadania">Cedula de ciudadania</option>
                    <option value="Cedula extranjera">Cedula extranjera</option>
                    <option value="Permiso de permanencia">Permiso de permanencia</option>
                    <option value="Pasaporte">Pasaporte</option>
                    </select>
                <input type="number" placeholder="Número de documento" name="numero_documento" required>
                <input type="text" placeholder="Ocupación" name="ocupacion" required>
                <label for="activo">¿Está activo?</label>
                <input type="checkbox" id="activo" name="activo">
                <select name="obra" required>
                    <option value="" disabled selected>Selecciona una obra</option>
                    <option value="San Sebastian">San Sebastian</option>
                    <option value="Asturias">Asturias</option>
                    <option value="Castelo">Castelo</option>
                </select>
                <input type="file" name="archivo">
                <button type="button" onclick="saveFormData('trabajadores')">Guardar</button>
            </form>

            <h2>Lista de Trabajadores</h2>
            <input type="text" id="trabajadores-search" onkeyup="searchTable('trabajadores-search', 'trabajadores-list')" placeholder="Buscar trabajador...">
            <div id="trabajadores-list"></div>
        `;
        fetchData('trabajadores');
        fetchProfileData(option);
    } else if (option === "constructoras") {
        formContainer.innerHTML = `
            <h2>Formulario de Constructoras</h2>
            <form id="constructoras-form" enctype="multipart/form-data">
                <input type="text" placeholder="Nombre" name="nombre" required>
                <input type="number" placeholder="NIT" name="nit" required>
                <input type="text" placeholder="Nombre de contacto" name="nombre_contacto" required>
                <input type="email" placeholder="Correo" name="correo" required>
                <input type="text" placeholder="Tipo de contrato" name="tipo_contrato" required>
                <input type="file" name="archivo">
                <button type="submit" onclick="saveFormData('constructoras')">Guardar</button>
            </form>
            <h2>Lista de Constructoras</h2>
            <input type="text" id="constructoras-search" onkeyup="searchTable('constructoras-search', 'constructoras-list')" placeholder="Buscar constructora...">
            <div id="constructoras-list"></div>
        `;
        fetchData('constructoras');
        fetchProfileData(option);
    } else if (option === "obras"){
        formContainer.innerHTML = `
            <h2>Formulario de Obras</h2>
            <form id="obras-form" enctype="multipart/form-data" onsubmit="event.preventDefault(); saveFormData('obras');">
                <input type="text" placeholder="Nombre" name="nombre" required>
                <input type="text" placeholder="Nombre de constructora" name="nombre_constructora" required>
                <input type="text" placeholder="Descripcion" name="descripcion" required>
                <button type="submit">Guardar</button>
            </form>
            <h2>Lista de Obras</h2>
            <input type="text" id="obras-search" onkeyup="searchTable('obras-search', 'obras-list')" placeholder="Buscar obra...">
            <div id="obras-list"></div>
        `;
        fetchData('obras');
        fetchProfileData(option);
    } else if (option === "perfil") {
        formContainer.innerHTML = `
       
        `;
        fetchProfileData(option);
    }
}


//ss

function editData(option, id) {
    var formContainer = document.getElementById("form-container");
    if (option === "trabajadores") {
        formContainer.innerHTML = `
            <h2>Editar Trabajador</h2>
            <form id="trabajadores-form" enctype="multipart/form-data">
                <input type="hidden" name="id" value="${id}">
                <input type="text" placeholder="Nombre" name="nombre" required>
                <select name="tipo_documento" required>
                    <option value="" disabled selected>Selecciona tipo de documento</option>
                    <option value="Cedula de ciudadania">Cedula de ciudadania</option>
                    <option value="Cedula extranjera">Cedula extranjera</option>
                    <option value="Permiso de permanencia">Permiso de permanencia</option>
                    <option value="Pasaporte">Pasaporte</option>
                </select>
                <input type="number" placeholder="Número de documento" name="numero_documento" required>
                <input type="text" placeholder="Ocupación" name="ocupacion" required>
                <label for="activo">¿Está activo?</label>
                <input type="checkbox" id="activo" name="activo">
                <select name="obra" required>
                    <option value="" disabled selected>Selecciona una obra</option>
                    <option value="San Sebastian">San Sebastian</option>
                    <option value="Asturias">Asturias</option>
                    <option value="Castelo">Castelo</option>
                </select>
                <input type="file" name="archivo">
                <button type="button" onclick="saveEditedData('trabajadores')">Guardar</button>
            </form>
        `;
        fetchTrabajadorData(id.toString());
    } else if (option === "constructoras") {
        formContainer.innerHTML = `
            <h2>Editar Constructora</h2>
            <form id="constructoras-form" enctype="multipart/form-data">
                <input type="hidden" name="id" value="${id}">
                <input type="text" placeholder="Nombre" name="nombre" required>
                <input type="number" placeholder="NIT" name="nit" required>
                <input type="text" placeholder="Nombre de contacto" name="nombre_contacto" required>
                <input type="email" placeholder="Correo" name="correo" required>
                <input type="text" placeholder="Tipo de contrato" name="tipo_contrato" required>
                <input type="file" name="archivo">
                <button type="button" onclick="saveEditedData('constructoras')">Guardar</button>
            </form>
        `;
        fetchConstructoraData(id.toString());
    } else if (option === "obras"){
        formContainer.innerHTML = `
            <h2>Formulario de Obras</h2>
            <form id="obras-form" enctype="multipart/form-data">
                <input type="hidden" name="id" value="${id}">
                <input type="text" placeholder="Nombre" name="nombre" required>
                <input type="text" placeholder="Nombre de constructora" name="nombre_constructora" required>
                <input type="text" placeholder="Descripcion" name="descripcion" required>
                <button type="button" onclick="saveEditedData('obras')">Guardar</button>
            </form>
            `;
        fetchObraData(id.toString());
    }
}

function fetchTrabajadorData(id) {
    fetch('editTrabajador.php?id=' + id)
    .then(response => response.json())
    .then(data => {
        document.querySelector('input[name="nombre"]').value = data.nombre;
        document.querySelector('select[name="tipo_documento"]').value = data.tipo_documento;
        document.querySelector('input[name="numero_documento"]').value = data.numero_documento;
        document.querySelector('input[name="ocupacion"]').value = data.ocupacion;
        document.querySelector('input[name="activo"]').checked = data.activo === 'Si';
        document.querySelector('select[name="obra"]').value = data.obra;
    })
    .catch(error => console.error('Error:', error));
}

function fetchConstructoraData(id) {
    fetch('editConstructora.php?id=' + id)
    .then(response => response.json())
    .then(data => {
        document.querySelector('input[name="nombre"]').value = data.nombre;
        document.querySelector('input[name="nit"]').value = data.nit;
        document.querySelector('input[name="nombre_contacto"]').value = data.nombre_contacto;
        document.querySelector('input[name="correo"]').value = data.correo;
        document.querySelector('select[name="tipo_contrato"]').value = data.tipo_contrato;
    })
    .catch(error => console.error('Error:', error));
}

function fetchObraData(id) {
    fetch('editObra.php?id=' + id)
    .then(response => response.json())
    .then(data => {
        document.querySelector('input[name="nombre"]').value = data.nombre;
        document.querySelector('input[name="nombre_constructora"]').value = data.nombre_constructora;
        document.querySelector('input[name="descripcion"]').value = data.descripcion;
    })
    .catch(error => console.error('Error:', error));
}

function saveEditedData(option) {
    var formId = option === 'obras' ? 'obras-form' : (option === 'trabajadores' ? 'trabajadores-form' : 'constructoras-form');
    var form = document.getElementById(formId);
    var formData = new FormData(form);

    formData.append('option', option);
    formData.append('id', form.elements.id.value);

    fetch('saveEditedData.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        alert(data);
        if (data.includes("Registro actualizado con éxito")) {
            fetchData(option); // Cargar y mostrar los datos guardados
        }
    })
    .catch(error => console.error('Error:', error));
}



// Función para guardar los datos del formulario
function saveFormData(option) {
    var formId = option === 'obras' ? 'obras-form' : (option === 'trabajadores' ? 'trabajadores-form' : 'constructoras-form');
    var form = document.getElementById(formId);
    var formData = new FormData(form);

    // Convertir el valor del checkbox a 'Si' o 'No'
    var activoCheckbox = document.getElementById('activo');
    if (activoCheckbox) {
        formData.set('activo', activoCheckbox.checked ? 'Si' : 'No');
    }

    formData.append('option', option);

    fetch('saveFormData.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        alert(data); // Mostrar el mensaje del servidor
        form.reset();
        fetchData(option); // Cargar y mostrar los datos guardados
    })
    .catch(error => console.error('Error:', error));
}


function deleteData(option, id) {
    console.log(option, id); // Añadir esta línea para depurar
    if (confirm("¿Estás seguro de que deseas eliminar este registro?")) {
        fetch('deleteData.php?option=' + option + '&id=' + id)
            .then(response => response.text())
            .then(data => {
                alert(data);
                if (data.includes("Registro eliminado exitosamente")) {
                    fetchData(option); // Vuelve a cargar los datos después de eliminar
                }
            })
            .catch(error => console.error('Error:', error));
    }
}

function fetchData(option) {
    var listContainer = option === 'obras' ? document.getElementById('obras-list') : (option === 'trabajadores' ? document.getElementById('trabajadores-list') : document.getElementById('constructoras-list'));

    fetch('getData.php?option=' + option)
        .then(response => response.json())
        .then(data => {
            var html = '<table>';
            if (data.length > 0) {
                // Encabezados de la tabla
                html += '<tr>';
                Object.keys(data[0]).forEach(key => {
                    html += '<th>' + key + '</th>';
                });
                html += '<th>Acciones</th>'; // Encabezado para el botón de eliminar
                html += '</tr>';
                // Filas de la tabla
                data.forEach(item => {
                    html += '<tr>';
                    Object.entries(item).forEach(([key, value]) => {
                        if (key !== 'archivo' || !value) {
                            html += '<td>' + value + '</td>';
                        } else {
                            html += '<td><a href="' + value + '" target="_blank">Abrir archivo</a></td>';
                        }
                    });
                    html += '<td><button onclick="editData(\'' + option + '\', \'' + item.ID + '\')">Editar</button></td>'; // Botón de editar
                    html += '<td><button onclick="deleteData(\'' + option + '\', \'' + item.ID + '\')">Eliminar</button></td>'; // Botón de eliminar
                    html += '</tr>';
                });
            } else {
                html += '<tr><td colspan="6">No hay datos disponibles</td></tr>'; // Ajustar el número de columnas
            }
            html += '</table>';
            listContainer.innerHTML = html;
        })
        .catch(error => console.error('Error:', error));
}


function searchTable(inputId, listId) {
    // Obtener el valor de búsqueda
    var input = document.getElementById(inputId);
    var filter = input.value.toUpperCase();

    // Obtener la tabla y las filas
    var table = document.getElementById(listId);
    var rows = table.getElementsByTagName("tr");

    // Iterar sobre todas las filas y ocultar aquellas que no coincidan con la búsqueda
    for (var i = 0; i < rows.length; i++) {
        var cells = rows[i].getElementsByTagName("td");
        var found = false;
        for (var j = 0; j < cells.length; j++) {
            var cell = cells[j];
            if (cell) {
                if (cell.innerHTML.toUpperCase().indexOf(filter) > -1) {
                    found = true;
                    break;
                }
            }
        }
        if (found) {
            rows[i].style.display = "";
        } else {
            rows[i].style.display = "none";
        }
    }
}


