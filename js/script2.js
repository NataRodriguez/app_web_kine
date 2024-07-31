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
            resultCell.innerHTML = `<input type="number" name="result${index}" data-exam="${exam}">`;
            statusCell.className = `status${index}`;

            row.appendChild(examNameCell);
            row.appendChild(resultCell);
            row.appendChild(statusCell);
            
            examTableBody.appendChild(row);
        });

        // Añadir evento para calcular el estado al ingresar los resultados
        examTableBody.addEventListener('input', calculateStatus);
    }

    // Función para calcular el estado del paciente
    function calculateStatus(event) {
        const age = parseInt(document.getElementById('patientAge').value);
        if (isNaN(age)) return;

        const examTableBody = document.getElementById('examTableBody');
        const rows = examTableBody.querySelectorAll('tr');

        let ageRange;
        if (age >= 60 && age <= 64) {
            ageRange = data.Hombre['60-64'];
        } else if (age >= 65 && age <= 69) {
            ageRange = data.Hombre['65-69'];
        } // Añadir más rangos de edad según el JSON
        
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
