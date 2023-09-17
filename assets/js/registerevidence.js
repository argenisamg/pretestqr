/**Globals */
let tablebody = document.getElementById('table-body');
//Modal
let modal = document.getElementById("ventanaModal");
let closemodal = document.getElementById("closemodal");
let exclose = document.querySelectorAll("#close");
let closemodalsummary = document.getElementById("closemodalsummary");
let imagemodal = document.getElementById('imagemodal');
let descriptiontext = document.getElementById('descriptiontext');

// Method for send the JSON and Image to PHP:
const sendJson = (fData) => {    
    let url = 'php/save_evidence.php';    
    fetch(url, {
        method: 'POST',        
        header: {'Content-Type' : 'application/json'},
        body: fData
    })
    .then(response => {
        return response.json();
    })
    .then(data=> {
        if (data.boolean) {            
            formulario.reset();            
            dataTable.ajax.reload();
            alert('Succes');
        } else {
            alert(`Error: ${data.msg}`);
        }
    })
    .catch(console.error());
}// end sendJson

const showImage = (qrSerial, pathh, description) => {    
    texth2.textContent = `SN: ${qrSerial}`;
    imagemodal.innerHTML = `<img src="${pathh}" class="image-modal"  width="500px" height="500px" alt="Not available" >`;   
    descriptiontext.textContent = description; 
    modal.style.display = "block";
    // alert('The clic functions very well');

}// end showImage

const mostrarMensajeSinRegistros = () => {
    alert('Empty data');
}

let dataTable;
const selectInfo = () => {    
    $(document).ready(function () {
        if (!$.fn.DataTable.isDataTable('#tableContent')) {
            dataTable = $('#tableContent').DataTable({
                ajax: {
                    url: 'php/consult_info.php',
                    type: 'GET',
                    dataType: 'json',
                    dataSrc: function(response) {
                        if (response && Array.isArray(response) && response.length > 0) {
                            return response;
                        } else {
                            mostrarMensajeSinRegistros();
                        }
                    },
                },
                columns: [
                    { data: 'row' },
                    { data: 'qr' },
                    { data: 'date' },
                    {
                        data: 'evidence',
                        render: function(data, type, row, meta) {
                            return `<img src="${data}" class="element-clic" onClick="showImage('${row.qr}', '${row.evidence}', '${row.description}');" width="50px" height="50px" alt="Not available" >`;
                        }
                    },
                    { data: 'employee' },
                    { data: 'shift' },
                    { data: 'description' }
                ],
                responsive: true,
                destroy: true,
                iDisplayLength: 10
            }); // end dataTable
        } // end if
    });
    
} // end selectInfo

/** Modal Events */
// Close modal when the user makes click outside it:
window.addEventListener("click", (event) => {
    if (event.target == modal) {
        texth2.textContent = "";
        modal.style.display = "none";
    }
});    

// Close modal when the user makes click on 'x':
exclose.forEach((elemento) => {
    elemento.addEventListener("click", () => {          
        if (modal.style.display === "block") {         
            texth2.textContent = "";       
            modal.style.display = "none";
        } 
    });           
});

closemodal.addEventListener("click", () => {
    modal.style.display = "none";
});

document.addEventListener('DOMContentLoaded',  () => {   
    selectInfo();        
    const formulario = document.getElementById("formulario");      
    formulario.onsubmit = (e) => {
        e.preventDefault();
        let fData = new FormData();
        const formData = {};
        const len = formulario.elements.length;
        let fileUpload = document.getElementById('fileevidence').files[0];
            for (let i = 0; i < len; i++) {
                let campo = formulario.elements[i];
                if ((campo.name && campo.type !== "submit") && (campo.type !== "button")) {
                    formData[campo.name] = campo.value.trim();
                }
            }//end for
            const isEmpty = Object.keys(formData).length === 0;
            if (isEmpty) {
                alert('Empty data, please enter all fields !');
                return;
            }// end if 
        fData.append('image', fileUpload);
        formData.fileevidence = fData.get('image').name;
        let json = JSON.stringify(formData, null, 2);
        //console.log(json);
        fData.append('json', json);
        sendJson(fData, formulario);        
    }      
});