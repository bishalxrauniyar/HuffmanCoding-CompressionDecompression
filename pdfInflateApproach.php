<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Compress PDF - Smaller PDFs in your browser!</title>
  <meta name="description" content="Free, In-browser, Privacy friendly PDF Compressor. Your files doesn't leave your browser." />
  <link rel="shortcut icon" type="image/x-icon" href="compresspdf-favicon.ico" />
  <link rel="icon" type="image/x-icon" href="compresspdf-favicon.ico" />
  <script defer src="https://cdn.jsdelivr.net/npm/pako@2.0.4/dist/pako.min.js"></script> <!-- For compression -->
</head>

<body>
  <style>
    body {
      margin: 0;
      background-color: #fefefe;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='4' height='4' viewBox='0 0 4 4'%3E%3Cpath fill='%23ff6347' fill-opacity='0.4' d='M1 3h1v1H1V3zm2-2h1v1H3V1z'%3E%3C/path%3E%3C/svg%3E");
    }

    #main_container {
      height: 100vh;
      display: flex;
      justify-content: center;
      font-family: Consolas, SFMono-Regular, Liberation Mono, Menlo, monospace;
    }

    #pdf_input_container {
      height: max-content;
      margin: 10px;
      align-self: center;
    }

    #range_container {
      display: inline-block;
      height: max-content;
      align-self: center;
      margin: 10px;
    }

    #compress_pdf_container {
      height: max-content;
      align-self: center;
      margin: 10px;
    }

    #compress_input_output {
      display: inline-block;
      width: 25px;
      max-width: 25px;
      margin-left: 5px;
      margin-right: 5px;
    }

    #compress_pdf {
      border: 1px solid rgba(255, 99, 71, 0.5);
      background: rgba(255, 99, 71, 0.2);
      padding: 5px;
      outline: none;
      animation: none;
    }

    #selected_pdf_container {
      height: max-content;
      margin: 10px;
      align-self: center;
    }
  </style>

  <div id="main_container">
    <div id="pdf_input_container">
      <input type="file" id="pdf_input" accept="application/pdf" />
    </div>

    <div id="compress_pdf_container">
      <button id="compress_pdf">Compress PDF</button>
      <button id="decompress_pdf" style="display:none;">Decompress PDF</button>
    </div>

    <div id="selected_pdf_container">
      <p id="pdf_file_name"></p>
      <a id="download_link" href="#" download="compressed.pdf" style="display: none;">Download Compressed PDF</a>
    </div>
  </div>

  <script>
    let selectedFile = null;
    let compressedData = null;

    // Get DOM elements
    const pdfInput = document.getElementById('pdf_input');
    const compressBtn = document.getElementById('compress_pdf');
    const decompressBtn = document.getElementById('decompress_pdf');
    const downloadLink = document.getElementById('download_link');
    const pdfFileName = document.getElementById('pdf_file_name');

    // Handle file input
    pdfInput.addEventListener('change', (event) => {
      selectedFile = event.target.files[0];
      if (selectedFile) {
        pdfFileName.textContent = `Selected file: ${selectedFile.name}`;
        downloadLink.style.display = 'none'; // Hide download link initially
      }
    });

    // Compress PDF
    compressBtn.addEventListener('click', () => {
      if (selectedFile) {
        const reader = new FileReader();
        reader.onload = function(e) {
          const arrayBuffer = e.target.result;
          const uint8Array = new Uint8Array(arrayBuffer);

          // Compress using pako
          compressedData = pako.deflate(uint8Array);

          // Create a Blob from compressed data
          const compressedBlob = new Blob([compressedData], {
            type: 'application/pdf'
          });
          const compressedUrl = URL.createObjectURL(compressedBlob);

          // Show download link
          downloadLink.href = compressedUrl;
          downloadLink.download = 'compressed_' + selectedFile.name;
          downloadLink.style.display = 'block';

          // Show decompress button
          decompressBtn.style.display = 'inline-block';
        };
        reader.readAsArrayBuffer(selectedFile);
      } else {
        alert("Please select a PDF file first.");
      }
    });

    // Decompress PDF
    decompressBtn.addEventListener('click', () => {
      if (compressedData) {
        // Decompress using pako
        const decompressedData = pako.inflate(compressedData);

        // Create a Blob from decompressed data
        const decompressedBlob = new Blob([decompressedData], {
          type: 'application/pdf'
        });
        const decompressedUrl = URL.createObjectURL(decompressedBlob);

        // Auto download decompressed PDF
        const decompressedLink = document.createElement('a');
        decompressedLink.href = decompressedUrl;
        decompressedLink.download = 'decompressed_' + selectedFile.name;
        decompressedLink.click();
      }
    });
  </script>

</body>

</html>