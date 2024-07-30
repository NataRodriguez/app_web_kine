document.addEventListener("DOMContentLoaded", function() {
    fetch('datos/datos-sitio.json')
        .then(response => response.json())
        .then(data => {
            const ageRangeSelector = document.getElementById('ageRangeSelector');
            const examTableBody = document.getElementById('examTableBody');
            
            // Obtener las edades y nombres de exámenes del JSON
            const ageRanges = Object.keys(data.Hombre);
            const firstAgeRange = data.Hombre[ageRanges[0]];
            const examNames = Object.keys(firstAgeRange);

            // Poblar el selector de rangos de edad
            ageRanges.forEach(range => {
                const option = document.createElement('option');
                option.value = range;
                option.textContent = range;
                ageRangeSelector.appendChild(option);
            });

            // Poblar la tabla con los nombres de los exámenes
            examNames.forEach((exam, index) => {
                const row = document.createElement('tr');
                const examNameCell = document.createElement('td');
                const minCell = document.createElement('td');
                const maxCell = document.createElement('td');
                
                examNameCell.textContent = exam;
                minCell.innerHTML = `<input type="number" name="valorMin${index + 1}" value="${firstAgeRange[exam].min}">`;
                maxCell.innerHTML = `<input type="number" name="valorMax${index + 1}" value="${firstAgeRange[exam].max}">`;

                row.appendChild(examNameCell);
                row.appendChild(minCell);
                row.appendChild(maxCell);
                
                examTableBody.appendChild(row);
            });

            // Escuchar el cambio en el selector de rangos de edad
            ageRangeSelector.addEventListener('change', function() {
                const selectedRange = ageRangeSelector.value;
                const selectedExams = data.Hombre[selectedRange];
                
                // Limpiar la tabla
                examTableBody.innerHTML = '';

                // Poblar la tabla con los exámenes del rango seleccionado
                Object.keys(selectedExams).forEach((exam, index) => {
                    const row = document.createElement('tr');
                    const examNameCell = document.createElement('td');
                    const minCell = document.createElement('td');
                    const maxCell = document.createElement('td');
                    
                    examNameCell.textContent = exam;
                    minCell.innerHTML = `<input type="number" name="valorMin${index + 1}" value="${selectedExams[exam].min}">`;
                    maxCell.innerHTML = `<input type="number" name="valorMax${index + 1}" value="${selectedExams[exam].max}">`;

                    row.appendChild(examNameCell);
                    row.appendChild(minCell);
                    row.appendChild(maxCell);
                    
                    examTableBody.appendChild(row);
                });
            });
        })
        .catch(error => console.error('Error al cargar el archivo JSON:', error));
});
