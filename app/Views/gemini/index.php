<?= $this->extend('layouts/default') ?>

<?= $this->section('content') ?>
<div class="container mt-5">
    <div class="card">
        <div class="card-header">
            <h1>AI Gemini API Interaction</h1>
        </div>
        <div class="card-body">
            <form id="gemini-form">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label for="prompt" class="form-label">Enter your prompt:</label>
                    <textarea id="prompt" name="prompt" class="form-control" rows="5" required></textarea>
                </div>

                <div class="mb-3">
                    <label for="media" class="form-label">Upload Media (Optional):</label>
                    <div id="file-inputs-container">
                        <div class="input-group mb-2">
                            <input type="file" class="form-control" name="media[]">
                            <button type="button" class="btn btn-danger remove-file-btn">Remove</button>
                        </div>
                    </div>
                    <button type="button" id="add-file-btn" class="btn btn-secondary mt-2">Add Another File</button>
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox" id="show-full-response" name="show-full-response" class="form-check-input">
                    <label for="show-full-response" class="form-check-label">Show Full API Response</label>
                </div>

                <button type="submit" id="submit-button" class="btn btn-primary" disabled>Generate Content</button>
            </form>

            <div class="d-flex justify-content-end mt-3">
                <button type="button" id="copy-response-btn" class="btn btn-secondary" style="display: none;">Copy Response</button>
            </div>
            <div id="response" class="mt-3 p-3 bg-light border rounded"></div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const supportedMimeTypes = [
        'image/png', 'image/jpeg', 'image/webp',
        'audio/mpeg', 'audio/mp3', 'audio/wav',
        'video/mov', 'video/mpeg', 'video/mp4', 'video/mpg', 'video/avi', 'video/wmv', 'video/mpegps', 'video/flv',
        'application/pdf',
        'text/plain'
    ];

    const fileInputsContainer = document.getElementById('file-inputs-container');
    const addFileBtn = document.getElementById('add-file-btn');
    const submitButton = document.getElementById('submit-button');
    const promptInput = document.getElementById('prompt');
    promptInput.addEventListener('input', updateMediaFeedback);
    const responseDiv = document.getElementById('response');
    const showFullResponseCheckbox = document.getElementById('show-full-response');
    const copyResponseBtn = document.getElementById('copy-response-btn');

    function createFileInputRow() {
        const row = document.createElement('div');
        row.classList.add('input-group', 'mb-2');

        const input = document.createElement('input');
        input.type = 'file';
        input.name = 'media[]';
        input.classList.add('form-control');
        input.addEventListener('change', updateMediaFeedback);

        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.textContent = 'Remove';
        removeBtn.classList.add('btn', 'btn-danger', 'remove-file-btn');

        removeBtn.addEventListener('click', () => {
            row.remove();
            updateMediaFeedback();
        });

        row.appendChild(input);
        row.appendChild(removeBtn);
        return row;
    }

    function updateMediaFeedback() {
        const allFileRows = fileInputsContainer.querySelectorAll('.input-group');
        let allSupported = true;
        let hasFiles = false;

        allFileRows.forEach((row) => {
            const input = row.querySelector('.form-control');
            const file = input.files.length > 0 ? input.files[0] : null;

            if (file) {
                hasFiles = true;
                if (!supportedMimeTypes.includes(file.type)) {
                    allSupported = false;
                    input.classList.add('is-invalid');
                } else {
                    input.classList.remove('is-invalid');
                }
            }
        });

        const hasPrompt = promptInput.value && promptInput.value.trim().length > 0;
        submitButton.disabled = !((hasPrompt || hasFiles) && allSupported);
    }

    addFileBtn.addEventListener('click', () => {
        const newFileRow = createFileInputRow();
        fileInputsContainer.appendChild(newFileRow);
        updateMediaFeedback();
    });

    fileInputsContainer.addEventListener('change', updateMediaFeedback);

    const existingRemoveBtns = fileInputsContainer.querySelectorAll('.remove-file-btn');
    existingRemoveBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            const row = e.currentTarget.closest('.input-group');
            if (row) row.remove();
            updateMediaFeedback();
        });
    });

    updateMediaFeedback();

    copyResponseBtn.addEventListener('click', async () => {
        const textToCopy = responseDiv.textContent;
        try {
            await navigator.clipboard.writeText(textToCopy);
            const originalText = copyResponseBtn.textContent;
            copyResponseBtn.textContent = 'Copied!';
            setTimeout(() => {
                copyResponseBtn.textContent = originalText;
            }, 2000);
        } catch (err) {
            console.error('Failed to copy: ', err);
            alert('Failed to copy response. Please copy manually.');
        }
    });

    document.getElementById('gemini-form').addEventListener('submit', async function(event) {
        event.preventDefault();
        updateMediaFeedback();

        if (submitButton.disabled) {
            return;
        }

        const formEl = document.getElementById('gemini-form');
        const formData = new FormData(formEl);
        formData.set('prompt', promptInput.value);

        try {
            responseDiv.innerHTML = '<div class="d-flex justify-content-center"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>';

            let apiUrl = '<?= url_to('gemini.generate') ?>';
            if (showFullResponseCheckbox.checked) {
                apiUrl += '?full_response=true';
            }

            const response = await fetch(apiUrl, {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (!response.ok) {
                const errorMessage = data.error || 'An unknown error occurred.';
                responseDiv.innerHTML = `<div class="alert alert-danger">${errorMessage}</div>`;
            } else {
                if (showFullResponseCheckbox.checked) {
                    responseDiv.innerHTML = `<pre>${JSON.stringify(data, null, 2)}</pre>`;
                } else {
                    let generatedText = '';
                    if (data.response) {
                        generatedText = data.response;
                    } else if (data.candidates && data.candidates.length > 0 && data.candidates[0].content && data.candidates[0].content.parts) {
                        data.candidates[0].content.parts.forEach(part => {
                            if (part.text) {
                                generatedText += part.text + '\n';
                            }
                        });
                    } else {
                        generatedText = JSON.stringify(data, null, 2);
                    }
                    responseDiv.innerHTML = generatedText;
                }
                copyResponseBtn.style.display = 'inline-block';
            }
        } catch (error) {
            console.error('Fetch error:', error);
            responseDiv.innerHTML = `<div class="alert alert-danger">An error occurred while communicating with the server. Please try again.</div>`;
            copyResponseBtn.style.display = 'none';
        }
    });
</script>
<?= $this->endSection() ?>
