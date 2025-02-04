<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clone Element by ID</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .form-container {
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #ccc;
        }
    </style>
</head>

<body>
    <div id="formArea">
        <div class="form-container" id="form1">
            <h3>Form 1</h3>
            <label for="name">Name:</label>
            <input type="text" id="name" name="name[]" required>
            <br>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email[]" required>
            <br>
            <button class="removeBtn">Remove</button>
        </div>
    </div>
    <button id="addFormBtn">Add Another Form</button>

    <script>
        $(document).ready(function() {
            // Function to clone an element by ID
            function cloneElementById(elementId, containerId) {
                // Clone the element
                var newElement = $('#' + elementId).clone();

                // Update the ID of the new element to ensure it's unique
                var newElementId = elementId.replace(/\d+$/, function(match) {
                    return parseInt(match) + 1; // Increment the number in the ID
                });
                newElement.attr('id', newElementId);

                // Update the title of the cloned form
                var newTitle = 'Form ' + (parseInt(elementId.match(/\d+/)[0]) + 1);
                newElement.find('h3').text(newTitle);

                // Clear the input values in the cloned element
                newElement.find('input').val('');

                // Append the new element to the specified container
                $('#' + containerId).append(newElement);
            }

            // Event listener for the button to add a new form
            $('#addFormBtn').click(function() {
                cloneElementById('form1', 'formArea');
            });

            // Event delegation to handle the remove button
            $('#formArea').on('click', '.removeBtn', function() {
                $(this).closest('.form-container').remove();
            });
        });
    </script>
</body>

</html>