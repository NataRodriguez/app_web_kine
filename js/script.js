document.addEventListener("DOMContentLoaded", function() {
    let data = {};

    // Función para cargar los datos del JSON
    function loadData() {
        fetch('datos/datos-sitio.json')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok ' + response.statusText);
                }
                return response.json();
            })
            .then(json => {
                data = json;
                populateSelectorsAndTable();
            })
            .catch(error => console.error('Error al cargar el archivo JSON:', error));
    }

    // Función para poblar el selector de rangos de edad y la tabla de exámenes
    function populateSelectorsAndTable() {
        const ageRangeSelector = document.getElementById('ageRangeSelector');
        const examTableBody = document.getElementById('examTableBody');

        const ageRanges = Object.keys(data.Hombre);
        const firstAgeRange = data.Hombre[ageRanges[0]];
        const examNames = Object.keys(firstAgeRange);

        // Poblar el selector de rangos de edad
        ageRangeSelector.innerHTML = '';
        ageRanges.forEach(range => {
            const option = document.createElement('option');
            option.value = range;
            option.textContent = range;
            ageRangeSelector.appendChild(option);
        });

        // Poblar la tabla con los nombres de los exámenes
        populateTable(examNames, firstAgeRange);

        // Escuchar el cambio en el selector de rangos de edad
        ageRangeSelector.addEventListener('change', function() {
            const selectedRange = ageRangeSelector.value;
            const selectedExams = data.Hombre[selectedRange];
            populateTable(Object.keys(selectedExams), selectedExams);
        });
    }

    // Función para poblar la tabla con los exámenes del rango seleccionado
    function populateTable(examNames, examData) {
        const examTableBody = document.getElementById('examTableBody');
        examTableBody.innerHTML = '';

        examNames.forEach((exam, index) => {
            const row = document.createElement('tr');
            const examNameCell = document.createElement('td');
            const minCell = document.createElement('td');
            const maxCell = document.createElement('td');
            
            examNameCell.textContent = exam;
            minCell.innerHTML = `<input type="number" name="valorMin${index + 1}" value="${examData[exam].min}">`;
            maxCell.innerHTML = `<input type="number" name="valorMax${index + 1}" value="${examData[exam].max}">`;

            row.appendChild(examNameCell);
            row.appendChild(minCell);
            row.appendChild(maxCell);
            
            examTableBody.appendChild(row);
        });
    }

    // Función para manejar el envío del formulario
    document.getElementById('examForm').addEventListener('submit', function(event) {
        event.preventDefault();

        const selectedRange = document.getElementById('ageRangeSelector').value;
        const examTableBody = document.getElementById('examTableBody');
        const rows = examTableBody.querySelectorAll('tr');

        rows.forEach((row, index) => {
            const cells = row.querySelectorAll('td');
            const examName = cells[0].textContent;
            const minValue = cells[1].querySelector('input').value;
            const maxValue = cells[2].querySelector('input').value;

            data.Hombre[selectedRange][examName] = {
                "min": minValue,
                "max": maxValue
            };
        });

        console.log('Datos actualizados:', data);
        alert('Datos actualizados localmente. Revise la consola para ver el JSON actualizado.');
    });

    // Cargar los datos al inicio
    loadData();
});
