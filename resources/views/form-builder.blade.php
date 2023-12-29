<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <title>Drag and Drop Form Builder</title>
    <style>
        .form-builder-container {
            min-height: 300px;
            border: 2px dashed #ddd;
            padding: 20px;
        }

        .form-field {
            margin-bottom: 10px;
        }

        .options-container {
            margin-top: 5px;
        }
    </style>
</head>

<body>

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="formFieldSelect" class="form-label">Select a Form Field</label>
                    <select class="form-select" id="formFieldSelect">
                        <option value="radio-group">Radio Group</option>
                        <option value="text">Text Field</option>
                    </select>
                </div>

                <div class="mb-3">
                    <button class="btn btn-primary" onclick="addField()">Add Field</button>
                </div>

                <div class="mb-3">
                    <button class="btn btn-success" onclick="saveForm()">Save Form</button>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-builder-container" id="formBuilderContainer" ondrop="drop(event)"
                    ondragover="allowDrop(event)">

                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function allowDrop(event) {
            event.preventDefault();
        }

        function drop(event) {
            event.preventDefault();
            const formFieldSelect = document.getElementById('formFieldSelect');
            const selectedFieldType = formFieldSelect.value;
            addFieldToForm(selectedFieldType);
        }

        function addFieldToForm(fieldType, fieldData) {
            const formBuilderContainer = document.getElementById('formBuilderContainer');
            const newField = document.createElement('div');
            newField.className = 'form-field';
            newField.setAttribute('draggable', 'true');
            newField.dataset.type = fieldType;


            const fieldLabel = fieldData ? fieldData.label : 'Unknown Field';

            if (fieldType === 'radio-group') {
                newField.innerHTML = `
                <label class="form-label" contenteditable="true">${fieldLabel}</label>
                <div class="options-container">
                    <div>
                        <input type="radio" id="option1" name="radio-group" value="option-1">
                        <label for="option1" contenteditable="true">Option 1</label>
                    </div>
                    <div>
                        <input type="radio" id="option2" name="radio-group" value="option-2">
                        <label for="option2" contenteditable="true">Option 2</label>
                    </div>
                    <div>
                        <input type="radio" id="option3" name="radio-group" value="option-3">
                        <label for="option3" contenteditable="true">Option 3</label>
                    </div>
                </div>
            `;
            } else if (fieldType === 'text') {

                newField.innerHTML = `
                <label class="form-label" contenteditable="true">${fieldLabel}</label>
                <input type="text" class="form-control" placeholder="${fieldLabel}">
            `;
            }

            newField.addEventListener('dragstart', (event) => {
                event.dataTransfer.setData('text/plain', fieldType);
            });

            formBuilderContainer.appendChild(newField);
        }

        function addField() {
            const formFieldSelect = document.getElementById('formFieldSelect');
            const selectedFieldType = formFieldSelect.value;
            addFieldToForm(selectedFieldType);
        }
        const csrfToken = $('meta[name="csrf-token"]').attr('content');

        function saveForm() {
            const formBuilderContainer = $('#formBuilderContainer');
            const formFields = formBuilderContainer.find('.form-field');

            const formData = formFields.map(function() {
                const fieldType = $(this).data('type');
                const fieldData = {};

                if (fieldType === 'radio-group') {
                    fieldData.type = 'radio-group';
                    fieldData.label = $(this).find('.form-label').text();
                    fieldData.values = $(this).find('input[type="radio"]').map(function() {
                        return {
                            label: $(this).next().text(),
                            value: $(this).val(),
                        };
                    }).get();
                } else if (fieldType === 'text') {
                    fieldData.type = 'text';
                    fieldData.label = $(this).find('.form-label').text();
                }

                return fieldData;
            }).get();

            saveFormDataToDatabase(formData);
        }

        function saveFormDataToDatabase(formData) {
            $.ajax({
                type: 'POST',
                url: '/save-form',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                },
                contentType: 'application/json; charset=utf-8',
                data: JSON.stringify({
                    formData: formData
                }),
                success: function(response) {
                    console.log(response.message);
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    console.log('Response Text:', xhr.responseText);
                    console.log('Status:', status);
                },
            });
        }

        function getFormDataAndDisplay() {
            $.ajax({
                type: 'GET',
                url: '/get-form-data',
                success: function(response) {
                    const formData = response.formData;

                    if (formData.length > 0) {

                        const formBuilderContainer = $('#formBuilderContainer');
                        formBuilderContainer.empty();


                        formData.forEach(function(field) {
                            addFieldToForm(field.type, field);
                        });

                        console.log('Form data loaded successfully.');
                    } else {
                        console.log('No form data found.');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                },
            });
        }


        $(document).ready(function() {
            getFormDataAndDisplay();
        });
    </script>
</body>

</html>
