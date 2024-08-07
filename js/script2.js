document.addEventListener("DOMContentLoaded", function() {
    let data = {};

    // Función para cargar los datos del JSON
    function loadData() {
        fetch('datos/datos-sitio.json')
            .then(response => response.json())
            .then(json => {
                data = json;
                populateTable();
            })
            .catch(error => console.error('Error al cargar el archivo JSON:', error));
    }

    // Función para poblar la tabla con los nombres de los exámenes
    function populateTable() {
        const examTableBody = document.getElementById('examTableBody');
        const ageRangeSelector = document.getElementById('patientAge');
        
        const ageRanges = Object.keys(data.Hombre);
        const firstAgeRange = data.Hombre[ageRanges[0]];
        const examNames = Object.keys(firstAgeRange);

        examNames.forEach((exam, index) => {
            const row = document.createElement('tr');
            const examNameCell = document.createElement('td');
            const resultCell = document.createElement('td');
            const statusCell = document.createElement('td');
            
            examNameCell.textContent = exam;
            resultCell.innerHTML = `<input type="number" name="result${index}" data-exam="${exam}" value="0" disabled>`;
            statusCell.className = `status${index}`;

            row.appendChild(examNameCell);
            row.appendChild(resultCell);
            row.appendChild(statusCell);
            
            examTableBody.appendChild(row);
        });

        // Añadir evento para calcular el estado al ingresar los resultados
        examTableBody.addEventListener('input', calculateStatus);

        // Añadir evento para habilitar los campos de entrada al ingresar la edad
        ageRangeSelector.addEventListener('input', enableInputs);
    }

    // Función para habilitar los inputs al ingresar la edad
    function enableInputs(event) {
        const age = parseInt(event.target.value);
        if (!isNaN(age)) {
            const inputs = document.querySelectorAll('#examTableBody input');
            inputs.forEach(input => {
                input.disabled = false;
            });
        } else {
            const inputs = document.querySelectorAll('#examTableBody input');
            inputs.forEach(input => {
                input.disabled = true;
            });
        }
    }

    // Función para calcular el estado del paciente
    function calculateStatus(event) {
        const age = parseInt(document.getElementById('patientAge').value);
        if (isNaN(age)) return;

        const examTableBody = document.getElementById('examTableBody');
        const rows = examTableBody.querySelectorAll('tr');

        let ageRange;
        for (const range in data.Hombre) {
            const [minAge, maxAge] = range.split('-').map(Number);
            if (age >= minAge && age <= maxAge) {
                ageRange = data.Hombre[range];
                break;
            }
        }

        rows.forEach((row, index) => {
            const exam = row.querySelector('input').getAttribute('data-exam');
            const result = parseFloat(row.querySelector('input').value);
            const statusCell = row.querySelector(`.status${index}`);

            if (!ageRange || !ageRange[exam]) {
                statusCell.textContent = 'Desconocido';
                return;
            }

            const min = parseFloat(ageRange[exam].min);
            const max = parseFloat(ageRange[exam].max);

            if (result < min) {
                statusCell.textContent = 'En riesgo';
            } else if (result > max) {
                statusCell.textContent = 'Excelente estado';
            } else {
                statusCell.textContent = 'Normal';
            }
        });
    }

    // Cargar los datos al inicio
    loadData();
});
