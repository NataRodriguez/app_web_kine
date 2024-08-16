 // Modificaciones para usar el selector de género en el script
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
        const genderSelector = document.getElementById('gender');
        
        const selectedGender = genderSelector.value;
        const ageRanges = Object.keys(data[selectedGender]);
        const firstAgeRange = data[selectedGender][ageRanges[0]];
        const examNames = Object.keys(firstAgeRange);

        examTableBody.innerHTML = ''; // Limpiar tabla antes de llenarla

        examNames.forEach((exam, index) => {
            const row = document.createElement('tr');
            const examNameCell = document.createElement('td');
            const resultCell = document.createElement('td');
            const statusCell = document.createElement('td');
            
            examNameCell.textContent = exam;
            resultCell.innerHTML = `<input type="number" step="any" name="result${index}" data-exam="${exam}" value="0" disabled>`;
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
        genderSelector.addEventListener('change', populateTable); // Actualizar tabla al cambiar el género
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
        const gender = document.getElementById('gender').value;
        if (isNaN(age)) return;
    
        const examTableBody = document.getElementById('examTableBody');
        const rows = examTableBody.querySelectorAll('tr');
    
        let ageRange;
        for (const range in data[gender]) {
            const [minAge, maxAge] = range.split('-').map(Number);
            if (age >= minAge && age <= maxAge) {
                ageRange = data[gender][range];
                break;
            }
        }
    
        rows.forEach((row, index) => {
            const examNameCell = row.querySelector('td:first-child').textContent;
            const resultInput = row.querySelector('input');
            const result = parseFloat(resultInput.value);
            const statusCell = row.querySelector(`.status${index}`);
            let estado = '';
    
            if (!ageRange || !ageRange[examNameCell]) {
                estado = 'Desconocido';
                statusCell.textContent = estado;
            } else {
                const min = parseFloat(ageRange[examNameCell].min);
                const max = parseFloat(ageRange[examNameCell].max);
    
                if (result < min) {
                    estado = 'En riesgo';
                    statusCell.textContent = estado;
                } else if (result > max) {
                    estado = 'Excelente estado';
                    statusCell.textContent = estado;
                } else {
                    estado = 'Normal';
                    statusCell.textContent = estado;
                }
            }
    
            // Actualizar o crear input hidden para el estado
            let hiddenStateInput = row.querySelector(`input[name="estado_${index}"]`);
            if (!hiddenStateInput) {
                hiddenStateInput = document.createElement('input');
                hiddenStateInput.type = 'hidden';
                hiddenStateInput.name = `estado_${index}`;
                row.appendChild(hiddenStateInput);
            }
            hiddenStateInput.value = estado;
    
            // Asegúrate de que el nombre del examen también se envíe
            let hiddenExamInput = row.querySelector(`input[name="examen_${index}"]`);
            if (!hiddenExamInput) {
                hiddenExamInput = document.createElement('input');
                hiddenExamInput.type = 'hidden';
                hiddenExamInput.name = `examen_${index}`;
                row.appendChild(hiddenExamInput);
            }
            hiddenExamInput.value = examNameCell;
        });
    }
    
    

    // Cargar los datos al inicio
    loadData();
});

// Función para generar el PDF
function generatePDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    const patientName = document.getElementById("patientName").value;
    const patientRut = document.getElementById("patientRut").value;
    const examDate = document.getElementById("examDate").value;
    const patientAge = document.getElementById("patientAge").value;
    const gender = document.getElementById("gender").value;

    doc.text(`Nombre del Paciente: ${patientName}`, 10, 10);
    doc.text(`RUT del Paciente: ${patientRut}`, 10, 20);
    doc.text(`Género: ${gender}`, 10, 30);
    doc.text(`Fecha del Examen: ${examDate}`, 10, 40);
    doc.text(`Edad del Paciente: ${patientAge}`, 10, 50);

    // Obtener los datos de la tabla
    const tableData = [];
    const rows = document.querySelectorAll("#examTableBody tr");

    rows.forEach(row => {
        const cells = row.querySelectorAll("td");
        const exam = cells[0].innerText;
        const result = cells[1].querySelector("input").value;
        const status = cells[2].innerText;

        tableData.push([exam, result, status]);
    });

    doc.autoTable({
        head: [['Examen', 'Resultado', 'Estado']],
        body: tableData,
        startY: 60
    });

    doc.save(`examen-${patientName}-${examDate}.pdf`);
}

