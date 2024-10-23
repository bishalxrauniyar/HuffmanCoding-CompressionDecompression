<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Compress PDF - Smaller PDFs in your browser!</title>
    <meta name="description" content="Free, In-browser, Privacy friendly PDF Compressor. Your files don't leave your browser." />
    <link rel="shortcut icon" type="image/x-icon" href="compresspdf-favicon.ico" />
    <link rel="icon" type="image/x-icon" href="compresspdf-favicon.ico" />
    <script defer src="js/pdfkit-standalone-0.10.0.js"></script>
    <script defer src="js/blob-stream-0.1.3.js"></script>
    <script src="js/pdf.min-2.5.207.js"></script>
    <script src="js/FileSaver.min-2.0.4.js"></script>
    <script src="js/sortable.min.1.10.2.js"></script>
    <script src="js/pdf.js"></script>
</head>

<body>
    <style>
        body {
            background-image: url('assets/pdfwallpaper.jpg');
            background-size: cover;
            background-position: center;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden;
            margin: 0;
            padding: 0;
        }

        #main_container {
            height: 60vh;
            width: 60vw;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            font-family: Consolas, SFMono-Regular, Liberation Mono, Menlo, monospace;
            background: rgba(255, 255, 255, 0.85);
            border-radius: 25px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        h1 {
            font-size: 2.5rem;
            color: #333;
            text-align: center;
            margin-bottom: 20px;
            font-family: Consolas, SFMono-Regular, Liberation Mono, Menlo, monospace;
        }

        #pdf_input_container,
        #range_container,
        #compress_pdf_container {
            margin: 10px;
            text-align: center;
        }

        input[type="file"] {
            border: 1px solid rgba(255, 99, 71, 0.5);
            background: rgba(255, 99, 71, 0.2);
            padding: 5px;
            border-radius: 10px;
            font-family: Consolas, SFMono-Regular, Liberation Mono, Menlo, monospace;
            cursor: pointer;
        }

        input[type="file"]:hover {
            box-shadow: 0px 1px 7px 1px rgba(255, 99, 71, 0.25);
            transform: translateY(-1px);
        }

        input[type="range"] {
            width: 500px;
        }

        button {
            border: 1px solid rgba(255, 99, 71, 0.5);
            background: rgba(255, 99, 71, 0.2);
            padding: 10px 20px;
            border-radius: 15px;
            font-family: Consolas, SFMono-Regular, Liberation Mono, Menlo, monospace;
            cursor: pointer;
            transition: 0.3s ease-in-out;
        }

        button:hover {
            box-shadow: 0px 4px 10px rgba(255, 99, 71, 0.3);
            transform: translateY(-3px);
        }

        button:active {
            box-shadow: none;
            transform: translateY(0);
        }

        #selected_pdf_list {
            margin-top: 10px;
        }

        .ghost-class {
            background-color: rgba(255, 99, 71, 0.5);
            border-radius: 5px;
        }
    </style>

    <div id="main_container">
        <h1>PDF Compressor</h1>

        <div id="pdf_input_container">
            <input id="pdf_input" type="file" accept="application/pdf" multiple />
        </div>

        <div id="selected_pdf_container">
            <div id="selected_pdf_list" title="Hold and drag the handle to order the output PDF pages"></div>
        </div>

        <div id="range_container">
            <input id="compress_input" title="Compression Ratio" type="range" min="0" max="1" value="0.5" step="0.1" />
            <p id="compress_input_output" title="Higher the Value, Better the Compression">
                0.5
            </p>
        </div>

        <div id="compress_pdf_container">
            <button id="compress_pdf" title="Compress and Combine selected PDF files in Specified order">
                Compress PDF
            </button>
        </div>
    </div>

    <div style="max-width: 0px; max-height: 0px; overflow: hidden">
        <canvas id="page_canvas"></canvas>
    </div>

    <script>
        const rangeInput = document.getElementById('compress_input');
        const output = document.getElementById('compress_input_output');

        // Update the displayed value when the range slider changes
        rangeInput.addEventListener('input', function() {
            output.textContent = rangeInput.value;
        });
    </script>

</body>

</html>